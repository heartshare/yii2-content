<?php

namespace insolita\content\controllers;

use insolita\things\ccactions\IndexAction;
use Yii;
use insolita\content\models\Category;
use insolita\content\search\CatSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use dosamigos\grid\ToggleAction;
use insolita\things\ccactions\CreateAction;
use insolita\things\ccactions\DeleteAction;
use insolita\things\ccactions\RemoveAction;
use insolita\things\ccactions\UpdateAction;
use insolita\things\ccactions\ViewAction;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
{
    public $title = '';
    public $icon = 'folder-open';

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
                        'actions' => ['index', 'create', 'update', 'view', 'toggle', 'delete', 'mass', 'recount'],
                        'roles' => ['artman'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['remove'],
                        'roles' => ['artman'],
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
                'modelClass' => Category::className(),
                'scenario'=>'toggle',
                'onValue' => 1,
                'offValue' => 0
            ],
            'index' => [
                'class' => IndexAction::className(),
                'modelClass' => Category::className(),
                'searchClass' => CatSearch::className(),
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => Category::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => Category::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => Category::className(),
            ],
            'remove' => [
                'class' => RemoveAction::className(),
                'modelClass' => Category::className(),
            ],
            'view' => [
                'class' => ViewAction::className(),
                'modelClass' => Category::className(),
            ],
        ];
        return $actions;
    }


    public function actionRecount($id)
    {
        /**
         * @var \insolita\content\models\Category $model
         * * @var \insolita\content\models\MetaData $metadata
         */
        $model = Category::findOne($id);
        $model->reCount();
        if ($model->cnt > 0) {
            $model->metaGenerate();
            Yii::$app->session->setFlash('success', 'Счётчик статей обновлен, мета-данные перестроены', false);
            return $this->redirect(['index']);
        }
        Yii::$app->session->setFlash('success', 'Счётчик статей обновлен', false);
        return $this->redirect(['index']);
    }


    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
