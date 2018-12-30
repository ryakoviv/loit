<?php

namespace api\modules\v1\controllers;

use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;


class ApiController extends ActiveController
{
    public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(), [
                'authenticator' => [
                    'class' => CompositeAuth::class,
                    'except' => ['create', 'login', 'resetpassword', 'options'],
                    'authMethods' => [
                        HttpBasicAuth::class,
                        HttpBearerAuth::class,
                        QueryParamAuth::class,
                    ],
                ],
            ]
        );
    }
}
