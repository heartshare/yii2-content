<?php
/**
 * @var yii\web\View                           $this
 * @var yii\data\ActiveDataProvider            $dataProvider
 * @var  insolita\content\search\ArticleSearch $searchModel
 *  * @var  insolita\content\models\Category $category
 */

$this->title = 'Статьи - за ' .\yii\helpers\Html::encode($data);
$this->params['breadcrumbs'][] = $this->title;
$this->params['metaKeys'] = $metadata['metaKey'];
$this->params['metaDesc'] = $metadata['metaDesc'];
?>
<div class="news-index">
    <?php
    echo \yii\widgets\ListView::widget(
        [
            'dataProvider' => $dataProvider,
            'itemView' => 'search_item',
            'viewParams' => ['type' => 'arts', 'query' => \yii\helpers\Html::encode($data)],
            'summary' => ''
        ]
    )
    ?>
</div>