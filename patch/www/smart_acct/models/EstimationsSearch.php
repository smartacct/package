<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Estimations;

/**
 * EstimationsSearch represents the model behind the search form of `app\models\Estimations`.
 */
class EstimationsSearch extends Estimations
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['estimationNo', 'customerName'], 'integer'],
            [['estimationDate'], 'safe'],
            [['subTotal', 'netTotal'], 'number'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Estimations::find();

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
            'estimationNo' => $this->estimationNo,
            'estimationDate' => $this->estimationDate,
            'subTotal' => $this->subTotal,
            'netTotal' => $this->netTotal,
            //'customerID' => $this->customerID,
        ]);
        
        $query->andFilterWhere(['like', 'customerName', $this->customerName]);
        
         if (!is_null($this->estimationDate) && 
			strpos($this->estimationDate, ' - ') !== false ) {
			list($start_date, $end_date) = explode(' - ', $this->estimationDate);
			$query->andFilterWhere(['between', 'date(estimationDate)', $start_date, $end_date]);
		}   


        return $dataProvider;
    }
}
