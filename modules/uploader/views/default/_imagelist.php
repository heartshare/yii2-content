<?php
/**
 * @var \yii\web\View                $this
 * @var \yii\data\ActiveDataProvider $dp
 **/
$images = $dp->getModels();
$icnt = $dp->getCount();
$pages = $dp->getPagination();
$pages->route = '/content/uploader/default/imagelist';
?>
<p class="text-info"><?= \insolita\things\helpers\Helper::Fa('info-cercle', 'lg'); ?><b>Кликните 2 раза по нужному изображению
    для выбора опций вставки!</b></p>

<div id="redactor_imagepjax">
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
                    echo $this->render('_imageitem', ['img' => $img]);
                    $i++;
                }
                ?>
            </tr>
        </table>
        <?php echo \yii\widgets\LinkPager::widget(
            [
                'pagination' => $pages,
                'linkOptions' => ['data-pjaxtarget_image' => 1]
            ]
        )?>
    <?php endif; ?>
</div>


