<?php


/**
 * @var yii\web\View                          $this
 * @var yii\data\ActiveDataProvider           $dataProvider
 * @var insolita\content\search\ArticleSearch $searchModel
 */

$managers = Yii::$app->getModule('content')->getAdminList();
$cats = \insolita\content\models\Category::getList();
echo \insolita\supergrid\grid\GridView::widget(
    [
        'id' => 'articlegrid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'kartik\grid\CheckboxColumn'],
            ['attribute' => 'id'],
            ['attribute' => 'cat_id', 'value' => function ($data) { return $data->cat->name; }, 'filter' => $cats],
            [
                'attribute' => 'cover_id',
                'value' => function ($data) { return $data->showCover('imgingrid'); },
                'format' => 'raw'
            ],
            ['attribute' => 'name', 'filter' => true],
            ['attribute' => 'slug'],
            ['attribute' => 'taglist'],
            [
                'attribute' => 'anons',
                'value' => function ($data) { return substr(strip_tags($data->anons), 0, 200) . '....'; },
                'format' => 'html'
            ],
            //           ['attribute'=>'full'],
//            ['attribute'=>'full_parsed'], 
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
            ['attribute' => 'bymanager', 'value'=>function($data) use($managers){return isset($managers[$data->bymanager])?$managers[$data->bymanager]:$data->bymanager;}],
            ['attribute' => 'metak'],
            ['attribute' => 'metadesc'],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{update}{view}  {remove}',
            ]
        ],
    ]
);?>