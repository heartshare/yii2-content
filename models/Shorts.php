<?php

namespace insolita\content\models;

use Yii;
use yii\helpers\ArrayHelper;
use \insolita\things\components\SActiveRecord;

/**
 * This is the model class for table "vg_smallnews".
 *
 * @property integer $id
 * @property string  $name
 * @property string  $text
 * @property integer $active
 * @property string  $created
 * @property string  $updated
 */
class Shorts extends SActiveRecord
{
    public static $titledAttribute = 'name';
    public  $gridDefaults = ['id', 'name', 'text', 'active', 'created','publishto', 'updated'];
    public  $ignoredAttributes = ['bymanager'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%smallnews}}';
    }

    public static function modelTitle($type = 'plural')
    {
        $titles = [
            'single' => 'Мини-Новость',
            'plural' => 'Мини-новости',
            'rod' => 'Мини-новости',
            'vin' => 'Мини-новость'
        ];
        return isset($titles[$type]) ? $titles[$type] : $titles['plural'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text'], 'required'],
            ['name','default','value'=>'...'],
            [['id', 'active'], 'integer'],
            [['text'], 'string'],
            [['created', 'updated'], 'safe'],
            [['name'], 'string', 'max' => 255],
            ['publishto', 'default', 'value' => date('Y-m-d H:i:s', time())]

        ];
    }

    public function scenarios()
    {
        return [
            'create' => ['id', 'name', 'text', 'active', 'created', 'updated','publishto'],
            'update' => ['id', 'name', 'text', 'active', 'created', 'updated','publishto'],
            'toggle' => ['active'],
            'delete' => []
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'text' => 'Содержание',
            'active' => 'Опубликовано?',
            'created' => 'Создано',
            'updated' => 'Обновлено',
            'bymanager' => 'Автор',
            'publishto'=>'Дата публикации'
        ];
    }


    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created = date('Y-m-d H:i:s', time());
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {

        parent::afterSave($insert, $changedAttributes);
    }

    public function metaGenerate($page = 0)
    {
        $cache = Yii::$app->cache->get('meta_novosti' . $page);
        if ($cache == null) {
            $metadata = MetaData::find()->where(['model' => self::className(), 'model_id' => 'novosti/' . $page])->one(
            );
            if (!$metadata) {
                $metadata = new  MetaData();
                $metadata->scenario = 'create';
                $metadata->model = self::className();
                $metadata->model_id = 'novosti/' . $page;
            } else {
                $metadata->scenario = 'update';
            }
            $limit = Yii::$app->params['news_pp'];
            $offset = $limit * $page;
            $data = Shorts::find()->select(['id', 'name', 'text'])->indexBy('id')->where(
                Shorts::tableName() . '.active=:a',
                [':a' => 1]
            )->orderBy(['created' => SORT_DESC])->offset($offset)->limit($limit)->asArray()->all();
            $titles = ArrayHelper::map($data, 'id', 'name');
            $texts = ArrayHelper::map($data, 'id', 'text');
            $data = array_reduce($titles, function ($res, $data) { return $res .= ' ' . $data; }) . array_reduce(
                    $texts,
                    function ($res, $data) { return $res .= ' ' . $data; }
                );
            $metadata->metadata = $data;
            $metadata->save(false);
            Yii::$app->cache->set(
                'meta_mewslist' . $page,
                ['metaKey' => $metadata->metak, 'metaDesc' => $metadata->metad],
                3600
            );
            return ['metaKey' => $metadata->metak, 'metaDesc' => $metadata->metad];
        }
        return $cache;
    }

}
