<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'PfERKexoezHmg5DziC86taf27j1b7Xmq',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,

        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'login' => '/users/index',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>/<slug:[A-Za-z0-9 -_.]+>' => '<controller>/<action>',
                '<controller:\w+>/<slug:[A-Za-z0-9 -_.]+>' => '<controller>/slug',
                'login' => 'home/login',
                'register' => 'home/register',
                'register-step2' => '/home/register-step2',
                'getSplash' => 'home/splash',
                'socialLogin' => 'home/social-login',
                'editProfile' => 'home/edit-profile',
                'updateProfileImage' => 'home/update-profile-image',
                'registerLocation'=>'home/register-location',
                'createGroup' => 'group/create-group',
                'groupList' => 'group/group-list',
                'getGroupDetail' => 'group/group-detail',
                'addMemberToGroup' => 'group/add-member-group',
                'leaveGroup' => 'group/leave-member-group',
                'suggestedFriendList' => 'friend/suggested-friend-list',
                'addFriendRequest'=>'friend/add-friend-request',
                'acceptFriendRequest'=>'friend/accept-friend-request',
                'addGroup'=>'group/add-group',
                'createPost' => 'post/create-post',
                'likePost'=>'post/like-post',
                'likeGroup'=>'post/like-group',
                'commentPost'=>'post/comment',
                'postList'=>'post/list',
                'likeList'=>'post/like-list',
                'deactivateAccount'=>'home/deactive-account',
                'forgotPassword'=>'home/forget-password',
                'search' => 'home/search',
                'likeProfile'=>'home/like-profile',
                'getFriend' => 'friend/get-friend',
                'searchGroup' => 'group/search-group'

            ],
        ],

    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
