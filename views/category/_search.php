<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View                      $this
 * @var insolita\content\search\CatSearch $model
 * @var yii\widgets\ActiveForm            $form
 */
?>

<div class="category-search">

    <?php $form = ActiveForm::begin(
        [
            'action' => ['index'],
            'method' => 'get',
        ]
    ); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'slug') ?>

    <?= $form->field($model, 'metaKey') ?>

    <?= $form->field($model, 'metaDesc') ?>

    <?php // echo $form->field($model, 'ord') ?>

    <?php // echo $form->field($model, 'cnt') ?>

    <?php // echo $form->field($model, 'updated') ?>

    <?php // echo $form->field($model, 'bymanager') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
