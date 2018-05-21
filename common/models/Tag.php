<?php

namespace common\models;

use common\components\traits\ModelErrorTrait;
use Yii;
use common\components\traits\TimestampTrait;
use common\components\traits\ModelFieldsTrait;
/**
 * This is the model class for table "{{%tag}}".
 * @SWG\Definition(type="object", @SWG\Xml(name="Tag"))
 *
 * @property int $tag_id @SWG\Property(property="tagId", type="integer", description="")
 * @property string $name Tag名称 @SWG\Property(property="name", type="string", description=" Tag名称")
 * @property int $type tag种类 @SWG\Property(property="type", type="integer", description=" tag种类")
 * @property int $sort 排序名称 @SWG\Property(property="sort", type="integer", description=" 排序名称")
 * @property int $updated_at 修改时间 @SWG\Property(property="updatedAt", type="integer", description=" 修改时间")
 * @property int $created_at 创建时间 @SWG\Property(property="createdAt", type="integer", description=" 创建时间")
 */
class Tag extends \yii\db\ActiveRecord
{

    use TimestampTrait;
    use ModelErrorTrait;
    use ModelFieldsTrait;
    /** @var integer 模板状态 */
    const template_official_status = 20;

    /** @var int 行业 */
    const TYPE_INDUSTRY = 2;
    /** @var int 风格 */
    const TYPE_STYLE = 1;
    /** @var int 功能 */
    const TYPE_FUNCTION = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tag}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['type', 'sort', 'updated_at', 'created_at'], 'integer'],
            [['name'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tag_id' => 'Tag ID',
            'name' => 'Name',
            'type' => 'Type',
            'sort' => 'Sort',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }

    public function frontendFields()
    {
        return [
            'tag_id', 'name',
        ];
    }

}
