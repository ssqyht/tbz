<?php

namespace api\controllers;

use common\components\traits\FuncTrait;
use common\models\FileCommon;
use common\models\OauthPublicKeys;
use Yii;
use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
{

    public function actions()
    {
        return [
            'doc' => [
                'class' => 'light\swagger\SwaggerAction',
                'restUrl' => \yii\helpers\Url::to(['/site/api'], true),
                'title' => '图帮主接口',
            ],
            'api' => [
                'class' => 'light\swagger\SwaggerApiAction',
                //The scan directories, you should use real path there.
                'scanDir' => [
                    Yii::getAlias('@api/common/controllers'),
                    Yii::getAlias('@api/common/swagger'),
                    Yii::getAlias('@api/modules/v1/swagger'),
                    Yii::getAlias('@api/modules/v1/controllers'),
                ],
                //The security key
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $content = '{"success":[{"id":1216499,"props":{"width":200,"height":200,"bleed":0,"unit":"px","splitLine":0,"usedColors":[]},"pages":[{"id":2644054,"props":{"bgColor":"#ffffff","bgId":"","guides":[]},"states":{"thumb":"/uploads/template4/member/201806/06/p_9806361a61b039f646ce9ae0c668ac37.png","updateAt":1528255815},"elements":[{"id":38922987,"type":"table","props":{"width":100,"height":60,"oWidth":500,"oHeight":300,"rotation":0,"x":50.000000000000014,"y":70,"flipX":false,"flipY":false,"opacity":1,"lock":false,"filter":{},"group":[]},"options":{"index":0,"colors":[{"oColor":"#FAD0A2","nColor":"#FAD0A2"},{"oColor":"#B25230","nColor":"#B25230"},{"oColor":"#FFFFFF","nColor":"#FFFFFF"},{"oColor":"#FDECD9","nColor":"#FDECD9"}],"html":"<tbody><tr style=\"height:20%\"><td style=\"width:25%\">\n                                    <font face=\"d6\" color=\"#B25230\" style=\"font-size:18px;text-align:center\">\n                                        \n                                            \n                                        \n                                    </font>\n                                </td><td style=\"width:25%\">\n                                    <font face=\"d6\" color=\"#B25230\" style=\"font-size:18px;text-align:center\">\n                                        \n                                            \n                                        \n                                    </font>\n                                </td><td style=\"width:25%\">\n                                    <font face=\"d6\" color=\"#B25230\" style=\"font-size:18px;text-align:center\">\n                                        \n                                            \n                                        \n                                    </font>\n                                </td><td style=\"width:25%\">\n                                    <font face=\"d6\" color=\"#B25230\" style=\"font-size:18px;text-align:center\">\n                                        \n                                            \n                                        \n                                    </font>\n                                </td></tr><tr style=\"height:20%\"><td style=\"width:25%\">\n                                    <font face=\"d6\" color=\"#B25230\" style=\"font-size:18px;text-align:center\">\n                                        \n                                            \n                                        \n                                    </font>\n                                </td><td style=\"width:25%\">\n                                    <font face=\"d6\" color=\"#B25230\" style=\"font-size:18px;text-align:center\">\n                                        \n                                            \n                                        \n                                    </font>\n                                </td><td style=\"width:25%\">\n                                    <font face=\"d6\" color=\"#B25230\" style=\"font-size:18px;text-align:center\">\n                                        \n                                            \n                                        \n                                    </font>\n                                </td><td style=\"width:25%\">\n                                    <font face=\"d6\" color=\"#B25230\" style=\"font-size:18px;text-align:center\">\n                                        \n                                            \n                                        \n                                    </font>\n                                </td></tr><tr style=\"height:20%\"><td style=\"width:25%\">\n                                    <font face=\"d6\" color=\"#B25230\" style=\"font-size:18px;text-align:center\">\n                                        \n                                            \n                                        \n                                    </font>\n                                </td><td style=\"width:25%\">\n                                    <font face=\"d6\" color=\"#B25230\" style=\"font-size:18px;text-align:center\">\n                                        \n                                            \n                                        \n                                    </font>\n                                </td><td style=\"width:25%\">\n                                    <font face=\"d6\" color=\"#B25230\" style=\"font-size:18px;text-align:center\">\n                                        \n                                            \n                                        \n                                    </font>\n                                </td><td style=\"width:25%\">\n                                    <font face=\"d6\" color=\"#B25230\" style=\"font-size:18px;text-align:center\">\n                                        \n                                            \n                                        \n                                    </font>\n                                </td></tr><tr style=\"height:20%\"><td style=\"width:25%\">\n                                    <font face=\"d6\" color=\"#B25230\" style=\"font-size:18px;text-align:center\">\n                                        \n                                            \n                                        \n                                    </font>\n                                </td><td style=\"width:25%\">\n                                    <font face=\"d6\" color=\"#B25230\" style=\"font-size:18px;text-align:center\">\n                                        \n                                            \n                                        \n                                    </font>\n                                </td><td style=\"width:25%\">\n                                    <font face=\"d6\" color=\"#B25230\" style=\"font-size:18px;text-align:center\">\n                                        \n                                            \n                                        \n                                    </font>\n                                </td><td style=\"width:25%\">\n                                    <font face=\"d6\" color=\"#B25230\" style=\"font-size:18px;text-align:center\">\n                                        \n                                            \n                                        \n                                    </font>\n                                </td></tr><tr style=\"height:20%\"><td style=\"width:25%\">\n                                    <font face=\"d6\" color=\"#B25230\" style=\"font-size:18px;text-align:center\">\n                                        \n                                            \n                                        \n                                    </font>\n                                </td><td style=\"width:25%\">\n                                    <font face=\"d6\" color=\"#B25230\" style=\"font-size:18px;text-align:center\">\n                                        \n                                            \n                                        \n                                    </font>\n                                </td><td style=\"width:25%\">\n                                    <font face=\"d6\" color=\"#B25230\" style=\"font-size:18px;text-align:center\">\n                                        \n                                            \n                                        \n                                    </font>\n                                </td><td style=\"width:25%\">\n                                    <font face=\"d6\" color=\"#B25230\" style=\"font-size:18px;text-align:center\">\n                                        \n                                            \n                                        \n                                    </font>\n                                </td></tr></tbody>"}}]}]}],"fail":[]}';
        $content = preg_replace_callback('/"index":(\d+)/', function ($matches) {
            var_dump($matches[1]);exit;
//            return $this->getTables()[$matches[0]]['id'];
        }, $content);
        exit;
        echo 'You must visit a module  "/v1"';
        exit;
    }


}
