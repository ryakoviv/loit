<?php

namespace api\modules\v1\models;

use yii\db\ActiveRecord;

class Location extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'location';
    }
}