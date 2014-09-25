<?php

namespace insolita\content\models;

use \insolita\things\components\SActiveRecord;
use insolita\things\behaviors\SlugModelBeh;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "vg_category".
 *
 * @property integer   $id
 * @property string    $name
 * @property string    $slug
 * @property string    $metaKey
 * @property string    $metaDesc
 * @property integer   $ord
 * @property integer   $cnt
 * @property string    $updated
 * @property Article[] $articles
 */
class Category extends SActiveRecord
{
    public static $titledAttribute = 'name';
    public  $gridDefaults = ['name', 'cnt', 'addtomenu'];
    public  $ignoredAttributes = ['bymanager'];
    public $addtomenu;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category}}';
    }

    public function behaviors()
    {
        return [
            'slug' => [
                'class' => SlugModelBeh::className(),
                'source_attribute' => 'name',
                'slug_attribute' => 'slug'
            ]
        ];
    }

    public static function modelTitle($type = 'plural')
    {
        $titles = [
            'single' => 'Раздел',
            'plural' => 'Разделы',
            'rod' => 'Раздела',
            'vin' => 'Раздел'
        ];
        return isset($titles[$type]) ? $titles[$type] : $titles['plural'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['ord', 'cnt'], 'integer'],
            [['updated'], 'safe'],
            [['name'], 'string', 'max' => 200],
            [['slug', 'metaKey', 'metaDesc'], 'string', 'max' => 255]
        ];
    }

    public function scenarios()
    {
        return [
            'create' => ['name', 'slug', 'metaKey', 'metaDesc', 'ord'],
            'update' => ['id', 'name', 'slug', 'metaKey', 'metaDesc', 'ord'],
            'recount' => [''],
            'delete' => ['id']
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
            'slug' => 'SEO-ссылка (автоматом)',
            'metaKey' => 'SEO-ключи',
            'metaDesc' => 'SEO-Описание',
            'ord' => 'Порядок',
            'cnt' => 'Кол-во статей',
            'updated' => 'Обновлено',
            'bymanager' => 'Автор',
            'addtomenu' => 'Ссылка для меню'
        ];
    }

    public function getArticles()
    {
        return $this->hasMany(Article::className(), ['cat_id' => 'id']);
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {

        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {

        parent::afterSave($insert, $changedAttributes);
    }

    public function reCount()
    {
        $this->scenario = 'recount';
        $this->cnt = Article::find()->where(['active' => 1, 'cat_id' => $this->id])->count();
        $this->save(false);
    }

    public function metaGenerate()
    {
        if ($this->cnt > 0) {
            $metadata = MetaData::find()->where(['model' => self::className(), 'model_id' => $this->id])->one();
            if (!$metadata) {
                $metadata = new  MetaData();
                $metadata->scenario = 'create';
                $metadata->model = self::className();
                $metadata->model_id = $this->id;
            } else {
                $metadata->scenario = 'update';
            }
            $data = Article::find()->select(['id', 'name', 'anons'])->indexBy('id')->where(
                ['active' => 1, 'cat_id' => $this->id]
            )->orderBy(['id' => SORT_DESC])->limit(10)->asArray()->all();
            $titles = ArrayHelper::map($data, 'id', 'name');
            $texts = ArrayHelper::map($data, 'id', 'anons');
            $data = array_reduce($titles, function ($res, $data) { return $res .= $data; }) . array_reduce(
                    $texts,
                    function ($res, $data) { return $res .= $data; }
                );
            $metadata->metadata = $data;
            $metadata->save(false);
            $this->metaKey = $metadata->metak;
            $this->metaDesc = $metadata->metad;
            $this->save(false);
        }
    }

    public function afterFind()
    {
        $this->addtomenu = Url::toRoute(['/content/front/category', 'slug' => $this->slug]);
        parent::afterFind();
    }

    public function getUrl()
    {
        return Yii::$app->params['siteurl'] . Url::toRoute(['content/front/category', 'slug' => $this->slug]);
    }

    public static function getLastModify()
    {
        return (new \yii\db\Query())->select('max(updated) as max')
            ->from(self::tableName())
            ->scalar();
    }
}
