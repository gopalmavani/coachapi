<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UserInfoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-info-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'first_name') ?>

    <?= $form->field($model, 'last_name') ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'password') ?>

    <?php // echo $form->field($model, 'dob') ?>

    <?php // echo $form->field($model, 'role') ?>

    <?php // echo $form->field($model, 'gender') ?>

    <?php // echo $form->field($model, 'about_user') ?>

    <?php // echo $form->field($model, 'goals') ?>

    <?php // echo $form->field($model, 'focus_areas') ?>

    <?php // echo $form->field($model, 'location') ?>

    <?php // echo $form->field($model, 'profession') ?>

    <?php // echo $form->field($model, 'is_active') ?>

    <?php // echo $form->field($model, 'is_enabled') ?>

    <?php // echo $form->field($model, 'last_logged_in') ?>

    <?php // echo $form->field($model, 'date_of_registration') ?>

    <?php // echo $form->field($model, 'created_date') ?>

    <?php // echo $form->field($model, 'modified_date') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
