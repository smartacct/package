<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customers".
 *
 * @property int $id
 * @property string $customerName
 * @property string $customerAddress
 * @property string $customerGstin
 */
class Customers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customerName', 'customerAddress', 'customerGstin'], 'required'],
            [['customerAddress'], 'string'],
            [['customerName'], 'string', 'max' => 255],
            [['customerGstin'], 'string', 'max' => 50],
            [['customerName'], 'unique','targetAttribute' => ['customerName', 'customerGstin']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customerName' => 'Customer Name',
            'customerAddress' => 'Customer Address',
            'customerGstin' => 'Customer Gstin',
        ];
    }
}
