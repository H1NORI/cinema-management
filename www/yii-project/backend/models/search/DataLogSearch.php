<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\DataLog;

/**
 * DataLogSearch represents the model behind the search form of `common\models\DataLog`.
 */
class DataLogSearch extends DataLog
{
    public int $pageSize = 20;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'model_id', 'event', 'created_at'], 'integer'],
            [['model', 'old_attributes', 'new_attributes'], 'safe'],
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
        $query = DataLog::find();

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
            'model_id' => $this->model_id,
            'event' => $this->event,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'model', $this->model])
            ->andFilterWhere(['like', 'old_attributes', $this->old_attributes])
            ->andFilterWhere(['like', 'new_attributes', $this->new_attributes]);

        return $dataProvider;
    }
}
