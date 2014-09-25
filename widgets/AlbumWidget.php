<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 24.08.14
 * Time: 18:17
 */

namespace insolita\content\widgets;


use insolita\gallery\models\Album;
use yii\helpers\Html;

class AlbumWidget extends TextWidget
{
    const ORIENT_HOR = 'hor';
    const ORIENT_VER = 'ver';

    const SORT_LATEST = 'latest';
    const SORT_RAND = 'rand';

    /**@var string $orient Вертикальная\Горизонтальная ориентация виджета */
    public $orient = self::ORIENT_HOR;

    /**@var integer $total Кол-во объектов */
    public $total = 5;

    /**@var string $sorttype Показывать последние\самыве просматривые или и то и то */
    public $sorttype = self::SORT_LATEST;

    /**@var bool $showanons Показывать анонс?/Только заголовок */
    public $showanons = false;


    public function run()
    {
        $this->text = $this->renderText();
        return parent::run();
    }

    public function renderText()
    {
        if ($this->sorttype == self::SORT_LATEST) {
            return $this->getLatest();
        } else {
            return $this->getRandom();
        }
    }

    public function getLatest()
    {
        $latest = Album::find()->where(['active' => 1])->joinWith(['cover'])->orderBy(['created' => SORT_DESC])->limit($this->total)->all();
        return ($latest)?$this->renderList($latest):'';
    }

    public function getRandom()
    {
        $rand = Album::find()->where(['active' => 1])->joinWith('cover')->orderBy('RAND()')->limit($this->total)->all();
        return $rand?$this->renderList($rand):'';
    }

    public function renderList($data)
    {
        $content = '';
        foreach ($data as $album) {
            /**@var Album $album * */
             $content .= ($this->orient == self::ORIENT_HOR ? '<div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">'
                : '<div>');
            $content .= Html::a(
                '<h4 class="text-centered">' . $album->name . '</h4>',
                $album->getUrl()
            );
            if ($this->showanons) {
                $content .= "<p>" . Html::a(
                        $album->cover->showCover('imgingrid pull-left'),
                        $album->getUrl()
                    ) . $album->comment. "</p>";
            } else{
                $content .= "<p class='text-centered'>" . Html::a(
                        $album->cover->showCover('img-thumbnail'),
                        $album->getUrl()
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
        return 'Альбомы';
    }

    /**
     * @method getActionRoute()
     * @return mixed (string|bool) route to you contoller where you render form with settings and save it with WidgetizerModel or false if
     * your widget don`t needed in settings
     * @example '/admin/widgets/search-widget'  - first slash required!!!
     */
    public function getActionRoute()
    {
        return '/content/widget/album-widget-configure';
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