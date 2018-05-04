<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace api\common\controllers;

use Yii;
use yii\web\Controller;

/**
 * 接口文档控制器
 * @package api\common\controllers
 * @author thanatos <thanatos915@163.com>
 */
class DocController extends Controller
{
    public function actions()
    {
        return [
            'index' => [
                'class' => 'light\swagger\SwaggerAction',
                'restUrl' => $this->getRestUrl(),
                'title' => '图帮主接口',
            ],
            'api' => [
                'class' => 'light\swagger\SwaggerApiAction',
                'scanDir' => $this->getScanDir(),
                'api_key' => 'balbalbal',
            ],
        ];
    }

    /**
     * swagger 扫描目录
     * @return array
     */
    public function getScanDir()
    {
        return [
            Yii::getAlias('@api/common/controllers'),
            Yii::getAlias('@common/models'),
            Yii::getAlias('@api/common/swagger'),
            Yii::getAlias('@api/modules/v1/swagger'),
            Yii::getAlias('@api/modules/v1/controllers'),
        ];
    }

    /**
     * 生成swagger.json的Url
     * @return string
     */
    public function getRestUrl()
    {
        return \yii\helpers\Url::to(['doc/api'], true);
    }

}