<?php
/**
 * @var yii\web\View                        $this
 * @var yii\data\ActiveDataProvider         $dataProvider
 * @var  insolita\content\search\NewsSearch $searchModel
 */

$this->title = 'Новости';
$this->params['breadcrumbs'][] = $this->title;
$this->params['metaKeys'] = $metadata['metaKey'];
$this->params['metaDesc'] = $metadata['metaDesc'];
?>
<div class="novosti-index">
    <?php
    echo \yii\widgets\ListView::widget(
        [
            'dataProvider' => $dataProvider,
            'itemView' => 'novosti_item',
            'pager' => ['class' => '\frontend\widgets\RevesedPager2'],
            'summary' => ''
        ]
    )
    ?>
</div>