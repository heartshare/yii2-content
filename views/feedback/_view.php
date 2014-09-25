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
            'mail:email',
            'text:ntext',
            [
                'attribute' => 'created',
                'format' => ['datetime']
            ],
            'ip',
            'mailed:boolean',
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