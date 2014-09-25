<?php     $form = \yii\widgets\ActiveForm::begin(
    [
        'id' => 'redactorAttachForm_' . $type,
        'enableClientValidation' => false,
        'enableAjaxValidation' => false,
        'options' => [
            'enctype' => 'multipart/form-data',
            'method' => 'post'
        ],
        'action' => ($type == \insolita\content\modules\uploader\models\Attach::TYPE_IMAGE)
                ? \yii\helpers\Url::to(['imageupl'])
                : \yii\helpers\Url::to(['fileupl'])
    ]
);
?>
<?php if ($type == \insolita\content\modules\uploader\models\Attach::TYPE_IMAGE): ?>
    <div id="redactor_imagelist" style="max-height:400px; overflow:auto;">

        <?php  \yii\widgets\Pjax::begin(
            [
                'id' => 'redactor_imagepjax',
                'linkSelector' => 'a[data-pjaxtarget_image]',
                'enablePushState' => false,
                'timeout' => 10000,
                'clientOptions' => [
                    'container' => '#redactor_imagepjax',
                    'push' => false,
                    'replace' => false,
                    'cache' => true
                ]
            ]
        );
        ?>
        <?= $this->render('_imagelist', ['model' => $model, 'dp' => $dp]); ?>
        <?php \yii\widgets\Pjax::end(); ?>
    </div>

    <div style="display: none" class="alert alert-danger" id="redactor_imgupload_error"></div>
    <div id="redactor_imguploaded"></div>
    <?= $form->field($model, 'imgfile')->fileInput(['id' => 'redactor_uplimage']); ?>
    <?= $form->field($model, 'filetitle')->textInput([]); ?>
    <div class="modal-footer">
        <button id="redactor_attach_uplimage" class="btn btn-success pull-left">Загрузить</button>
        <button class="btn  btn-danger redactor_btn_modal_close">Отменить</button>
    </div>
<?php else: ?>
    <div id="redactor_filelist" style="max-height:400px; overflow:auto;">
        <?php  \yii\widgets\Pjax::begin(
            [
                'id' => 'redactor_filepjax',
                'linkSelector' => 'a[data-pjaxtarget_file]',
                'enablePushState' => false,
                'timeout' => 10000,
                'clientOptions' => [
                    'container' => '#redactor_filepjax',
                    'push' => false,
                    'replace' => false,
                    'cache' => true
                ]
            ]
        );
        ?>
        <?= $this->render('_filelist', ['model' => $model, 'dp' => $dp]); ?>
        <?php \yii\widgets\Pjax::end(); ?>
    </div>

    <div style="display: none" class="alert alert-danger" id="redactor_fileupload_error"></div>
    <div id="redactor_fileuploaded"></div>
    <?= $form->field($model, 'file')->fileInput(['id' => 'redactor_uplfile']); ?>
    <?= $form->field($model, 'filetitle')->textInput([]); ?>
    <div class="modal-footer">
        <button id="redactor_attach_uplfile" class="btn btn-success pull-left">Загрузить</button>
        <button class="btn  btn-danger redactor_btn_modal_close">Отменить</button>
    </div>
<?php endif; ?>
<?php \yii\widgets\ActiveForm::end(); ?>