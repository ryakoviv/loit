<?php

namespace api\modules\v1\controllers;

use api\common\controllers\ApiController;
use api\modules\v1\models\Thing;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;

class ThingController extends ApiController
{
    public $modelClass = Thing::class;

    public function actions()
    {
        $actions = parent::actions();

        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if ($action === 'update' || $action === 'delete') {
            if ($model->open_by_user_id !== \Yii::$app->user->id) {
                throw new ForbiddenHttpException(sprintf('You can only %s thing that you\'ve created.', $action));
            }
        }
    }

    public function prepareDataProvider($action, $filter)
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        return new ActiveDataProvider([
            'query' => Thing::find()->where(['open_by_user_id' => Yii::$app->user->id])->andWhere($filter),
            'pagination' => [
                'params' => $requestParams,
            ],
            'sort' => [
                'params' => $requestParams,
            ],
        ]);
    }
}
