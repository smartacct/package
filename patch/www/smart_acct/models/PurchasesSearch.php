<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Purchases;

/**
 * PurchasesSearch represents the model behind the search form of `app\models\Purchases`.
 */
class PurchasesSearch extends Purchases
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['purchaseNo', 'supplierName'], 'integer'],
            [['purchaseDate'], 'safe'],
            [['cgstTotal', 'sgstTotal', 'igstTotal', 'subTotal', 'taxTotal', 'netTotal'], 'number'],
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
        $query = Purchases::find();

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
            'purchaseNo' => $this->purchaseNo,
            'purchaseDate' => $this->purchaseDate,
            'cgstTotal' => $this->cgstTotal,
            'sgstTotal' => $this->sgstTotal,
            'igstTotal' => $this->igstTotal,
            'subTotal' => $this->subTotal,
            'taxTotal' => $this->taxTotal,
            'netTotal' => $this->netTotal,
            //'supplierID' => $this->supplierID,
        ]);
        
        $query->andFilterWhere(['like', 'supplierName', $this->supplierName]);
        
         if (!is_null($this->purchaseDate) && 
			strpos($this->purchaseDate, ' - ') !== false ) {
			list($start_date, $end_date) = explode(' - ', $this->purchaseDate);
			$query->andFilterWhere(['between', 'date(purchaseDate)', $start_date, $end_date]);
		}   


        return $dataProvider;
    }
}
