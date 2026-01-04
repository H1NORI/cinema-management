<?php
return [
    'id' => 'app-api-tests',
    'basePath' => dirname(__DIR__),
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=cinema_management_mysql;dbname=cinema_management;local_infile=1',
            'dsn' => 'mysql:host=cinema_management_mysql;dbname=cinema_management_test;local_infile=1',
            'username' => 'yii',
            'password' => 'yii',
            'charset' => 'utf8',
            'attributes' => [
                PDO::MYSQL_ATTR_LOCAL_INFILE => true,
            ],
        ],
        'security' => [
            'class' => 'yii\base\Security',
        ],
        'request' => [
            'csrfParam' => '_csrf-api',
            'cookieValidationKey' => 'test',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null,
        ],
        'log' => [
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'jwt' => [
            'class' => \kaabar\jwt\Jwt::class,
            'key' => 'AMqey0yAVrqmhR82RMlWB3zqMpvRP0zaaOheEeq2tmmcEtRYNj',
        ],
    ],
    'params' => [
        'jwt' => [
            'issuer' => 'https://api.example.com',  //name of your project (for information only)
            'audience' => 'https://example.com',  //description of the audience, eg. the website using the authentication (for info only)
            'id' => 'AMqey0yAVrqmhR82RMlWB3zqMpvRP0zaaOheEeq2tmmcEtRYNj',  //a unique identifier for the JWT, typically a random string
            'expire' => '+24 hour',
            'request_time' => '+5 seconds', //the time between the two requests. (optional)
        ],
    ]
];
