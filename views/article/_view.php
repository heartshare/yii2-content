<?php

use yii\helpers\Html;

/**
 * @var \insolita\content\models\News $model
 **/
?>
<div class="page-header">
    <h1><?= Html::encode($model->name) ?></h1>
</div>
<div class="news-preview">
    <?= $model->anons; ?>
</div>
<div>
    <?= $model->full_parsed; ?>
</div>
