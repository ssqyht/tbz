<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace common\models\forms;


use common\components\vendor\Model;
use common\models\Member;

class FrontendUploadForm extends Model
{

    // 用户上传素材
    const METHOD_MEMBER_MATERIAL = 'member_material';

    public $filename;
    public $etag;
    public $mimeType;
    public $size;
    public $folder_id;
    public $width;
    public $height;
    public $method;
    public $format;
    public $user_id;

    public function rules()
    {
        return [
            [['filename', 'etag', 'mimeType', 'size', 'method', 'format',], 'required'],
            [['user_id', 'width', 'height', 'folder_id'], 'default', 'value' => 0],
            [['user_id', 'width', 'height', 'folder_id'], 'filter', 'filter' => 'intval'],
            [['filename', 'etag', 'mimeType', 'size', 'width', 'height', 'method', 'format', 'user_id'], 'string'],
            [['size', 'user_id', 'folder_id', 'width', 'height'], 'integer'],
            ['user_id', 'exist', 'targetAttribute' => 'id', 'targetClass' => Member::class],
            // 验证文件上传方式
            ['method', 'in', 'range' => [static::METHOD_MEMBER_MATERIAL]]
        ];
    }

    /**
     * 提交图片信息
     * @param $params
     * @return bool
     * @author thanatos <thanatos915@163.com>
     */
    public function submit($params)
    {
        $this->load($params, '');
        if (!$this->validate()) {
            return false;
        }

    }

}