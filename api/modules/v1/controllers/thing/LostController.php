<?php

namespace api\modules\v1\controllers\thing;

use api\modules\v1\controllers\ThingController;
use api\modules\v1\models\Thing;

class LostController extends ThingController
{
    public $modelClass = Thing::class;
    public $modelType = Thing::TYPE_LOST;
    public $createScenario = Thing::SCENARIO_LOST;
    public $updateScenario = Thing::SCENARIO_LOST;
}
