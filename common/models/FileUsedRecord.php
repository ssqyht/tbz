<?php

namespace common\models;

use common\components\traits\ModelErrorTrait;
use common\components\traits\TimestampTrait;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%file_used_record}}".
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $file_id 文件ID
 * @property int $purpose 用例
 * @property int $purpose_id 用途ID
 * @property int $created_at 登录时间
 */
class FileUsedRecord extends \yii\db\ActiveRecord
{
    use TimestampTrait;
    use ModelErrorTrait;

    /** @var int 用户头像 */
    const PURPOSE_HEADIMG = 10;
    /** @var int 模板使用类型 */
    const PURPOSE_TEMPLATE = 12;
    /** @var int 素材使用类型 */
    const PURPOSE_MATERIAL = 13;
    /** @var int 分类缩略图 */
    const PURPOSE_CLASSIFY = 14;

    /** @var int purpose 最大值 */
    const PURPOSE_MAX = self::PURPOSE_MATERIAL;

    /** @var string 增加使用记录 */
    const SCENARIO_CREATE = 'create';
    /** @var string 删除使用记录 */
    const SCENARIO_DROP = 'drop';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file_used_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'file_id', 'purpose', 'purpose_id'], 'required'],
            [['user_id', 'file_id', 'purpose_id', 'created_at'], 'integer'],
            [['purpose'], 'integer', 'min' => 1, 'max' => static::PURPOSE_MAX],
        ];
    }


    public function scenarios()
    {
        $scenarios = [
            static::SCENARIO_CREATE => ['user_id', 'file_id', 'purpose', 'purpose_id'],
            static::SCENARIO_DROP => ['user_id', 'file_id', 'purpose', 'purpose_id'],
        ];
        return ArrayHelper::merge(parent::scenarios(), $scenarios);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'file_id' => 'File ID',
            'purpose' => 'Purpose',
            'purpose_id' => 'Purpose ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * 删除文件使用记录
     * @param int $user_id 用户id
     * @param int $file_id 文件id
     * @param int $purpose 文件用途
     * @param int $purpose_id 用途表id
     * @return bool|FileUsedRecord|false|int|null|string
     * @author thanatos <thanatos915@163.com>
     */
    public static function dropRecord($user_id, $file_id, $purpose, $purpose_id)
    {
        $model = new static(['scenario' => static::SCENARIO_DROP]);
        $data = [
            'user_id' => $user_id,
            'file_id' => $file_id,
            'purpose' => $purpose,
            'purpose_id'  => $purpose_id
        ];
        if ($result = $model->submit($data)) {
            return $result;
        } else {
            return $model;
        }

    }

    /**
     * 增加文件引用记录
     * @param int $user_id 用户id
     * @param int $file_id 文件id
     * @param int $purpose 文件用途
     * @param int $purpose_id 用途表id
     * @return bool|FileUsedRecord|false|int|null|string
     * @author thanatos <thanatos915@163.com>
     */
    public static function createRecord($user_id, $file_id, $purpose, $purpose_id)
    {
        $model = new static(['scenario' => static::SCENARIO_CREATE]);
        $data = [
            'user_id' => $user_id,
            'file_id' => $file_id,
            'purpose' => $purpose,
            'purpose_id'  => $purpose_id
        ];
        if ($result = $model->submit($data)) {
            return $result;
        } else {
            return $model;
        }
    }

    /**
     * 文件处理总入口
     * @return bool|FileUsedRecord|false|int|null|string
     */
    public function submit($params)
    {
        $this->load($params, '');
        if (!$this->validate()) {
            return false;
        }

        // 添加使用记录时 检查动作
        if ($this->scenario == static::SCENARIO_CREATE) {
            return $this->create();
        }
        // 删除使用记录
        elseif ($this->scenario === static::SCENARIO_DROP) {
            return $this->drop();
        }

    }

    /**
     * 添加使用记录
     * @return bool|FileUsedRecord|null|string
     */
    protected function create()
    {

        $model = '';
        // 用户头像只有一个
        if ($this->purpose === static::PURPOSE_HEADIMG) {
            if ($model = $this->findByUserUsed()) {
                $model->file_id = $this->file_id;
            }
        }
        // 我的素材只能添加一个
        if ($this->purpose === static::PURPOSE_MATERIAL) {
            $model = $this->findByUserUsed();
        }

        if (!is_object($model)) {
            $model = clone $this;
        }

        return $model->save() ? $model : false;
    }

    /**
     * 删除使用记录
     * @return bool|false|int
     */
    protected function drop()
    {
        $model = static::findOne($this->getAttributes(['user_id', 'file_id', 'purpose', 'purpose_id']));
        if (empty($model)) {
            return true;
        }
        try {
            $result = $model->delete();
        } catch (\Exception $e) {
            $this->addError('', $e->getMessage());
            return false;
        } catch (\Throwable $e)  {
            $this->addError('', $e->getMessage());
            return false;
        }
        return $result;
    }

    /**
     * 根据使用文件种类查询
     * @return FileUsedRecord|null
     */
    private function findByUserUsed()
    {
        $condition = [
            'user_id' => $this->user_id,
            'purpose' => $this->purpose,
            'purpose_id' => $this->purpose_id,
        ];
        switch ($this->purpose) {
            case static::PURPOSE_HEADIMG:
                $method = 'findOne';
                break;
            case static::PURPOSE_MATERIAL:
                $method = 'findOne';
                $condition['file_id'] = $this->file_id;
                break;
        }
        return static::findOne($condition);
    }


}
