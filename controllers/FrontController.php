<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 18.08.14
 * Time: 2:01
 */

namespace insolita\content\controllers;


use insolita\content\models\Article;
use insolita\content\models\Category;
use insolita\content\models\Feedback;
use insolita\content\models\News;
use insolita\content\models\Page;
use insolita\content\models\Shorts;
use insolita\content\models\Tags;
use insolita\content\search\ArticleSearch;
use insolita\content\search\NewsSearch;
use insolita\content\search\ShortsSearch;
use insolita\content\widgets\SearchWidget;
use insolita\things\helpers\Helper;
use yii\base\Event;
use yii\bootstrap\ActiveForm;
use yii\caching\DbDependency;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

class FrontController extends Controller
{

    const EVENT_VIEW_NEWS = 'newsview';
    const EVENT_VIEW_ARTICLE = 'artview';
    const EVENT_VIEW_PAGE = 'pageview';
    const EVENT_USER_SEARCH = 'search';

    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ]
        ];
    }

    public function beforeAction($action)
    {
        Event::on(self::className(), self::EVENT_VIEW_NEWS, ['insolita\content\models\News', 'onView']);
        Event::on(self::className(), self::EVENT_VIEW_ARTICLE, ['insolita\content\models\Article', 'onView']);
        Event::on(self::className(), self::EVENT_VIEW_PAGE, ['insolita\content\models\Page', 'onView']);
        return parent::beforeAction($action);
    }

    public function actionNewslist()
    {
        $this->layout = '@app/views/layouts/' . \Yii::$app->params['newsLayout'];
        $news = new NewsSearch();
        $cd = new DbDependency();
        $cd->sql = 'SELECT MAX(updated) FROM ' . News::tableName();
        $cd->reusable = false;
        $dp = \Yii::$app->db->cache(
            function ($db) use ($news) { return $news->frontsearch(\Yii::$app->request->getQueryParams()); },
            3600,
            $cd
        );
        $metadata = $news->metaGenerate($dp->getPagination()->page);
        return $this->render(
            'newslist',
            [
                'dataProvider' => $dp,
                'searchModel' => $news,
                'metadata' => $metadata
            ]
        );
    }
    public function actionNovosti()
    {
        $this->layout = '@app/views/layouts/' . \Yii::$app->params['newsLayout'];
        $news = new ShortsSearch();
        $cd = new DbDependency();
        $cd->sql = 'SELECT MAX(updated) FROM ' . Shorts::tableName();
        $cd->reusable = false;
        $dp = \Yii::$app->db->cache(
            function ($db) use ($news) { return $news->frontsearch(\Yii::$app->request->getQueryParams()); },
            3600,
            $cd
        );
        $metadata = $news->metaGenerate($dp->getPagination()->page);
        return $this->render(
            'novosti',
            [
                'dataProvider' => $dp,
                'searchModel' => $news,
                'metadata' => $metadata
            ]
        );
    }

    public function actionNews($slug)
    {
        $this->layout = '@app/views/layouts/' . \Yii::$app->params['newsLayout'];
        $news = News::find()->where(
            'slug=:s AND active=:a AND publishto<=:d',
            [':s' => $slug, ':a' => 1, ':d' => date('Y-m-d H:i', time())]
        )->one();
        if (!$news) {
            throw new HttpException(404, 'Запрошенная cтраница не существует');
        }

        $this->trigger(self::EVENT_VIEW_NEWS, new Event(['sender' => $news]));
        return $this->render('news_full', ['model' => $news]);
    }

    public function actionArticle($slug)
    {
        $this->layout = '@app/views/layouts/' . \Yii::$app->params['artLayout'];
        $article = Article::find()->where(
            'slug=:s AND active=:a AND publishto<=:d',
            [':s' => $slug, ':a' => 1, ':d' => date('Y-m-d H:i', time())]
        )->one();
        if (!$article) {
            throw new HttpException(404, 'Запрошенная cтраница не существует');
        }
        $this->trigger(self::EVENT_VIEW_ARTICLE, new Event(['sender' => $article]));
        return $this->render('art_full', ['model' => $article]);
    }

    public function actionArticlesBydate($date){
        $this->layout = '@app/views/layouts/' . \Yii::$app->params['artLayout'];
        $arts = new ArticleSearch();
        $arts->scenario='datasearch';
        $arts->publishto=$date;
        $cd = new DbDependency();
        $cd->sql = 'SELECT MAX(updated) FROM ' . Article::tableName();
        $cd->reusable = true;
        $dp = \Yii::$app->db->cache(
            function ($db) use ($arts) {
                return $arts->datasearch(
                    \Yii::$app->request->getQueryParams()
                );
            },
            3600,
            $cd
        );
        $metadata = $arts->metaGenerateByDate($date,$dp->getPagination()->page);
         return $this->render(
            'artlist_bd',
            [
                'dataProvider' => $dp,
                'searchModel' => $arts,
                'data'=>$date,
                'metadata' => $metadata
            ]
        );
    }
    public function actionNewsBydate($date){
        $this->layout = '@app/views/layouts/' . \Yii::$app->params['newsLayout'];
        $news = new NewsSearch();
        $news->publishto=$date;
        $cd = new DbDependency();
        $cd->sql = 'SELECT MAX(updated) FROM ' . News::tableName();
        $cd->reusable = false;
        $dp = \Yii::$app->db->cache(
            function ($db) use ($news) { return $news->datasearch(\Yii::$app->request->getQueryParams()); },
            3600,
            $cd
        );
        $metadata = $news->metaGenerateByDate($date,$dp->getPagination()->page);
        return $this->render(
            'newslist_bd',
            [
                'dataProvider' => $dp,
                'searchModel' => $news,
                'data'=>$date,
                'metadata' => $metadata
            ]
        );
    }

    public function actionPage($slug)
    {
        $this->layout = '@app/views/layouts/' . \Yii::$app->params['pageLayout'];
        $page = Page::findOne(['slug' => $slug]);
        if (!$page) {
            throw new HttpException(404, 'Запрошенная cтраница не существует');
        }

        $this->trigger(self::EVENT_VIEW_PAGE, new Event(['sender' => $page]));
        return $this->render('page', ['page' => $page]);
    }

    public function actionCategory($slug)
    {
        $this->layout = '@app/views/layouts/' . \Yii::$app->params['artLayout'];
        $arts = new ArticleSearch();
        $cat = Category::findOne(['slug' => $slug]);
        if (!$cat) {
            throw new HttpException(404, 'Запрошенная cтраница не существует');
        }
        $cd = new DbDependency();
        $cd->sql = 'SELECT MAX(updated) FROM ' . Article::tableName();
        $cd->reusable = true;

        $dp = \Yii::$app->db->cache(
            function ($db) use ($arts, $cat) {
                return $arts->frontsearch(
                    $cat->id,
                    \Yii::$app->request->getQueryParams()
                );
            },
            3600,
            $cd
        );
        return $this->render(
            'artlist',
            [
                'dataProvider' => $dp,
                'searchModel' => $arts,
                'category' => $cat
            ]
        );
    }

    public function actionShowtag($name)
    {
        $this->layout = '@app/views/layouts/' . \Yii::$app->params['siteLayout'];
        $tag = Tags::findTag($name);
        $newsmodel = new NewsSearch();
        $artmodel = new ArticleSearch();
        $cd = new DbDependency();
        $cd->sql = 'SELECT MAX(updated) FROM ' . News::tableName();
        $cd->reusable = true;
        $newsdp = \Yii::$app->db->cache(
            function ($db) use ($newsmodel, $name) {
                return $newsmodel->tagsearch(
                    $name,
                    \Yii::$app->request->getQueryParams()
                );
            },
            3600,
            $cd
        );
        $cd2 = new DbDependency();
        $cd2->sql = 'SELECT MAX(updated) FROM ' . Article::tableName();
        $cd2->reusable = true;
        $artsdp = \Yii::$app->db->cache(
            function ($db) use ($artmodel, $name) {
                return $artmodel->tagsearch(
                    $name,
                    \Yii::$app->request->getQueryParams()
                );
            },
            3600,
            $cd
        );

        return $this->render(
            'showtag',
            [
                'newsdp' => $newsdp,
                'artsdp' => $artsdp,
                'tag' => $tag,
                'newsmodel' => $newsmodel,
                'artmodel' => $artmodel
            ]
        );
    }

    public function actionSearch()
    {
        $this->layout = '@app/views/layouts/' . \Yii::$app->params['siteLayout'];
        $query = \Yii::$app->request->post('query', null);
        $type = \Yii::$app->request->post('type', 'content');
        $query_strip = strip_tags($query);
        $newsmodel = new NewsSearch();
        $newsmodel->scenario = 'usersearch';
        $newsmodel->search_query = $query_strip;
        $artmodel = new ArticleSearch();
        $artmodel->scenario = 'usersearch';
        $artmodel->search_query = $query_strip;
        $newsdp = null;
        $artsdp = null;
        $this->trigger(self::EVENT_USER_SEARCH, new Event(['data' => [$query], 'sender' => $this]));
        if (SearchWidget::SEARCH_TYPE_CONTENT or $type == SearchWidget::SEARCH_TYPE_NEWS) {
            if (!$newsmodel->validate()) {
                return $this->render(
                    'searchres',
                    [
                        'error' => Html::errorSummary($newsmodel),
                        'newsdp' => $newsdp,
                        'artsdp' => $artsdp,
                        'query' => $query_strip,
                        'newsmodel' => $newsmodel,
                        'artmodel' => $artmodel
                    ]
                );
            }
            $newsdp = \Yii::$app->db->cache(
                function ($db) use ($newsmodel, $query_strip) {
                    return $newsmodel->usersearch(
                        \Yii::$app->request->getQueryParams()
                    );
                },
                3600
            );
        }
        if (SearchWidget::SEARCH_TYPE_CONTENT or $type == SearchWidget::SEARCH_TYPE_ARTICLES) {
            if (!$artmodel->validate()) {
                return $this->render(
                    'searchres',
                    [
                        'error' => Html::errorSummary($artmodel),
                        'newsdp' => $newsdp,
                        'artsdp' => $artsdp,
                        'query' => $query_strip,
                        'newsmodel' => $newsmodel,
                        'artmodel' => $artmodel
                    ]
                );
            }
            $artsdp = \Yii::$app->db->cache(
                function ($db) use ($artmodel, $query_strip) {
                    return $artmodel->usersearch(
                        \Yii::$app->request->getQueryParams()
                    );
                },
                3600
            );
        }
        return $this->render(
            'searchres',
            [
                'error' => false,
                'newsdp' => $newsdp,
                'artsdp' => $artsdp,
                'query' => $query_strip,
                'newsmodel' => $newsmodel,
                'artmodel' => $artmodel
            ]
        );
    }

    public function actionFeedback()
    {
        $this->layout = '@app/views/layouts/' . \Yii::$app->params['pageLayout'];
        $model = new Feedback();
        $model->scenario = 'create';
        if (\Yii::$app->request->isAjax && $model->load(\Yii::$app->request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if (\Yii::$app->request->isPost) {
            if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
                if ($model->save(false)) {
                    \Yii::$app->session->setFlash(
                        'success',
                        'Спасибо! Ваше сообщение отправлено, скоро мы с вами свяжемся!'
                    );
                    return $this->redirect(['feedback']);
                }

            }
        }
        return $this->render('feedback', ['model' => $model]);
    }

} 