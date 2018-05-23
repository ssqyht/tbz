<?php

namespace common\models\forms;

use common\models\TbzSubject;
use yii\base\Model;
use common\components\traits\ModelErrorTrait;;
class TbzSubjectForm extends Model
{
    use ModelErrorTrait;

    /* @var integer 回收站状态 */
    const UNDERLINE_STATUS = 7;

    public $sort;
    public $title;
    public $description;
    public $seo_keyword;
    public $seo_description;
    public $thumbnail;
    public $seo_title;
    public $banner;
    public $status;
    public function rules()
    {
        return [
            [['sort', 'title', 'description', 'seo_keyword', 'seo_description', 'thumbnail', 'seo_title', 'banner', 'status'], 'required'],
            ['sort', 'integer'],
            ['title', 'string', 'max' => 150],
            [['description', 'seo_keyword', 'seo_description'], 'string', 'max' => 255],
            [['thumbnail', 'seo_title'], 'string', 'max' => 100],
            ['banner', 'string', 'max' => 60],
            ['status', 'string', 'max' => 2],
        ];
    }

    /**
     * 获取所有属性
     * @return array
     */
    private function _getParam()
    {
        return $this->attributes;
    }

    /**
     * @param $params
     * @return bool|TbzSubject
     * 添加模板新消息
     */
    public function TbzSubjectAdd()
    {
        if (!$this->validate()) {
            return false;
        }
        $tbz_subject = new TbzSubject();
        if ($tbz_subject->load($this->attributes,'') && $tbz_subject->save(false)) {
            return $tbz_subject;
        } else {
            return false;
        }
    }

    /**
     * @param $id
     * @return bool|TbzSubject|null
     * 修改模板信息
     */
    public function TbzSubjectUpdate($id)
    {
        if(!$this->validate()){
            $this->addError('id','修改时id不能为空');
            return false;
        }
        $tbz_subject = TbzSubject::findOne($id);
        if (!$tbz_subject) {
            return false;
        }
        if ($tbz_subject->load($this->attributes,'') && $tbz_subject->save(false)) {
            return $tbz_subject;
        } else {
            return false;
        }
    }

    /**
     * @param $id
     * @return bool
     * 删除模板专题
     */
    public function TbzSubjectDelete($id){
        $tbz_subject = TbzSubject::findOne($id);
        if (!$tbz_subject){
            return false;
        }
        $tbz_subject->status = static::UNDERLINE_STATUS;
        if ($tbz_subject ->save(false)){
            return true;
        }else{
            return false;
        }
    }
}