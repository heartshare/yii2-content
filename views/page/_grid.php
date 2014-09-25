<?php

use yii\helpers\Html;
use \insolita\supergrid\grid\GridView;

/**
 * @var yii\web\View                       $this
 * @var yii\data\ActiveDataProvider        $dataProvider
 * @var insolita\content\search\PageSearch $searchModel
 */

echo GridView::widget(
    [
        'id' => 'pagegrid',
        'dataProvider' => $dataProvider,


        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id'],
            [
                'attribute' => 'name',
                'format' => 'html',
                'value' => function ($data) {
                        return Html::a(
                            $data->name,
                            \yii\helpers\Url::toRoute(['/content/page/look', 'slug' => $data->slug]),
                            ['target' => '_blank']
                        );
                    }
            ],
            ['attribute' => 'slug'],
            [
                'attribute' => 'addtomenu',
                'format' => 'raw',
                'value' => function ($data) {
                        return $data->addtomenu . ' ' . Html::a(
                            \insolita\things\helpers\Helper::Fa('windows'),
                            \yii\helpers\Url::toRoute(
                                ['/menu/default/create', 'url' => $data->addtomenu, 'name' => $data->name]
                            ),
                            [
                                'class' => 'btn btn-sm btn-default',
                                'title' => 'Добавить в меню',
                                'data-pjax' => 0,
                                'target' => '_blank'
                            ]
                        );
                    }
            ],
            // ['attribute'=>'full_parsed'],
            ['attribute' => 'views'],
            ['attribute' => 'updated', 'format' => 'datetime', 'label' => \insolita\things\helpers\Helper::Fa('clock-o', 2)],
            ['attribute' => 'bymanager'],
            ['attribute' => 'metak'],
            ['attribute' => 'metadesc'],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{update} {view} {remove}',
            ]
        ],
    ]
);?>