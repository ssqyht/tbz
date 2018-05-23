<?php

namespace common\models;

use Yii;
use common\components\traits\TimestampTrait;

/**
 * This is the model class for table "{{%material_member}}".
 * @SWG\Definition(type="object", @SWG\Xml(name="MaterialMember"))
 *
 * @property int $id @SWG\Property(property="id", type="integer", description="")
 * @property int $user_id 用户id @SWG\Property(property="userId", type="integer", description=" 用户id")
 * @property int $folder_id 文件夹 @SWG\Property(property="folderId", type="integer", description=" 文件夹")
 * @property string $file_name 文件名 @SWG\Property(property="fileName", type="string", description=" 文件名")
 * @property string $thumbnail 图片路径 @SWG\Property(property="thumbnail", type="string", description=" 图片路径")
 * @property int $file_id 文件id @SWG\Property(property="fileId", type="integer", description=" 文件id")
 * @property int $mode 素材模式 临时，正式 @SWG\Property(property="mode", type="integer", description=" 素材模式 临时，正式")
 * @property int $created_at 创建时间 @SWG\Property(property="createdAt", type="integer", description=" 创建时间")
 */
class MaterialMember extends \yii\db\ActiveRecord
{

    use TimestampTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%material_member}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at'], 'required'],
            [['user_id', 'folder_id', 'file_id', 'mode', 'created_at'], 'integer'],
            [['file_name', 'thumbnail'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户id',
            'folder_id' => '文件夹',
            'file_name' => '文件名',
            'thumbnail' => '图片路径',
            'file_id' => '文件id',
            'mode' => '素材模式 临时，正式',
            'created_at' => '创建时间',
        ];
    }
}
