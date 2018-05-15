<?php
/**
 * @user: thanatos <thanatos915@163.com>
 */
$rules = [
    '' => 'site/index',
    'wechat/server' => 'wechat/server',
];

$restUrls = [
    'modules' => ['v1', 'v2'],
    'rules' => [
        [
            'controller' => ['user', 'category', 'classify'],
            'extraPatterns' => [
                'POST,OPTIONS login' => 'login',
                'POST,OPTIONS bind' => 'bind',
            ],
        ],
        // 微信配置
        'GET,POST,OPTIONS wechat/qrcode' => 'wechat/qrcode',
        'POST,OPTIONS wechat/session' => 'wechat/session',
        'POST,OPTIONS wechat/refresh' => 'wechat/refresh',
        // 验证码
        'POST,OPTIONS main/send-sms' => 'main/send-sms',

        // 开始文档
        'doc/index' => 'doc/index',
        'doc/api' => 'doc/api',
        'gain-template-cover/get-cover' => 'gain-template-cover/get-cover',
        'gain-template-cover/add-cover' => 'gain-template-cover/add-cover',
        'gain-template-cover/update-cover' => 'gain-template-cover/update-cover',
        'gain-template-cover/delete-cover' => 'gain-template-cover/delete-cover',
        'gain-template-cover/migrate-old-data' => 'gain-template-cover/migrate-old-data',
    ],
    'pluralize' => false
];
$builtUrls = \common\extension\RestUrlRules::prepare($restUrls);

return \yii\helpers\ArrayHelper::merge($rules, $builtUrls);