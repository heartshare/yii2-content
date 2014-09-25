<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 31.07.14
 * Time: 18:22
 *
 *
 * @var \yii\web\View $this
 * @var \insolita\uploader\models\Attach $file
 */

$popover_content
    = '
<div id="redactor_image_popover_' . $file->id . '">
        <div>'
    . (\Yii::$app->getModule('content')->getModule('uploader')->deleteAllow ?
        \yii\helpers\Html::a(
            "Удалить",
            '#',
            [
                'data-redactor_filedeleter' => $file->id,
                'class' => 'btn btn-danger btn-sm',
                'data-confirm' => 'Вы уверены что хотите это сделать?'
            ]
        ) : '')
    . '</div> <div>'

    . (\Yii::$app->getModule('content')->getModule('uploader')->htmlinsertAllow ? \yii\helpers\Html::button(
        "Вставить html",
        ['data-redactor_inshtml_file' => $file->id, 'class' => 'btn btn-info btn-sm']
    ) : '')
    . (\Yii::$app->getModule('content')->getModule('uploader')->bbinsertAllow ? \yii\helpers\Html::button(
        "Вставить bb-code",
        ['data-redactor_insbb_file' => $file->id, 'class' => 'btn btn-info btn-sm']
    ) : '')
    . '
        </div>
    </div>';
?>
<tr id="filerow<?= $file->id ?>" data-redactor_file_row="<?= $file->id ?>" data-content='<?= $popover_content ?>'>

    <td id="redactor_file_title_<?= $file->id ?>"><?= $file->filetitle ?>
        </div></td>
    <td id="redactor_file_size_<?= $file->id ?>"><?= $file->filesize ?></td>
    <td><?= $file->updated; ?>
        <div id="redactor_file_data_<?= $file->id ?>" style="display: none"
             data-url_file="<?= \Yii::$app->getModule('content')->getModule('uploader')->file_url . $file->filename ?>"
            >
    </td>
</tr>
