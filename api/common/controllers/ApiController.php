<?php

namespace api\common\controllers;

use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;


class ApiController extends ActiveController
{
    protected $authExcept = ['options'];
    protected $authOptional = [];

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(), [
                'authenticator' => [
                    'class' => CompositeAuth::class,
                    'except' => $this->authExcept,
                    'optional' => $this->authOptional,
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
