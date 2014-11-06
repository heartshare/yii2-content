<?php

namespace insolita\content\controllers;

use insolita\things\ccactions\IndexAction;
use Yii;
use insolita\content\models\Feedback;
use insolita\content\search\FeedbackSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use dosamigos\grid\ToggleAction;
use insolita\things\ccactions\CreateAction;
use insolita\things\ccactions\RemoveAction;
use insolita\things\ccactions\ViewAction;

/**
 * FeedbackController implements the CRUD actions for Feedback model.
 */
class FeedbackController extends Controller
{
    public $title = '';
    public $icon = 'envelope-square';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'toggle', 'delete', 'mass'],
                        'roles' => ['staffaccess'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['remove'],
                        'roles' => ['admin'],
                    ],
                ]

            ],

        ];
    }

    public function actions()
    {
        $actions = [
            'toggle' => [
                'class' => ToggleAction::className(),
                'modelClass' => Feedback::className(),
                'onValue' => 1,
                'offValue' => 0
            ],
            'index' => [
                'class' => IndexAction::className(),
                'modelClass' => Feedback::className(),
                'searchClass' => FeedbackSearch::className(),
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => Feedback::className(),
            ],
            'delete' => [
                'class' => RemoveAction::className(),
                'modelClass' => Feedback::className(),
            ],
            'view' => [
                'class' => ViewAction::className(),
                'modelClass' => Feedback::className(),
            ],
        ];
        return $actions;
    }


    /**
     * Finds the Feedback model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Feedback the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Feedback::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
