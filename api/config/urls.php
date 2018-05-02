<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */
$rules = [
    '' => 'site/index',
    '<wechat>/server' => 'wechat/server',
    '<wechat>/qrcode' => 'wechat/qrcode',
    '<wechat>/session' => 'wechat/session',
    'v1/doc/index' => 'v1/doc/index',
    'v1/doc/api' => 'v1/doc/api',
];

$restUrls = [
    'modules' => ['v1', 'v2'],
    'rules' => [
        [
            'controller' => ['user']
        ],
        'doc/index' => 'doc/index',
        'doc/api' => 'doc/api',
    ],
    'pluralize' => false
];
$builtUrls = \common\extension\RestUrlRules::prepare($restUrls);

return \yii\helpers\ArrayHelper::merge($rules, $builtUrls);