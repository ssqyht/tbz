<?php

use yii\db\Migration;

/**
 * Handles the creation of table `material_member`.
 */
class m180521_085313_create_material_member_table extends Migration
{
    public $table_name = '{{%material_member}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->table_name, [
            'id' => $this->primaryKey()->unsigned(),
            'source' => $this->tinyInteger(2)->notNull()->comment('文件模块来源(1:自主上传源文件,2:询价单提交,3:模板,4:新闻中心,5：商品,6:商品分类,7:虚拟记录,8:编辑器用户图片,9:评价晒图,10:优惠券展示图,11:积分商城的商品图片,12:管理后台独立静态页背景图,13:图帮主用户素材,14:图帮主会员头像上传,15:团队logo素材,16:团队素材,17:合作商,18:合作商模板分类图,19:图帮主秘籍缩略图,20:合作商产品链接logo,21:图帮主专题缩略图,22:社群吐槽缩略图 23:图帮主专题列表缩略图 24:编辑器表单图片)')->defaultValue(0)->unsigned(),
            'sid' => $this->tinyInteger(2)->notNull()->comment('来源模块对应信息id')->defaultValue(0)->unsigned(),
            'user_id' => $this->integer(10)->notNull()->comment('用户id')->defaultValue(0)->unsigned(),
            'session_id'=> $this->string(30)->notNull()->comment('匿名sessionId(用于匿名用户临时编辑器图库)')->defaultValue(''),
            'filename'=> $this->string(100)->notNull()->comment('文件名')->defaultValue(''),
            'old_name'=> $this->string(100)->notNull()->comment('原文件名')->defaultValue(''),
            'title'=> $this->string(60)->notNull()->comment('素材标题')->defaultValue(''),
            'width' => $this->integer(6)->notNull()->comment('编辑器用户素材宽px')->defaultValue(0)->unsigned(),
            'height' => $this->integer(6)->notNull()->comment('编辑器用户素材高px')->defaultValue(0)->unsigned(),
            'size' => $this->integer(10)->notNull()->comment('文件大小')->defaultValue(0)->unsigned(),
            'status' => $this->tinyInteger(2)->notNull()->comment('10正常,7到回收站,3删除')->defaultValue(10)->unsigned(),
            'folder_id' => $this->integer(10)->notNull()->comment('所在文件夹')->defaultValue(0)->unsigned(),
            'created_at' => $this->integer(11)->notNull()->comment('创建日期')->defaultValue(0)->unsigned(),
            'updated_at' => $this->integer(11)->notNull()->comment('修改时间')->defaultValue(0)->unsigned(),
        ]);
        $this->addCommentOnTable($this->table_name, '个人素材信息表');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->table_name);
    }
}
