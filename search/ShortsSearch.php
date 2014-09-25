<?php

namespace insolita\content\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use insolita\content\models\Shorts;

/**
 * ShortsSearch represents the model behind the search form about `insolita\content\models\Shorts`.
 */
class ShortsSearch extends Shorts
{
    public function rules()
    {
        return [
            [['id', 'active', 'bymanager'], 'integer'],
            [['name', 'text', 'created', 'updated'], 'safe'],
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

    public function frontsearch($params){
        $query = Shorts::find()->active()->orderBy(['created'=>SORT_DESC]);

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

    public function search($params)
    {
        $query = Shorts::find();

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
                'active' => $this->active,
                'created' => $this->created,
                'updated' => $this->updated,
                'bymanager' => $this->bymanager,
            ]
        );

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'text', $this->text]);

        return $dataProvider;
    }
}
