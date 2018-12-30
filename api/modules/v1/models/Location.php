<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 26.06.2018
 * Time: 22:11
 */

namespace api\modules\v1\models;

use yii\db\ActiveRecord;

class Location extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'locations';
    }
}