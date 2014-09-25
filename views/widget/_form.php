<?php
use Yii;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var insolita\content\models\Widgets $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="widgets-form">

    <?php     $form = ActiveForm::begin(
        [
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'id' => 'modalform',
            'enableClientValidation' => false,
            'enableAjaxValidation' => false,
            'action' => ($model->isNewRecord
                    ? \yii\helpers\Url::to(['create'])
                    : \yii\helpers\Url::to(
                        ['update', 'id' => $model->{$model->getPk()}]
                    ))
        ]
    );
    ?>
    <?php
        \insolita\supergrid\panels\Panel::begin(
            [
                'on_lookmod'=>['newpage'],
                'title' => ($model->isNewRecord
                        ?
                        \insolita\things\helpers\Helper::Fa('plus-circle', 'lg') . ' Добавление'
                        :
                        \insolita\things\helpers\Helper::Fa('pencil-square-o', 'lg') . ' Редактирование "'
                        . $model->{$model::$titledAttribute} . '"'),
                'footer' => '<span class="pull-right">'
                    . Html::submitButton(
                        \insolita\things\helpers\Helper::Fa('check-circle', 'lg')
                        . 'Сохранить',
                        [
                            'class' => 'btn btn-success',
                            'title' => 'Сохранить запись (Enter)',
                            'id' => 'dirsubmit_' . ($model->isNewRecord ? 'addmodal' : 'updmodal')
                        ]
                    )
                    . '</span>'
                    . Html::a(
                        \insolita\things\helpers\Helper::Fa('times-circle', 'lg')
                        . 'Отмена',
                        ['index'],
                        [
                            'class' => 'btn btn-danger',
                            'title' => 'Отмена (Esc)',
                            'id' => 'cancel_' . ($model->isNewRecord ? 'addmodal' : 'updmodal')
                        ]
                    ),

            ]
        );

    echo $form->errorSummary([$model]);
    ?>
    <div id="resp_success" style="display: none" class="alert alert-success"></div>
    <div id="resp_error" class="alert alert-danger" style="display: none"></div>
    <?php     echo Form::widget(
        [
            'model' => $model,
            'form' => $form,
            'columns' => 1,
            'attributes' => [

                'name' => [
                    'type' => Form::INPUT_TEXT,
                    'options' => ['placeholder' => ' Название...', 'maxlength' => 100]
                ],
                'class' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => ' Тип...', 'maxlength' => 200]],
                'options' => [
                    'type' => Form::INPUT_TEXTAREA,
                    'options' => ['placeholder' => '  Опции...', 'rows' => 6]
                ],
                'content' => [
                    'type' => Form::INPUT_TEXTAREA,
                    'options' => ['placeholder' => '  Содержание...', 'rows' => 6]
                ],
                'page' => [
                    'type' => Form::INPUT_TEXT,
                    'options' => ['placeholder' => ' страницы...', 'maxlength' => 255]
                ],
                'pos' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => ' Положение...']],

            ]
        ]
    );?>

    <?php \insolita\supergrid\panels\Panel::end(); ?>

    <?php ActiveForm::end(); ?>

</div>
