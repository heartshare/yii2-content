<?php
/**
 * @var yii\web\View                 $this
 * @var insolita\content\models\News $model
 */

$this->title = $model->{$model::$titledAttribute};
$this->params['metaKeys'] = $model->metak;
$this->params['metaDesc'] = $model->metadesc;
$this->params['noshare'] = true;

$this->params['breadcrumbs'][] = ['label' => $model::modelTitle(), 'url' => ['/content/front/newslist']];
$this->params['breadcrumbs'][] = $this->title;

?>
    <div class="news-view">
        <div class="page-header">
            <h1><?= \yii\helpers\Html::encode($model->name) ?></h1>
        </div>
        <div class="news-preview">
            <?= (\Yii::$app->params['use_newscover']) ? $model->showCoverMid('pull-left') : ''; ?> <?= $model->anons; ?>
        </div>
        <div>
            <?= $model->full_parsed; ?>
        </div>
        <div class="clearfix"></div>

        <?php echo \insolita\share42\ShareWidget::widget(
            [
                'shareOptions' => ['data' => ['title' => $model->name, 'description' => $model->anons]],
                'mode' => \insolita\share42\ShareWidget::MODE_HOR,
                'showcounters' => false
            ]
        )?>
    </div>
<?php
echo \himiklab\colorbox\Colorbox::widget(
    [
        'targets' => [
            '[rel="imagelink"]' => [
                'maxWidth' => 1024,
                'maxHeight' => 900
            ]
        ]
    ]
);
?>