<?php

namespace api\modules\v1\models;

use common\models\User as commonUser;

/**
 * {@inheritdoc}
 */
class User extends commonUser
{
    const SCENARIO_LOGIN = 'login';

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_LOGIN] = $this::attributes();
        return $scenarios;
    }

    public function login($password)
    {
        $this->scenario = self::SCENARIO_LOGIN;
        if ($this->validatePassword($password)) {
//            $this->generateAuthKey();
            return $this->save(false);
        }
        return false;
    }

    public function getLocation()
    {
        return $this->hasOne(Location::class, ['id' => 'location_id']);
    }

    public function getImage()
    {
        return $this->hasOne(Image::class, ['id' => 'image_id']);
    }

    public function getThings()
    {
        return $this->hasMany(Thing::class, ['open_by_user_id' => 'id']);
    }

    public function getLostThings()
    {
        return $this->hasMany(Thing::class, ['open_by_user_id' => 'id'])
            ->where(['type' => Thing::TYPE_LOST]);
    }

    public function getFoundThings()
    {
        return $this->hasMany(Thing::class, ['open_by_user_id' => 'id'])
            ->where(['type' => Thing::TYPE_FOUND]);
    }

    public function getSupportedThings()
    {
        return $this->hasMany(Thing::class, ['id' => 'thing_id'])
            ->viaTable('user_shared_thing', ['user_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields = parent::fields();

        if ($this->scenario === self::SCENARIO_LOGIN) {
            $fields['auth'] = function () {
                return base64_encode($this->auth_key);
            };
        }

        unset($fields['password_hash'], $fields['password_reset_token'], $fields['status'], $fields['auth_key']);

        return $fields;
    }
}
