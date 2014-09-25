<?php

use yii\helpers\Html;

/**
 * @var yii\web\View                 $this
 * @var insolita\content\models\News $model
 */

$this->title = $model->{$model::$titledAttribute};
$this->params['breadcrumbs'][] = ['label' => $model::modelTitle(), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-view">
    <?= $this->render('_view', ['model' => $model]); ?>


</div>
