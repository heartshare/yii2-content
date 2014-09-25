<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 24.08.14
 * Time: 18:17
 */

namespace insolita\content\widgets;


use insolita\content\models\News;
use insolita\gallery\models\Foto;
use insolita\things\helpers\Helper;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;

class FotoWidget extends TextWidget
{
    const ORIENT_HOR = 'hor';
    const ORIENT_VER = 'ver';

    const FOTO_LATEST = 'latest';
    const FOTO_BEST = 'best';
    const FOTO_RAND = 'rand';
    const FOTO_BOTH = 'both';

    /**@var string $orient Вертикальная\Горизонтальная ориентация виджета */
    public $orient = self::ORIENT_HOR;

    /**@var integer $total Кол-во объектов */
    public $total = 5;

    /**@var string $sorttype Показывать последние\самыве просматривые или и то и то */
    public $sorttype = self::FOTO_LATEST;

    /**@var bool $showanons Показывать анонс?/Только заголовок */
    public $showanons = false;


    public function run()
    {
        $this->text = $this->renderText();
        return parent::run();
    }

    public function renderText()
    {
        if ($this->sorttype == self::FOTO_BOTH) {
            return Tabs::widget(
                [
                    'items' => [
                        ['label' => 'Новое', 'content' => $this->getLatest()],
                        ['label' => 'Лучшее', 'content' => $this->getBest()]
                    ]
                ]
            );
        } elseif ($this->sorttype == self::FOTO_LATEST) {
            return $this->getLatest();
        } elseif ($this->sorttype == self::FOTO_RAND) {
            return $this->getRandom();
        } else {
            return $this->getBest();
        }
    }

    public function getLatest()
    {
        $latest = Foto::find()->where(['istemp' => 0])->orderBy(['created' => SORT_DESC])->limit($this->total)->all();
        return ($latest)?$this->renderList($latest):'';
    }

    public function getBest()
    {
        $best = Foto::find()->where(['istemp' => 0])->andWhere(['isbest' => 1])->orderBy(['updated' => SORT_DESC])
            ->limit($this->total)->all();
        return ($best)?$this->renderList($best):'';
    }

    public function getRandom()
    {
        $rand = Foto::find()->where(['istemp' => 0])->orderBy('RAND()')->limit($this->total)->all();
        return $rand?$this->renderList($rand):'';
    }

    public function renderList($data)
    {
        $content = '';
        $urlman=\Yii::$app->id !== 'app-backend'?\Yii::$app->urlManager:\Yii::$app->fronturlManager;
        foreach ($data as $foto) {
            /**@var Foto $foto * */
             $content .= ($this->orient == self::ORIENT_HOR ? '<div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">'
                : '<div>');
            $content .= Html::a(
                '<h4 class="text-centered">' . $foto->name_ru . '</h4>',
                $urlman->createAbsoluteUrl(['/gallery/front/pic', 'id' => $foto->id])
            );
            if ($this->showanons) {
                $content .= "<p>" . Html::a(
                        $foto->showCover('imgingrid pull-left'),
                        $urlman->createAbsoluteUrl(['/gallery/front/pic', 'id' => $foto->id])
                    ) . $foto->desc_ru . "</p>";
            } else{
                $content .= "<p class='text-centered'>" . Html::a(
                        $foto->showCover('img-thumbnail'),
                        $urlman->createAbsoluteUrl(['/gallery/front/pic', 'id' => $foto->id])
                    ). "</p>";
            }
            $content .= '<div class="clearfix"></div></div>';
        }
        return '<div class="text-padded"><div class="row">' . $content . '</div></div>';
    }

    /**
     * @method getFriendlyName()
     * @return string Friendly name of your widget for user interface
     **/
    public function getFriendlyName()
    {
        return 'Фото';
    }

    /**
     * @method getActionRoute()
     * @return mixed (string|bool) route to you contoller where you render form with settings and save it with WidgetizerModel or false if
     * your widget don`t needed in settings
     * @example '/admin/widgets/search-widget'  - first slash required!!!
     */
    public function getActionRoute()
    {
        return '/content/widget/foto-widget-configure';
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