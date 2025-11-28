<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'controllerMap' => [
                'admin' => 'backend\controllers\AdminController',
                'security' => 'backend\controllers\user\SecurityController',
            ],
            'modelMap' => [
                'User' => 'common\models\User',
            ],
            'admins' => ['admin']
        ],
        'rbac' => [
            'class' => 'dektrium\rbac\RbacWebModule',
            'as access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return \Yii::$app->user->identity->isAdmin;
                        }
                    ],
                ],
            ],
        ],
        'admin' => [
            'class' => 'hail812\admin\Module',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'gii' => [
            'class' => 'yii\gii\Module',
            'generators' => [
                'crud' => [
                    'class' => \yii\gii\generators\crud\Generator::class,
                    'templates' => [
                        // template name => path to your template
                        // use @backend to point to backend/templates/crud/default
                        'AdminCRUD' => '@backend/templates/crud/default',
                    ],
                ],
            ],
        ]
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@backend/themes/adminlte3/views'
                ],
            ],
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
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
    'params' => $params,
];
