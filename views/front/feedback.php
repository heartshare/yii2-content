<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 24.08.14
 * Time: 11:00
 */
$this->title = 'Контакты и форма обратной связи';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php if (Yii::$app->params['show_contactform'] && Yii::$app->params['show_contactinfo']): ?>
    <div class="row">
        <div class="col-md-6">
            <?php \insolita\supergrid\panels\Panel::begin(
                [
                    'style' => \insolita\supergrid\panels\Panel::PANEL_SUCCESS,
                    'title' => 'Оставьте сообщение',
                    'options' => ['id' => 'contacts']
                ]
            ) ?>
            <?php $form = \yii\bootstrap\ActiveForm::begin(
                [
                    'method' => 'post',
                    'action' => \yii\helpers\Url::toRoute(['/content/front/feedback']),
                    'enableClientValidation' => false,
                    'enableAjaxValidation' => false,
                ]
            )?>
            <?= $form->field($model, 'name')->textInput()->label('Представьтесь, пожалуйста'); ?>
            <?= $form->field($model, 'mail')->textInput()->label('Телефон или E-mail для связи') ?>
            <?= $form->field($model, 'text')->textarea()->label('Ваше сообщение'); ?>
            <?= $form->field($model, 'captcha')->widget(
                '\yii\captcha\Captcha',
                ['captchaAction' => '/content/front/captcha']
            )->label('Введите код с картинки') ?>
            <?= \yii\helpers\Html::submitButton('Отправить', ['class' => 'btn btn-success']) ?>
            <?php \yii\bootstrap\ActiveForm::end(); ?>
            <?php \insolita\supergrid\panels\Panel::end(); ?>
        </div>
        <div class="col-md-6">
            <?php \insolita\supergrid\panels\Panel::begin(
                [
                    'style' => \insolita\supergrid\panels\Panel::PANEL_INFO,
                    'title' => 'Наши контакты',
                    'options' => ['id' => 'contacts']
                ]
            ) ?>
            <?= \yii\helpers\Html::decode(Yii::$app->params['contactinfo']); ?>
            <?php \insolita\supergrid\panels\Panel::end(); ?>
        </div>
    </div>
<?php elseif (Yii::$app->params['show_contactform']): ?>
    <?php \insolita\supergrid\panels\Panel::begin(
        [
            'style' => \insolita\supergrid\panels\Panel::PANEL_SUCCESS,
            'title' => 'Оставьте сообщение',
            'options' => ['id' => 'contacts']
        ]
    ) ?>
    <?php $form = \yii\bootstrap\ActiveForm::begin(
        [
            'method' => 'post',
            'action' => \yii\helpers\Url::toRoute(['/content/front/feedback']),
            'enableClientValidation' => true,
            'enableAjaxValidation' => true,
        ]
    )?>
    <?= $form->field($model, 'name')->textInput()->label('Представьтесь, пожалуйста'); ?>
    <?= $form->field($model, 'mail')->textInput()->label('Телефон или E-mail для связи') ?>
    <?= $form->field($model, 'text')->textarea()->label('Ваше сообщение'); ?>
    <?= $form->field($model, 'captcha')->widget(
        \yii\captcha\Captcha::className(),
        ['captchaAction' => '/content/front/captcha']
    )->label('Введите код с картинки') ?>
    <?= \yii\helpers\Html::submitButton('Отправить', ['class' => 'btn btn-success']) ?>
    <?php \yii\bootstrap\ActiveForm::end(); ?>
    <?php \insolita\supergrid\panels\Panel::end(); ?>
<?php
elseif (Yii::$app->params['show_contactinfo']): ?>
    <?php \insolita\supergrid\panels\Panel::begin(
        ['style' => \insolita\supergrid\panels\Panel::PANEL_INFO, 'title' => 'Наши контакты', 'options' => ['id' => 'contacts']]
    ) ?>
    <?= \yii\helpers\Html::decode(Yii::$app->params['contactinfo']); ?>
    <?php \insolita\supergrid\panels\Panel::end(); ?>
<?php
else: ?>
    '----'
<?php endif; ?>
<?php
echo \himiklab\colorbox\Colorbox::widget(
    [
        'targets' => [
            '[rel="imagelink"]' => [
                'maxWidth' => 1024,
                'maxHeight' => 900
            ]
        ]
    ]
);
?>