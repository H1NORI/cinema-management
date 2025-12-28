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
    'modules' => [
        'v1' => [
            // 'basePath' => '@app/modules/v1',
            'class' => 'api\modules\v1\Module'
        ],
    ],
    'components' => [
        'jwt' => [
            'class' => \kaabar\jwt\Jwt::class,
            'key' => 'AMqey0yAVrqmhR82RMlWB3zqMpvRP0zaaOheEeq2tmmcEtRYNj',
        ],
        'request' => [
            'csrfParam' => '_csrf-api',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null,
            'identityCookie' => ['name' => '_identity-api', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the api
            'name' => 'advanced-api',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
            'class' => 'common\components\ApiErrorHandler',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/program'],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'PUT <id:\d+>/add-programmer' => 'add-programmer',
                        'PUT <id:\d+>/add-staff' => 'add-staff',
                        'PUT <id:\d+>/update-state' => 'update-state',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/screening'],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET <program_id:\d+>' => 'index',
                        'GET index/<program_id:\d+>' => 'index',
                        'PUT <id:\d+>/submit' => 'submit',
                        'DELETE <id:\d+>/withdraw' => 'withdraw',
                        'PUT <id:\d+>/assign-handler' => 'assign-handler',
                        'PUT <id:\d+>/review' => 'review',
                        'PUT <id:\d+>/approve' => 'approve',
                        'PUT <id:\d+>/reject' => 'reject',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/user'],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'PUT update-password' => 'update-password',
                        'PUT update-status/<id:\d+>' => 'update-status',
                    ],
                ],
                'v1/auth/logout/<id:\d+>' => 'v1/auth/logout',
                // [
                //     'class' => 'yii\rest\UrlRule',
                //     'controller' => ['v1/group'],
                //     'pluralize' => false,
                //     'extraPatterns' => [
                //         'POST archive/<id:\d+>' => 'archive',
                //     ],
                // ],
                // '' => 'v1/site/index',
                // 'v1/site' => 'v1/site/index',
                // 'v1/site/<action>' => 'v1/site/<action>',
            ],
        ],
    ],
    'params' => $params,
];
