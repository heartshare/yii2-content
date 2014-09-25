<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 31.07.14
 * Time: 18:22
 *
 *
 * @var \yii\web\View $this
 * @var \insolita\uploader\models\Attach $img
 */

$popover_content
    = '
<div id="redactor_image_popover_' . $img->id . '">
        <div>'
    . (\Yii::$app->getModule('content')->getModule('uploader')->deleteAllow ?
        \yii\helpers\Html::a(
            "Удалить",
            '#',
            [
                'data-redactor_imgdeleter' => $img->id,
                'class' => 'btn btn-danger btn-sm',
                'data-confirm' => 'Вы уверены что хотите это сделать?'
            ]
        ) : '')

    . \yii\helpers\Html::label("Подпись", "redactor_img_alt" . $img->id)
    . \yii\helpers\Html::textInput("img_alt", $img->filetitle, ["id" => "redactor_img_alt" . $img->id])
    . '</div> <div>'
    . (is_array(\Yii::$app->getModule('content')->getModule('uploader')->imageClasses) ?
        \yii\helpers\Html::label("Css класс", "redactor_img_css" . $img->id)
        . \yii\helpers\Html::dropDownList(
            "img_css",
            '',
            \Yii::$app->getModule('content')->getModule('uploader')->imageClasses,
            ["id" => "redactor_img_css" . $img->id]
        )
        : \yii\helpers\Html::hiddenInput("img_css", '', ["id" => "redactor_img_css" . $img->id])
    ) . '</div><div>'
    . \yii\helpers\Html::label("Размер", "redactor_imgsize" . $img->id)
    . \yii\helpers\Html::dropDownList(
        "img_size",
        "thumb",
        ['thumb' => "Превью", 'mid' => "Средний размер", 'big' => "Большой размер", 'orig' => 'Оригинальный размер'],
        ["id" => "redactor_imgsize" . $img->id]
    )
    . '</div><div>'
    . \yii\helpers\Html::label("Выравнивание", "redactor_imgalign" . $img->id)
    . \yii\helpers\Html::dropDownList(
        "img_align",
        "",
        ['no' => 'Нет', 'left' => "Слева", 'right' => "Справа"],
        ["id" => "redactor_imgalign" . $img->id]
    )
    . '</div><div>'
    . \yii\helpers\Html::label("Увеличение по клику", "redactor_ispreview" . $img->id)
    . \yii\helpers\Html::checkbox("is_preview", false, ["id" => "redactor_ispreview" . $img->id])
    . '</div><div>'

    . (\Yii::$app->getModule('content')->getModule('uploader')->htmlinsertAllow ? \yii\helpers\Html::button(
        "Вставить html",
        ['data-redactor_inshtml_img' => $img->id, 'class' => 'btn btn-info btn-sm']
    ) : '')
    . (\Yii::$app->getModule('content')->getModule('uploader')->bbinsertAllow ? \yii\helpers\Html::button(
        "Вставить bb-code",
        ['data-redactor_insbb_img' => $img->id, 'class' => 'btn btn-info btn-sm']
    ) : '')
    . '
        </div>
    </div>';
?>
<td>
    <?=
    \yii\helpers\Html::a(
        \yii\helpers\Html::img(
            \Yii::$app->getModule('content')->getModule('uploader')->thumb_url . $img->filename,
            ['class' => 'img-thumbnail imgingrid', 'id' => 'rimg_' . $img->id]
        ),
        'javascript:void(0)',
        [
            'data-redactor_listedimg' => $img->id,
            'data-pjax' => 0,
            'id' => 'aimg_' . $img->id,
            'title' => $img->filetitle,
            'data-content' => $popover_content
        ]
    );?>


    <div id="redactor_images_data_<?= $img->id ?>" style="display: none"
         data-url_thumb="<?= \Yii::$app->getModule('content')->getModule('uploader')->thumb_url . $img->filename ?>"
         data-url_orig="<?= \Yii::$app->getModule('content')->getModule('uploader')->orig_url . $img->filename ?>"
         data-url_mid="<?= \Yii::$app->getModule('content')->getModule('uploader')->mid_url . $img->filename ?>"
         data-url_big="<?= \Yii::$app->getModule('content')->getModule('uploader')->big_url . $img->filename ?>"
        >
    </div>
</td>