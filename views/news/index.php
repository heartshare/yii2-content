<?php

use yii\helpers\Html;
use yii\widgets\Pjax;

/**
 * @var yii\web\View                       $this
 * @var yii\data\ActiveDataProvider        $dataProvider
 * @var insolita\content\search\NewsSearch $searchModel
 */

$this->title = $searchModel::modelTitle();
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="news-index">
    <div class="page-header">
        <h1><?= \insolita\things\helpers\Helper::Fa($this->context->icon, 'lg') . Html::encode($this->title) ?></h1>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>



    <?php \insolita\supergrid\panels\CustControlPanel::begin(
        [
            'gridid'=>'newsgrid',
            'showRefresh'=>true,
            'showAdd'=>true,
            'showColSelector'=>true,
            'showPerpage'=>true,
            'showMassact'=>true,
            'massActionModel'=>$massactmodel,
            'showLookmod'=>false,
            'pjaxselector'=>'#gridpjax',
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
