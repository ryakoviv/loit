<?php

namespace api\modules\v1\models;

use yii\data\ActiveDataProvider;
use yii\base\Event;

class ThingSearch extends Thing
{
    public function rules(): array
    {
        return [
            [['id', 'name', 'description', 'is_closed', 'location_center_lat', 'location_center_lng', 'location_radius'], 'safe'],
        ];
    }


    public function search($params)
    {
        $scenario = $this->scenario;
        Event::on(Thing::class, Thing::EVENT_AFTER_FIND, function ($event) use ($scenario) {
            $event->sender->scenario = $scenario;
        });

        if (!($this->load($params, '') && $this->validate())) {
            return new ActiveDataProvider([
                'query' => Thing::find(),
            ]);
        }
        if ($this->location_center_lat && $this->location_center_lng && $this->location_radius) {
            $lat = $this->location_center_lat;
            $lng = $this->location_center_lng;
            $radius = $this->location_radius;

            $query = Thing::find()->select([
                Thing::tableName(). '.*',
//                "6371000 * 2 * ASIN(SQRT(
//                POWER(SIN(($lat - abs([[location_center_lat]])) * pi()/180 / 2),
//                2) + COS($lat * pi()/180 ) * COS(abs([[location_center_lat]]) *
//                pi()/180) * POWER(SIN(($lng - [[location_center_lng]]) *
//                pi()/180 / 2), 2) )) AS location_distance_to_center",
                "(6371000 * 2 * ASIN(SQRT(
                POWER(SIN(($lat - abs([[location_center_lat]])) * pi()/180 / 2),
                2) + COS($lat * pi()/180 ) * COS(abs([[location_center_lat]]) *
                pi()/180) * POWER(SIN(($lng - [[location_center_lng]]) *
                pi()/180 / 2), 2) ))) - ([[location_radius]] + $radius) AS location_distance_to_circle"
            ]);

            $query->andFilterHaving(['<=', 'location_distance_to_circle', 0]);
        } else {
            $query = Thing::find();
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->andFilterWhere(['id' => $this->id])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['is_closed' => $this->is_closed]);

        $query->andFilterWhere(['type' => $this->type])
            ->andFilterWhere(['open_by_user_id' => $this->open_by_user_id]);

        return $dataProvider;
    }
}