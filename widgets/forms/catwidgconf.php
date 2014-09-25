<?php
use kartik\widgets\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\bootstrap\ActiveForm $form
 * @var array                    $positions
 */

$this->title = ' Настройка виджета "' . $wmodel->name . '"';
$this->params['breadcrumbs'][] = ['label' => 'Виджеты', 'url' => ['/widgetman/widgetman/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

    <h1><?= \insolita\things\helpers\Helper::Fa($this->context->icon, 'lg') . Html::encode($this->title) ?></h1>
<?php     $form = ActiveForm::begin(
    [
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'id' => 'modalform',
        'enableClientValidation' => false,
        'enableAjaxValidation' => false,
        'options' => ['enctype' => 'multipart/form-data'],
        'method' => 'post',
        'action' => \yii\helpers\Url::toRoute(['/content/widget/cat-widget-configure', 'id' => $wmodel->id])
    ]
);
?>
<?php
\insolita\supergrid\panels\Panel::begin(
    [
        'title' => \insolita\things\helpers\Helper::Fa('cog', 'lg') . ' Настройка "' . $wmodel->name . '"',
        'footer' => '<span class="pull-right">'
            . Html::submitButton(
                \insolita\things\helpers\Helper::Fa('check-circle', 'lg') . 'Сохранить',
                ['class' => 'btn btn-success', 'title' => 'Сохранить запись (Enter)', 'id' => 'dirsubmit_cfg']
            )
            . '</span>'
            . Html::a(
                \insolita\things\helpers\Helper::Fa('times-circle', 'lg') . 'Отмена',
                ['/widgetman/widgetman/index'],
                ['class' => 'btn btn-danger', 'title' => 'Отмена', 'id' => 'cancel_cfg']
            ),

    ]
);

echo $form->errorSummary([$model, $wmodel]);
?>
<?= $form->field($model, 'title')->textInput([])->label('Заголовок')->hint('Не обязательно'); ?>
<?=
$form->field($model, 'icon')->widget(
    '\insolita\iconpicker\Iconpicker',
    [
        'removePrefix' => true
    ]
)->label('Иконка')->hint('Не обязательно'); ?>

<?= $form->field($model, 'showcount')->dropDownList([0 => 'Нет', 1 => 'Да'])->label('Показывать ко-во статей'); ?>
<?= $form->field($model, 'showempty')->dropDownList([0 => 'Нет', 1 => 'Да'])->label('Показывать пустые категории'); ?>
<?= $form->field($model, 'positions')->dropDownList($positions)->label('Расположение'); ?>
<?= $form->field($model, 'ord')->textInput([])->label('Порядковый номер'); ?>

<?php
\insolita\supergrid\panels\Panel::end();
ActiveForm::end(); ?>