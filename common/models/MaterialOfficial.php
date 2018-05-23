<?php

namespace common\models;

use Yii;
use common\components\traits\TimestampTrait;

/**
 * This is the model class for table "{{%material_official}}".
 * @SWG\Definition(type="object", @SWG\Xml(name="MaterialOfficial"))
 *
 * @property int $id @SWG\Property(property="id", type="integer", description="")
 * @property int $user_id 用户ID @SWG\Property(property="userId", type="integer", description=" 用户ID")
 * @property int $cid 素材分类ID @SWG\Property(property="cid", type="integer", description=" 素材分类ID")
 * @property string $name 素材名 @SWG\Property(property="name", type="string", description=" 素材名")
 * @property string $tags 素材搜索标签 @SWG\Property(property="tags", type="string", description=" 素材搜索标签")
 * @property string $thumbnail 文件路径 @SWG\Property(property="thumbnail", type="string", description=" 文件路径")
 * @property int $file_id 文件id @SWG\Property(property="fileId", type="integer", description=" 文件id")
 * @property int $file_type 文件类型 @SWG\Property(property="fileType", type="integer", description=" 文件类型")
 * @property int $width 宽度 @SWG\Property(property="width", type="integer", description=" 宽度")
 * @property int $height 高度 @SWG\Property(property="height", type="integer", description=" 高度")
 * @property int $status 素材状态 @SWG\Property(property="status", type="integer", description=" 素材状态")
 * @property int $created_at 创建时间 @SWG\Property(property="createdAt", type="integer", description=" 创建时间")
 * @property int $updated_at 修改时间 @SWG\Property(property="updatedAt", type="integer", description=" 修改时间")
 * @property string $extra_contents 素材额外字段 @SWG\Property(property="extraContents", type="string", description=" 素材额外字段")
 */
class MaterialOfficial extends \yii\db\ActiveRecord
{

    use TimestampTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%material_official}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'cid', 'file_id', 'file_type', 'created_at', 'updated_at', 'extra_contents'], 'required'],
            [['user_id', 'cid', 'file_id', 'file_type', 'width', 'height', 'status', 'created_at', 'updated_at'], 'integer'],
            [['extra_contents'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['tags', 'thumbnail'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'cid' => '素材分类ID',
            'name' => '素材名',
            'tags' => '素材搜索标签',
            'thumbnail' => '文件路径',
            'file_id' => '文件id',
            'file_type' => '文件类型',
            'width' => '宽度',
            'height' => '高度',
            'status' => '素材状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
            'extra_contents' => '素材额外字段',
        ];
    }
}
