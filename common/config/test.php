<?php
return [
    'id' => 'tubangzhu-tests',
    'basePath' => dirname(__DIR__),
    'components' => [
        'request' => [
            'enableCsrfCookie' => false,
            'client' => 'tubangzhu_web',
            'handle' => 'backend',
        ]
    ],
];
