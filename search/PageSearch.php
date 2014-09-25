<?php

namespace insolita\content\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use insolita\content\models\Page;

/**
 * PageSearch represents the model behind the search form about `insolita\content\models\Page`.
 */
class PageSearch extends Page
{
    public function rules()
    {
        return [
            [['id', 'views', 'bymanager'], 'integer'],
            [['name', 'slug', 'full', 'full_parsed', 'updated', 'metak', 'metadesc'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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
        $query = Page::find();

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
                'views' => $this->views,
                'updated' => $this->updated,
                'bymanager' => $this->bymanager,
            ]
        );

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'full', $this->full])
            ->andFilterWhere(['like', 'full_parsed', $this->full_parsed])
            ->andFilterWhere(['like', 'metak', $this->metak])
            ->andFilterWhere(['like', 'metadesc', $this->metadesc]);

        return $dataProvider;
    }
}
