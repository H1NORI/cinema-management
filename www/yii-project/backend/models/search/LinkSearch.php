<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\LinkForm;

/**
 * LinkSearch represents the model behind the search form of `backend\models\LinkForm`.
 */
class LinkSearch extends LinkForm
{
    public int $pageSize = 20;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'is_header', 'priority', 'status', 'created_at', 'updated_at'], 'integer'],
            [['label', 'url', 'icon', 'icon_style', 'target'], 'safe'],
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
        $query = LinkForm::find();

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
            'is_header' => $this->is_header,
            'priority' => $this->priority,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'label', $this->label])
            ->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'icon', $this->icon])
            ->andFilterWhere(['like', 'icon_style', $this->icon_style])
            ->andFilterWhere(['like', 'target', $this->target]);

        return $dataProvider;
    }
}
