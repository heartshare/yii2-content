<?php
use Yii;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/**
 * @var yii\web\View                $this
 * @var app\models\MassActionsModel $model
 * @var yii\widgets\ActiveForm      $form
 */
?>

<div class="massactions-form">
    <?php  $form = ActiveForm::begin(
        [
            'id' => 'massaction-form',
            'type' => 'inline',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'action' => ['mass'],

        ]
    ); ?>
    <?=
    Html::tag(
        'div',
        '',
        [
            'id' => 'massform_error',
            'class' => 'alert alert-danger',
            'style' => 'display:none'
        ]
    ) . Html::tag(
        'div',
        'Данные успешно добавлены!',
        [
            'id' => 'massform_success',
            'class' => 'alert alert-success',
            'style' => 'display:none'
        ]
    )?>

    <input type="hidden" name="MassActionsModel[ids]" value="" id="sels">
    <?= $form->field($model, 'act')->dropDownList($model->getActlist(), ['class' => 'input-sm']); ?>
    <?= Html::a('Выполнить', '#', ['class' => 'btn btn-primary btn-sm', 'id' => 'mass_submit', 'data-pjax' => 1]) ?>

    <?php ActiveForm::end(); ?>
</div>
