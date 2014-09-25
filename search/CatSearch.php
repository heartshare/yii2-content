<?php

namespace insolita\content\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use insolita\content\models\Category;

/**
 * CatSearch represents the model behind the search form about `insolita\content\models\Category`.
 */
class CatSearch extends Category
{
    public function rules()
    {
        return [
            [['id', 'ord', 'cnt', 'bymanager'], 'integer'],
            [['name', 'slug', 'metaKey', 'metaDesc', 'updated'], 'safe'],
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
        $query = Category::find();

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
                'ord' => $this->ord,
                'cnt' => $this->cnt,
                'updated' => $this->updated,
                'bymanager' => $this->bymanager,
            ]
        );

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'metaKey', $this->metaKey])
            ->andFilterWhere(['like', 'metaDesc', $this->metaDesc]);

        return $dataProvider;
    }
}
