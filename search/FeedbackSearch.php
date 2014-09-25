<?php

namespace insolita\content\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use insolita\content\models\Feedback;

/**
 * FeedbackSearch represents the model behind the search form about `insolita\content\models\Feedback`.
 */
class FeedbackSearch extends Feedback
{
    public function rules()
    {
        return [
            [['id', 'mailed'], 'integer'],
            [['mail', 'text', 'created', 'ip'], 'safe'],
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
        $query = Feedback::find();

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
                'created' => $this->created,
                'mailed' => $this->mailed,
            ]
        );

        $query->andFilterWhere(['like', 'mail', $this->mail])
            ->andFilterWhere(['like', 'text', $this->text])
            ->andFilterWhere(['like', 'ip', $this->ip]);

        return $dataProvider;
    }
}
