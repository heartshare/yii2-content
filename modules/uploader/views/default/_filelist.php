<?php
/**
 * @var \yii\web\View                $this
 * @var \yii\data\ActiveDataProvider $dp
 **/
$files = $dp->getModels();
$fcnt = $dp->getCount();
$pages = $dp->getPagination();
$pages->route = '/content/uploader/default/filelist';
?>
<p class="text-info"><?= \insolita\things\helpers\Helper::Fa('info-cercle', 'lg'); ?><b>Кликните 2 раза по нужному файлу для
    выбора опций вставки!</b></p>

<div id="redactor_imagepjax">
    <?php if ($fcnt == 0): ?>
        Нет ни одного загруженного файла
    <?php else: ?>
        <table class="table table-bordered table-condensed table-striped table-hover">
            <tr>
                <td>Название</td>
                <td>Размер</td>
                <td>Дата</td>
            </tr>
            <?php
            foreach ($files as $file) {
                echo $this->render('_fileitem', ['file' => $file]);
            }
            ?>

        </table>
        <?php echo \yii\widgets\LinkPager::widget(
            [
                'pagination' => $pages,
                'linkOptions' => ['data-pjaxtarget_file' => 1]
            ]
        )?>
    <?php endif; ?>
</div>
