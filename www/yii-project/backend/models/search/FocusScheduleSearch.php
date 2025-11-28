<?php

namespace backend\models\search;

use backend\models\FocusScheduleForm;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\FocusSchedule;

/**
 * FocusScheduleSearch represents the model behind the search form of `common\models\FocusScheduleForm`.
 */
class FocusScheduleSearch extends FocusScheduleForm
{
    public int $pageSize = 20;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'mode', 'status', 'days_of_week', 'created_at', 'updated_at'], 'integer'],
            [['name', 'start_at', 'end_at'], 'safe'],
            [['pageSize'], 'integer'],
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
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = FocusScheduleForm::find();

        $this->load($params, $formName);

        $this->pageSize = (int) ($this->pageSize ?: 20);

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->pageSize,
            ],
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'mode' => $this->mode,
            'status' => $this->status,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'days_of_week' => $this->days_of_week,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
