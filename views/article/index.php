<?php

use yii\helpers\Html;

;
use yii\widgets\Pjax;

/**
 * @var yii\web\View                          $this
 * @var yii\data\ActiveDataProvider           $dataProvider
 * @var insolita\content\search\ArticleSearch $searchModel
 */

$this->title = $searchModel::modelTitle();
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="article-index">
    <div class="page-header">
        <h1><?= \insolita\things\helpers\Helper::Fa($this->context->icon, 'lg') . Html::encode($this->title) ?></h1>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>



    <?php
    \insolita\supergrid\panels\CustControlPanel::begin(
        [
            'gridid'=>'articlegrid',
            'showRefresh'=>true,
            'showAdd'=>true,
            'showColSelector'=>true,
            'showPerpage'=>true,
            'showMassact'=>true,
            'showLookmod'=>false,
            'pjaxselector'=>'#gridpjax',
            'massActionModel'=>$massactmodel,
            'buttons'=>[],
            'ajaxify'=>[

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
