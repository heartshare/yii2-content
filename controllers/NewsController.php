<?php

namespace insolita\content\controllers;

use insolita\things\ccactions\IndexmassAction;
use insolita\things\ccactions\MassAction;
use insolita\things\ccactions\RemoveAction;
use Yii;
use insolita\content\models\News;
use insolita\content\search\NewsSearch;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use insolita\things\helpers\Helper;
use yii\web\Response;
use yii\filters\AccessControl;
use dosamigos\grid\ToggleAction;
use dosamigos\editable\EditableAction;

use insolita\content\modules\cover\models\Covers;

/**
 * NewsController implements the CRUD actions for News model.
 */
class NewsController extends Controller
{
    public $title = '';
    public $icon = 'file-text-o';


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
                        'actions' => ['index', 'create', 'update', 'view', 'preview', 'toggle', 'delete', 'mass'],
                        'roles' => ['newsman'],
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
                'modelClass' => News::className(),
                'onValue' => 1,
                'scenario'=>'toggle',
                'offValue' => 0
            ],
            'editable' => [
                'class' => EditableAction::className(),
                'modelClass' => News::className(),
                'scenario' => 'editable',
                'forceCreate' => false
            ],
            'index' => [
                'class' => IndexmassAction::className(),
                'modelClass' => News::className(),
                'searchClass' => NewsSearch::className(),
                'massactlist' => ['del' => 'Снять с публикации']
            ],
            'remove' => [
                'class' => RemoveAction::className(),
                'modelClass' => News::className(),
            ],

        ];
        $actions['mass'] = [
            'class' => MassAction::className(),
            'modelClass' => News::className(),
            'searchClass' => NewsSearch::className(),
            'massactlist' => ['del']
        ];
        return $actions;
    }

    public function actionCreate()
    {
        $model = new News();
        $cover = new Covers();
        $model->scenario = 'create';
        Yii::$app->response->format = (Yii::$app->request->isAjax) ? Response::FORMAT_JSON : Response::FORMAT_HTML;
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $model->save(false);
                return (Yii::$app->request->isAjax)
                    ? ['state' => true, 'error' => '']
                    : $this->redirect(
                        Url::to(['index'])
                    );
            } else {
                return (Yii::$app->request->isAjax) ? ['state' => false, 'error' => Helper::errorSummary([$model])]
                    : $this->render('create', ['model' => $model, 'cover' => $cover]);
            }
        } elseif (Yii::$app->request->isAjax) {
            return [
                'title' => Helper::Fa('plus-circle', 'lg') . ' Добавить ' . $model::modelTitle('vin'),
                'body' => $this->renderAjax('_form', ['model' => $model, 'cover' => $cover])
            ];
        } else {
            return $this->render('create', ['model' => $model, 'cover' => $cover]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $cover = isset($model->cover) ? $model->cover : new Covers();
        $model->scenario = 'update';
        $cover->scenario = 'update';
        Yii::$app->response->format = (Yii::$app->request->isAjax) ? Response::FORMAT_JSON : Response::FORMAT_HTML;
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $model->save(false);
                return (Yii::$app->request->isAjax)
                    ? ['state' => true, 'error' => '']
                    : $this->redirect(
                        Url::to(['index'])
                    );
            } else {
                return (Yii::$app->request->isAjax) ? [
                    'state' => false,
                    'error' => Helper::errorSummary([$model, $cover])
                ] : $this->render('update', ['model' => $model, 'cover' => $cover]);
            }
        } elseif (Yii::$app->request->isAjax) {
            return [
                'title' => Helper::Fa('pencil-square', 'lg') . 'Редактирование ' . $model::modelTitle('rod') . ' '
                    . $model->{$model::$titledAttribute},
                'body' => $this->renderAjax('_form', ['model' => $model, 'cover' => $cover])
            ];
        } else {
            return $this->render('update', ['model' => $model, 'cover' => $cover]);
        }
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        Yii::$app->response->format = (Yii::$app->request->isAjax) ? Response::FORMAT_JSON : Response::FORMAT_HTML;
        return Yii::$app->request->isAjax
            ? $this->renderAjax('_view', ['model' => $model])
            : $this->render(
                'view',
                ['model' => $model]
            );
    }


    /**
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return News the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
