<?php
/**
 * Created by PhpStorm.
 * User: Deepak
 * Date: 8/16/2018
 * Time: 5:20 PM
 */
use app\assets\AppAsset;
use yii\helpers\Html;
AppAsset::register($this);
?>
<?= Html::csrfMetaTags() ?>
<?php $this->beginPage() ?>
<?php $this->head() ?>
<?php $this->beginBody() ?>
<div>
    <?= $content ?>
</div>
<?php $this->endBody() ?>
<?php $this->endPage() ?>

