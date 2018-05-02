<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\module',
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'response' => [
            'class' => 'common\extension\Response',
        ],
        'user' => [
            'identityClass' => 'common\models\Member',
            'enableAutoLogin' => false,
            'identityCookie' => ['name' => '_identity-tubangzhu',],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'tubangzhu-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => ['_SERVER'],
                ],
            ],
        ],
        /*
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        */
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => require __DIR__ . '/urls.php',
        ],
    ],
    'params' => $params,
];
