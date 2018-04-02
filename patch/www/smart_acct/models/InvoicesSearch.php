<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Invoices;

/**
 * InvoicesSearch represents the model behind the search form of `app\models\Invoices`.
 */
class InvoicesSearch extends Invoices
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoiceNo', 'customerName'], 'integer'],
            [['invoiceDate'], 'safe'],
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
        $query = Invoices::find();

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
            'invoiceNo' => $this->invoiceNo,
            'invoiceDate' => $this->invoiceDate,
            'cgstTotal' => $this->cgstTotal,
            'sgstTotal' => $this->sgstTotal,
            'igstTotal' => $this->igstTotal,
            'subTotal' => $this->subTotal,
            'taxTotal' => $this->taxTotal,
            'netTotal' => $this->netTotal,
            //'customerID' => $this->customerID,
        ]);
        
        $query->andFilterWhere(['like', 'customerName', $this->customerName]);
        
         if (!is_null($this->invoiceDate) && 
			strpos($this->invoiceDate, ' - ') !== false ) {
			list($start_date, $end_date) = explode(' - ', $this->invoiceDate);
			$query->andFilterWhere(['between', 'date(invoiceDate)', $start_date, $end_date]);
		}   


        return $dataProvider;
    }
}
