<?php
/**
 * Created by PhpStorm.
 * User: IT07
 * Date: 2018/5/18
 * Time: 9:17
 */
namespace common\models\forms;
use common\models\Upfile;
use yii\base\Model;
use common\components\traits\ModelErrorTrait;;
class UpfileForm extends \yii\base\Model
{
    use ModelErrorTrait;
    /** @var int 到回收站状态 */
    const RECYCLE_BIN_STATUS = 7;
    public $source;
    public $sid;
    public $team_id;
    public $user_id;
    public $session_id;
    public $filename;
    public $old_name;
    public $title;
    public $width;
    public $height;
    public $size;
    public $status;
    public $folder_id;
    public function rules()
    {
        return [
            [['source', 'sid', 'team_id', 'user_id', 'width', 'height', 'size', 'status', 'folder_id'], 'integer'],
            [['session_id'], 'string', 'max' => 30],
            [['filename', 'old_name'], 'string', 'max' => 100],
            [['title'], 'string', 'max' => 60],
        ];
    }

    /**
     * 创建素材
     * @return bool|Upfile
     */
    public function addUpfile()
    {
        if (!$this->validate()) {
            return false;
        }
        $model = new Upfile();
        if ($model->load($this->attributes, '') && $model->save(false)) {
            return $model;
        }
        return false;
    }

    /**
     * 修改素材
     * @param $id
     * @return bool|Upfile|null
     */
    public function updateUpfile($id)
    {
        if (!$id) {
            $this->addError('id', '唯一标识不能为空');
            return false;
        }
        if (!$this->validate()) {
            return false;
        }
        $model = Upfile::findOne($id);
        if (!$model) {
            $this->addError('', '该素材不存在');
            return false;
        }
        if ($model->load($this->attributes, '') && $model->save(false)) {
            return $model;
        }
        $this->addError('', '修改失败');
        return false;
    }

    public function deleteUpfile($id){
        $model = Upfile::findOne($id);
        if (!$model){
            $this->addError('id','该素材不存在');
        }
        $model->status = 7;
        if ($model->save(false)){
            return true;
        }
        $this->addError('','删除失败');
        return false;
    }
}