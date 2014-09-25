<?php

namespace insolita\content\controllers;

 use Yii;
use insolita\content\models\Article;
use insolita\content\search\ArticleSearch;
 use yii\web\Controller;
 use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use dosamigos\grid\ToggleAction;
use insolita\things\ccactions\CreateAction;
use insolita\things\ccactions\DeleteAction;
use insolita\things\ccactions\MassAction;
use insolita\things\ccactions\IndexmassAction;
use insolita\things\ccactions\RemoveAction;
use insolita\things\ccactions\UpdateAction;
use insolita\things\ccactions\ViewAction;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class ArticleController extends Controller
{
    public $title = '';
    public $icon = 'file-text';

    public function beforeAction($action)
    {
        parent::beforeAction($action);
        Yii::$app->getModule('supergrid')->setLookmod('newpage');
        return true;
    }

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
                        'actions' => ['index', 'create', 'update', 'view', 'toggle', 'delete', 'mass'],
                        'roles' => ['artman'],
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
                'modelClass' => Article::className(),
                'scenario'=>'toggle',
                'onValue' => 1,
                'offValue' => 0
            ],
            'index' => [
                'class' => IndexmassAction::className(),
                'modelClass' => Article::className(),
                'searchClass' => ArticleSearch::className(),
                'massactlist' => ['del' => 'Удалить']
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => Article::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => Article::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => Article::className(),
            ],
            'remove' => [
                'class' => RemoveAction::className(),
                'modelClass' => Article::className(),
            ],
            'view' => [
                'class' => ViewAction::className(),
                'modelClass' => Article::className(),
            ],
        ];
        $actions['mass'] = [
            'class' => MassAction::className(),
            'modelClass' => Article::className(),
            'searchClass' => ArticleSearch::className(),
            'massactlist' => ['del']
        ];
        return $actions;
    }


    /**
     * Finds the Article model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Article the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Article::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
