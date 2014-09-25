<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 07.08.14
 * Time: 13:09
 */

namespace insolita\content\modules\cover\controllers;


use insolita\content\modules\cover\models\Covers;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'upload' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'list', 'upload', 'delete'],
                        'roles' => ['@'],
                    ],
                ]

            ],

        ];
    }

    public function actionIndex($type = 'all')
    {
        $model = new Covers();
        $query = Covers::find();
        if ($type != 'all') {
            $query->where(['conttype' => $type]);
        }
        $dpimg = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 9,
            ],
            'sort' => [
                'defaultOrder' => ['updated' => SORT_DESC]
            ]
        ]);
        return $this->renderAjax('index', ['model' => $model, 'dpimg' => $dpimg, 'type' => $type]);
    }

    public function actionList($type = 'all')
    {
        $model = new Covers();
        $query = Covers::find();
        if ($type != 'all') {
            $query->where(['conttype' => $type]);
        }
        $dpimg = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 9,
            ],
            'sort' => [
                'defaultOrder' => ['updated' => SORT_DESC]
            ]
        ]);
        return $this->renderAjax('_coverlist', ['model' => $model, 'dpimg' => $dpimg, 'type' => $type]);
    }

    public function actionUpload($type)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Covers();
        $model->scenario = 'create';
        $model->conttype = $type;
        $model->image = UploadedFile::getInstance($model, 'image');
        if ($model->save()) {

            return [
                'state' => true,
                'id' => $model->id,
                'filename' => $model->filename,
                'url' => \Yii::$app->getModule('content')->getModule('cover')->cover_url
            ];
        } else {
            return ['state' => false, 'error' => $model->formattedErrors()];
        }
    }

    public function actionDelete()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $id = \Yii::$app->request->post('id');
        if (!$id) {
            return ['state' => false];
        }
        $model = Covers::findOne($id);
        if ($model) {
            $model->delete();
            return ['state' => true];
        } else {
            return ['state' => false];
        }
    }
} 