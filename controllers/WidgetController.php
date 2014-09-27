<?php

namespace insolita\content\controllers;


use insolita\content\widgets\AlbumWidget;
use insolita\content\widgets\ArchiveWidget;
use insolita\content\widgets\ArticleWidget;
use insolita\content\widgets\NewsWidget;
use insolita\content\widgets\FotoWidget;
use insolita\content\widgets\SearchWidget;
use insolita\content\widgets\SliderWidget;
use insolita\content\widgets\TagWidget;
use insolita\content\widgets\TextWidget;
use insolita\widgetman\IWidget;
use insolita\widgetman\models\Widgetman;
use insolita\widgetman\WidgetmanModule;
use Yii;
use yii\base\DynamicModel;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * WidgetController implements the CRUD actions for Widgets model.
 */
class WidgetController extends Controller
{
    public $title = '';
    public $icon = 'puzzle-piece';

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
                        'actions' => '',
                        'roles' => ['widgetman'],
                    ],
                ]

            ],

        ];
    }

    public function actionSearchWidgetConfigure($id)
    {
        /**
         * @var WidgetmanModule $module
         * @var Widgetman       $wmodel
         **/
        $module = Yii::$app->getModule('widgetman');
        $wmodel = $this->findModel($id);
        $wmodel->scenario = 'configure';
        $positions = $module->getWidgetPlaces($wmodel->class);
        $wmodel->options = Json::decode($wmodel->options);

        $model = new DynamicModel(
            [
                'searchtype' => $wmodel->options['searchtype'],
                'title' => isset($wmodel->options['title']) ? $wmodel->options['title'] : '',
                'icon' => isset($wmodel->options['icon']) ? $wmodel->options['icon'] : '',
                'mode' => isset($wmodel->options['mode']) ? $wmodel->options['mode'] : IWidget::MODE_FLAT,
                'type' => isset($wmodel->options['type']) ? $wmodel->options['type'] : IWidget::TYPE_DEFAULT,
                'positions' => $wmodel->position,
                'ord' => $wmodel->ord
            ], []
        );
        $model->addRule(
            ['searchtype'],
            'in',
            [
                'range' => [
                    SearchWidget::SEARCH_TYPE_ALL,
                    SearchWidget::SEARCH_TYPE_NEWS,
                    SearchWidget::SEARCH_TYPE_ARTICLES
                ]
            ]
        );
        $model->addRule(['title', 'icon'], 'string', ['max' => 200]);
        $model->addRule(
            ['mode'],
            'in',
            ['range' => array_keys(IWidget::$modes)]
        );
        $model->addRule(
            ['type'],
            'in',
            ['range' => array_keys(IWidget::$types)]
        );
        $model->addRule(['positions'], 'in', ['range' => array_keys($positions)]);
        $model->addRule(['ord'], 'integer');
        $model->addRule(['ord'], 'default', ['value' => 10]);


        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $wmodel->options = $model->getAttributes(null, ['positions', 'ord']);
            $wmodel->content = '';
            $wmodel->position = $model->positions;
            $wmodel->ord = $model->ord;
            if ($wmodel->save()) {
                Yii::$app->session->setFlash('success', 'Настройки модуля сохранены');
                return $this->redirect(['/widgetman/widgetman/index']);
            }
        }

        return $this->render(
            '../../widgets/forms/searchwidgconf',
            ['model' => $model, 'wmodel' => $wmodel, 'positions' => $positions]
        );
    }

    public function actionArtWidgetConfigure($id)
    {
        /**
         * @var WidgetmanModule $module
         * @var Widgetman       $wmodel
         **/
        $module = Yii::$app->getModule('widgetman');
        $wmodel = $this->findModel($id);
        $wmodel->scenario = 'configure';
        $positions = $module->getWidgetPlaces($wmodel->class);
        $wmodel->options = Json::decode($wmodel->options);

        $model = new DynamicModel(
            [
                'title' => isset($wmodel->options['title']) ? $wmodel->options['title'] : '',
                'icon' => isset($wmodel->options['icon']) ? $wmodel->options['icon'] : '',
                'mode' => isset($wmodel->options['mode']) ? $wmodel->options['mode'] : IWidget::MODE_FLAT,
                'type' => isset($wmodel->options['type']) ? $wmodel->options['type'] : IWidget::TYPE_DEFAULT,
                'orient' => $wmodel->options['orient'],
                'total' => $wmodel->options['total'],
                'category' => $wmodel->options['category'],
                'sorttype' => $wmodel->options['sorttype'],
                'showcover' => $wmodel->options['showcover'],
                'showanons' => $wmodel->options['showanons'],
                'anonslimit' => $wmodel->options['anonslimit'],
                'positions' => $wmodel->position,
                'ord' => $wmodel->ord
            ], []
        );
        $model->addRule(
            ['mode'],
            'in',
            ['range' => array_keys(IWidget::$modes)]
        );
        $model->addRule(
            ['type'],
            'in',
            ['range' => array_keys(IWidget::$types)]
        );
        $model->addRule(['orient'], 'in', ['range' => [ArticleWidget::ORIENT_HOR, ArticleWidget::ORIENT_VER]]);
        $model->addRule(
            ['sorttype'],
            'in',
            ['range' => [ArticleWidget::NEWS_BOTH, ArticleWidget::NEWS_LATEST, ArticleWidget::NEWS_POPULAR]]
        );
        $model->addRule(['showanons', 'showcover'], 'in', ['range' => [0, 1]]);
        $model->addRule(['title', 'icon'], 'string', ['max' => 200]);
        $model->addRule(['positions'], 'in', ['range' => array_keys($positions)]);
        $model->addRule(['ord', 'total', 'anonslimit', 'category'], 'integer');
        $model->addRule(['ord'], 'default', ['value' => 10]);
        $model->addRule(['total'], 'default', ['value' => 5]);
        $model->addRule(['category'], 'default', ['value' => 0]);
        $model->addRule(['anonslimit'], 'default', ['value' => 0]);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $wmodel->options = $model->getAttributes(null, ['positions', 'ord']);
            $wmodel->content = '';
            $wmodel->position = $model->positions;
            $wmodel->ord = $model->ord;
            if ($wmodel->save()) {
                Yii::$app->session->setFlash('success', 'Настройки модуля сохранены');
                return $this->redirect(['/widgetman/widgetman/index']);
            }
        }

        return $this->render(
            '../../widgets/forms/artswidgconf',
            ['model' => $model, 'wmodel' => $wmodel, 'positions' => $positions]
        );
    }

    public function actionTextWidgetConfigure($id)
    {
        /**
         * @var WidgetmanModule $module
         * @var Widgetman       $wmodel
         **/
        $module = Yii::$app->getModule('widgetman');
        $wmodel = $this->findModel($id);
        $wmodel->scenario = 'configure';
        $positions = $module->getWidgetPlaces($wmodel->class);
        $wmodel->options = Json::decode($wmodel->options);

        $model = new DynamicModel(
            [
                'title' => isset($wmodel->options['title']) ? $wmodel->options['title'] : '',
                'icon' => isset($wmodel->options['icon']) ? $wmodel->options['icon'] : '',
                'mode' => isset($wmodel->options['mode']) ? $wmodel->options['mode'] : IWidget::MODE_FLAT,
                'type' => isset($wmodel->options['type']) ? $wmodel->options['type'] : IWidget::TYPE_DEFAULT,
                'text' => $wmodel->options['text'],
                'positions' => $wmodel->position,
                'ord' => $wmodel->ord
            ], []
        );
        $model->addRule(
            ['mode'],
            'in',
            ['range' => array_keys(IWidget::$modes)]
        );
        $model->addRule(
            ['type'],
            'in',
            ['range' => array_keys(IWidget::$types)]
        );
        $model->addRule(['title', 'icon'], 'string', ['max' => 200]);
        $model->addRule(['text'], 'string', [ 'max' => 10000]);
        $model->addRule(['text'], 'default', ['value' => '']);
        $model->addRule(['positions'], 'in', ['range' => array_keys($positions)]);
        $model->addRule(['ord'], 'integer');
        $model->addRule(['ord'], 'default', ['value' => 10]);


        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $wmodel->options = $model->getAttributes(null, ['positions', 'ord']);
            $wmodel->content = '';
            $wmodel->position = $model->positions;
            $wmodel->ord = $model->ord;
            if ($wmodel->save()) {
                Yii::$app->session->setFlash('success', 'Настройки модуля сохранены');
                return $this->redirect(['/widgetman/widgetman/index']);
            }
        }

        return $this->render(
            '../../widgets/forms/textwidgconf',
            ['model' => $model, 'wmodel' => $wmodel, 'positions' => $positions]
        );
    }

    public function actionNewsWidgetConfigure($id)
    {
        /**
         * @var WidgetmanModule $module
         * @var Widgetman       $wmodel
         **/
        $module = Yii::$app->getModule('widgetman');
        $wmodel = $this->findModel($id);
        $wmodel->scenario = 'configure';
        $positions = $module->getWidgetPlaces($wmodel->class);
        $wmodel->options = Json::decode($wmodel->options);

        $model = new DynamicModel(
            [
                'title' => isset($wmodel->options['title']) ? $wmodel->options['title'] : '',
                'icon' => isset($wmodel->options['icon']) ? $wmodel->options['icon'] : '',
                'mode' => isset($wmodel->options['mode']) ? $wmodel->options['mode'] : IWidget::MODE_FLAT,
                'type' => isset($wmodel->options['type']) ? $wmodel->options['type'] : IWidget::TYPE_DEFAULT,
                'orient' => $wmodel->options['orient'],
                'total' => $wmodel->options['total'],
                'sorttype' => $wmodel->options['sorttype'],
                'showcover' => $wmodel->options['showcover'],
                'showanons' => $wmodel->options['showanons'],
                'anonslimit' => $wmodel->options['anonslimit'],
                'positions' => $wmodel->position,
                'ord' => $wmodel->ord
            ], []
        );
        $model->addRule(
            ['mode'],
            'in',
            ['range' => array_keys(IWidget::$modes)]
        );
        $model->addRule(
            ['type'],
            'in',
            ['range' => array_keys(IWidget::$types)]
        );
        $model->addRule(['orient'], 'in', ['range' => [NewsWidget::ORIENT_HOR, NewsWidget::ORIENT_VER]]);
        $model->addRule(
            ['sorttype'],
            'in',
            ['range' => [NewsWidget::NEWS_BOTH, NewsWidget::NEWS_LATEST, NewsWidget::NEWS_POPULAR]]
        );
        $model->addRule(['showanons', 'showcover'], 'in', ['range' => [0, 1]]);
        $model->addRule(['title', 'icon'], 'string', ['max' => 200]);
        $model->addRule(['positions'], 'in', ['range' => array_keys($positions)]);
        $model->addRule(['ord', 'total', 'anonslimit'], 'integer');
        $model->addRule(['ord'], 'default', ['value' => 10]);
        $model->addRule(['total'], 'default', ['value' => 5]);
        $model->addRule(['anonslimit'], 'default', ['value' => 0]);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $wmodel->options = $model->getAttributes(null, ['positions', 'ord']);
            $wmodel->content = '';
            $wmodel->position = $model->positions;
            $wmodel->ord = $model->ord;
            if ($wmodel->save()) {
                Yii::$app->session->setFlash('success', 'Настройки модуля сохранены');
                return $this->redirect(['/widgetman/widgetman/index']);
            }
        }

        return $this->render(
            '../../widgets/forms/newswidgconf',
            ['model' => $model, 'wmodel' => $wmodel, 'positions' => $positions]
        );
    }

    public function actionArchiveWidgetConfigure($id)
    {
        /**
         * @var WidgetmanModule $module
         * @var Widgetman       $wmodel
         **/
        $module = Yii::$app->getModule('widgetman');
        $wmodel = $this->findModel($id);
        $wmodel->scenario = 'configure';
        $positions = $module->getWidgetPlaces($wmodel->class);
        $wmodel->options = Json::decode($wmodel->options);

        $model = new DynamicModel(
            [
                'title' => isset($wmodel->options['title']) ? $wmodel->options['title'] : '',
                'icon' => isset($wmodel->options['icon']) ? $wmodel->options['icon'] : '',
                'mode' => isset($wmodel->options['mode']) ? $wmodel->options['mode'] : IWidget::MODE_FLAT,
                'type' => isset($wmodel->options['type']) ? $wmodel->options['type'] : IWidget::TYPE_DEFAULT,
                'searchtype' => $wmodel->options['searchtype'],
                'depth' => $wmodel->options['depth'],
                'positions' => $wmodel->position,
                'ord' => $wmodel->ord
            ], []
        );
        $model->addRule(
            ['mode'],
            'in',
            ['range' => array_keys(IWidget::$modes)]
        );
        $model->addRule(
            ['type'],
            'in',
            ['range' => array_keys(IWidget::$types)]
        );
        $model->addRule(['depth'], 'in', ['range' => [ArchiveWidget::DEPTH_DAYS, ArchiveWidget::DEPTH_MONTH]]);
        $model->addRule(
            ['searchtype'],
            'in',
            ['range' => [ArchiveWidget::SEARCH_NEWS, ArchiveWidget::SEARCH_ARTS]]
        );
        $model->addRule(['title', 'icon'], 'string', ['max' => 200]);
        $model->addRule(['positions'], 'in', ['range' => array_keys($positions)]);
        $model->addRule(['ord'], 'integer');
        $model->addRule(['ord'], 'default', ['value' => 10]);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $wmodel->options = $model->getAttributes(null, ['positions', 'ord']);
            $wmodel->content = '';
            $wmodel->position = $model->positions;
            $wmodel->ord = $model->ord;
            if ($wmodel->save()) {
                Yii::$app->session->setFlash('success', 'Настройки модуля сохранены');
                return $this->redirect(['/widgetman/widgetman/index']);
            }
        }

        return $this->render(
            '../../widgets/forms/archivewidgconf',
            ['model' => $model, 'wmodel' => $wmodel, 'positions' => $positions]
        );
    }

    public function actionShortsWidgetConfigure($id)
    {
        /**
         * @var WidgetmanModule $module
         * @var Widgetman       $wmodel
         **/
        $module = Yii::$app->getModule('widgetman');
        $wmodel = $this->findModel($id);
        $wmodel->scenario = 'configure';
        $positions = $module->getWidgetPlaces($wmodel->class);
        $wmodel->options = Json::decode($wmodel->options);

        $model = new DynamicModel(
            [
                'title' => isset($wmodel->options['title']) ? $wmodel->options['title'] : '',
                'icon' => isset($wmodel->options['icon']) ? $wmodel->options['icon'] : '',
                'mode' => isset($wmodel->options['mode']) ? $wmodel->options['mode'] : IWidget::MODE_FLAT,
                'type' => isset($wmodel->options['type']) ? $wmodel->options['type'] : IWidget::TYPE_DEFAULT,
                'orient' => $wmodel->options['orient'],
                'total' => $wmodel->options['total'],
                'showanons' => $wmodel->options['showanons'],
                'anonslimit' => $wmodel->options['anonslimit'],
                'positions' => $wmodel->position,
                'ord' => $wmodel->ord
            ], []
        );
        $model->addRule(
            ['mode'],
            'in',
            ['range' => array_keys(IWidget::$modes)]
        );
        $model->addRule(
            ['type'],
            'in',
            ['range' => array_keys(IWidget::$types)]
        );
        $model->addRule(['orient'], 'in', ['range' => [NewsWidget::ORIENT_HOR, NewsWidget::ORIENT_VER]]);

        $model->addRule(['showanons'], 'in', ['range' => [0, 1]]);
        $model->addRule(['title', 'icon'], 'string', ['max' => 200]);
        $model->addRule(['positions'], 'in', ['range' => array_keys($positions)]);
        $model->addRule(['ord', 'total', 'anonslimit'], 'integer');
        $model->addRule(['ord'], 'default', ['value' => 10]);
        $model->addRule(['anonslimit'], 'default', ['value' => 0]);
        $model->addRule(['total'], 'default', ['value' => 5]);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $wmodel->options = $model->getAttributes(null, ['positions', 'ord']);
            $wmodel->content = '';
            $wmodel->position = $model->positions;
            $wmodel->ord = $model->ord;
            if ($wmodel->save()) {
                Yii::$app->session->setFlash('success', 'Настройки модуля сохранены');
                return $this->redirect(['/widgetman/widgetman/index']);
            }
        }

        return $this->render(
            '../../widgets/forms/shortswidgconf',
            ['model' => $model, 'wmodel' => $wmodel, 'positions' => $positions]
        );
    }

    public function actionFotoWidgetConfigure($id)
    {
        /**
         * @var WidgetmanModule $module
         * @var Widgetman       $wmodel
         **/
        $module = Yii::$app->getModule('widgetman');
        $wmodel = $this->findModel($id);
        $wmodel->scenario = 'configure';
        $positions = $module->getWidgetPlaces($wmodel->class);
        $wmodel->options = Json::decode($wmodel->options);

        $model = new DynamicModel(
            [
                'title' => isset($wmodel->options['title']) ? $wmodel->options['title'] : '',
                'icon' => isset($wmodel->options['icon']) ? $wmodel->options['icon'] : '',
                'mode' => isset($wmodel->options['mode']) ? $wmodel->options['mode'] : IWidget::MODE_FLAT,
                'type' => isset($wmodel->options['type']) ? $wmodel->options['type'] : IWidget::TYPE_DEFAULT,
                'orient' => $wmodel->options['orient'],
                'total' => $wmodel->options['total'],
                'sorttype' => $wmodel->options['sorttype'],
                'showanons' => $wmodel->options['showanons'],
                'positions' => $wmodel->position,
                'ord' => $wmodel->ord
            ], []
        );
        $model->addRule(
            ['mode'],
            'in',
            ['range' => array_keys(IWidget::$modes)]
        );
        $model->addRule(
            ['type'],
            'in',
            ['range' => array_keys(IWidget::$types)]
        );
        $model->addRule(['orient'], 'in', ['range' => [FotoWidget::ORIENT_HOR, FotoWidget::ORIENT_VER]]);
        $model->addRule(
            ['sorttype'],
            'in',
            ['range' => [FotoWidget::FOTO_RAND, FotoWidget::FOTO_BEST, FotoWidget::FOTO_LATEST, FotoWidget::FOTO_BOTH]]
        );
        $model->addRule(['showanons'], 'in', ['range' => [0, 1]]);
        $model->addRule(['title', 'icon'], 'string', ['max' => 200]);
        $model->addRule(['positions'], 'in', ['range' => array_keys($positions)]);
        $model->addRule(['ord', 'total'], 'integer');
        $model->addRule(['ord'], 'default', ['value' => 10]);
        $model->addRule(['total'], 'default', ['value' => 5]);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $wmodel->options = $model->getAttributes(null, ['positions', 'ord']);
            $wmodel->content = '';
            $wmodel->position = $model->positions;
            $wmodel->ord = $model->ord;
            if ($wmodel->save()) {
                Yii::$app->session->setFlash('success', 'Настройки модуля сохранены');
                return $this->redirect(['/widgetman/widgetman/index']);
            }
        }

        return $this->render(
            '../../widgets/forms/fotowidgconf',
            ['model' => $model, 'wmodel' => $wmodel, 'positions' => $positions]
        );
    }

    public function actionAlbumWidgetConfigure($id)
    {
        /**
         * @var WidgetmanModule $module
         * @var Widgetman       $wmodel
         **/
        $module = Yii::$app->getModule('widgetman');
        $wmodel = $this->findModel($id);
        $wmodel->scenario = 'configure';
        $positions = $module->getWidgetPlaces($wmodel->class);
        $wmodel->options = Json::decode($wmodel->options);

        $model = new DynamicModel(
            [
                'title' => isset($wmodel->options['title']) ? $wmodel->options['title'] : '',
                'icon' => isset($wmodel->options['icon']) ? $wmodel->options['icon'] : '',
                'mode' => isset($wmodel->options['mode']) ? $wmodel->options['mode'] : IWidget::MODE_FLAT,
                'type' => isset($wmodel->options['type']) ? $wmodel->options['type'] : IWidget::TYPE_DEFAULT,
                'orient' => $wmodel->options['orient'],
                'total' => $wmodel->options['total'],
                'sorttype' => $wmodel->options['sorttype'],
                'showanons' => $wmodel->options['showanons'],
                'positions' => $wmodel->position,
                'ord' => $wmodel->ord
            ], []
        );
        $model->addRule(
            ['mode'],
            'in',
            ['range' => array_keys(IWidget::$modes)]
        );
        $model->addRule(
            ['type'],
            'in',
            ['range' => array_keys(IWidget::$types)]
        );
        $model->addRule(['orient'], 'in', ['range' => [FotoWidget::ORIENT_HOR, FotoWidget::ORIENT_VER]]);
        $model->addRule(
            ['sorttype'],
            'in',
            ['range' => [AlbumWidget::SORT_LATEST, AlbumWidget::SORT_RAND]]
        );
        $model->addRule(['showanons'], 'in', ['range' => [0, 1]]);
        $model->addRule(['title', 'icon'], 'string', ['max' => 200]);
        $model->addRule(['positions'], 'in', ['range' => array_keys($positions)]);
        $model->addRule(['ord', 'total'], 'integer');
        $model->addRule(['ord'], 'default', ['value' => 10]);
        $model->addRule(['total'], 'default', ['value' => 5]);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $wmodel->options = $model->getAttributes(null, ['positions', 'ord']);
            $wmodel->content = '';
            $wmodel->position = $model->positions;
            $wmodel->ord = $model->ord;
            if ($wmodel->save()) {
                Yii::$app->session->setFlash('success', 'Настройки модуля сохранены');
                return $this->redirect(['/widgetman/widgetman/index']);
            }
        }

        return $this->render(
            '../../widgets/forms/albwidgconf',
            ['model' => $model, 'wmodel' => $wmodel, 'positions' => $positions]
        );
    }

    public function actionScriptWidgetConfigure($id)
    {
        /**
         * @var WidgetmanModule $module
         * @var Widgetman       $wmodel
         **/
        $module = Yii::$app->getModule('widgetman');
        $wmodel = $this->findModel($id);
        $wmodel->scenario = 'configure';
        $positions = $module->getWidgetPlaces($wmodel->class);
        $wmodel->options = Json::decode($wmodel->options);

        $model = new DynamicModel(
            [
                'text' => $wmodel->options['text'],
                'positions' => $wmodel->position,
                'ord' => $wmodel->ord
            ], []
        );
        $model->addRule(['text'], 'string', ['min' => 3, 'max' => 10000]);
        $model->addRule(['positions'], 'in', ['range' => array_keys($positions)]);
        $model->addRule(['ord'], 'integer');
        $model->addRule(['ord'], 'default', ['value' => 10]);


        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $wmodel->options = $model->getAttributes(null, ['positions', 'ord']);
            $wmodel->content = '';
            $wmodel->position = $model->positions;
            $wmodel->ord = $model->ord;
            if ($wmodel->save()) {
                Yii::$app->session->setFlash('success', 'Настройки модуля сохранены');
                return $this->redirect(['/widgetman/widgetman/index']);
            }
        }

        return $this->render(
            '../../widgets/forms/scriptwidgconf',
            ['model' => $model, 'wmodel' => $wmodel, 'positions' => $positions]
        );
    }

    public function actionCatWidgetConfigure($id)
    {
        /**
         * @var WidgetmanModule $module
         * @var Widgetman       $wmodel
         **/
        $module = Yii::$app->getModule('widgetman');
        $wmodel = $this->findModel($id);
        $wmodel->scenario = 'configure';
        $positions = $module->getWidgetPlaces($wmodel->class);
        $wmodel->options = Json::decode($wmodel->options);

        $model = new DynamicModel(
            [
                'title' => isset($wmodel->options['title']) ? $wmodel->options['title'] : '',
                'icon' => isset($wmodel->options['icon']) ? $wmodel->options['icon'] : '',
                'type' => isset($wmodel->options['type']) ? $wmodel->options['type'] : IWidget::TYPE_DEFAULT,
                'showcount' => $wmodel->options['showcount'],
                'showempty' => $wmodel->options['showempty'],
                'positions' => $wmodel->position,
                'ord' => $wmodel->ord
            ], []
        );


        $model->addRule(
            ['type'],
            'in',
            ['range' => array_keys(IWidget::$types)]
        );
        $model->addRule(['showcount', 'showempty'], 'in', ['range' => [0, 1]]);
        $model->addRule(['title', 'icon'], 'string', ['max' => 200]);
        $model->addRule(['positions'], 'in', ['range' => array_keys($positions)]);
        $model->addRule(['ord'], 'integer');
        $model->addRule(['ord'], 'default', ['value' => 10]);


        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $wmodel->options = $model->getAttributes(null, ['positions', 'ord']);
            $wmodel->content = '';
            $wmodel->position = $model->positions;
            $wmodel->ord = $model->ord;
            if ($wmodel->save()) {
                Yii::$app->session->setFlash('success', 'Настройки модуля сохранены');
                return $this->redirect(['/widgetman/widgetman/index']);
            }
        }

        return $this->render(
            '../../widgets/forms/catwidgconf',
            ['model' => $model, 'wmodel' => $wmodel, 'positions' => $positions]
        );
    }

    public function actionSliderWidgetConfigure($id)
    {
        /**
         * @var WidgetmanModule $module
         * @var Widgetman       $wmodel
         **/
        $module = Yii::$app->getModule('widgetman');
        $wmodel = $this->findModel($id);
        $wmodel->scenario = 'configure';
        $positions = $module->getWidgetPlaces($wmodel->class);
        $wmodel->options = Json::decode($wmodel->options);

        $model = new DynamicModel(
            [
                'width' => $wmodel->options['width'],
                'height' => $wmodel->options['height'],
                'startslide' => $wmodel->options['startslide'],
                'album' => $wmodel->options['album'],
                'limit' => $wmodel->options['limit'],
                'sorttype' => $wmodel->options['sorttype'],
                'positions' => $wmodel->position,
                'ord' => $wmodel->ord
            ], []
        );
        $model->addRule(
            ['sorttype'],
            'in',
            ['range' => [SliderWidget::SORT_BYBEST, SliderWidget::SORT_BYNEW, SliderWidget::SORT_BYRAND]]
        );
        $model->addRule(['positions'], 'in', ['range' => array_keys($positions)]);
        $model->addRule(['startslide'], 'in', ['range' => [0, 1]]);
        $model->addRule(['ord', 'limit', 'album'], 'integer');
        $model->addRule(['width', 'height'], 'string');
        $model->addRule(['ord'], 'default', ['value' => 10]);
        $model->addRule(['limit'], 'default', ['value' => 7]);
        $model->addRule(['album'], 'default', ['value' => 0]);
        $model->addRule(['height'], 'default', ['value' => '400px']);
        $model->addRule(['width'], 'default', ['value' => '100%']);

        $model->width = '100%';
        $model->height = '400px';
        $model->limit = 7;
        $model->sorttype = SliderWidget::SORT_BYRAND;


        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $wmodel->options = $model->getAttributes(null, ['positions', 'ord']);
            $wmodel->content = '';
            $wmodel->position = $model->positions;
            $wmodel->ord = $model->ord;
            if ($wmodel->save()) {
                Yii::$app->session->setFlash('success', 'Настройки модуля сохранены');
                return $this->redirect(['/widgetman/widgetman/index']);
            }
        }

        return $this->render(
            '../../widgets/forms/sliderwidgconf',
            ['model' => $model, 'wmodel' => $wmodel, 'positions' => $positions]
        );
    }

    public function actionTagWidgetConfigure($id)
    {
        /**
         * @var WidgetmanModule $module
         * @var Widgetman       $wmodel
         **/
        $module = Yii::$app->getModule('widgetman');
        $wmodel = $this->findModel($id);
        $wmodel->scenario = 'configure';
        $positions = $module->getWidgetPlaces($wmodel->class);
        $wmodel->options = Json::decode($wmodel->options);

        $model = new DynamicModel(
            [
                'title' => isset($wmodel->options['title']) ? $wmodel->options['title'] : '',
                'icon' => isset($wmodel->options['icon']) ? $wmodel->options['icon'] : '',
                'mode' => isset($wmodel->options['mode']) ? $wmodel->options['mode'] : IWidget::MODE_FLAT,
                'type' => isset($wmodel->options['type']) ? $wmodel->options['type'] : IWidget::TYPE_DEFAULT,
                'sorttype' => $wmodel->options['sorttype'],
                'viewtype' => $wmodel->options['viewtype'],
                'limit' => $wmodel->options['limit'],
                'positions' => $wmodel->position,
                'ord' => $wmodel->ord
            ], []
        );
        $model->addRule(
            ['mode'],
            'in',
            ['range' => array_keys(IWidget::$modes)]
        );
        $model->addRule(
            ['type'],
            'in',
            ['range' => array_keys(IWidget::$types)]
        );
        $model->addRule(
            ['sorttype'],
            'in',
            ['range' => [TagWidget::SORT_ALPHA, TagWidget::SORT_FREEQ]]
        );
        $model->addRule(
            ['viewtype'],
            'in',
            ['range' => [TagWidget::VIEW_CLOUD, TagWidget::VIEW_LIST]]
        );
        $model->addRule(['title', 'icon'], 'string', ['max' => 200]);
        $model->addRule(['positions'], 'in', ['range' => array_keys($positions)]);
        $model->addRule(['ord', 'limit'], 'integer');
        $model->addRule(['ord'], 'default', ['value' => 10]);
        $model->addRule(['limit'], 'default', ['value' => 20]);

        $model->limit = 20;

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $wmodel->options = $model->getAttributes(null, ['positions', 'ord']);
            $wmodel->content = '';
            $wmodel->position = $model->positions;
            $wmodel->ord = $model->ord;
            if ($wmodel->save()) {
                Yii::$app->session->setFlash('success', 'Настройки модуля сохранены');
                return $this->redirect(['/widgetman/widgetman/index']);
            }
        }

        return $this->render(
            '../../widgets/forms/tagwidgconf',
            ['model' => $model, 'wmodel' => $wmodel, 'positions' => $positions]
        );
    }

    public function findModel($id)
    {
        if (($model = Widgetman::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
