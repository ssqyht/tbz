<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
          ],
        'cache' => 'console\controllers\CacheController'
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'logVars' => [],
                ],
            ],
        ],
        'request' => [
            'class' => 'yii\console\Request',
        ],
        'dbMigrateDdy' => [
            'class' => 'yii\db\Connection',
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 0,
            'schemaCache' => 'cache',
        ],
        'dbMigrateTbz' => [
            'class' => 'yii\db\Connection',
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 0,
            'schemaCache' => 'cache',
        ],
    ],
    'params' => $params,
];
