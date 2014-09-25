<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 24.08.14
 * Time: 18:17
 */

namespace insolita\content\widgets;


use insolita\content\search\NewsSearch;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;

class NewsWidget extends TextWidget
{
    const ORIENT_HOR = 'hor';
    const ORIENT_VER = 'ver';

    const NEWS_LATEST = 'latest';
    const NEWS_POPULAR = 'popular';
    const NEWS_BOTH = 'both';

    /**@var string $orient Вертикальная\Горизонтальная ориентация виджета */
    public $orient = self::ORIENT_HOR;

    /**@var integer $total Кол-во объектов */
    public $total = 5;

    /**@var string $sorttype Показывать последние\самыве просматривые или и то и то */
    public $sorttype = self::NEWS_BOTH;

    /**@var bool $showcover Показывать обложки? */
    public $showcover = true;

    /**@var bool $showanons Показывать анонс?/Только заголовок */
    public $showanons = true;

    /**@var integer $anonslimit До скольки символов обрезать анонс (если он будет показан) */
    public $anonslimit = 200;

    public function run()
    {
        $this->text = $this->renderText();
        return parent::run();
    }

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
        $latest = NewsSearch::find()->published()->joinWith('cover')->orderBy(['publishto' => SORT_DESC])->limit($this->total)
            ->all();
        return $this->renderList($latest);
    }

    public function getPopular()
    {
        $popular = NewsSearch::find()->published()->joinWith('cover')->orderBy(['views' => SORT_DESC])->limit($this->total)
            ->all();
        return $this->renderList($popular);
    }

    public function renderList($data)
    {
        $content = '';
        foreach ($data as $news) {
            /**@var NewsSearch $news * */
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
            $content .= Html::a(
                '<h4 class="text-centered">' . $news->name . '</h4>',
                \Yii::$app->params['siteurl'] . Url::toRoute(['/content/front/news', 'slug' => $news->slug])
            );
            if ($this->showcover && $this->showanons) {
                $content .= "<p>" . $news->showCover('imgingrid pull-left') . $news->anons . "</p>";
            } elseif ($this->showcover) {
                $content .= "<p style='text-align:center;'>" . $news->showCover('imgingrid') . "</p>";
            } elseif ($this->showanons) {
                $content .= "<p>" . $news->anons . "...</p>";
            }
            $content .= '<p class="pull-right">' . Html::a(
                    "Далее >",
                    \Yii::$app->params['siteurl'] . Url::toRoute(['/content/front/news', 'slug' => $news->slug])
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
        return 'Новости';
    }

    /**
     * @method getActionRoute()
     * @return mixed (string|bool) route to you contoller where you render form with settings and save it with WidgetizerModel or false if
     * your widget don`t needed in settings
     * @example '/admin/widgets/search-widget'  - first slash required!!!
     */
    public function getActionRoute()
    {
        return '/content/widget/news-widget-configure';
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