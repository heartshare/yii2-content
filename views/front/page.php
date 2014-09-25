<?php
/**
 * @var yii\web\View                  $this
 * @var \insolita\content\models\Page $page
 */

$this->title = \yii\helpers\Html::encode($page->name);
$this->params['breadcrumbs'][] = $this->title;
$this->params['metaKeys'] = $page->metak;
$this->params['metaDesc'] = $page->metadesc;
?>
<div class="page-header">
    <h1><?= $this->title ?></h1>
</div>
<div class="news-preview">
    <?= $page->full_parsed; ?>
</div>