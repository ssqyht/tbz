<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */

namespace common\components\traits;

use Yii;

trait ModelFieldsTrait
{

    /**
     * 规范每个模型类返回值处理
     * @return mixed
     * @author thanatos <thanatos915@163.com>
     */
    public function fields()
    {
        $controller = Yii::$app->controller;
        $fields = parent::fields();

        if ($controller->isFrontend())
            $fields = self::$frontendFields;

        return $fields;
    }

}