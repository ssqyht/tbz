<?php
return [
    'components' => [
        'db' => [
            'dsn' => 'mysql:host=localhost;dbname=yii2advanced',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
        'sms' => [
            'app_key' => '23376936',
            'app_secret' => '811551fb042fdbb50a40fb3c134dac0d',
        ]
    ],
];
