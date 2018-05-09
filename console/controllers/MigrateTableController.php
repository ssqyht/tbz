<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace console\controllers;

use common\models\forms\FileUpload;
use common\models\Member;
use common\models\MemberOauth;
use Yii;
use yii\base\Exception;
use yii\console\Controller;
use yii\data\SqlDataProvider;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;
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
                $imageUrl = Yii::$app->params['image_url'] . '/uploads/face/'.$model['id'].'_180.png';
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

    public function getPageSize()
    {
        return $this->test ? 3 : $this->defaultPageSize;
    }

}