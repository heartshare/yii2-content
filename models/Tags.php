<?php

namespace insolita\content\models;

use Yii;
use yii\data\ActiveDataProvider;
use insolita\things\components\SActiveRecord;

/**
 * This is the model class for table "vg_tags".
 *
 * @property integer  $tag_id
 * @property string   $tagname
 * @property integer  $freeq
 *
 * @property Tagged[] $taggeds
 */
class Tags extends SActiveRecord
{
    public static $titledAttribute = 'tagname';
    public  $gridDefaults = ['tag_id', 'tagname'];
    public  $ignoredAttributes = [];

    /**
     * Минимальный размер шрифта
     */
    const MIN_FONT_SIZE = 1;

    /**
     * Максимальный размер шрифта
     */
    const MAX_FONT_SIZE = 7;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tags}}';
    }

    public static function modelTitle($type = 'plural')
    {
        $titles = [
            'single' => 'Tags',
            'plural' => 'Tags',
            'rod' => 'Tags',
            'vin' => 'Tags'
        ];
        return isset($titles[$type]) ? $titles[$type] : $titles['plural'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tagname'], 'trim'],
            [['tagname'], 'filter', 'filter' => function ($attr) { return strip_tags($attr); }],
            [['tagname'], 'required'],
            [['tagname'], 'unique'],
            [['freeq'], 'integer'],
            [['tagname'], 'string', 'max' => 50, 'min' => 2]
        ];
    }

    public function scenarios()
    {
        return [
            'create' => ['tag_id', 'tagname', 'freeq'],
            'update' => ['tag_id', 'tagname', 'freeq'],
        ];
    }

    public static function findTag($name)
    {
        return self::findOne(['tagname' => $name]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tag_id' => 'Tag ID',
            'tagname' => 'Tagname',
            'freeq' => 'freeq'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArttags()
    {
        return $this->hasMany(Tagged::className(), ['tagid' => 'tag_id'])->andOnCondition('conttype="art"');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNewstags()
    {
        return $this->hasMany(Tagged::className(), ['tagid' => 'tag_id'])->andOnCondition('conttype="news"');
    }

    public function getArticles()
    {
        return $this->hasMany(Article::className(), ['id' => 'contid'])->via('arttags');
    }

    public function getNews()
    {
        return $this->hasMany(News::className(), ['id' => 'contid'])->via('newstags');
    }

    public function getTaggeds()
    {
        return $this->hasMany(Tagged::className(), ['tagid' => 'tag_id']);
    }


    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {

        }
        return parent::beforeSave($insert);
    }


    public function frontSearchNews($tagname, $params)
    {
        $query = self::find()->joinWith(['news'])->where("tagname=:n", [":n" => $tagname]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['news_pp'],
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        return $dataProvider;
    }

    public function frontSearchArts($tagname, $params)
    {
        $query = self::find()->joinWith(['articles'])->where("tagname=:n", [":n" => $tagname]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['news_pp'],
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        return $dataProvider;
    }

    public function freeqRecount()
    {
        $this->freeq = Tagged::find()->where(['tagid' => $this->tag_id])->count();
        $this->save(false, ['freeq']);
    }

    public function freeqDecr()
    {
        $this->freeq = $this->freeq - 1;
        $this->save(false, ['freeq']);
    }

    public function freeqIncr()
    {
        $this->freeq = $this->freeq + 1;
        $this->save(false, ['freeq']);
    }

    /**
     * Возвращает теги вместе с их весом
     *
     * @param integer $limit число возвращаемых тегов
     *
     * @return array вес с индексом равным имени тега
     */
    public static function findTagWeights($limit = 20)
    {
        $tags = array();

        $models = self::find()->limit($limit)->all();

        $sizeRange = self::MAX_FONT_SIZE - self::MIN_FONT_SIZE;
        $minCnt = $query = (new \yii\db\Query())
            ->select('MIN(freeq) as freeq')
            ->from(self::tableName())
            ->scalar();
        $maxCnt = $query = (new \yii\db\Query())
            ->select('MAX(freeq) as freeq')
            ->from(self::tableName())
            ->scalar();
        $minCount = log($minCnt + 1);
        $maxCount = log($maxCnt + 1);
        $countRange = $maxCount - $minCount;

        foreach ($models as $model) {
            $tags[$model->tagname] = round(
                self::MIN_FONT_SIZE + (log($model->freeq + 1) - $minCount) * ($sizeRange / $countRange)
            );
        }

        return $tags;
    }


}
