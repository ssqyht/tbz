<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace common\models\forms;

use Yii;
use common\components\traits\FuncTrait;
use common\components\validators\FileUploadValidator;
use common\components\validators\PathValidator;
use common\components\vendor\OriginFIle;
use common\extension\Code;
use common\models\FileCommon;
use common\models\Member;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\validators\UrlValidator;

/**
 * Class FrontendUploadForm
 * @property OriginFIle|bool $fileData
 * @package common\models\forms
 * @author thanatos <thanatos915@163.com>
 */
class FrontendUploadForm extends Model
{

    /** @var string 用户自助上传文件 */
    const SCENARIO_FRONTEND = 'frontend';
    /** @var string 系统上传文件 */
    const SCENARIO_INTERNAL = 'internal';
    /** @var string 系统上传文件替换掉之前的文件，并删除闲现有文件 */
    const SCENARIO_INTERNAL_REPLACE = 'internal_replace';

    const TEMP_DIR = 'temporary';
    /** @var string 存放模板缩略图 */
    const DIR_TEMPLATE = 'template';
    /** @var string 存放官方素材 */
    const DIR_MATERIAL = 'material';
    /** @var string 存放用户素材 */
    const DIR_ELEMENT = 'element';
    /** @var string 其它文件 */
    const DIR_OTHER = 'other';

    // 用户上传素材
    const METHOD_MEMBER_MATERIAL = 'member_material';
    // 正常的上传文件
    const METHOD_NORMAL = 'normal';

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

    public $file_url;
    public $dir;
    public $replace;

    private $_fileData;

    public function rules()
    {
        return [
            [['filename', 'etag', 'mimeType', 'size', 'method', 'format', 'file_url', 'dir', 'replace'], 'required'],
            [['user_id', 'width', 'height', 'folder_id'], 'default', 'value' => 0],
            [['user_id', 'width', 'height', 'folder_id'], 'filter', 'filter' => 'intval'],
            [['filename', 'etag', 'mimeType', 'method', 'format', 'file_url', 'dir', 'replace'], 'string'],
            [['size', 'user_id', 'folder_id', 'width', 'height'], 'integer'],
//            ['user_id', 'exist', 'targetAttribute' => 'id', 'targetClass' => Member::class],
            // 验证文件上传方式
            ['method', 'in', 'range' => [static::METHOD_MEMBER_MATERIAL]],
            // 验证文件大小
            ['size', FileUploadValidator::class, 'method' => FileUploadValidator::METHOD_FILE_SIZE],
            // 验证文件上传类型
            ['mimeType', FileUploadValidator::class, 'method' => FileUploadValidator::METHOD_MIME_TYPE],
            ['dir', function () {
                if (!in_array($this->dir, [static::DIR_ELEMENT, static::DIR_MATERIAL, static::DIR_OTHER, static::DIR_TEMPLATE]))
                    // 目录不存在
                    $this->addError('dir', Code::DIR_NOT_EXIST);
            }],
            ['file_url', 'validateFileUrl'],
            // 验证路径格式
            ['replace', PathValidator::class]
        ];
    }

    /**
     * 验证文件Url
     * @author thanatos <thanatos915@163.com>
     */
    public function validateFileUrl()
    {
        $validator = new FileUploadValidator(['method' => FileUploadValidator::METHOD_FILE_SIZE]);
        // 文件大小
        if (!$validator->validate($this->fileData->length, $error)) {
            return $this->addError('file_url', $error);
        }
        // 文件格式
        $validator->method = FileUploadValidator::METHOD_MIME_TYPE;
        if (!$validator->validate($this->fileData->type, $error)) {
            return $this->addError('file_url', $error);
        }
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $data = [
            static::SCENARIO_FRONTEND => ['filename', 'etag', 'user_id', 'mimeType', 'size', 'method', 'format', 'folder_id'],
            static::SCENARIO_INTERNAL => ['file_url', 'dir'],
            static::SCENARIO_INTERNAL_REPLACE => ['file_url', 'dir', 'replace'],
        ];
        return ArrayHelper::merge($scenarios, $data);
    }

