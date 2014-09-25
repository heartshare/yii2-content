<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;

$this->title = $model->{$model::$titledAttribute};
?>
<?=
DetailView::widget(
    [
        'model' => $model,
        'condensed' => false,
        'hover' => true,
        'mode' => DetailView::MODE_VIEW,
        'panel' => [
            'heading' => Html::encode($this->title),
            'type' => DetailView::TYPE_PRIMARY,
        ],
        'attributes' => [
            'id',
            'name',
            'slug',
            'metaKey',
            'metaDesc',
            'ord',
            'cnt',
            [
                'attribute' => 'updated',
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
            'bymanager',
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