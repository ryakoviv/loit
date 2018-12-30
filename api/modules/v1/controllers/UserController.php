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
        $username = Yii::$app->request->post('username');
        $password = Yii::$app->request->post('password');
        if (!$username || !$password) {
            throw new NotFoundHttpException();
        }
        $model = User::findByUsername($username);
        if (empty($model)) {
            throw new NotFoundHttpException('User not found');
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
           $business = new Business();
           $business->name = 'test';
           $business->save();
           $user->link('businesses', $business);
           return $user;
       }
       return $form->getErrors();
    }
}
