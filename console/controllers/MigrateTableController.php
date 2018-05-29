<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace console\controllers;

use common\models\Classify;
use common\models\FileUsedRecord;
use common\models\forms\FileUpload;
use common\models\Member;
use common\models\MemberOauth;
use common\models\Tag;
use Yii;
use yii\base\Exception;
use yii\console\Controller;
use yii\data\SqlDataProvider;
use yii\db\Query;
use yii\helpers\Console;

/**
 * 5.0数据迁移类
 * @package console\controllers
 * @author thanatos <thanatos915@163.com>
 */
class MigrateTableController extends Controller
{

    public $test;

    private $defaultPageSize = 5000;

    public function options($actionID)
    {
        return ['test'];
    }

    public function optionAliases()
    {
        return ['t' => 'test'];
    }

    /**
     * 用户表迁移
     * @throws \yii\db\Exception
     * @author thanatos <thanatos915@163.com>
     */
    public function actionUser()
    {
        $db = Yii::$app->dbMigrateDdy;

        $query = (new Query())
            ->from('com_member');

        $count = $db->createCommand($query->select('count(*)')->createCommand($db)->getRawSql())->queryScalar();

        $dataProvider = new SqlDataProvider([
            'db' => $db,
            'sql' => $query->select('*')->createCommand($db)->getRawSql(),
            'totalCount' => $count,
            'pagination' => [
                'pageSize' => $this->getPageSize(),
            ],
        ]);

        $errorIds = [];
        $breakIds = [];
        $successAmount = 0;
        // 查询一次
        $dataProvider->prepare();
        // 循环分页
        for ($currentPage = 0; $currentPage < $dataProvider->pagination->getPageCount(); $currentPage++) {
            // 重置数据
            if ($currentPage > 0) {
                $dataProvider->pagination->setPage($currentPage);
                $dataProvider->prepare(true);
            }

            // 处理数据
            $models = $dataProvider->getModels();
            $data = [];
            foreach ($models as $key => $model) {
                $headimg_id = 0;
                $headimg_url = '';
                // 头像
                $imageUrl = Yii::$app->params['image_url'] . '/uploads/face/' . $model['id'] . '_180.png';
                echo $imageUrl . "\n";
                if ($result = FileUpload::upload($imageUrl, FileUpload::DIR_OTHER)) {
                    $headimg_id = $result->file_id ?? 0;
                    $headimg_url = $result->path ?? '';
                }


                $isBreak = false;
                // 用户状态
                if ($model['gid'] == 0) {
                    $isBreak = true;
                }
                if (!empty($model['punish'])) {
                    $punish = json_decode(stripslashes($model['punish']), true);
                    if (!empty($punish) && is_array($punish) && (empty($punish['deadline']) || time() < $punish['deadline'])) {
                        if (in_array('1', $punish['type'])) {
                            $isBreak = true;
                        }
                    }
                }
                if ($isBreak) {
                    // 跳过迁移，记录日志
                    $breakIds[] = $model['id'];
                    continue;
                }

                $data = [
                    'username' => $model['nickname'] ?: ($model['name'] ?: ($model['mobile'] ?: $model['email'])),
                    'mobile' => $model['mobile'] ?: '',
                    'sex' => $model['sex'],
                    'headimg_id' => $headimg_id ?: 0,
                    'headimg_url' => $headimg_url ?: '',
                    'coin' => $model['coin'],
                    'last_login_time' => strtotime($model['lastTime']),
                    'password_hash' => '',
                    'salt' => $model['salt'],
                    'password' => $model['password'],
                    'status' => 10,
                    'created_at' => strtotime($model['created']),
                    'updated_at' => time(),
                ];

                $transaction = Member::getDb()->beginTransaction();
                try {
                    $member = new Member();
                    $member->load($data, '');
                    $member->id = $model['id'];
                    if (!($member->validate() && $member->save())) {
                        throw new Exception('save member error');
                    }

                    $oauthKey = $model['qqUnionID'] ?: ($model['wxUnionID'] ?: '');
                    if ($oauthKey) {
                        $oauthModel = new MemberOauth();
                        $oauthModel->load([
                            'user_id' => $member->id,
                            'oauth_name' => MemberOauth::OAUTH_QQ,
                            'oauth_key' => $oauthKey
                        ], '');
                        if (!($oauthModel->validate() && $oauthModel->save())) {
                            throw new Exception('save member_oauth error');
                        }
                    }
                    // 添加文件使用日志
                    if ($member->headimg_id) {
                        $usedModel = new FileUsedRecord(['scenario' => FileUsedRecord::SCENARIO_CREATE]);
                        $usedModel->load([
                            'user_id' => $member->id,
                            'file_id' => $member->headimg_id,
                            'purpose' => FileUsedRecord::PURPOSE_HEADIMG,
                            'purpose_id' => $member->id,
                        ], '');
                        if (!$usedModel->save()) {
                            throw new Exception('save file_used_record error');
                        }
                    }

                    $successAmount++;
                    $this->stdout('Member: ' . $model['id'] . '迁移成功' . "\n", Console::FG_GREEN);
                    $transaction->commit();
                } catch (\Throwable $throwable) {
                    $transaction->rollBack();
                    // 记录错误
                    $errorIds[] = $model['id'];
                    Yii::error($throwable->getMessage(), 'migrateUser');
                    break;
                }
            }

            if ($this->test && $currentPage > 1) {
                break;
            }

        }

        $this->stdout('迁移失败: ' . (implode(',', $errorIds) ?: '""') . "\n", Console::FG_RED);
        $this->stdout('跳过迁移: ' . (implode(',', $breakIds) ?: '""') . "\n", Console::FG_YELLOW);
        $this->stdout('迁移成功数: ' . $successAmount . "\n", Console::FG_GREEN);

    }

