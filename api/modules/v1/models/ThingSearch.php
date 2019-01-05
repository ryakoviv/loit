<?php

namespace api\modules\v1\models;

use yii\data\ActiveDataProvider;
use yii\base\Event;

class ThingSearch extends Thing
{
    public function rules()
    {
        return [
            [['id', 'name', 'description', 'is_closed'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return parent::scenarios();
    }

    public function search($params)
    {
        $scenario = $this->scenario;
        Event::on(Thing::class, Thing::EVENT_AFTER_FIND, function ($event) use ($scenario) {
            $event->sender->scenario = $scenario;
        });
        $query = Thing::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params, '') && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['is_closed' => $this->is_closed]);

        $query->andFilterWhere(['type' => $this->type])
            ->andFilterWhere(['open_by_user_id' => $this->open_by_user_id]);

        return $dataProvider;
    }

    public function fields()
    {
        $fields = parent::fields();

        $fields['opener'] = 'opener';
        $fields['supporters'] = 'supporters';

        return $fields;
    }
}