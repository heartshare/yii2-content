<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 18.08.14
 * Time: 2:04
 *
 * @var yii\web\View                  $this
 * @var yii\data\ActiveDataProvider   $newsdp
 * @var yii\data\ActiveDataProvider   $artsdp
 * @var  insolita\content\models\Tags $tag
 */

$this->title = 'Поиск по записям с меткой:' . \yii\helpers\Html::encode($tag->tagname);
$this->params['breadcrumbs'][] = $this->title;
$this->params['metaKeys'] = \yii\helpers\Html::encode($tag->tagname);
$this->params['metaDesc'] = $this->title;
?>

<div class="showtag-index">
    <?php
    echo \yii\widgets\ListView::widget(
        [
            'dataProvider' => $newsdp,
            'itemView' => 'news_item',
            'emptyText' => '',
            'id' => 'newslist',
            'summary' => '<div class="page-header">Новостей с меткой "' . \yii\helpers\Html::encode($tag->tagname)
                . '" : <span class="badge alert-info">{totalCount}</span></div>',
        ]
    )
    ?>

    <?php
    echo \yii\widgets\ListView::widget(
        [
            'id' => 'artlist',
            'dataProvider' => $artsdp,
            'itemView' => 'arttag_item',
            'summary' => '<div class="page-header">Статей с меткой "' . \yii\helpers\Html::encode($tag->tagname)
                . '" : <span class="badge alert-info">{totalCount}</span></div>',
            'emptyText' => ''
        ]
    )
    ?>
    <br/>
</div>