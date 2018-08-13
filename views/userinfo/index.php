<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserInfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Infos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-info-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create User Info', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'user_id',
            'first_name',
            'last_name',
            'email:email',
            'password',
            //'dob',
            //'role',
            //'gender',
            //'about_user',
            //'goals',
            //'focus_areas',
            //'location',
            //'profession',
            //'is_active',
            //'is_enabled',
            //'last_logged_in',
            //'date_of_registration',
            //'created_date',
            //'modified_date',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
