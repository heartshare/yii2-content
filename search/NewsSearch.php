<?php

namespace insolita\content\search;

use frontend\widgets\ReversPagination2;
use insolita\things\helpers\Helper;
use insolita\things\validators\AdvDateValidator;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use insolita\content\models\News;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;

/**
 * NewsSearch represents the model behind the search form about `insolita\content\models\News`.
 */
class NewsSearch extends News
{
    public $search_query;

    public function rules()
    {
        return [
            [['id', 'active', 'views'], 'integer'],
            [['name'], 'string'],
            [
                [
                    'name',
                    'slug',
                    'anons',
                    'full',
                    'full_parsed',
                    'created',
                    'updated',
                    'metak',
                    'metadesc',
                    'publishto'
                ],
                'safe'
            ],
            ['search_query', 'string', 'min' => 3, 'max' => '100'],
            ['search_query', 'required', 'on' => 'usersearch'],
            ['publishto','required','on'=>'datasearch'],
            ['publishto',AdvDateValidator::className(),'format'=>['Y-m-d','Y-m']]
        ];
    }

    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                'usersearch' => ['search_query'],
                'datasearch'=>['publishto'],
                'usersearchresult' => [],
                'default' => ['id', 'name', 'slug', 'anons', 'active', 'created', 'updated', 'publishto']
            ]
        );
    }
    public function behaviors(){
        return [
            'customizer'=>[
                'class'=>'insolita\supergrid\behaviors\CustomizeModelBehavior',
                'scenarios'=>['default']
            ]
        ];
    }
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'search_query' => 'Поисковый запрос',
                'publishto'=>'Дата публикациии'
            ]
        );
    }

    public function search($params)
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->pageSize,
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(
            [
                'id' => $this->id,
                'cover_id' => $this->cover_id,
                'active' => $this->active,
                'views' => $this->views,
                'created' => $this->created,
                'updated' => $this->updated,
                'publishto' => $this->publishto,
            ]
        );

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'anons', $this->anons])
            ->andFilterWhere(['like', 'full', $this->full])
            ->andFilterWhere(['like', 'full_parsed', $this->full_parsed])
            ->andFilterWhere(['like', 'metak', $this->metak])
            ->andFilterWhere(['like', 'metadesc', $this->metadesc]);

        return $dataProvider;
    }

    public function frontsearch($params)
    {

        $query = self::find()
            ->where('{{%news}}.active=:a', [':a' => 1])
            ->andWhere(new Expression('{{%news}}.publishto<=NOW()'))
            ->orderBy(['{{%news}}.publishto' => SORT_DESC]);

        if(Yii::$app->params['use_tags']){
            $query->joinWith('tags');
        }
        if(Yii::$app->params['use_newscover']){
            $query->joinWith('cover');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'class' => '\frontend\widgets\ReversPagination2',
                'mode' => ReversPagination2::MOD_NONE,
                'pageSize' => Yii::$app->params['news_pp']

            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        return $dataProvider;
    }

    public function tagsearch($tag, $params)
    {
        $query = self::find()
            ->where('active=:a and publishto<=:b', [':a' => 1, ':b' => date('Y-m-d H:i', time())])
            ->joinWith(
                [
                    'tags' => function ($query) use ($tag) {
                            $query->where('tagname=:t', [':t' => $tag]);
                        }
                ]
            )
            ->joinWith(['cover'])
            ->orderBy(['publishto' => SORT_DESC]);

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

    public function datasearch($params){
        $this->scenario='datasearch';
        $query = self::find()
            ->where('{{%news}}.active=:a and {{%news}}.publishto<=:b', [':a' => 1, ':b' => date('Y-m-d H:i:s', time())])
            ->orderBy(['{{%news}}.publishto' => SORT_DESC]);
        if (!(($this->load($params) or $this->publishto) && $this->validate())) {
            return null;
        }
        $depth=explode('-',$this->publishto);
        if(count($depth)==2){
            $query->andWhere(['between','{{%news}}.publishto',$this->publishto.'-01 00:00:00',$this->publishto.'-31 23:59:59']);
        }else{
            $query->andWhere(['between','{{%news}}.publishto',$this->publishto.' 00:00:00',$this->publishto.' 23:59:59']);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['news_pp'],
            ],
        ]);

         return $dataProvider;
    }

    public function usersearch($params)
    {
        $model = new NewsSearch();
        $model->setScenario('usersearchresult');

        $query = $model::find()
            ->where('{{%news}}.active=:a and {{%news}}.publishto<=:b', [':a' => 1, ':b' => date('Y-m-d H:i', time())])
            ->orderBy(['{{%news}}.publishto' => SORT_DESC]);
        if (!(($this->load($params) or $this->search_query) && $this->validate())) {
            return null;
        }
        $query->andFilterWhere(['like', '{{%news}}.name', $this->search_query])
            ->orFilterWhere(['like', '{{%news}}.anons', $this->search_query])
            ->orFilterWhere(['like', '{{%news}}.full_parsed', $this->search_query]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['news_pp'],
            ],
        ]);

        $models = $dataProvider->getModels();
        foreach ($models as $m) {
            $m->search_query = $this->search_query;
            $m->makeSearchResult();
        }
        $dataProvider->setModels($models);
        //$this->detachBehavior('setscenario');
        return $dataProvider;
    }


    public function makeSearchResult()
    {
        $p = new HtmlPurifier();

        $puryConf = [
            'AutoFormat.Linkify' => false,
            'AutoFormat.AutoParagraph' => false,
            'HTML.Allowed' => ''
        ];
        $this->anons = $p->process($this->anons, $puryConf);
        $this->full_parsed = $p->process($this->full_parsed, $puryConf);
        if (mb_strpos($this->name, $this->search_query, null, 'UTF-8') !== false) {
            $this->name = str_replace(
                $this->search_query,
                '<span class="highlight">' . $this->search_query . '</span>',
                $this->name
            );
        }
        if (($pos = mb_strpos($this->anons, $this->search_query, null, 'UTF-8')) !== false) {
            if (($len = mb_strlen($this->anons, 'UTF-8')) > 1000) {
                $spos = ($pos < 100) ? 0 : $pos - 100;
                $epos = ($len > ($pos + 150)) ? $len : $pos + 150;
                $this->anons = '...' . mb_substr($this->anons, $spos, $epos, 'UTF-8') . '...';
            }
            $this->anons = str_replace(
                $this->search_query,
                '<span class="highlight">' . $this->search_query . '</span>',
                $this->anons
            );
        } elseif (($pos = mb_strpos($this->full_parsed, $this->search_query, null, 'UTF-8')) !== false) {
            if (($len = mb_strlen($this->full_parsed, 'UTF-8')) > 1000) {
                $spos = ($pos < 100) ? 0 : $pos - 100;
                $epos = ($len > ($pos + 150)) ? $len : $pos + 150;
                $this->full_parsed = '...' . mb_substr($this->full_parsed, $spos, $epos, 'UTF-8') . '...';
            }
            $this->full_parsed = str_replace(
                $this->search_query,
                '<span class="highlight">' . $this->search_query . '</span>',
                $this->full_parsed
            );
        }
    }

    public function afterFind(){
    }
}
