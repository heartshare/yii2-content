<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View                 $this
 * @var insolita\content\models\Page $model
 */

$this->title = $model->{$model::$titledAttribute};
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-header">
    <h1><?= Html::encode($model->name) ?></h1>
</div>
<div class="news-preview">
    <?= $model->full_parsed; ?>
</div>
