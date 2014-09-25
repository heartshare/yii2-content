<?php

use yii\helpers\Html;
use \insolita\supergrid\grid\GridView;

/**
 * @var yii\web\View                      $this
 * @var yii\data\ActiveDataProvider       $dataProvider
 * @var insolita\content\search\CatSearch $searchModel
 */

$managers = \backend\models\Manager::getList();

echo GridView::widget(
    [
        'id' => 'categorygrid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id'],
            ['attribute' => 'name'],
            ['attribute' => 'slug'],
            ['attribute' => 'metaKey'],
            ['attribute' => 'metaDesc'],
            ['attribute' => 'ord'],
            ['attribute' => 'cnt'],
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
            ['attribute' => 'updated', 'format' => 'datetime', 'label' => \insolita\things\helpers\Helper::Fa('clock-o', 2)],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{recount}&nbsp;{update}&nbsp;&nbsp;{remove}',
                'buttons' => [
                    'update' => function ($url, $model) {
                            if ($this->context->_showtype == 'newpage') {
                                return \yii\bootstrap\Button::widget(
                                    [
                                        'encodeLabel' => false,
                                        'label' => \insolita\things\helpers\Helper::Fa('pencil'),
                                        'tagName' => 'a',
                                        'options' => [
                                            'class' => 'btn btn-sm btn-default',
                                            'data-pjax' => 0,
                                            'href' => \yii\helpers\Url::toRoute(
                                                    ['update', 'id' => $model->{$model->getPk()}]
                                                ),
                                        ]
                                    ]
                                );
                            } else {
                                return \yii\bootstrap\Button::widget(
                                    [
                                        'encodeLabel' => false,
                                        'label' => \insolita\things\helpers\Helper::Fa('pencil'),
                                        'tagName' => 'a',
                                        'options' => [
                                            'class' => 'btn btn-sm btn-default',
                                            'data-modaler' => true,
                                            'data-link' => \yii\helpers\Url::toRoute(
                                                    ['update', 'id' => $model->{$model->getPk()}]
                                                )
                                        ]
                                    ]
                                );
                            }

                        },
                    'remove' => function ($url, $model) {
                            return \yii\bootstrap\Button::widget(
                                [
                                    'encodeLabel' => false,
                                    'label' => \insolita\things\helpers\Helper::Fa('trash-o'),
                                    'tagName' => 'a',
                                    'options' => [
                                        'class' => 'btn btn-sm btn-danger',
                                        'data-deleter' => 1,
                                        'data-method' => 'post',
                                        'data-confirm' => 'Вы уверены что хотите совершить это действие?',
                                        'data-pjax' => 0,
                                        'pjaxtarget' => '#gridpjax',
                                        'href' => \yii\helpers\Url::toRoute(
                                                ['remove', 'id' => $model->{$model->getPk()}]
                                            )
                                    ]
                                ]
                            );
                        },
                    'recount' => function ($url, $model) {
                            return \yii\bootstrap\Button::widget(
                                [
                                    'encodeLabel' => false,
                                    'label' => \insolita\things\helpers\Helper::Fa('refresh'),
                                    'tagName' => 'a',
                                    'options' => [
                                        'title' => 'Пересчет количества статей и обновление мета-тегов',
                                        'class' => 'btn btn-sm btn-info',
                                        'data-pjax' => 0,
                                        'href' => \yii\helpers\Url::toRoute(
                                                ['recount', 'id' => $model->{$model->getPk()}]
                                            ),
                                    ]
                                ]
                            );
                        }
                ]
            ]
        ],
    ]
);?>