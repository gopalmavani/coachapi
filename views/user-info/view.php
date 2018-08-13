<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\UserInfo */

$this->title = $model->user_id;
$this->params['breadcrumbs'][] = ['label' => 'User Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-info-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->user_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->user_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'user_id',
            'first_name',
            'last_name',
            'email:email',
            'password',
            'dob',
            'role',
            'gender',
            'about_user',
            'goals',
            'focus_areas',
            'location',
            'profession',
            'is_active',
            'is_enabled',
            'last_logged_in',
            'date_of_registration',
            'created_date',
            'modified_date',
        ],
    ]) ?>

</div>
