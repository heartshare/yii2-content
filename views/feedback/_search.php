<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View                           $this
 * @var insolita\content\search\FeedbackSearch $model
 * @var yii\widgets\ActiveForm                 $form
 */
?>

<div class="feedback-search">

    <?php $form = ActiveForm::begin(
        [
            'action' => ['index'],
            'method' => 'get',
        ]
    ); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'mail') ?>

    <?= $form->field($model, 'text') ?>

    <?= $form->field($model, 'created') ?>

    <?= $form->field($model, 'ip') ?>

    <?php // echo $form->field($model, 'mailed') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
