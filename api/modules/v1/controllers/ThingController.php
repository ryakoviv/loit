<?php

namespace api\modules\v1\controllers;

use api\common\controllers\ApiController;
use api\modules\v1\models\Thing;
use api\modules\v1\models\ThingSearch;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ConflictHttpException;

class ThingController extends ApiController
{
    public $modelClass = Thing::class;
    public $modelType = null;
    public $createScenario = Thing::SCENARIO_DEFAULT;
    public $updateScenario = Thing::SCENARIO_DEFAULT;
    protected $authExcept = ['public', 'options'];

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
            if ($model->open_by_user_id !== Yii::$app->user->id) {
                throw new ForbiddenHttpException(sprintf('You can only %s thing that you\'ve created.', $action));
            }
        } elseif ($action === 'share') {
            if ($model->open_by_user_id === Yii::$app->user->id) {
                throw new ForbiddenHttpException(sprintf('You cannot %s thing that you\'ve created.', $action));
            }
        }
    }

    public function prepareDataProvider($action, $filter)
    {
        $searchModel = new ThingSearch(['scenario' => Thing::SCENARIO_SEARCH_PRIVATE]);
        if ($this->modelType) {
            $searchModel->type = $this->modelType;
        }
        $searchModel->open_by_user_id = Yii::$app->user->id;
        return $searchModel->search(Yii::$app->request->get());
    }

    public function findModel($id, $action = null) {
        $filter = ['id' => $id];
        if ($this->modelType) {
            $filter['type'] = $this->modelType;
        }
        $model = Thing::findOne($filter);
        if ($model) {
            return $model;
        }
        throw new NotFoundHttpException("Object not found: $id");
    }

    public function actionShare($id)
    {
        $model = $this->findModel($id);
        $this->checkAccess('share', $model);
        $status = $model->addSupporter(Yii::$app->user->identity);
        if (!$status) {
            throw new ConflictHttpException("User already added to supporters");
        }
    }

    public function actionPublic()
    {
        $searchModel = new ThingSearch(['scenario' => Thing::SCENARIO_SEARCH_PUBLIC]);
        if ($this->modelType) {
            $searchModel->type = $this->modelType;
        }
        return $searchModel->search(Yii::$app->request->get());
    }
}
