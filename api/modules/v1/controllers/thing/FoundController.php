<?php

namespace api\modules\v1\controllers\thing;

use api\modules\v1\controllers\ThingController;
use api\modules\v1\models\Thing;

class FoundController extends ThingController
{
    public $modelClass = Thing::class;
    public $modelType = Thing::TYPE_FOUND;
    public $createScenario = Thing::SCENARIO_FOUND;
    public $updateScenario = Thing::SCENARIO_FOUND;
}
