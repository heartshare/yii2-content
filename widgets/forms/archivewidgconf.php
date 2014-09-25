<?php
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use insolita\content\widgets\TextWidget;
use \insolita\content\widgets\NewsWidget;

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
        'method' => 'post',
        'action' => \yii\helpers\Url::toRoute(['/content/widget/archive-widget-configure', 'id' => $wmodel->id])
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

<?=
$form->field($model, 'searchtype')
    ->dropDownList(
        [
            \insolita\content\widgets\ArchiveWidget::SEARCH_ARTS=>'Статьи',
            \insolita\content\widgets\ArchiveWidget::SEARCH_NEWS=>'Новости'
        ]
    )
    ->label('Объект архива');?>
<?=
$form->field($model, 'depth')
    ->dropDownList(
        [
            \insolita\content\widgets\ArchiveWidget::DEPTH_MONTH=>'По месяцам',
            \insolita\content\widgets\ArchiveWidget::DEPTH_DAYS=>'По дням'
        ]
    )
    ->label('Глубина архива');?>


<?=
$form->field($model, 'mode')->dropDownList(
    [TextWidget::MODE_FLAT => 'Без обрамления', TextWidget::MODE_BOX => 'Блок', TextWidget::MODE_PANEL => 'Панель']
)->label('Обрамление виджета'); ?>
<?= $form->field($model, 'positions')->dropDownList($positions)->label('Расположение'); ?>
<?= $form->field($model, 'ord')->textInput([])->label('Порядковый номер'); ?>

<?php
\insolita\supergrid\panels\Panel::end();
ActiveForm::end(); ?>