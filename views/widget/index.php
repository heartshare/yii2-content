<?php

use yii\helpers\Html;

;
use yii\widgets\Pjax;

/**
 * @var yii\web\View                                $this
 * @var yii\data\ActiveDataProvider                 $dataProvider
 * @var common\modules\content\search\WidgetsSearch $searchModel
 */

$this->title = $searchModel::modelTitle();
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="widgets-index">
    <div class="page-header">
        <h1><?= \insolita\things\helpers\Helper::Fa($this->context->icon, 'lg') . Html::encode($this->title) ?></h1>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>



    <?php \insolita\supergrid\panels\CustControlPanel::begin(
        [
            'showRefresh'=>true,
            'showAdd'=>true,
            'showColSelector'=>true,
            'showPerpage'=>true,
            'showMassact'=>false,
            'showLookmod'=>false,
            'pjaxselector'=>'#gridpjax',
            'buttons'=>[],
            'ajaxify'=>[
                [
                    'selector' => '[data-modaler]',
                    'options' => ['id' => 'updmodal'],
                    'pjaxSelector' => '#gridpjax',
                    'autofocus' => '#widgets-name',
                    'hotKeys' => false,
                    'showSaveMoreButton' => false,
                ],
                [
                    'selector' => '#addBtn',
                    'options' => ['id' => 'addmodal'],
                    'pjaxSelector' => '#gridpjax',
                    'autofocus' => '#widgets-name',

                ]
            ],
        ]
    );?>


    <div id="updmodal_inner"></div>
    <div id="addmodal_inner"></div>

    <?php Pjax::begin(["id" => "gridpjax"]);
    echo $this->render("_grid", ["dataProvider" => $dataProvider, "searchModel" => $searchModel]);
    Pjax::end();?>
    <?php \insolita\supergrid\panels\CustControlPanel::end(); ?>


</div>
