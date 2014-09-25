<?php

use \insolita\supergrid\grid\GridView;

/**
 * @var yii\web\View                       $this
 * @var yii\data\ActiveDataProvider        $dataProvider
 * @var insolita\content\search\NewsSearch $searchModel
 */

$managers = \backend\models\Manager::getList();

echo GridView::widget(
    [
        'id' => 'newsgrid',
        'dataProvider' => $dataProvider,


        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'kartik\grid\CheckboxColumn'],
            ['attribute' => 'id'],
            [
                'attribute' => 'cover_id',
                'value' => function ($data) { return $data->showCover('imgingrid'); },
                'format' => 'raw'
            ],
            ['attribute' => 'name', 'filter' => true],
            ['attribute' => 'taglist'],
            ['attribute' => 'slug'],
            [
                'attribute' => 'anons',
                'value' => function ($data) { return substr(strip_tags($data->anons), 0, 200) . '....'; },
                'format' => 'html'
            ],
            [
                'attribute' => 'active',
                'format' => 'raw',
                'class' => \dosamigos\grid\ToggleColumn::className(),
                'afterToggle' => 'function(r, data){if(r){jQuery.pjax.reload("#gridpjax")};}'
            ],
            ['attribute' => 'views'],
            [
                'attribute' => 'publishto',
                'format' => 'datetime',
                'label' => \insolita\things\helpers\Helper::Fa('clock-o', 2) . 'публикации'
            ],
            [
                'attribute' => 'created',
                'format' => 'datetime',
                'label' => \insolita\things\helpers\Helper::Fa('clock-o', 2) . 'создания'
            ],
            [
                'attribute' => 'updated',
                'format' => 'datetime',
                'label' => \insolita\things\helpers\Helper::Fa('clock-o', 2) . 'изменения'
            ],
            ['attribute' => 'metak'],
            ['attribute' => 'metadesc'],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{update}{view}  {remove}',
            ]
        ],
    ]
);?>