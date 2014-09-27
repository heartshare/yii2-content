<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 24.08.14
 * Time: 18:18
 */

namespace insolita\content\widgets;


use insolita\content\models\Category;
use insolita\widgetman\WidgetizerInterface;
use kartik\widgets\SideNav;
use yii\bootstrap\Widget;
use yii\helpers\Url;

class CategoryWidget extends Widget implements WidgetizerInterface
{
    public $title = '';
    public $icon = '';
    public $type = 'default';
    public $showcount = true;
    public $showempty = false;

    public function run()
    {
        parent::run();
        return $this->renderText();
    }

    public function renderText()
    {
        $cats = Category::find();
        if (!$this->showempty) {
            $cats->where('cnt>0');
        }
        $cats = $cats->all();
        $items = [];
        foreach ($cats as $cat) {
            /**@var Category $cat */
            $items[] = [
                'label' => $cat->name . (!$this->showcount ? '' : '<span class="badge">' . $cat->cnt . '</span>'),
                'url' => \Yii::$app->params['siteurl'] . Url::toRoute(['/content/front/category', 'slug' => $cat->slug])
            ];
        }
        if ($this->icon) {
            $this->title = '<i class="fa fa-' . $this->icon . ' fa-lg"></i> ' . $this->title;
        }
        return SideNav::widget(
            [
                'type'=>$this->type,
                'heading' => $this->title,
                'encodeLabels' => false,
                'items' => $items
            ]
        );
    }


    /**
     * @method getFriendlyName()
     * @return string Friendly name of your widget for user interface
     **/
    public function getFriendlyName()
    {
        return 'Категории статей';
    }

    /**
     * @method getActionRoute()
     * @return mixed (string|bool) route to you contoller where you render form with settings and save it with WidgetizerModel or false if
     * your widget don`t needed in settings
     * @example '/admin/widgets/search-widget'  - first slash required!!!
     */
    public function getActionRoute()
    {
        return '/content/widget/cat-widget-configure';
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