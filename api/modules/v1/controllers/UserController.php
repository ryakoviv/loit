<?php

namespace api\modules\v1\controllers;

use Yii;
use api\modules\v1\models\User;
use api\modules\v1\models\SignupForm;
use yii\web\NotFoundHttpException;

/**
 * Country Controller API
 *
 * @author Budi Irawan <deerawan@gmail.com>
 */
class UserController extends ApiController
{
    public $modelClass = 'api\modules\v1\models\User';

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

    public function actionLogin()
    {
        $email = Yii::$app->request->post('email');
        $password = Yii::$app->request->post('password');
        if (!$email || !$password) {
            throw new NotFoundHttpException('Parameters are not found');
        }
        $model = User::findByEmail($email);
        if (empty($model)) {
            throw new NotFoundHttpException('User is not found');
        }

        if ($model->login($password)) {
            return $model;
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
           return $user;
       }
       return $form->getErrors();
    }
}
