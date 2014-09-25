<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 18.08.14
 * Time: 2:04
 *
 * @var yii\web\View                           $this
 * @var yii\data\ActiveDataProvider            $dp
 * @var  insolita\content\search\ArticleSearch $model
 * * @var  insolita\content\models\Category $category
 */
?>
<div class="box box-primary art-item">
    <div class="box-header">
        &nbsp;&nbsp;<h3 class="box-title"><?= \yii\helpers\Html::a(
                $model->name,
                \yii\helpers\Url::toRoute(
                    ['/content/front/article', 'category' => $category->slug, 'slug' => $model->slug]
                ),
                []
            ); ?></h3>
    </div>
    <div class="box-body">
        <?php if (\Yii::$app->params['use_artcover']): ?>
            <div class="row">
                <div class="col-lg-2 col-md-2">
                    <?= $model->showCover() ?>
                </div>
                <div class="col-lg-10 col-md-10">
                    <label class="label label-primary"><?= date("d.m.Y", strtotime($model->publishto)); ?></label>
                    <?= $model->anons . \yii\helpers\Html::a(
                        'Далее',
                        \yii\helpers\Url::to(
                            ['/content/front/article', 'category' => $category->slug, 'slug' => $model->slug]
                        ),
                        ['class' => 'pull-right']
                    ) ?>
                    Метки:   <?php
                    if ($model->tags) {
                        //$tags=explode(',',$model->taglist);
                        foreach ($model->tags as $tag) {
                            echo \yii\helpers\Html::a(
                                    '<span class="label label-success">' . $tag->tagname . '</span>',
                                    ['/content/front/showtag', 'name' => $tag->tagname],
                                    []
                                ) . ' ';
                        }
                    }
                    ?>
                </div>
            </div>

        <?php else: ?>
            <label class="label label-primary"><?= date("d.m.Y", strtotime($model->publishto)); ?></label>
            <?= $model->anons . \yii\helpers\Html::a(
                'Далее',
                \yii\helpers\Url::to(['/content/front/article', 'category' => $category->slug, 'slug' => $model->slug]),
                ['class' => 'pull-right']
            ) ?>
            Метки:   <?php
            if ($model->tags) {
                //$tags=explode(',',$model->taglist);
                foreach ($model->tags as $tag) {
                    echo \yii\helpers\Html::a(
                            '<span class="label label-success">' . $tag->tagname . '</span>',
                            ['/content/front/showtag', 'name' => $tag->tagname],
                            []
                        ) . ' ';
                }
            }
            ?>
        <?php endif; ?>
    </div>
    <!-- /.box-body -->
</div>