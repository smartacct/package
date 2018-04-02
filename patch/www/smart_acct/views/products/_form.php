<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Products */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="products-form">

    <div class="box box-default">
		<div class="box-body">
			
			<?php $form = ActiveForm::begin(['id' => 'products-form','enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
			
			<div class="row">
				<div class="col-md-4">
					<?= $form->field($model, 'productName')->textInput() ?>
				</div>
				<div class="col-md-4">
					<?= $form->field($model, 'productCode')->textInput() ?>
				</div>
				<div class="col-md-4">
					<?= $form->field($model, 'sno')->textInput() ?>
				</div>
				<div class="col-md-4">
					<?= $form->field($model, 'hsnCode')->textInput() ?>
				</div>
				<div class="col-md-4">
					<?= $form->field($model, 'per')->textInput() ?>
				</div>
				<div class="col-md-4">
					<?= $form->field($model, 'price')->textInput() ?>
				</div>
				
				<div class="col-md-3">
					<?= $form->field($model, 'cgstPer')->textInput() ?>
				</div>
				<div class="col-md-3">
					<?= $form->field($model, 'sgstPer')->textInput() ?>
				</div>
				<div class="col-md-3">
					<?= $form->field($model, 'igstPer')->textInput() ?>
				</div>
				<div class="col-md-3">
					<?= $form->field($model, 'opening_stock')->textInput() ?>
				</div>
			    
			    <div class="col-md-6">
					<?//= $form->field($model, 'brand')->textInput() ?>
				</div>
				<div class="col-md-6">
					<?//= $form->field($model, 'model')->textInput() ?>
				</div>
				
				

				<div class="col-md-12">
			
					<div class="form-group">
						<?= Html::submitButton('Save', ['name'=>'save','class' => 'btn btn-success']) ?>
						<?//= Html::submitButton('Save & Create', ['name'=>'save_create','class' => 'btn btn-success']) ?>
					</div>
				</div>	
			</div>
			<?php ActiveForm::end(); ?>
			 
		</div>
	</div>
	
</div>
