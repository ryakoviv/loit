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
 * @property string $location_text
 * @property float $location_center_lat
 * @property float $location_center_lng
 * @property float $location_radius
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

    public $location_distance_to_circle;
    const TYPE_LOST = 1;
    const TYPE_FOUND = 2;

    const SCENARIO_SAVE_LOST = 'save_lost';
    const SCENARIO_SAVE_FOUND = 'save_found';
    const SCENARIO_SEARCH_PRIVATE = 'search_private';
    const SCENARIO_SEARCH_PUBLIC = 'search_public';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'thing';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'description'], 'trim'],
            [
                ['name', 'description', 'happened_at', 'location_text', 'location_center_lat', 'location_center_lng', 'location_radius'],
                'required'
            ],
            ['name', 'string', 'max' => 100],
            [['description', 'location_text'], 'string', 'max' => 255],
            [
                ['location_center_lat', 'location_center_lng', 'location_radius'],
                'number'
            ],
            ['is_closed', 'default', 'value' => 0],
            ['is_closed', 'in', 'range' => [0, 1]],
            ['type', 'in', 'range' => [self::TYPE_LOST, self::TYPE_FOUND]],
            [['open_by_user_id', 'type'], 'required', 'on' => self::SCENARIO_DEFAULT],
            [['open_by_user_id', 'type'], 'integer', 'on' => self::SCENARIO_DEFAULT]
        ];
    }

    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_SAVE_LOST] = $this::attributes();
        $scenarios[self::SCENARIO_SAVE_FOUND] = $this::attributes();
        $scenarios[self::SCENARIO_SEARCH_PRIVATE] = $this::attributes();
        $scenarios[self::SCENARIO_SEARCH_PUBLIC] = $this::attributes();
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

    public function getImage()
    {
        return $this->hasOne(Image::class, ['id' => 'image_id']);
    }

    public function getSupporters()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])
            ->viaTable('user_shared_thing', ['thing_id' => 'id']);
    }

    public function fields(): array
    {
        $fields = parent::fields();

        if ($this->scenario === self::SCENARIO_SEARCH_PUBLIC) {
            $fields['opener'] = 'opener';
        }
        $fields['supporters_num'] = function () {
            return $this->getSupporters()->count();
        };
        unset($fields['image_id']);
        $fields['image'] = 'image';

        return $fields;
    }

    public function addSupporter($user): bool
    {
        $alreadyAdded = $this->getSupporters()->where(['id' => $user->id])->exists();
        if ($alreadyAdded) {
            return false;
        }
        $this->link('supporters', $user);
        return true;
    }

    public function beforeValidate(): bool
    {
        $image = new Image();
        $image->loadImageFile();
        $status = $image->validateImageFile();
        if (!$status) {
            $this->addErrors($image->getErrors());
            return false;
        }
        return parent::beforeValidate();
    }

    public function beforeSave($insert): bool
    {
        $image = new Image();
        if ($insert) {
            $image->loadImageFile();
            $image->saveImageFile();
            $this->image_id = $image->id;
            switch ($this->scenario) {
                case self::SCENARIO_SAVE_LOST:
                    $this->type = self::TYPE_LOST;
                    $this->open_by_user_id = Yii::$app->user->id;
                    break;
                case self::SCENARIO_SAVE_FOUND:
                    $this->type = self::TYPE_FOUND;
                    $this->open_by_user_id = Yii::$app->user->id;
                    break;
            }
        } else {
            $image->loadImageFile();
            $image->saveImageFile();
            if ($image->id !== $this->image_id) {
                if ($this->image_id) {
                    $oldImage = Image::findOne($this->image_id);
                    if ($oldImage) {
                        $oldImage->delete();
                    }
                }
                $this->image_id = $image->id;
            }
        }
        return parent::beforeSave($insert);
    }

}