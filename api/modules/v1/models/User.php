<?php

namespace api\modules\v1\models;

use common\models\User as identUser;


class User extends identUser
{
    public function login($password)
    {
        if ($this->validatePassword($password)) {
//            $this->generateAuthKey();
            return $this->save(false);
        }
        return false;
    }

    public function fields()
    {
        $fields = parent::fields();

        $fields['auth'] = function (){
            return base64_encode($this->auth_key);
        };
//        $fields['businesses'] = 'businesses';
        unset($fields['password_hash'], $fields['password_reset_token'], $fields['status'], $fields['auth_key']);

        return $fields;
    }
    //TODO: has many things
}