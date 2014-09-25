<div id="redactor_imagelist" style="max-height: 500px;height:100px; overflow:auto;">
    <?= $this->render('_imagelist', ['model' => $model, 'dp' => $dp]); ?>
</div>
<div id="redactor_image_options">
    <div class="row">
        <div class="col-sm-4 col-md-4">
            <?= \yii\helpers\Html::label('Название фото', 'redactor_img_alt') ?>
            <?= \yii\helpers\Html::textInput('img_alt', '', ['id' => 'redactor_img_alt']) ?>
        </div>
        <div class="col-sm-3 col-md-3">
            <?= \yii\helpers\Html::label('Размер', 'redactor_imgsize') ?>
            <?=
            \yii\helpers\Html::dropDownList(
                'img_size',
                '',
                [1 => 'Превью', 2 => 'Средний размер', 3 => 'Большой размер'],
                ['id' => 'redactor_imgsize']
            ) ?>
        </div>
        <div class="col-sm-3 col-md-3">
            <?= \yii\helpers\Html::label('Увеличение по клику', 'redactor_ispreview') ?>
            <?=
            \yii\helpers\Html::checkbox('is_preview', false, ['id' => 'redactor_ispreview']) ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button id="redactor_inserthtml_btn" class="btn btn-success pull-left">Вставить HTML</button>
    <button id="redactor_insertbbcode_btn" class="btn btn-primary pull-left">Вставить BB-code</button>
    <button class="btn  btn-danger redactor_btn_modal_close" data-dismiss="modal">Отменить</button>
</div>