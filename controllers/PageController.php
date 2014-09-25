<?php

namespace insolita\content\controllers;

use insolita\things\ccactions\CreateAction;
use insolita\things\ccactions\DeleteAction;
use insolita\things\ccactions\IndexAction;
use insolita\things\ccactions\RemoveAction;
use insolita\things\ccactions\UpdateAction;
use insolita\things\ccactions\ViewAction;
use Yii;
use insolita\content\models\Page;
use insolita\content\search\PageSearch;
use backend\common\StaffController;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use dosamigos\grid\ToggleAction;

/**
 * PageController implements the CRUD actions for Page model.
 */
class PageController extends Controller
{
    public $title = '';
    public $icon = 'files-o';

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
                        'actions' => ['look'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'create', 'update', 'view', 'toggle', 'delete', 'mass'],
                        'roles' => ['@'],
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
                'modelClass' => Page::className(),
                'onValue' => 1,
                'scenario'=>'toggle',
                'offValue' => 0
            ],
            'index' => [
                'class' => IndexAction::className(),
                'modelClass' => Page::className(),
                'searchClass' => PageSearch::className(),
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => Page::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => Page::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => Page::className(),
            ],
            'remove' => [
                'class' => RemoveAction::className(),
                'modelClass' => Page::className(),
            ],
            'view' => [
                'class' => ViewAction::className(),
                'modelClass' => Page::className(),
            ],
        ];
        return $actions;
    }


    public function actionLook($slug)
    {
        $model = $this->findModelbySlug($slug);
        return $this->render('look', ['model' => $model]);
    }

    /**
     * Finds the Page model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Page the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Page::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelbySlug($slug)
    {
        if (($model = Page::findOne(['slug' => $slug])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
