<?php

use yii\helpers\Html;

/**
 * @var \insolita\content\models\Page $model
 **/
?>
<div class="page-header">
    <h1><?= Html::encode($model->name) ?></h1>
</div>
<div class="news-preview">
    <?= $model->full_parsed; ?>
</div>
