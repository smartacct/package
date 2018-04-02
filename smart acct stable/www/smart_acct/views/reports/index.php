<?php
use yii\helpers\Html;
use app\components\Helper;
use kartik\daterange\DateRangePicker;
use yii\widgets\ActiveForm;

$this->title = 'Reports';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reports-index">

	<div class="form">
	 
		<?php $form = ActiveForm::begin(); ?>
	     <div class="row">
			<div class="col-md-6">
			<label>Date Range</label>	
			<?= DateRangePicker::widget([
					'model' => $model,
					'attribute' => 'date_range',
					'convertFormat' => false,
					'presetDropdown'=>false,
					'hideInput'=>true,
					'pluginOptions' => [
						'locale' => [
							'format' => 'DD/MM/YYYY'
						],
					],
				]) ?>
			 </div>
			 <div class="col-md-6">	
				<?= $form->field($model, 'type')->dropDownList(['sales' => 'Sales Ledger', 'purchase' => 'Purchase Ledger', 'estimation' => 'Estimation Ledger'],['prompt'=>'Select...']) ?>
			</div>	
		 </div>	 
			<div class="form-group">
				<?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
			</div>
		<?php ActiveForm::end(); ?>
	 
	</div><!-- form -->
</div>
