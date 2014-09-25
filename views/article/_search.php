<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View                          $this
 * @var insolita\content\search\ArticleSearch $model
 * @var yii\widgets\ActiveForm                $form
 */
?>

<div class="article-search">

    <?php $form = ActiveForm::begin(
        [
            'action' => ['index'],
            'method' => 'get',
        ]
    ); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'cat_id') ?>

    <?= $form->field($model, 'cover_id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'slug') ?>

    <?php // echo $form->field($model, 'anons') ?>

    <?php // echo $form->field($model, 'full') ?>

    <?php // echo $form->field($model, 'full_parsed') ?>

    <?php // echo $form->field($model, 'active') ?>

    <?php // echo $form->field($model, 'views') ?>

    <?php // echo $form->field($model, 'created') ?>

    <?php // echo $form->field($model, 'updated') ?>

    <?php // echo $form->field($model, 'bymanager') ?>

    <?php // echo $form->field($model, 'metak') ?>

    <?php // echo $form->field($model, 'metadesc') ?>

    <?php // echo $form->field($model, 'publishto') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
