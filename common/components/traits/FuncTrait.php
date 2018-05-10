<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace common\components\traits;


use common\models\FileCommon;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\validators\UrlValidator;

/**
 * Trait FuncTraits
 * @package common\components\traits
 * @author thanatos <thanatos915@163.com>
 */
trait FuncTrait
{

    /**
     * 获取远程图片内容
     * @param $url
     * @return array|bool
     */
    public static function getSourceOrigin($url)
    {
        $validator = new UrlValidator();
        if (!$validator->validate($url)) {
            return false;
        }
        $client = new Client(['transport' => CurlTransport::class]);
        $response = $client->createRequest()->setMethod('GET')->setUrl($url)->send();
        if (!$response->isOk) {
            return false;
        }
        return ['content' => $response->content, 'mime' => $response->headers->get('content-type')];
    }

    /**
     * 返回base64后远程图片
     * @param array $content 由 getSourceOrigin 返回的数据
     * @return bool|string
     */
    public static function base64Image($content)
    {
        if (empty($content)) {
            return false;
        }

        return 'data:'. $content['mime'] .';base64,'.base64_encode($content['content']);
    }

    /**
     * 获取svg宽高信息
     * @param $svg
     * @return array
     */
    public static function getSvgSize($svg)
    {
        $width = 0;
        $height = 0;
        // 拿出svg标签
        if (preg_match("/\<svg([\s\S]*?)\>/i", $svg, $matches)) {
            // width height
            if (preg_match("/(width=\".*?\").*?(height=\".*?\").*?/i", $matches[1], $widthMatches)) {
                $width = trim(str_ireplace('width="', '', $widthMatches[1]),'px"');
                $height = trim(str_ireplace('height="', '', $widthMatches[2]),'px"');
            }
            // style
            elseif (preg_match("/(style=\".*?\").*?/i", $matches[1], $styleMatches)) {
                $str = (trim(str_ireplace(['style="', 'width:', 'height:', 'px'], '', str_replace(' ', '', $styleMatches[1])), '"'));
                list($width, $height) = explode(';', $str);
            }
            // viewbox
            elseif (preg_match("/(viewbox=\".*?\").*?/i", $matches[1], $viewMatches)) {
                // 去掉 viewbox= 和两端空格
                $str = trim(str_ireplace("viewbox=\"", "", $viewMatches[1]), '"');
                // 取出宽高信息
                list(,, $width, $height) = explode(' ', $str);
            }
        }
        return ['height' => $height, 'width' => $width];
    }

}