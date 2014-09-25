<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View                     $this
 * @var insolita\content\models\Feedback $model
 */

$this->title = $model->{$model::$titledAttribute};
$this->params['breadcrumbs'][] = ['label' => $model::modelTitle(), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="feedback-view">
    <div class="page-header">
        <h1><?= \insolita\things\helpers\Helper::Fa($this->context->icon, 'lg') . Html::encode($this->title) ?></h1>
    </div>


    <?=
    DetailView::widget(
        [
            'model' => $model,
            'condensed' => false,
            'hover' => true,
            'mode' => DetailView::MODE_VIEW,
            'panel' => [
                'heading' => $this->title,
                'type' => DetailView::TYPE_PRIMARY,
            ],
            'attributes' => [
                'id',
                'mail:email',
                'text:ntext',
                [
                    'attribute' => 'created',
                    'format' => ['datetime',
                        (isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime']))
                            ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A'
                    ],
                    'type' => DetailView::INPUT_WIDGET,
                    'widgetOptions' => [
                        'class' => DateControl::classname(),
                        'type' => DateControl::FORMAT_DATETIME
                    ]
                ],
                'ip',
                'mailed:email',
            ],
            'deleteOptions' => [
                'url' => ['delete', 'id' => $model->id],
                'data' => [
                    'confirm' => Yii::t('app', 'Вы уверены что хотите удалить этот элемент?'),
                    'method' => 'post',
                ],
            ],
            'enableEditMode' => false,
        ]
    ) ?>

</div>
