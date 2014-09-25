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

$this->title = 'Результаты поиска';
$this->params['breadcrumbs'][] = $this->title;
?>
<?=
\insolita\content\widgets\SearchWidget::widget(
    [
        'searchtype' => \insolita\content\widgets\SearchWidget::SEARCH_TYPE_CONTENT,
        'query' => $query,
        'mode'=>\insolita\content\widgets\SearchWidget::MODE_FLAT    ]
)
?>
<?php if ($error): ?>
    <div class="alert alert-block alert-danger"><?= $error ?> </div>

<?php endif ?>
<div class="showtag-index">
    <?php
    if ($newsdp) {
        echo \yii\widgets\ListView::widget(
            [
                'dataProvider' => $newsdp,
                'itemView' => 'search_item',
                'emptyText' => \insolita\things\helpers\Helper::Fa('frown-o', 'lg') . 'По вашему запросу нет ни одной новости',
                'viewParams' => ['type' => 'news', 'query' => $query],
                'id' => 'newslist',
                'summary' => '<div class="page-header">Найдено новостей  : <span class="badge alert-info">{totalCount}</span></div>',
            ]
        );
    }

    ?>

    <?php
    if ($artsdp) {
        echo \yii\widgets\ListView::widget(
            [
                'id' => 'artlist',
                'dataProvider' => $artsdp,
                'itemView' => 'search_item',
                'viewParams' => ['type' => 'arts', 'query' => $query],
                'summary' => '<div class="page-header">Найдено статей  : <span class="badge alert-info">{totalCount}</span></div>',
                'emptyText' => \insolita\things\helpers\Helper::Fa('frown-o', 'lg') . 'По вашему запросу нет ни одной статьи',
            ]
        );
    }

    ?>
    <br/>
</div>