    /**
     * 迁移分类表
     * @throws \yii\db\Exception
     * @author thanatos <thanatos915@163.com>
     */
    public function actionProduct()
    {

        // 清空表
        Classify::getDb()->createCommand()->delete(Classify::tableName())->execute();

        $db = Yii::$app->dbMigrateDdy;
        $query = (new Query())
            ->from('com_template_product')
            ->where('coopId = 0 and status = 1 and name != parentName and product = parentProduct');

        $list = $query->all($db);
        $data = [];
        foreach ($list as $key => $model) {
            $data[] = [
                'category_id' => 0,
                'pid' => 0,
                'name' => $model['parentName'],
                'default_price' => 0,
                'is_hot' => 0,
                'is_new' => 0,
                'default_edit' => '',
                'order_link' => '',
                'thumbnail' => '',
                'thumbnail_id' => 0,
                'sort' => 0,
                'is_open' => 0,
                'status' => 20,
                'is_recommend' => 0,
                'created_at' => time(),
                'updated_at' => time(),
            ];
        }
        Classify::getDb()->createCommand()->batchInsert(Classify::tableName(), ['category_id', 'pid', 'name', 'default_price', 'is_hot', 'is_new', 'default_edit', 'order_link', 'thumbnail', 'thumbnail_id', 'sort', 'is_open', 'is_recommend', 'status', 'created_at', 'updated_at'], $data)->execute();

        $parentList = Classify::findAll(['pid' => 0]);
        foreach ($parentList as $k => $parent) {
            $query = (new Query())
                ->from('com_template_product')
                ->where('coopId = 0 and status = 1 and parentName = "' . $parent->name . '"');

            $list = $query->all($db);
            $data = [];
            foreach ($list as $key => $model) {
                $category = $this->getCategory($model['type']);
                if ($category) {
                    // 上传文件
                    $imageUrl = Yii::$app->params['image_url'] . '/uploads' . $model['thumbnail'];
                    if ($result = FileUpload::upload($imageUrl, FileUpload::DIR_OTHER)) {
                        $thumbnail_id = $result->file_id ?: 0;
                        $thumbnail = $result->path ?: '';
                    }

                    $data[] = [
                        'category_id' => $category,
                        'pid' => $parent->classify_id,
                        'name' => $model['name'],
                        'default_price' => $model['defaultPrice'],
                        'is_hot' => $model['recommend'] == 1 ? 1 : 0,
                        'is_new' => $model['recommend'] == 2 ? 1 : 0,
                        'default_edit' => $model['editConfig'],
                        'order_link' => $model['goodsLink'] ?: '',
                        'thumbnail' => $thumbnail ?: '',
                        'thumbnail_id' => $thumbnail_id ?: 0,
                        'sort' => $model['sort'] ?: 0,
                        'is_open' => $model['isOpen'],
                        'is_recommend' => (int)$model['recommend2'] ? 1 : 0,
                        'status' => 20,
                        'created_at' => time(),
                        'updated_at' => time(),
                    ];
                }
            }
            Classify::getDb()->createCommand()->batchInsert(Classify::tableName(), ['category_id', 'pid', 'name', 'default_price', 'is_hot', 'is_new', 'default_edit', 'order_link', 'thumbnail', 'thumbnail_id', 'sort', 'is_open', 'is_recommend', 'status', 'created_at', 'updated_at'], $data)->execute();

        }


        // 插入没有子分类的值
        $query = (new Query())
            ->from('com_template_product')
            ->where('coopId = 0 and status = 1 and name = parentName');

        $list = $query->all($db);

        $data = [];
        foreach ($list as $key => $model) {
            $category = $this->getCategory($model['type']);
            if ($category) {
                // 上传文件
                $imageUrl = Yii::$app->params['image_url'] . '/uploads' . $model['thumbnail'];
                if ($result = FileUpload::upload($imageUrl, FileUpload::DIR_OTHER)) {
                    $thumbnail_id = $result->file_id ?: 0;
                    $thumbnail = $result->path ?: '';
                }

                $data[] = [
                    'category_id' => $category,
                    'pid' => 0,
                    'name' => $model['name'],
                    'default_price' => $model['defaultPrice'],
                    'is_hot' => $model['recommend'] == 1 ? 1 : 0,
                    'is_new' => $model['recommend'] == 2 ? 1 : 0,
                    'default_edit' => $model['editConfig'],
                    'order_link' => $model['goodsLink'] ?: '',
                    'thumbnail' => $thumbnail ?: '',
                    'thumbnail_id' => $thumbnail_id ?: 0,
                    'sort' => $model['sort'] ?: 0,
                    'is_open' => $model['isOpen'],
                    'is_recommend' => (int)$model['recommend2'] ? 1 : 0,
                    'status' => 20,
                    'created_at' => time(),
                    'updated_at' => time(),
                ];
            }
        }

        Classify::getDb()->createCommand()->batchInsert(Classify::tableName(), ['category_id', 'pid', 'name', 'default_price', 'is_hot', 'is_new', 'default_edit', 'order_link', 'thumbnail', 'thumbnail_id', 'sort', 'is_open', 'is_recommend', 'status', 'created_at', 'updated_at'], $data)->execute();
        // 更新文件引用
        /** @var Classify[] $models */
        $models = Classify::find()->all();
        $data = [];
        foreach ($models as $key => $model) {
            if ($model->thumbnail_id) {
                $data[] = [
                    'user_id' => 1,
                    'file_id' => $model->thumbnail_id,
                    'purpose' => FileUsedRecord::PURPOSE_CLASSIFY,
                    'purpose_id' => $model->classify_id,
                    'created_at' => time(),
                ];
            }
        }
        FileUsedRecord::getDb()->createCommand()->batchInsert(FileUsedRecord::tableName(), ['user_id', 'file_id', 'purpose', 'purpose_id', 'created_at'], $data)->execute();
        $this->stdout('迁移成功' . "\n", Console::FG_GREEN);

    }

