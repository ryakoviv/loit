<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/params.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),    
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'basePath' => '@app/modules/v1',
            'class' => 'api\modules\v1\Module'
        ]
    ],
    'components' => [
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'multipart/form-data' => 'yii\web\MultipartFormDataParser'
            ],
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => env('API_COOKIE_VALIDATION_KEY'),
        ],
        'user' => [
            'identityClass' => 'api\modules\v1\models\User',
            'enableAutoLogin' => false,
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
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/location',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'OPTIONS <action:\w+>' => 'options'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/user',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET me' => 'me',
                        'POST login' => 'login',
                        'OPTIONS <action:\w+>' => 'options'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/thing',
                        'v1/thing/lost',
                        'v1/thing/found'
                    ],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST share/{id}' => 'share',
                        'GET public' => 'public',
                        'OPTIONS <action:\w+>' => 'options'
                    ]
                ],
            ],
        ]
    ],
    'params' => $params,
];
