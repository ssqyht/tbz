<?php

namespace common\models;

use Yii;
use common\components\traits\TimestampTrait;
use common\components\traits\ModelFieldsTrait;
/**
 * This is the model class for table "{{%upfile}}".
 * @SWG\Definition(type="object", @SWG\Xml(name="Upfile"))
 *
 * @property int $id @SWG\Property(property="id", type="integer", description="")
 * @property int $source 文件模块来源(1:自主上传源文件,2:询价单提交,3:模板,4:新闻中心,5：商品,6:商品分类,7:虚拟记录,8:编辑器用户图片,9:评价晒图,10:优惠券展示图,11:积分商城的商品图片,12:管理后台独立静态页背景图,13:图帮主用户素材,14:图帮主会员头像上传,15:团队logo素材,16:团队素材,17:合作商,18:合作商模板分类图,19:图帮主秘籍缩略图,20:合作商产品链接logo,21:图帮主专题缩略图,22:社群吐槽缩略图 23:图帮主专题列表缩略图 24:编辑器表单图片) @SWG\Property(property="source", type="integer", description=" 文件模块来源(1:自主上传源文件,2:询价单提交,3:模板,4:新闻中心,5：商品,6:商品分类,7:虚拟记录,8:编辑器用户图片,9:评价晒图,10:优惠券展示图,11:积分商城的商品图片,12:管理后台独立静态页背景图,13:图帮主用户素材,14:图帮主会员头像上传,15:团队logo素材,16:团队素材,17:合作商,18:合作商模板分类图,19:图帮主秘籍缩略图,20:合作商产品链接logo,21:图帮主专题缩略图,22:社群吐槽缩略图 23:图帮主专题列表缩略图 24:编辑器表单图片)")
 * @property int $sid 来源模块对应信息id @SWG\Property(property="sid", type="integer", description=" 来源模块对应信息id")
 * @property int $team_id 团队id @SWG\Property(property="teamId", type="integer", description=" 团队id")
 * @property int $user_id 用户id @SWG\Property(property="userId", type="integer", description=" 用户id")
 * @property string $session_id 匿名sessionId(用于匿名用户临时编辑器图库) @SWG\Property(property="sessionId", type="string", description=" 匿名sessionId(用于匿名用户临时编辑器图库)")
 * @property string $filename 文件名 @SWG\Property(property="filename", type="string", description=" 文件名")
 * @property string $old_name 原文件名 @SWG\Property(property="oldName", type="string", description=" 原文件名")
 * @property string $title 素材标题 @SWG\Property(property="title", type="string", description=" 素材标题")
 * @property int $width 编辑器用户素材宽px @SWG\Property(property="width", type="integer", description=" 编辑器用户素材宽px")
 * @property int $height 编辑器用户素材高px @SWG\Property(property="height", type="integer", description=" 编辑器用户素材高px")
 * @property int $size 文件大小 @SWG\Property(property="size", type="integer", description=" 文件大小")
 * @property int $status 10正常,7到回收站,3删除 @SWG\Property(property="status", type="integer", description=" 10正常,7到回收站,3删除")
 * @property int $folder_id 所在文件夹 @SWG\Property(property="folderId", type="integer", description=" 所在文件夹")
 * @property int $created_at 创建日期 @SWG\Property(property="createdAt", type="integer", description=" 创建日期")
 * @property int $updated_at 修改时间 @SWG\Property(property="updatedAt", type="integer", description=" 修改时间")
 */
class Upfile extends \yii\db\ActiveRecord
{

    use TimestampTrait;
    use ModelFieldsTrait;

    /** @var string 素材正常状态 */
    const STATUS_NORMAL = '10';

    /** @var string 回收站 */
    const STATUS_TRASH = '7';

    /** @var string 删除状态 */
    const STATUS_DELETE = '3';
    /**
     * @var array 前端页面返回参数
     */
    static $frontendFields = ['title','source', 'sid','folder_id', 'team_id','user_id', 'width','height','size','status','session_id','filename','old_name'];
    /**
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%upfile}}';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source', 'sid', 'team_id', 'user_id', 'width', 'height', 'size', 'status', 'folder_id', 'created_at', 'updated_at'], 'integer'],
            [['session_id'], 'string', 'max' => 30],
            [['filename', 'old_name'], 'string', 'max' => 100],
            [['title'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'source' => 'Source',
            'sid' => 'Sid',
            'team_id' => 'Team ID',
            'user_id' => 'User ID',
            'session_id' => 'Session ID',
            'filename' => 'Filename',
            'old_name' => 'Old Name',
            'title' => 'Title',
            'width' => 'Width',
            'height' => 'Height',
            'size' => 'Size',
            'status' => 'Status',
            'folder_id' => 'Folder ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 排序
     * @return \yii\db\ActiveQuery
     */
    public static function sort()
    {
        return static::find()->orderBy(['id' => SORT_DESC]);
    }
    /**
     * @param bool $insert
     * @param array $changedAttributes
     * 更新缓存
     */
    public function afterSave($insert, $changedAttributes)
    {
        // 更新缓存
        if ($changedAttributes) {
            Yii::$app->dataCache->updateCache(static::class);
        }
        parent::afterSave($insert, $changedAttributes);
    }
}
