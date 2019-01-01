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
        unset($actions['create']);

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

    public function prepareDataProvider()
    {
        return new ActiveDataProvider([
            'query' => Thing::find()->where(['open_by_user_id' => Yii::$app->user->id]),
        ]);
    }

    public function actionCreateLost()
    {
        $thing = new Thing();
        $thing->load(Yii::$app->request->post(), '');
        $thing->open_by_user_id = Yii::$app->user->id;
        $thing->type = $thing::TYPE_LOST;
        $thing->save();
        return $thing;
    }

    public function actionCreateFound()
    {
        $thing = new Thing();
        $thing->load(Yii::$app->request->post(), '');
        $thing->open_by_user_id = Yii::$app->user->id;
        $thing->type = $thing::TYPE_FOUND;
        $thing->save();
        return $thing;
    }
}