    /**
     * 提交图片信息
     * @param $params
     * @return bool|FileCommon|null
     * @author thanatos <thanatos915@163.com>
     */
    public function submit($params)
    {
        $this->load($params, '');
        if (!$this->validate()) {
            return false;
        }

        // 新的文件名
        $filename =  '';
        // 原始的文件名
        $oldPath = '';
        // 根据不同的场景初始化变量
        switch ($this->scenario) {
            case static::SCENARIO_INTERNAL_REPLACE:
                $filename = $this->replace;
                $oldPath = $this->file_url;
                break;

            case static::SCENARIO_INTERNAL:
                $filename = $this->generateFileName();
                $oldPath = $this->file_url;
                break;

            case static::SCENARIO_FRONTEND:
                // 检查文件唯一性
                if ($file = FileCommon::findByEtag($this->etag)) {
                    // 删除图片
                    $this->rollbackFile($this->filename);
                    return $file;
                }
                $filename = $this->generateFileName();
                $oldPath = $this->filename;
                break;
        }
        $fullFilename = UPLOAD_BASE_DIR . DIRECTORY_SEPARATOR . $filename;
        $width = $height = 0;
        if ($this->fileData->extType == FileCommon::EXT_SVG) {
            // SVG 替换后上传
            $content = static::repairSvgTag($this->fileData->content);
            $result = Yii::$app->oss->putObject($fullFilename, $content);
            list('height' => $height, 'width' => $width) = $this->fileData->getSvgSize();
        } else {
            // 其他文件直接上传
            $result = Yii::$app->oss->putObjectOrigin($fullFilename, $oldPath);
            // 设置图片的宽高信息
            if ($this->scenario == static::SCENARIO_FRONTEND) {
                $width = $this->width;
                $height = $this->height;
            } else {
                list('height' => $height, 'width' => $width) = Yii::$app->oss->getObjectSize($fullFilename);
            }
        }

        if (empty($result)) {
            $this->addError('url', Code::SERVER_FAILED);
            return false;
        }

        if ($this->scenario == static::SCENARIO_FRONTEND || $this->scenario == static::SCENARIO_INTERNAL_REPLACE) {
            $this->rollbackFile($oldPath);
        }

        if ($this->scenario == static::SCENARIO_INTERNAL_REPLACE || $this->scenario == static::SCENARIO_INTERNAL) {
            // 检查文件唯一性
            if ($file = FileCommon::findByEtag($result->etag)) {
                // 删除图片
                $this->rollbackFile($fullFilename);
                return $file;
            }
        }
        // 文件信息
        $params = [
            'etag' => $result->etag,
            'path' => $filename,
            'size' => $result->size_upload,
            'type' => $this->fileData->extType,
            'width' => $width,
            'height' => $height,
        ];

        // 添加记录
        $model = new FileCommon();
        if (!($fileModel = $model->create($params))) {
            $this->addErrors($model->getErrors());
            $this->rollbackFile($fullFilename);
            return false;
        }

        // 前端上传的话处理后续步骤
        /*
        if ($this->scenario == static::SCENARIO_FRONTEND) {
            switch ($this->method) {
                case static::METHOD_MEMBER_MATERIAL:
                    $model = new MaterialForm();
                    $model->load([
                        'file_id' => $fileModel->file_id,
                        'folder_id' => $this->folder_id,
                    ], '');
            }
        }*/

        return $fileModel;
    }

    /**
     * 生成唯一的文件路径
     * @return string
     * @author thanatos <thanatos915@163.com>
     */
    public function generateFileName()
    {
        switch ($this->scenario) {
            // 替换原来文件，直接返回要替换的文件名
            case static::SCENARIO_INTERNAL_REPLACE:
                return $this->replace;
            default:
                try {
                    $filename = Yii::$app->security->generateRandomString(20);
                } catch (\Throwable $throwable) {
                    $filename = md5(uniqid());
                }
                $extension = $this->fileData->extString;
                return $this->dir . DIRECTORY_SEPARATOR . date('Ym') . DIRECTORY_SEPARATOR . $filename . '.' . $extension ?? 'png';
        }
    }

    /**
     * 获取文件Header信息
     * @return array|bool
     * @author thanatos <thanatos915@163.com>
     */
    public function getFileData()
    {
        if ($this->_fileData === null) {
            $this->_fileData = FuncTrait::getSourceOrigin($this->file_url);
        }
        return $this->_fileData;
    }


    /**
     * 回滚 删除文件
     * @param $filename
     * @return bool
     */
    private function rollbackFile($filename)
    {
        if (empty(Yii::$app->oss->deleteObject($filename))) {
            $this->addError('url', Code::SERVER_FAILED);
            Yii::error(['name' => 'deleteFile', 'params' => [$filename]]);
            return false;
        }
        return true;
    }

    /**
     * @param $content
     * @return mixed
     * @author thanatos <thanatos915@163.com>
     */
    public static function repairSvgTag($content) {
        //定义待检测替换标签数组
        $svgTagArr = array(
            //标签
            'altglyph' => 'altGlyph',
            'altglyphdef' => 'altGlyphDef',
            'altglyphitem' => 'altGlyphItem',
            'animatecolor' => 'animateColor',
            'animatemotion' => 'animateMotion',
            'animatetransform' => 'animateTransform',
            'clippath' => 'clipPath',
            'feblend' => 'feBlend',
            'fecolormatrix' => 'feColorMatrix',
            'fecomponenttransfer' => 'feComponentTransfer',
            'fecomposite' => 'feComposite',
            'feconvolvematrix' => 'feConvolveMatrix',
            'fediffuselighting' => 'feDiffuseLighting',
            'fedisplacementmap' => 'feDisplacementMap',
            'fedistantlight' => 'feDistantLight',
            'feflood' => 'feFlood',
            'fefunca' => 'feFuncA',
            'fefuncb' => 'feFuncB',
            'fefuncg' => 'feFuncG',
            'fefuncr' => 'feFuncR',
            'fegaussianblur' => 'feGaussianBlur',
            'feimage' => 'feImage',
            'femerge' => 'feMerge',
            'femergenode' => 'feMergeNode',
            'femorphology' => 'feMorphology',
            'feoffset' => 'feOffset',
            'fepointlight' => 'fePointLight',
            'fespecularlighting' => 'feSpecularLighting',
            'fespotlight' => 'feSpotLight',
            'fetile' => 'feTile',
            'feturbulence' => 'feTurbulence',
            'foreignobject' => 'foreignObject',
            'glyphref' => 'glyphRef',
            'lineargradient' => 'linearGradient',
            'radialgradient' => 'radialGradient',
            'textpath' => 'textPath',
            //属性
            'viewbox' => 'viewBox',
        );
        $findArr = array_keys($svgTagArr);
        return str_ireplace($findArr, $svgTagArr, $content);
    }

}