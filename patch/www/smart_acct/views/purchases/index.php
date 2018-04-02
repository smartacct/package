<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\components\Helper;
use kartik\daterange\DateRangePicker;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PurchasesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Purchases';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="purchases-index">

    <div class="box box-default">
<div class="box-body">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Purchase', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'purchaseNo',
            'purchaseDate',
            //'cgstTotal',
            //'sgstTotal',
            //'igstTotal',
            //'subTotal',
            'supplierName',
             [
            'attribute'  => 'taxTotal',
            'format'  => 'html',
            'value'  => function ($data) {
                 return Helper::amount_to_money($data->taxTotal);
            },
           
			],
             [
            'attribute'  => 'netTotal',
            'format'  => 'html',
            'value'  => function ($data) {
                 return Helper::amount_to_money($data->netTotal);
            },
           
			],
           
            //'supplierID',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
</div>
</div>
