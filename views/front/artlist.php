<?php
/**
 * @var yii\web\View                           $this
 * @var yii\data\ActiveDataProvider            $dataProvider
 * @var  insolita\content\search\ArticleSearch $searchModel
 *  * @var  insolita\content\models\Category $category
 */

$this->title = 'Статьи - ' . \yii\helpers\Html::encode($category->name);
$this->params['breadcrumbs'][] = $this->title;
$this->params['metaKeys'] = $category->metaKey;
$this->params['metaDesc'] = $category->metaDesc;
?>
<div class="news-index">
    <?php
    echo \yii\widgets\ListView::widget(
        [
            'dataProvider' => $dataProvider,
            'itemView' => 'art_item',
            'viewParams' => ['category' => $category],
            'summary' => ''
        ]
    )
    ?>
</div>