<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\Business;
use Yii;
use api\modules\v1\models\User;
use api\modules\v1\models\SignupForm;
use yii\web\NotFoundHttpException;

class LocationController extends ApiController
{
    public $modelClass = 'api\modules\v1\models\Location';
}
