<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace common\components\vendor;


use common\models\FileCommon;
use yii\helpers\ArrayHelper;

/**
 * Class OriginFIle
 * @property int $extType 文件对应的Type值
 * @property string $extString 文件对应的扩展名
 * @package common\components\vendor
 * @author thanatos <thanatos915@163.com>
 */
class OriginFIle extends Model
{
    public $content;
    public $type;
    public $length;

    private $_ext_mime_type;
    private $_ext_mime_ext;


    /**
     * 返回mime对应的type值
     * @return int
     */
    public function getExtType()
    {
        if ($this->_ext_mime_type === null) {
            $tmp = ArrayHelper::map(FileCommon::$extension, 'mime', 'type');
            $this->_ext_mime_type = $tmp[$this->type];
            unset($tmp);
        }
        return $this->_ext_mime_type;
    }

    /**
     * 返回mime对应的扩展名
     * @return mixed
     */
    public function getExtString()
    {
        if ($this->_ext_mime_ext === null) {
            $tmp = ArrayHelper::map(FileCommon::$extension, 'mime', 'ext');
            $this->_ext_mime_ext = $tmp[$this->type];
            unset($tmp);
        }
        return $this->_ext_mime_ext;
    }

    /**
     * 获取svg宽高信息
     * @return array
     */
    public function getSvgSize()
    {
        $width = 0;
        $height = 0;
        // 拿出svg标签
        if (preg_match("/\<svg([\s\S]*?)\>/i", $this->content, $matches)) {
            // width height
            if (preg_match("/(width=\".*?\").*?(height=\".*?\").*?/i", $matches[1], $widthMatches)) {
                $width = trim(str_ireplace('width="', '', $widthMatches[1]), 'px"');
                $height = trim(str_ireplace('height="', '', $widthMatches[2]), 'px"');
            } // style
            elseif (preg_match("/(style=\".*?\").*?/i", $matches[1], $styleMatches)) {
                $str = (trim(str_ireplace(['style="', 'width:', 'height:', 'px'], '', str_replace(' ', '', $styleMatches[1])), '"'));
                list($width, $height) = explode(';', $str);
            } // viewbox
            elseif (preg_match("/(viewbox=\".*?\").*?/i", $matches[1], $viewMatches)) {
                // 去掉 viewbox= 和两端空格
                $str = trim(str_ireplace("viewbox=\"", "", $viewMatches[1]), '"');
                // 取出宽高信息
                list(, , $width, $height) = explode(' ', $str);
            }
        }
        return ['height' => $height, 'width' => $width];
    }


}