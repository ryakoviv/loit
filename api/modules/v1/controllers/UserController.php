<?php

namespace api\modules\v1\controllers;

use Yii;
use api\modules\v1\models\User;
use api\modules\v1\models\SignupForm;
use yii\web\NotFoundHttpException;
use api\common\controllers\ApiController;
use yii\web\ServerErrorHttpException;

class UserController extends ApiController
{
    protected $authExcept = ['create', 'login', 'resetpassword', 'options'];

    public $modelClass = User::class;

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['index']);
        return $actions;
    }

    public function actionMe()
    {
        return Yii::$app->user->identity;
    }

    public function actionLogin()
    {
        $email = Yii::$app->request->post('email');
        $password = Yii::$app->request->post('password');
        if (!$email || !$password) {
            throw new NotFoundHttpException('Parameters are not found');
        }
        $user = User::findByEmail($email);
        if (empty($user)) {
            throw new NotFoundHttpException('User is not found');
        }

        if ($user->login($password)) {
            return $user->auth;
        } else {
            throw new NotFoundHttpException();
        }
    }

    public function actionCreate()
    {
       $form =  new SignupForm();
       $form->load(Yii::$app->request->post(), '');
       $user = $form->signup();
       if ($user) {
           return $user->auth;
       }
       if (!$form->getErrors()) {
           throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
       }

       return $form;
    }
}
