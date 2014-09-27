<?php

namespace insolita\content\search;

use insolita\things\helpers\Helper;
use insolita\things\validators\AdvDateValidator;
use Yii;
use yii\data\ActiveDataProvider;
use insolita\content\models\Article;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;

/**
 * ArticleSearch represents the model behind the search form about `insolita\content\models\Article`.
 */
class ArticleSearch extends Article
{
    public $search_query;

    public function rules()
    {
        return [
            [['id', 'cat_id', 'cover_id', 'active', 'views', 'bymanager'], 'integer'],
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
            ['search_query', 'string', 'min' => 3, 'max' => 150],
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
                'default' => ['id', 'name', 'slug', 'anons', 'active', 'created', 'updated', 'publishto', 'cat_id']
            ]
        );
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
    public function behaviors(){
        return [
            'customizer'=>[
                'class'=>'insolita\supergrid\behaviors\CustomizeModelBehavior',
                'scenarios'=>['default']
            ]
        ];
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
                'cat_id' => $this->cat_id,
                'cover_id' => $this->cover_id,
                'active' => $this->active,
                'views' => $this->views,
                'created' => $this->created,
                'updated' => $this->updated,
                'bymanager' => $this->bymanager,
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


    public function frontsearch($cat, $params)
    {
        $query = self::find()->where('active=:a and publishto<=:b', [':a' => 1, ':b' => date('Y-m-d H:i', time())])
            ->andWhere(['cat_id' => $cat])
            ->with(['tags', 'cover'])
            ->orderBy(['publishto' => SORT_DESC]);
        if(Yii::$app->params['use_tags']){
            $query->joinWith('tags');
        }
        if(Yii::$app->params['use_artcover']){
            $query->joinWith('cover');
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['articles_pp'],
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
            ->joinWith(['cat'])
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
                'pageSize' => Yii::$app->params['articles_pp'],
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        return $dataProvider;
    }

    public function usersearch($params)
    {
        $query = self::find()
            ->where(
                '{{%article}}.active=:a and {{%article}}.publishto<=:b',
                [':a' => 1, ':b' => date('Y-m-d H:i', time())]
            )
            ->joinWith(['cat'])
            ->orderBy(['{{%article}}.publishto' => SORT_DESC]);
        if (!(($this->load($params) or $this->search_query) && $this->validate())) {
            return null;
        }
        $query->andFilterWhere(['like', '{{%article}}.name', $this->search_query])
            ->orFilterWhere(['like', '{{%article}}.anons', $this->search_query])
            ->orFilterWhere(['like', '{{%article}}.full_parsed', $this->search_query]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['articles_pp'],
            ],
        ]);

        $models = $dataProvider->getModels();
        foreach ($models as $m) {
            $m->search_query = $this->search_query;
            $m->makeSearchResult();
        }
        $dataProvider->setModels($models);
        return $dataProvider;
    }

    public function afterFind()
    {
        parent::afterFind();
        if ($this->scenario == 'usersearch') {

        }
    }

    public function makeSearchResult()
    {
        $p = new HtmlPurifier();
        Helper::logs('article makeSearchResult - ' . $this->search_query);
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
                $this->anons = '...' . mb_substr($this->anons, $spos, 300, 'UTF-8') . '...';
            }
            $this->anons = str_replace(
                $this->search_query,
                '<span class="highlight">' . $this->search_query . '</span>',
                $this->anons
            );
        } elseif (($pos = mb_strpos($this->full_parsed, $this->search_query, null, 'UTF-8')) !== false) {
            if (($len = mb_strlen($this->full_parsed, 'UTF-8')) > 1000) {
                $spos = ($pos < 100) ? 0 : $pos - 100;
                $this->full_parsed = '...' . mb_substr($this->full_parsed, $spos, 300, 'UTF-8') . '...';
            }
            $this->full_parsed = str_replace(
                $this->search_query,
                '<span class="highlight">' . $this->search_query . '</span>',
                $this->full_parsed
            );
        }
    }

    public function datasearch($params){
        $query = self::find()
        ->joinWith(['cat'])
            ->where('{{%article}}.active=:a and {{%article}}.publishto<=:b', [':a' => 1, ':b' => date('Y-m-d H:i:s', time())])
            ->orderBy(['{{%article}}.publishto' => SORT_DESC]);
        if (!(($this->load($params) or $this->publishto) && $this->validate())) {
            return null;
        }
        $depth=explode('-',$this->publishto);
        if(count($depth)==2){
            $query->andWhere(['between','{{%article}}.publishto',$this->publishto.'-01 00:00:00',$this->publishto.'-31 23:59:59']);
        }else{
            $query->andWhere(['between','{{%article}}.publishto',$this->publishto.' 00:00:00',$this->publishto.' 23:59:59']);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['articles_pp'],
            ],
        ]);

        return $dataProvider;
    }
}
