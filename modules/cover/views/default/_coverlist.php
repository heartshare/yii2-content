<?php
/**
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $dpimg
 **/
$images = $dpimg->getModels();
$icnt = $dpimg->getCount();
$pages = $dpimg->getPagination();
$pages->route = '/content/cover/default/list';
?>
<div id="coverpjax">
    <?php if ($icnt == 0): ?>
        Нет ни одного загруженного изображения
    <?php else: ?>
        <table class="table table-bordered table-condensed">
            <tr>
                <?php
                $i = 0;
                foreach ($images as $img) {
                    if (($i % 3 == 0) && $i != 0) {
                        echo '</tr><tr>';
                    }
                    echo '<td><span class="pull-right">'
                        . \yii\helpers\Html::a(
                            \insolita\things\helpers\Helper::Fa('times', 'lg'),
                            'javascript:void(0)',
                            ['data-remcover' => $img->id, 'data-pjax' => 0]
                        )
                        . '</span>'
                        . \yii\helpers\Html::a(
                            \yii\helpers\Html::img(
                                \Yii::$app->getModule('content')->getModule('cover')->cover_url . $img->filename,
                                ['class' => 'img-thumbnail imgingrid', 'id' => 'cov' . $img->id]
                            ),
                            'javascript:void(0)',
                            ['data-choose' => $img->id, 'data-pjax' => 0, 'id' => $img->id]
                        ) . '</td>';
                    $i++;
                }
                ?>
            </tr>
        </table>
        <?php echo \yii\widgets\LinkPager::widget(
            [
                'pagination' => $pages,
                'linkOptions' => ['data-pjaxtarget_cover' => 1]
            ]
        )?>
    <?php endif; ?>
</div>


