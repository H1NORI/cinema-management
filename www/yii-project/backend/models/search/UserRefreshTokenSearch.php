<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserRefreshToken;

/**
 * UserRefreshTokenSearch represents the model behind the search form of `common\models\UserRefreshToken`.
 */
class UserRefreshTokenSearch extends UserRefreshToken
{
    public int $pageSize = 20;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_refresh_tokenID', 'urf_userID'], 'integer'],
            [['urf_token', 'urf_ip', 'urf_user_agent', 'urf_created'], 'safe'],
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
        $query = UserRefreshToken::find();

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
            'user_refresh_tokenID' => $this->user_refresh_tokenID,
            'urf_userID' => $this->urf_userID,
            'urf_created' => $this->urf_created,
        ]);

        $query->andFilterWhere(['like', 'urf_token', $this->urf_token])
            ->andFilterWhere(['like', 'urf_ip', $this->urf_ip])
            ->andFilterWhere(['like', 'urf_user_agent', $this->urf_user_agent]);

        return $dataProvider;
    }
}
