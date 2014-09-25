<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 24.08.14
 * Time: 18:18
 */

namespace insolita\content\widgets;


use insolita\widgetman\WidgetizerInterface;
use yii\base\Widget;

class ScriptWidget extends Widget implements WidgetizerInterface
{

    public $text = '';

    public function init()
    {
        parent::init();

    }

    public function run()
    {
        return $this->text;
    }

    /**
     * @method getFriendlyName()
     * @return string Friendly name of your widget for user interface
     **/
    public function getFriendlyName()
    {
        return 'Скрипт(счётчики и т.п)';
    }

    /**
     * @method getActionRoute()
     * @return mixed (string|bool) route to you contoller where you render form with settings and save it with WidgetizerModel or false if
     * your widget don`t needed in settings
     * @example '/admin/widgets/search-widget'  - first slash required!!!
     */
    public function getActionRoute()
    {
        return '/content/widget/script-widget-configure';
    }

    /**
     * @method getIsScript()
     * @return bool - if widget for script places, return true, if for content - return false
     *
     */
    public function getIsScript()
    {
        return true;
    }

    public function allowCache()
    {
        return false;
    }
}