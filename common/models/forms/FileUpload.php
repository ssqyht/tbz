<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace common\models\forms;

use common\components\traits\funcTraits;
use common\models\FileCommon;
use Yii;
use common\extension\Code;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\imagine\Image;
use yii\validators\UrlValidator;

/**
 * Class FileUpload
 * @property string $content
 * @property string $mime
 * @package common\models\forms
 */
class FileUpload extends Model
{

    /** @var string|resource */
    public $source;

    /** @var array 远程文件信息 */
    private $_content;
    private $_mime;

    private $_ext_mime_type;
    private $_ext_mime_ext;

    /**
     * 上传文件
     * @param $url
     * @return bool|FileCommon|null
     */
    public static function upload($url)
    {
        $model = new static();
        if ($model->load(['url' => $url], '') && $result = $model->submit()) {
            return $result;
        } else {
            $model->addErrors($result->getErrors());
            return false;
        }
    }

    public function rules()
    {
        return [
            [['url'], 'required'],
            // 文件是否存在
            ['url', function () {
                if (!is_string($this->source) || !(new UrlValidator())->validate($this->source)) {
                    $this->addError('source', Code::FILE_NOT_EXIST);
                }
            }],
            // 文件是否合法
            ['url', function () {
                if (!$this->getIsAllowByMime()) {
                    return $this->addError('source', Code::FILE_EXTENSION_NOT_ALLOW);
                }
                // 修正SVG标签不正确
                if ($this->getExtType() == FileCommon::EXT_SVG) {
                    $this->repairSvgTag();
                }
            }]
        ];
    }

    /**
     * 上传文件
     * @return bool|FileCommon|null
     */
    public function submit()
    {
        if (!$this->validate()) {
            return false;
        }

        // 生成文件名
        $filename = $this->generateFileName();
        $this->getImageSize();
        // 上传OSS
        $result = Yii::$app->oss->putObject($filename, $this->content);
        if (empty($result)) {
            $this->addError('source', Code::SERVER_FAILED);
            return false;
        }

        // 检查文件唯一性
        if ($file = FileCommon::findByEtag($result->etag)) {
            // 删除图片
            $this->rollbackFile($filename);
            return $file;
        }

        // 文件信息
        $params = [
            'etag' => $result->etag,
            'path' => $filename,
            'size' => $result->size_upload,
            'type' => $this->getExtType(),
        ];

        // 宽高信息
        list('height' => $params['height'], 'width' => $params['width']) = $this->getImageSize();

        // 添加记录
        $model = new FileCommon();
        if ($model->load($params, '') && ($fileModel = $model->create())) {
            return $fileModel;
        } else {
            $this->addErrors($model->getErrors());
            $this->rollbackFile($filename);
            return false;
        }

    }

    /**
     * 生成唯一的文件路径
     * @return string
     */
    public function generateFileName()
    {
        $dir = 'member';
        try {
            $filename = Yii::$app->security->generateRandomString();
        } catch (\Throwable $throwable) {
            $filename = md5(uniqid());
        }
        $extension = $this->getExtString();
        return base64_encode($dir) . DIRECTORY_SEPARATOR . base64_encode(date('Ym')) . $filename . '.' . $extension ?? 'png';
    }

    /**
     * 判断是否是允许的文件
     * @return bool
     */
    public function getIsAllowByMime()
    {
        return in_array($this->mime, ArrayHelper::getColumn(FileCommon::$extension, 'mime'));
    }

    /**
     * 返回mime对应的扩展名
     * @return mixed
     */
    public function getExtString()
    {
        if ($this->_ext_mime_ext === null) {
            $tmp = ArrayHelper::map(FileCommon::$extension, 'mime', 'ext');
            $this->_ext_mime_ext = $tmp[$this->mime];
            unset($tmp);
        }
        return $this->_ext_mime_ext;
    }

    /**
     * 返回mime对应的type值
     * @return int
     */
    public function getExtType()
    {
        if ($this->_ext_mime_type === null) {
            $tmp = ArrayHelper::map(FileCommon::$extension, 'mime', 'type');
            $this->_ext_mime_type = $tmp[$this->mime];
            unset($tmp);
        }
        return $this->_ext_mime_type;
    }

    /**
     * @param $value
     */
    public function setContent($value)
    {
        $this->_content = $value;
    }

    /**
     * @return array
     */
    public function getContent()
    {
        $this->sourceContent();
        return $this->_content;
    }

    /**
     * @return mixed
     */
    public function getMime()
    {
        $this->sourceContent();
        return $this->_mime;
    }

    /**
     * 读取远程文件信息
     * @return mixed
     */
    protected function sourceContent()
    {
        if ($this->_content === null && $this->_mime === null) {
            list('content' => $this->_content, 'mime' => $this->_mime)= funcTraits::getSourceOrigin($this->source);
        }
    }

    /**
     * 获取图片文件的宽高
     * @return array
     * @internal
     */
    protected function getImageSize()
    {
        $height = 0;
        $width = 0;
        if (in_array($this->getExtType(), [FileCommon::EXT_GIF, FileCommon::EXT_PNG, FileCommon::EXT_JPEG, FileCommon::EXT_JPG])) {
            $image = Image::getImagine()->load($this->content);
            $width = $image->getSize()->getWidth();
            $height = $image->getSize()->getHeight();
        }

        // SVG宽高信息
        if ($this->getExtType() === FileCommon::EXT_SVG) {
            list('height' => $height, 'width' => $width) = funcTraits::getSvgSize($this->content);
        }

        return ['height' => round($height) ?? 0, 'width' => round($width) ?? 0];
    }


    /**
     * @return string
     */
    protected function repairSvgTag() {
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
        $this->setContent(str_ireplace($findArr, $svgTagArr, $this->content));
    }


    /**
     * 回滚 删除文件
     * @param $filename
     * @return bool
     */
    private function rollbackFile($filename)
    {
        if (empty(Yii::$app->oss->deleteObject($filename))) {
            $this->addError('source', Code::SERVER_FAILED);
            Yii::error(['name' => 'deleteFile', 'params' => [$filename]]);
            return false;
        }
        return true;
    }

}