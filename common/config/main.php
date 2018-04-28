<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language' => 'zh-CN',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'class' => 'common\extension\User',
            'identityClass' => 'common\models\Member',
        ],
        // 微信配置
        'wechat' => [
            'class' => 'thanatos\wechat\Wechat',
            'log' => [
                'level' => 'debug',
                'permission' => '0777',
                'file' => '@runtime/logs/wechat.log'
            ]
        ],
        // OSS配置
        'oss' => [
            'class' => 'thanatos\oss\Oss',
        ],
    ],
];
