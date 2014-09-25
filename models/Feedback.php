<?php

namespace insolita\content\models;

use Yii;
use yii\helpers\HtmlPurifier;

/**
 * This is the model class for table "vg_feedback".
 *
 * @property integer $id
 * @property string  $mail
 * @property string  $text
 * @property string  $created
 * @property string  $ip
 * @property integer $mailed
 */
class Feedback extends \insolita\things\components\SActiveRecord
{
    public static $titledAttribute = 'name';
    public  $gridDefaults = ['name', 'created', 'mail', 'text', 'ip'];
    public  $ignoredAttributes = [];

    public $captcha;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%feedback}}';
    }

    public static function modelTitle($type = 'plural')
    {
        $titles = [
            'single' => 'Обратная связь',
            'plural' => 'Сообщения обратной связи',
            'rod' => 'Сообщения',
            'vin' => 'Сообщение'
        ];
        return isset($titles[$type]) ? $titles[$type] : $titles['plural'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['mail', 'required', 'on' => 'create'],
            ['name', 'required', 'on' => 'create'],
            ['text', 'required', 'on' => 'create'],
            ['text', 'string', 'max' => 5000],
            ['mailed', 'integer'],
            ['mail', 'string', 'max' => 200],
            ['name', 'string', 'max' => 200],
            ['ip', 'string', 'max' => 15],
            ['captcha', 'captcha', 'enableClientValidation' => false, 'captchaAction' => '/content/front/captcha']
        ];
    }

    public function scenarios()
    {
        return [
            'default' => ['name', 'mail'],
            'create' => ['name', 'mail', 'text', 'captcha'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'mail' => 'E-mail/Phone',
            'text' => 'Текст',
            'created' => 'Создано',
            'ip' => 'IP',
            'mailed' => 'Замылено',
            'captcha' => 'Проверочный код'
        ];
    }


    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->text = HtmlPurifier::process($this->text, ['HTML.Allowed' => '']);
            $this->ip = Yii::$app->request->getUserIP();
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (Yii::$app->params['contactmail']) {
            $this->sendEmail();
        }
         parent::afterSave($insert, $changedAttributes);
    }

    public function sendEmail()
    {
        return Yii::$app->mailer->compose()
            ->setTo(Yii::$app->params['contactmail'])
            ->setFrom([$this->email => $this->name])
            ->setSubject(Yii::$app->params['siteurl'] . ' Cообщение из формы обратной связи')
            ->setTextBody($this->text)
            ->send();
    }

}
