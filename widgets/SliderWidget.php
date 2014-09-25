<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 24.08.14
 * Time: 18:20
 */

namespace insolita\content\widgets;


use dosamigos\gallery\Carousel;
use insolita\gallery\GalleryModule;
use insolita\gallery\models\Foto;
use insolita\widgetman\WidgetizerInterface;
use insolita\jgallery\JGalleryWidget;
use yii\bootstrap\Widget;
use yii\caching\DbDependency;
use yii\helpers\Html;

class SliderWidget extends Widget implements WidgetizerInterface
{
    const SORT_BYNEW = 'bynew';
    const SORT_BYRAND = 'rand';
    const SORT_BYBEST = 'best';


    public $album = 0;

    public $limit = 7;

    public $sorttype = self::SORT_BYRAND;

    public $height = '400px';

    public $width = '100%';

    public $startslide = false;

    public function init()
    {
        parent::init();

    }

    public function run()
    {
        $this->runScript();
        return $this->renderContent();
    }

    public function renderContent()
    {
        $module = GalleryModule::getInstance();
        $fotosquery = Foto::find()->limit($this->limit)->where(['istemp' => 0]);
        if ($this->sorttype == self::SORT_BYNEW) {
            $fotosquery->orderBy(['updated' => SORT_DESC]);
        } elseif ($this->sorttype == self::SORT_BYBEST) {
            $fotosquery->orderBy(['id' => SORT_DESC]);
            $fotosquery->andWhere(['isbest' => 1]);
        } else {
            $fotosquery->orderBy('RAND()');
        }

        if ($this->album) {
            $fotosquery->andWhere(['album_id' => $this->album]);
        }
        $db=\Yii::$app->db;
        $cd=new DbDependency();
        $cd->sql='SELECT MAX(updated) FROM {{%foto}}';
        $cd->reusable=true;
        $cd->db=$db;

        $fotos = $db->cache(function($db) use($fotosquery){return $fotosquery->all($db);},86400,$cd);
        $items = '<div id="gallery_sl">';
        foreach ($fotos as $foto) {
            $items .= Html::img(
                $module->getUrl($foto->album_id, GalleryModule::SIZE_BIG, $foto->file),
                [
                    'alt' => Html::tag('b', $foto->name_ru . '/' . $foto->name_en)
                ]
            );

        }
        $items .= '</div>';
        return $items;
    }

    public function runScript()
    {
        echo JGalleryWidget::widget(
            [
                'selector' => '#gallery_sl',
                'pluginOptions' => [
                    'width' => $this->width,
                    'height' => $this->height,
                    'mode' => 'slider',
                    'thumbType' => 'number',
                    'slideshowAutostart' => $this->startslide,
                    "transition" => "rotatePushBottom_page",
                    "transitionBackward" => "rotateCarouselLeftOut_rotateCarouselLeftIn",
                    "transitionCols" => "6",
                    "transitionRows" => "1",
                    "thumbnailsPosition" => "top",
                    'autostart' => false,
                    'canClose' => true,
                    'backgroundColor' => '#000',
                    'textColor' => '#fff',
                    'title' => false,
                    'titleExpanded' => false,
                    'browserHistory' => false
                ]
            ]
        );
    }


    /**
     * @method getFriendlyName()
     * @return string Friendly name of your widget for user interface
     **/
    public function getFriendlyName()
    {
        return 'Слайдер галереи';
    }

    /**
     * @method getActionRoute()
     * @return mixed (string|bool) route to you contoller where you render form with settings and save it with WidgetizerModel or false if
     * your widget don`t needed in settings
     * @example '/admin/widgets/search-widget'  - first slash required!!!
     */
    public function getActionRoute()
    {
        return '/content/widget/slider-widget-configure';
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
        return false;
    }
}