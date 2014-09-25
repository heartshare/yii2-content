<?php

use \insolita\supergrid\grid\GridView;

/**
 * @var yii\web\View                           $this
 * @var yii\data\ActiveDataProvider            $dataProvider
 * @var insolita\content\search\FeedbackSearch $searchModel
 */


echo GridView::widget(
    [
        'id' => 'feedbackgrid',
        'dataProvider' => $dataProvider,


        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id'],
            ['attribute' => 'name'],
            ['attribute' => 'mail'],
            ['attribute' => 'text', 'value' => function ($model) { return mb_substr($model->text, 0, 150) . '...'; }],
            ['attribute' => 'created', 'format' => 'datetime', 'label' => \insolita\things\helpers\Helper::Fa('clock-o', 2)],
            ['attribute' => 'ip'],
            ['attribute' => 'mailed'],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}{delete}'
            ]
        ],
    ]
);?>