<?php
$items = [];
if (\Yii::$app->getModule('content')->getModule('uploader')->imageUploadAllow) {
    $items[] = [
        'label' => 'Загрузить изображение',
        'active' => true,
        'content' => $this->render(
                '_attachform',
                [
                    'model' => $model,
                    'dp' => $dpimg,
                    'type' => \insolita\content\modules\uploader\models\Attach::TYPE_IMAGE
                ]
            )
    ];
}
if (\Yii::$app->getModule('content')->getModule('uploader')->fileUploadAllow) {
    $items[] = [
        'label' => 'Загрузить файл',
        'content' => $this->render(
                '_attachform',
                [
                    'model' => $model,
                    'dp' => $dpfile,
                    'type' => \insolita\content\modules\uploader\models\Attach::TYPE_FILE
                ]
            )
    ];
}
?>
<section id="redactor-modal-image-manager">
    <div class="modal-body">
        <?php echo \yii\bootstrap\Tabs::widget(
            [
                'items' => (!empty($items))
                        ? $items
                        : [
                            'label' => 'Некорректная настройка модуля',
                            'content' => 'Разрешите загрузку файлов или изображений'
                        ]
            ]
        )?>

    </div>


</section>

