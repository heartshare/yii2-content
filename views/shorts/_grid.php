<?php

use \insolita\supergrid\grid\GridView;

/**
 * @var yii\web\View                         $this
 * @var yii\data\ActiveDataProvider          $dataProvider
 * @var insolita\content\search\ShortsSearch $searchModel
 */

echo GridView::widget(
    [
        'id' => 'shortsgrid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            ['class' => 'kartik\grid\CheckboxColumn'],
            ['attribute' => 'id'],
            ['attribute' => 'name'],
            [
                'attribute' => 'text',
                'value' => function ($data) { return mb_substr(strip_tags($data->text), 0, 125, 'UTF-8') . '...'; }
            ],
            [
                'attribute' => 'active',
                'format' => 'raw',
                'class' => \dosamigos\grid\ToggleColumn::className(),
                'afterToggle' => 'function(r, data){if(r){jQuery.pjax.reload("#gridpjax")};}'
            ],
            [
                'attribute' => 'publishto',
                'format' => 'datetime',
                'label' => \insolita\things\helpers\Helper::Fa('clock-o', 2) . 'публикации'
            ],
            ['attribute' => 'updated', 'format' => 'datetime', 'label' => \insolita\things\helpers\Helper::Fa('clock-o', 2)],
            [
                'class' => 'kartik\grid\ActionColumn',
            ]
        ],
    ]
);?>