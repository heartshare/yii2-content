<?php

namespace insolita\content\controllers;

use insolita\things\ccactions\CreateAction;
use insolita\things\ccactions\DeleteAction;
use insolita\things\ccactions\IndexmassAction;
use insolita\things\ccactions\MassAction;
use insolita\things\ccactions\RemoveAction;
use insolita\things\ccactions\UpdateAction;
use insolita\things\ccactions\ViewAction;
use Yii;
use insolita\content\models\Shorts;
use insolita\content\search\ShortsSearch;
use backend\common\StaffController;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use dosamigos\grid\ToggleAction;

/**
 * ShortsController implements the CRUD actions for Shorts model.
 */
class ShortsController extends Controller
{
    public $title = '';
    public $icon = 'comment-o';

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
                'modelClass' => Shorts::className(),
                'scenario'=>'toggle',
                'onValue' => 1,
                'offValue' => 0
            ],
            'index' => [
                'class' => IndexmassAction::className(),
                'modelClass' => Shorts::className(),
                'searchClass' => ShortsSearch::className(),
                'massactlist' => ['del' => 'Удалить']
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => Shorts::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => Shorts::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => Shorts::className(),
            ],
            'remove' => [
                'class' => RemoveAction::className(),
                'modelClass' => Shorts::className(),
            ],
            'view' => [
                'class' => ViewAction::className(),
                'modelClass' => Shorts::className(),
            ],
        ];
        $actions['mass'] = [
            'class' => MassAction::className(),
            'modelClass' => Shorts::className(),
            'searchClass' => ShortsSearch::className(),
            'massactlist' => ['del']
        ];
        return $actions;
    }


    /**
     * Finds the Shorts model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Shorts the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Shorts::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
