<?php

namespace api\modules\v1\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use Yii;

class Thing extends ActiveRecord
{

    const TYPE_LOST = 1;
    const TYPE_FOUND = 2;

//    const SCENARIO_LOST = 'lost';
//    const SCENARIO_FOUND = 'found';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'thing';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'description'], 'trim'],
            [['name', 'description', 'open_by_user_id'], 'required'],
            ['name', 'string', 'max' => 100],
            ['description', 'string', 'max' => 255],
            ['is_closed', 'default', 'value' => 0],
            ['is_closed', 'in', 'range' => [0, 1]],
        ];
    }

//    public function scenarios()
//    {
//        $scenarios = parent::scenarios();
//        $scenarios[self::SCENARIO_LOST] = [];
//        $scenarios[self::SCENARIO_FOUND] = [];
//        return $scenarios;
//    }

    public function getOpener()
    {
        return $this->hasOne(User::class, ['id' => 'open_by_user_id']);
    }

    public function fields()
    {
        $fields = parent::fields();

        $fields['opener'] = 'opener';

        return $fields;
    }

//    public function beforeSave($insert)
//    {
//        if ($insert) {
//            switch ($this->scenario) {
//                case self::TYPE_LOST:
//                    $this->type = self::TYPE_LOST;
//                    break;
//                case self::TYPE_FOUND:
//                    $this->type = self::TYPE_FOUND;
//                    break;
//                default:
//                    return false;
//            }
//        }
//        return parent::beforeSave($insert);
//    }

}