    /**
     * 迁移Tag表
     * @throws \yii\db\Exception
     * @author thanatos <thanatos915@163.com>
     */
    public function actionTags()
    {
        $db = Yii::$app->dbMigrateDdy;
        Tag::getDb()->createCommand()->delete(Tag::tableName())->execute();
        $query = (new Query())
            ->from('com_template_industry')
            ->where(['coopId' => 0]);

        $list = $query->all($db);

        $data = [];
        foreach ($list as $key => $model) {
            $data[] = [
                'name' => $model['name'],
                'type' => Tag::TYPE_INDUSTRY,
                'sort' => 0,
                'created_at' => time(),
                'updated_at' => time(),
            ];
        }

        Tag::getDb()->createCommand()->batchInsert(Tag::tableName(), ['name', 'type', 'sort', 'created_at', 'updated_at'], $data)->execute();

        $query = (new Query())
            ->from('com_template_style')
            ->where(['coopId' => 0]);

        $list = $query->all($db);

        $data = [];
        foreach ($list as $key => $model) {
            $data[] = [
                'name' => $model['name'],
                'type' => Tag::TYPE_STYLE,
                'sort' => 0,
                'created_at' => time(),
                'updated_at' => time(),
            ];
        }

        Tag::getDb()->createCommand()->batchInsert(Tag::tableName(), ['name', 'type', 'sort', 'created_at', 'updated_at'], $data)->execute();

        $query = (new Query())
            ->from('com_template_function')
            ->where(['coopId' => 0]);

        $list = $query->all($db);

        $data = [];
        foreach ($list as $key => $model) {
            $data[] = [
                'name' => $model['name'],
                'type' => Tag::TYPE_FUNCTION,
                'sort' => 0,
                'created_at' => time(),
                'updated_at' => time(),
            ];
        }

        Tag::getDb()->createCommand()->batchInsert(Tag::tableName(), ['name', 'type', 'sort', 'created_at', 'updated_at'], $data)->execute();

        $this->stdout('迁移成功' . "\n", Console::FG_GREEN);

    }

    public function getPageSize()
    {
        return $this->test ? 3 : $this->defaultPageSize;
    }

    private function getCategory($type)
    {
        switch ($type) {
            case 0:
                return 2;
            case 2:
                return 4;
            case 3:
                return 6;
            case 5:
                return 7;
            case 6:
                return 5;
            case 7:
                return 3;

        }
    }

}