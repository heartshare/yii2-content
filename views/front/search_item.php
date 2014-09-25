<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 18.08.14
 * Time: 2:04
 *
 * @var yii\web\View                        $this
 * @var yii\data\ActiveDataProvider         $dp
 * @var  insolita\content\search\NewsSearch $model
 *
 * @var string                              $type
 * @var string                              $query
 */

if ($type == 'news') {
    $url = \yii\helpers\Url::to(['/content/front/news', 'slug' => $model->slug]);
} elseif ($type == 'arts') {
    $url = \yii\helpers\Url::toRoute(
        ['/content/front/article', 'category' => $model->cat->slug, 'slug' => $model->slug]
    );
}
$searchpart = (strpos($model->full_parsed, 'highlight') !== false) ? $model->full_parsed : $model->anons;
?>

<div class="box box-primary search-item">
    <div class="box-header">
        &nbsp;&nbsp;<h3 class="box-title"><?= \yii\helpers\Html::a($model->name, $url, []); ?></h3>
    </div>
    <div class="box-body">
        <label class="label label-primary"><?= date("d.m.Y", strtotime($model->publishto)); ?></label>

        <p><?= $searchpart ?></p>
        <?= \yii\helpers\Html::a('Далее', $url, ['class' => 'pull-right']) ?>
    </div>
    <div class="clearfix"></div>
</div><!-- /.box-body -->
