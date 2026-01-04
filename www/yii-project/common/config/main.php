<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'modules' => [
        // 'user' => [
        //     'class' => 'dektrium\user\Module',
        //     'modelMap' => [
        //         'User' => 'common\models\User',
        //     ],
        // ],
    ],
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'errorStandarts' => [
            'class' => 'common\components\ErrorStandarts',
        ],
    ],
];
