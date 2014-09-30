<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View                   $this
 * @var insolita\content\models\News   $model
 * @var insolita\content\models\Covers $cover
 * @var yii\widgets\ActiveForm         $form
 */
?>

<div class="news-form">


    <?php     $form = ActiveForm::begin(
        [
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'id' => 'modalform',
            'enableClientValidation' => false,
            'enableAjaxValidation' => false,
            'options' => ['enctype' => 'multipart/form-data'],
            'action' => ($model->isNewRecord
                    ? \yii\helpers\Url::to(['create'])
                    : \yii\helpers\Url::to(
                        ['update', 'id' => $model->{$model->getPk()}]
                    ))
        ]
    );
    ?>
    <?php
        \insolita\supergrid\panels\Panel::begin(
            [
                'title' => ($model->isNewRecord
                        ?
                        \insolita\things\helpers\Helper::Fa('plus-circle', 'lg') . ' Добавление'
                        :
                        \insolita\things\helpers\Helper::Fa('pencil-square-o', 'lg') . ' Редактирование "'
                        . $model->{$model::$titledAttribute} . '"'),
                'footer' => '<span class="pull-right">'
                    . Html::submitButton(
                        \insolita\things\helpers\Helper::Fa('check-circle', 'lg')
                        . 'Сохранить',
                        [
                            'class' => 'btn btn-success',
                            'title' => 'Сохранить запись (Enter)',
                            'id' => 'dirsubmit_' . ($model->isNewRecord ? 'addmodal' : 'updmodal')
                        ]
                    )
                    . '</span>'
                    . Html::a(
                        \insolita\things\helpers\Helper::Fa('times-circle', 'lg')
                        . 'Отмена',
                        ['index'],
                        [
                            'class' => 'btn btn-danger',
                            'title' => 'Отмена (Esc)',
                            'id' => 'cancel_' . ($model->isNewRecord ? 'addmodal' : 'updmodal')
                        ]
                    ),

            ]
        );

    echo $form->errorSummary([$model]);
    echo $form->errorSummary([$cover]);
    ?>
    <div id="resp_success" style="display: none" class="alert alert-success"></div>
    <div id="resp_error" class="alert alert-danger" style="display: none"></div>
    <?php     echo Form::widget(
        [
            'model' => $model,
            'form' => $form,
            'columns' => 1,
            'attributes' => [
                'name' => [
                    'type' => Form::INPUT_TEXT,
                    'options' => ['placeholder' => ' Название...', 'maxlength' => 200]
                ],
                'publishto' => [
                    'type' => Form::INPUT_WIDGET,
                    'widgetClass' => DateControl::classname(),
                    'hint' => 'Можно указать дату публикации задним числом, или отложить выставив будущую дату.
Оставьте пустой для установки текущей даты автоматом',
                    'options' => ['type' => DateControl::FORMAT_DATE]
                ],
            ]
        ]
    );?>

    <?php echo $form->field($model, 'anons')->widget(
        \vova07\imperavi\Widget::className(),
        [
            'id' => 'red_anons',
            'plugins' => [
                'subsup' => '\insolita\extimperavi\SubsupPluginAsset',
                'faicons' => '\insolita\extimperavi\FaiconsPluginAsset',
            ],
            'settings' => [
                'lang' => 'ru',
                'convertDivs' => false,
                'convertVideoLinks' => true,
                'pastePlainText' => true
            ]
        ]
    )?>
    <?php echo $form->field($model, 'full')->widget(
        \vova07\imperavi\Widget::className(),
        [
            'id' => 'red_full',
            'plugins' => ['subsup' => '\insolita\extimperavi\SubsupPluginAsset',
                'faicons' => '\insolita\extimperavi\FaiconsPluginAsset',
                'attachmanager' => '\insolita\extimperavi\AttachManagerPluginAsset'],
            'settings' => \yii\helpers\ArrayHelper::merge(
                    Yii::$app->getModule('content')->getModule('uploader')->getRedactorSettings(),
                    [
                        'lang' => 'ru',
                        'convertDivs' => false,
                        'convertVideoLinks' => true,
                        'pastePlainText' => true,
                        'deniedTags' => ['html', 'head', 'link', 'body', 'meta', 'script', 'footer', 'applet']
                    ]
                )
        ]
    )?>
    <?php if(Yii::$app->params['use_tags']):?>
    <?php
    $alltags = array_values(\insolita\content\models\Tags::getList());
    $alltags = count($alltags) ? $alltags : ["" => ""];
    echo $form->field($model, 'taglist')->widget(
        \kartik\widgets\Select2::classname(),
        [
            'data' => $alltags,
            'options' => ['placeholder' => 'Метки'],
            'pluginOptions' => [
                'tags' => $alltags,
                'allowClear' => 'true',
                'tokenSeparators' => [",", "  "],
                'minimumInputLength' => 0,
                'maximumInputLength' => 45
            ],
        ]
    );?>
    <?php endif;?>
    <?php
    $model->loadDefaultValues();
    echo $form->field($model, 'active')->checkbox()?>
    <?php if (Yii::$app->params['use_newscover']): ?>
        <fieldset id="cover">
            <legend>Титульное изображение</legend>
            <div class="row">
                <div class="col-sm-8 col-md-8 col-lg-8">
                    <?php echo $form->field($model, 'nocover')->checkbox() ?>
                    <?php echo $form->field($model, 'selcover')->widget(
                        \insolita\content\modules\cover\widgets\CoverChooser::className(),
                        [
                            'id' => 'covch',
                            'cols' => 4,
                            'covtype' => 'news',
                            'showto' => 'selcov'
                        ]
                    )->label('Выбрать') ?>
                </div>
                <div class="col-sm-4 col-md-4 col-lg-4" id="selcov"><?= $model->showCover() ?></div>
            </div>
        </fieldset>
    <?php endif; ?>
    <?php echo \yii\bootstrap\Collapse::widget(
        [
            'items' => [
                [
                    'label'=>'SEO-данные (не обязательно, генерируются автоматом)',
                    'content' => Form::widget(
                            [

                                'model' => $model,
                                'form' => $form,
                                'columns' => 2,
                                'attributes' => [
                                    'slug' => [
                                        'type' => Form::INPUT_TEXT,
                                        'options' => ['placeholder' => ' SEO-Ссылка(автоматом)...', 'maxlength' => 255]
                                    ],
                                    'metak' => [
                                        'type' => Form::INPUT_TEXT,
                                        'options' => ['placeholder' => ' SEO-ключи...', 'maxlength' => 255]
                                    ],
                                    'metadesc' => [
                                        'type' => Form::INPUT_TEXT,
                                        'options' => ['placeholder' => ' SEO-описание...', 'maxlength' => 255]
                                    ],

                                ]
                            ]
                        )
                ]
            ]
        ]
    )?>


    <?php \insolita\supergrid\panels\Panel::end(); ?>
    <?php ActiveForm::end(); ?>

</div>