<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace common\models\forms;

use common\components\traits\FuncTrait;
use common\models\FileCommon;
use Yii;
use common\extension\Code;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\imagine\Image;
use yii\validators\UrlValidator;


/**
 * 文件上传助手类
 * Class FileUpload
 * @property string $content
 * @package common\models\forms
 * @author thanatos <thanatos915@163.com>
 */
class FileUpload extends Model
{

    /** @var string 存放模板缩略图 */
    const DIR_TEMPLATE = 'template';
    /** @var string 存放官方素材 */
    const DIR_MATERIAL = 'material';
    /** @var string 存放用户素材 */
    const DIR_ELEMENT  = 'element';
    /** @var string 其它文件 */
    const DIR_OTHER = 'other';

    /** @var string*/
    public $url;
    /** @var string */
    public $dir;

    /** @var array 远程文件信息 */
    private $_content;
    private $_mime;

    private $_ext_mime_type;
    private $_ext_mime_ext;

    /**
     * 上传文件
     * @param string $url 文件URl
     * @param string $dir 存放位置
     * @return bool|FileCommon|null
     * @author thanatos <thanatos915@163.com>
     */
    public static function upload($url, $dir = self::DIR_OTHER)
    {
        $model = new static();
        if ($result = $model->submit(['url' => $url, 'dir' => $dir])) {
            return $result;
        } else {
            $model->addErrors($model->getErrors());
            return false;
        }
    }

    public function rules()
    {
        return [
            [['url', 'dir'], 'required'],
            ['dir', function(){
                if (!in_array($this->dir, [static::DIR_ELEMENT, static::DIR_MATERIAL, static::DIR_OTHER, static::DIR_TEMPLATE]))
                    // 目录不存在
                    $this->addError('dir', Code::DIR_NOT_EXIST);
            }],
            // 文件是否存在
            ['url', function () {
                if (!is_string($this->url) || !(new UrlValidator())->validate($this->url)) {
                    $this->addError('url', Code::FILE_NOT_EXIST);
                }
            }],
            // 文件是否合法
            ['url', function () {
                if (!$this->getIsAllowByMime()) {
                    return $this->addError('url', Code::FILE_EXTENSION_NOT_ALLOW);
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

        // 生成文件名
        $filename = $this->generateFileName();
        $this->getImageSize();
        // 上传OSS
        $result = Yii::$app->oss->putObject($filename, $this->content);
        if (empty($result)) {
            $this->addError('url', Code::SERVER_FAILED);
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
        if (($fileModel = $model->create($params))) {
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
        $baseDir = base64_encode('uploads');
        try {
            $filename = Yii::$app->security->generateRandomString(20);
        } catch (\Throwable $throwable) {
            $filename = md5(uniqid());
        }
        $extension = $this->getExtString();
        return $baseDir . DIRECTORY_SEPARATOR . $this->dir. DIRECTORY_SEPARATOR . date('Ym') . DIRECTORY_SEPARATOR .   $filename . '.' . $extension ?? 'png';
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
            list('content' => $this->_content, 'mime' => $this->_mime)= FuncTrait::getSourceOrigin($this->url);
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
            list('height' => $height, 'width' => $width) = FuncTrait::getSvgSize($this->content);
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
            $this->addError('url', Code::SERVER_FAILED);
            Yii::error(['name' => 'deleteFile', 'params' => [$filename]]);
            return false;
        }
        return true;
    }

}