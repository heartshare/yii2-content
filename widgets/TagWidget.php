<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 24.08.14
 * Time: 18:18
 */

namespace insolita\content\widgets;


use insolita\content\models\Tags;
use yii\helpers\Html;

class TagWidget extends TextWidget
{
    const SORT_ALPHA = 'alpha';
    const SORT_FREEQ = 'freeq';

    const VIEW_LIST = 'list';
    const VIEW_CLOUD = 'cloud';

    public $sorttype = self::SORT_FREEQ;
    public $viewtype = self::VIEW_CLOUD;

    public $limit = 20;


    public function run()
    {
        $this->text = $this->renderText();
        return parent::run();
    }

    public function renderText()
    {
        $tags = Tags::findTagWeights($this->limit);
        if (!$tags) {
            return '';
        }
        if ($this->sorttype == self::SORT_ALPHA) {
            ksort($tags);
        } else {
            arsort($tags);
        }

        if ($this->viewtype == self::VIEW_LIST) {
            $tpl = '';
            foreach ($tags as $tag => $weight) {
                $taglink = Html::a(
                    $tag,
                    ['/content/front/showtag', 'name' => $tag],
                    ['class' => 'tag_weight_' . $weight]
                );
                $tpl .= Html::tag('li', $taglink);
            }
            return Html::tag('ul', $tpl, ['nav nav-stacked']);
        } else {
            $tpl = [];
            foreach ($tags as $tag => $weight) {
                $taglink = Html::a(
                    $tag,
                    ['/content/front/showtag', 'name' => $tag],
                    ['class' => 'tag_weight_' . $weight]
                );
                $tpl[] = $taglink;
            }
            return implode(', ', $tpl);
            //return Html::tag('div', $tpl, ['class'=>'tagcloud']);
        }
    }


    /**
     * @method getFriendlyName()
     * @return string Friendly name of your widget for user interface
     **/
    public function getFriendlyName()
    {
        return 'Теги (метки)';
    }

    /**
     * @method getActionRoute()
     * @return mixed (string|bool) route to you contoller where you render form with settings and save it with WidgetizerModel or false if
     * your widget don`t needed in settings
     * @example '/admin/widgets/search-widget'  - first slash required!!!
     */
    public function getActionRoute()
    {
        return '/content/widget/tag-widget-configure';
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