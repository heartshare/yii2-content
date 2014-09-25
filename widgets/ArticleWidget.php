<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 24.08.14
 * Time: 18:18
 */

namespace insolita\content\widgets;


use insolita\content\models\Article;
use yii\bootstrap\Tabs;
use yii\caching\DbDependency;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;

class ArticleWidget extends NewsWidget
{

    public $category = 0;

    public function renderText()
    {
        if ($this->sorttype == self::NEWS_BOTH) {
            return Tabs::widget(
                [
                    'items' => [
                        ['label' => 'Новое', 'content' => $this->getLatest()],
                        ['label' => 'Популярное', 'content' => $this->getPopular()]
                    ]
                ]
            );
        } elseif ($this->sorttype == self::NEWS_LATEST) {
            return $this->getLatest();
        } else {
            return $this->getPopular();
        }
    }

    public function getLatest()
    {
        $db=\Yii::$app->getDb();
        $dep=new DbDependency();
        $dep->sql='SELECT MAX(updated) FROM {{%news}}';
        $dep->reusable=true;
        $dep->db=$db;

        $latest = Article::find()->published()->joinWith(['cover', 'cat'])->orderBy(['publishto' => SORT_DESC])->limit(
            $this->total
        );
        if ($this->category) {
            $latest->andWhere(['cat_id' => $this->category]);
        }
        $res=$db->cache(function($db) use($latest){return $latest->all();},86400,$dep);
        return $this->renderList($res);
    }


    public function getPopular()
    {
        $popular = Article::find()->published()->joinWith(['cover', 'cat'])->orderBy(['views' => SORT_DESC])->limit(
            $this->total
        );
        if ($this->category) {
            $popular->andWhere(['cat_id' => $this->category]);
        }
        return $this->renderList($popular->all());
    }

    public function renderList($data)
    {
        $content = '';
        foreach ($data as $news) {
            /**@var Article $news * */
            if ($this->showanons) {
                $p = new HtmlPurifier();
                $puryConf = [
                    'AutoFormat.Linkify' => false,
                    'AutoFormat.AutoParagraph' => false,
                    'HTML.Allowed' => ''
                ];
                $news->anons = $p->process($news->anons, $puryConf);
                if ($this->anonslimit > 0) {
                    $news->anons = mb_substr($news->anons, 0, $this->anonslimit, 'UTF-8');
                }
            }
            $content .= ($this->orient == self::ORIENT_HOR ? '<div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">'
                : '<div>');
            $content .= '<h4>' . Html::a(
                    $news->name,
                    Url::toRoute(['/content/front/article', 'slug' => $news->slug, 'category' => $news->cat->name],true)
                ) . '</h4>';
            if ($this->showcover && $this->showanons) {
                $content .= "<p>" . $news->showCover('imgingrid pull-left') . $news->anons . "</p>";
            } elseif ($this->showcover) {
                $content .= "<p style='text-align:center;'>" . $news->showCover('imgingrid') . "</p>";
            } elseif ($this->showanons) {
                $content .= "<p>" . $news->anons . "...</p>";
            }
            $content .= '<p class="pull-right">' . Html::a(
                    "Далее >",
                    Url::toRoute(['/content/front/article', 'slug' => $news->slug, 'category' => $news->cat->name],true)
                 ) . '</p><div class="clearfix"></div></div>';
        }
        return '<div class="text-padded"><div class="row">' . $content . '</div></div>';
    }

    /**
     * @method getFriendlyName()
     * @return string Friendly name of your widget for user interface
     **/
    public function getFriendlyName()
    {
        return 'Статьи';
    }

    /**
     * @method getActionRoute()
     * @return mixed (string|bool) route to you contoller where you render form with settings and save it with WidgetizerModel or false if
     * your widget don`t needed in settings
     * @example '/admin/widgets/search-widget'  - first slash required!!!
     */
    public function getActionRoute()
    {
        return '/content/widget/art-widget-configure';
    }

    /**
     * @method getIsScript()
     * @return bool - if widget for script places, return true, if for content - return false
     *
     */
    public function getIsScript()
    {
        return false;
    }

    public function allowCache()
    {
        return true;
    }
} 