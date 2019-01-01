<?php

namespace api\modules\v1\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use Yii;

/**
 * Thing model
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $happened_at
 * @property boolean $is_closed
 * @property integer $closed_by_user_id
 * @property integer $open_by_user_id
 * @property integer $closed_by_item_id
 * @property integer $image_id
 * @property integer $location_id
 * @property integer $type
 * @property integer $created_at
 * @property integer $updated_at
 */
class Thing extends ActiveRecord
{

    const TYPE_LOST = 1;
    const TYPE_FOUND = 2;

    const SCENARIO_LOST = 'lost';
    const SCENARIO_FOUND = 'found';

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
            [['name', 'description'], 'required'],
            ['name', 'string', 'max' => 100],
            ['description', 'string', 'max' => 255],
            ['is_closed', 'default', 'value' => 0],
            ['is_closed', 'in', 'range' => [0, 1]],
            ['type', 'in', 'range' => [self::TYPE_LOST, self::TYPE_FOUND]],
            [['open_by_user_id', 'type'], 'required', 'on' => self::SCENARIO_DEFAULT],
            [['open_by_user_id', 'type'], 'integer', 'on' => self::SCENARIO_DEFAULT]
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_LOST] = $this::attributes();
        $scenarios[self::SCENARIO_FOUND] = $this::attributes();
        return $scenarios;
    }

    public function getOpener()
    {
        return $this->hasOne(User::class, ['id' => 'open_by_user_id']);
    }

    public function getCloser()
    {
        return $this->hasOne(User::class, ['id' => 'close_by_user_id']);
    }

    public function getRelatedThing()
    {
        return $this->hasOne(self::class, ['id' => 'closed_by_item_id']);
    }

    public function getLocation()
    {
        return $this->hasOne(Location::class, ['id' => 'location_id']);
    }

    public function getImage()
    {
        return $this->hasOne(Image::class, ['id' => 'image_id']);
    }

    public function getSupporters()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])
            ->viaTable('user_shared_thing', ['thing_id' => 'id']);
    }

    public function fields()
    {
        $fields = parent::fields();

        $fields['opener'] = 'opener';

        return $fields;
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            switch ($this->scenario) {
                case self::SCENARIO_LOST:
                    $this->type = self::TYPE_LOST;
                    $this->open_by_user_id = Yii::$app->user->id;
                    break;
                case self::SCENARIO_FOUND:
                    $this->type = self::TYPE_FOUND;
                    $this->open_by_user_id = Yii::$app->user->id;
                    break;
            }
        }
        return parent::beforeSave($insert);
    }

}