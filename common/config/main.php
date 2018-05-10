<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language' => 'zh-CN',
    'components' => [
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'redis',
            'port' => 6379,
            'database' => 0,
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 0,
            'schemaCache' => 'cache',
            'tablePrefix' => 'tu_',
        ],
        'user' => [
            'class' => 'yii\web\User',
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
