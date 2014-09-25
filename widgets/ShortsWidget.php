<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 24.08.14
 * Time: 18:17
 */

namespace insolita\content\widgets;


use insolita\content\search\NewsSearch;
use insolita\content\search\ShortsSearch;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;

class ShortsWidget extends TextWidget
{
    const ORIENT_HOR = 'hor';
    const ORIENT_VER = 'ver';


    /**@var string $orient Вертикальная\Горизонтальная ориентация виджета */
    public $orient = self::ORIENT_HOR;

    /**@var integer $total Кол-во объектов */
    public $total = 5;


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
        return $this->getLatest();
    }

    public function getLatest()
    {
        $latest = ShortsSearch::find()->active()->orderBy(['created' => SORT_DESC])->limit($this->total)
            ->all();
        return $this->renderList($latest);
    }


    public function renderList($data)
    {
        $content = '';
        foreach ($data as $news) {
            /**@var ShortsSearch $news * */
            if ($this->showanons) {
                $p = new HtmlPurifier();
                $puryConf = [
                    'AutoFormat.Linkify' => false,
                    'AutoFormat.AutoParagraph' => false,
                    'HTML.Allowed' => ''
                ];
                $news->text = $p->process($news->text, $puryConf);
                if ($this->anonslimit > 0) {
                    $news->text = mb_substr($news->text, 0, $this->anonslimit, 'UTF-8');
                }
            }
            $content .= ($this->orient == self::ORIENT_HOR ? '<div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">'
                : '<div>');
            $content .='<h4 class="text-centered">' . $news->name . '</h4>';
             if ($this->showanons) {
                $content .= "<p>" . $news->text .($this->anonslimit > 0?'...':''). "</p>";
            }
            $content.='<div class="clearfix"></div></div>';

        }
        $content .= '<p class="text-centered">' . Html::a(
                "Смотреть все >",
                \Yii::$app->params['siteurl'] . Url::toRoute(['/content/front/novosti'])
            ) . '</p>';
        return '<div class="text-padded"><div class="row">' . $content . '</div></div>';
    }

    /**
     * @method getFriendlyName()
     * @return string Friendly name of your widget for user interface
     **/
    public function getFriendlyName()
    {
        return 'Мини-Новости';
    }

    /**
     * @method getActionRoute()
     * @return mixed (string|bool) route to you contoller where you render form with settings and save it with WidgetizerModel or false if
     * your widget don`t needed in settings
     * @example '/admin/widgets/search-widget'  - first slash required!!!
     */
    public function getActionRoute()
    {
        return '/content/widget/shorts-widget-configure';
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