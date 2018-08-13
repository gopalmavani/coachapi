<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UserInfo;

/**
 * UserInfoSearch represents the model behind the search form of `app\models\UserInfo`.
 */
class UserInfoSearch extends UserInfo
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'gender', 'is_active', 'is_enabled'], 'integer'],
            [['first_name', 'last_name', 'email', 'password', 'dob', 'role', 'about_user', 'goals', 'focus_areas', 'location', 'profession', 'last_logged_in', 'date_of_registration', 'created_date', 'modified_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = UserInfo::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'user_id' => $this->user_id,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'is_active' => $this->is_active,
            'is_enabled' => $this->is_enabled,
            'last_logged_in' => $this->last_logged_in,
            'date_of_registration' => $this->date_of_registration,
            'created_date' => $this->created_date,
            'modified_date' => $this->modified_date,
        ]);

        $query->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'role', $this->role])
            ->andFilterWhere(['like', 'about_user', $this->about_user])
            ->andFilterWhere(['like', 'goals', $this->goals])
            ->andFilterWhere(['like', 'focus_areas', $this->focus_areas])
            ->andFilterWhere(['like', 'location', $this->location])
            ->andFilterWhere(['like', 'profession', $this->profession]);

        return $dataProvider;
    }
}
