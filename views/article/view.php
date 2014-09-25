<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View                    $this
 * @var insolita\content\models\Article $model
 */

$this->title = $model->{$model::$titledAttribute};
$this->params['breadcrumbs'][] = ['label' => $model::modelTitle(), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-view">
    <?= $this->render('_view', ['model' => $model]); ?>

</div>
