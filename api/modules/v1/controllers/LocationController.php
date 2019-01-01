<?php

namespace api\modules\v1\controllers;

use api\common\controllers\ApiController;
use api\modules\v1\models\Location;

class LocationController extends ApiController
{
    public $modelClass = Location::class;
}
