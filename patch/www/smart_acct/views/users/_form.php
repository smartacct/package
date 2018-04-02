<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Users */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="users-form">

    <?php $form = ActiveForm::begin(); ?>

    <?//= $form->field($model, 'username')->textInput() ?>

    <?//= $form->field($model, 'auth_key')->textarea(['rows' => 6]) ?>

    <?//= $form->field($model, 'password_hash')->textarea(['rows' => 6]) ?>
    
    <?= $form->field($model, 'company_name')->textInput() ?>
    <?= $form->field($model, 'address')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'invoice_start_no')->textInput() ?>
    <?= $form->field($model, 'act_key')->textInput() ?>

    <?//= $form->field($model, 'password_reset_token')->textarea(['rows' => 6]) ?>

    <?//= $form->field($model, 'email')->textarea(['rows' => 6]) ?>

    <?//= $form->field($model, 'status')->textInput() ?>

    <?//= $form->field($model, 'role')->textInput() ?>

    <?//= $form->field($model, 'created_at')->textInput() ?>

    <?//= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
		

    </div>

    <?php ActiveForm::end(); ?>

</div>
