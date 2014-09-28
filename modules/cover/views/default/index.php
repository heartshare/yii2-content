<div id="coverlist" style="max-height:400px; overflow:auto;">
    <p class="text-info"><?= \insolita\things\helpers\Helper::Fa('info-cercle', 'lg'); ?><b>Для выбора кликните 2 раза по
        нужному изображению!</b></p>
    <?php  \yii\widgets\Pjax::begin(
        [
            'id' => 'coverpjax',
            'linkSelector' => 'a[data-pjaxtarget_cover]',
            'enablePushState' => false,
            'timeout' => 10000,
            'clientOptions' => [
                'container' => '#coverpjax',
                'push' => false,
                'replace' => false,
                'cache' => true
            ]
        ]
    );
    ?>
    <?= $this->render('_coverlist', ['model' => $model, 'dpimg' => $dpimg, 'type' => $type]); ?>
    <?php \yii\widgets\Pjax::end(); ?>
</div>
<?= \yii\helpers\Html::button('Отменить выбор', ['id' => 'remchoose']) ?>
<?php     $form = \yii\widgets\ActiveForm::begin(
    [
        'id' => 'coverForm',
        'enableClientValidation' => false,
        'enableAjaxValidation' => false,
        'options' => [
            'enctype' => 'multipart/form-data',
            'method' => 'post'
        ],
        'action' => \yii\helpers\Url::to(['upload', 'type' => $type])
    ]
);
?>

<?=
$form->field($model, 'image')->widget(
    \dosamigos\fileupload\FileUpload::className(),
    [
        'url' => \yii\helpers\Url::to(['upload', 'type' => $type]),
        'options' => ['accept' => 'image/*'],
        'clientOptions' => [
            'maxFileSize' => 2000000
        ],
        'clientEvents' => [
            'fileuploaddone' =>
                'function (e, data){
                    console.log("uploaded with state "+JSON.stringify(data.responseJSON))
                    $.pjax.reload({container:"#coverpjax",timeout:5000,url:"'
                                 . yii\helpers\Url::to(['list', 'type' => $type])
                                 . '",push:false,replace:false,scrollTo:"#coverpjax"});
                }',
            'fileuploadfail'=>
                'function (e, data){
                    console.log("upload fail "+JSON.stringify(data.responseJSON));
                }',
        ]
    ]
);
?>
<?php \yii\widgets\ActiveForm::end(); ?>