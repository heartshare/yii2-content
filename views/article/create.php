<?php

use yii\helpers\Html;

/**
 * @var yii\web\View                    $this
 * @var insolita\content\models\Article $model
 */

$this->title = 'Добавить ' . $model::modelTitle('vin');
$this->params['breadcrumbs'][] = ['label' => $model::modelTitle(), 'url' => ['index']];
$this->params['breadcrumbs'][] = Html::encode($this->title);
?>
<div class="article-create">
    <div class="page-header">
        <h1><?= \insolita\things\helpers\Helper::Fa($this->context->icon, 'lg') . Html::encode($this->title) ?></h1>
    </div>
    <?=
    $this->render(
        '_form',
        [
            'model' => $model,
        ]
    ) ?>

</div>
