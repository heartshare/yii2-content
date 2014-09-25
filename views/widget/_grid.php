<?php

use yii\helpers\Html;
use \insolita\supergrid\grid\GridView;

/**
 * @var yii\web\View                          $this
 * @var yii\data\ActiveDataProvider           $dataProvider
 */

//$managers=\backend\models\Manager::getList();

echo GridView::widget(
    [
        'id' => 'widgetsgrid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id'],
            ['attribute' => 'name'],
            ['attribute' => 'class'],
            ['attribute' => 'options'],
            ['attribute' => 'content'],
            ['attribute' => 'page'],
            ['attribute' => 'pos'],
            [
                'attribute' => 'active',
                'format' => 'raw',
                'class' => \dosamigos\grid\ToggleColumn::className(),
                'afterToggle' => 'function(r, data){if(r){jQuery.pjax.reload("#gridpjax")};}'
            ],
            ['attribute' => 'updated', 'format' => 'datetime', 'label' => \insolita\things\helpers\Helper::Fa('clock-o', 2)],
            [
                'class' => 'kartik\grid\ActionColumn',
            ]
        ],
    ]
);?>