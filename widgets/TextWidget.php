<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 24.08.14
 * Time: 18:18
 */

namespace insolita\content\widgets;


use insolita\widgetman\IWidget;
use insolita\widgetman\WidgetizerInterface;

class TextWidget extends IWidget implements WidgetizerInterface
{


    public function init()
    {
        parent::init();
    }

    public function run()
    {
       return parent::run();
    }

    public function renderBox()
    {
        return $this->render('box_tpl',['type' => $this->type,'icon'=>$this->icon ,'title' => $this->title, 'content' => $this->text]);
    }

    public function renderPanel()
    {
        return $this->render('panel_tpl',['type' => $this->type,'icon'=>$this->icon ,'title' => $this->title, 'content' => $this->text]);

    }

    public function renderFlat()
    {
        return $this->render('flat_tpl',['type' => $this->type,'icon'=>$this->icon ,'title' => $this->title, 'content' => $this->text]);
    }

    /**
     * @method getFriendlyName()
     * @return string Friendly name of your widget for user interface
     **/
    public function getFriendlyName()
    {
        return 'Произвольный текст';
    }

    /**
     * @method getActionRoute()
     * @return mixed (string|bool) route to you contoller where you render form with settings and save it with WidgetizerModel or false if
     * your widget don`t needed in settings
     * @example '/admin/widgets/search-widget'  - first slash required!!!
     */
    public function getActionRoute()
    {
        return '/content/widget/text-widget-configure';
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