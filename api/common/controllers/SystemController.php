<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace api\common\controllers;


use common\models\forms\FileUpload;
use Yii;
use common\components\vendor\RestController;

/**
 * Class SystemController
 * @package api\common\controllers
 * @author thanatos <thanatos915@163.com>
 */
class SystemController extends RestController
{

    /**
     * 获取Oss上传的JSSDK签名
     * @author thanatos <thanatos915@163.com>
     */
    public function actionOssPolicy()
    {
        return Yii::$app->oss->getSignature(UPLOAD_BASE_DIR . DIRECTORY_SEPARATOR . FileUpload::TEMP_DIR);
    }
}