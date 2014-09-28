<?php
use Yii;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var insolita\content\models\Page $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="page-form">

    <?php     $form = ActiveForm::begin(
        [
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'id' => 'modalform',
            'enableClientValidation' => false,
            'enableAjaxValidation' => false,
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
                'on_lookmod'=>['newpage'],
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

            ]
        ]
    );?>
    <?php echo $form->field($model, 'full')->widget(
        \vova07\imperavi\Widget::className(),
        [
            'id' => 'red_full',
            'plugins' => ['subsup' => '\insolita\extimperavi\SubsupPluginAsset',
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

    <?php echo \yii\bootstrap\Collapse::widget(
        [
            'items' => [
                'SEO-данные (не обязательно, генерируются автоматом)' => [
                    'content' => Form::widget(
                            [
                                'model' => $model,
                                'form' => $form,
                                'columns' => 1,
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

    <?php  \insolita\supergrid\panels\Panel::end();?>

    <?php ActiveForm::end(); ?>

</div>
