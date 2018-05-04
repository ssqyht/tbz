<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */
$rules = [
    '' => 'site/index',
    '<wechat>/server' => 'wechat/server',
];

$restUrls = [
    'modules' => ['v1', 'v2'],
    'rules' => [
        [
            'controller' => ['user'],
            'extraPatterns' => [
                'POST,OPTIONS login' => 'login'
            ],
        ],
        // 微信配置
        'GET,POST,OPTIONS wechat/qrcode' => 'wechat/qrcode',
        'POST,OPTIONS wechat/session' => 'wechat/session',
        // 开始文档
        'doc/index' => 'doc/index',
        'doc/api' => 'doc/api',
    ],
    'pluralize' => false
];
$builtUrls = \common\extension\RestUrlRules::prepare($restUrls);

return \yii\helpers\ArrayHelper::merge($rules, $builtUrls);