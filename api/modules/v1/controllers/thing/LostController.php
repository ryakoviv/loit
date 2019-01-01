<?php

namespace api\modules\v1\controllers\thing;

use api\common\controllers\ApiController;
use api\modules\v1\models\Thing;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class LostController extends ApiController
{
    public $modelClass = Thing::class;
    public $createScenario = Thing::SCENARIO_LOST;

    public function actions()
    {
        $actions = parent::actions();

        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        $actions['view']['findModel'] = [$this, 'findModel'];
        $actions['delete']['findModel'] = [$this, 'findModel'];
        $actions['update']['findModel'] = [$this, 'findModel'];
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
            'query' => Thing::find()->where(['open_by_user_id' => Yii::$app->user->id, 'type' => Thing::TYPE_LOST])->andWhere($filter),
            'pagination' => [
                'params' => $requestParams,
            ],
            'sort' => [
                'params' => $requestParams,
            ],
        ]);
    }

    public function findModel($id, $viewAction) {
        $model = Thing::findOne(['id' => $id, 'type' => Thing::TYPE_LOST]);
        if ($model) {
            return $model;
        }
        throw new NotFoundHttpException("Object not found: $id");
    }
}
