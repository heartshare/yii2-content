<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 22.08.14
 * Time: 23:03
 */

namespace insolita\content\widgets;


use insolita\widgetman\WidgetizerInterface;
use yii\bootstrap\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

class SearchWidget extends TextWidget
{
    const SEARCH_TYPE_ALL = 'all';
    const SEARCH_TYPE_CONTENT = 'content';
    const SEARCH_TYPE_NEWS = 'news';
    const SEARCH_TYPE_ARTICLES = 'articles';
    public $searchtype = 'content';

    public $query = '';

    public function init()
    {
        parent::init();

    }

    public function run()
    {
        if (!$this->searchtype or !in_array(
                $this->searchtype,
                [self::SEARCH_TYPE_ALL, self::SEARCH_TYPE_CONTENT, self::SEARCH_TYPE_ARTICLES, self::SEARCH_TYPE_NEWS]
            )
        ) {
            $this->searchtype = 'content';
        }
        $this->text = $this->renderSearchForm();
        return parent::run();
    }

    public function renderSearchForm()
    {
        $form = Html::beginForm(
                Url::toRoute(['/content/front/search', 'type' => $this->searchtype]),
                'post',
                [
                    'class' => 'inline-from',
                    'role' => 'search'
                ]
            ) . '
        <div class="input-group">
          <input type="text" class="form-control" placeholder="Поиск по сайту" name="query" id="srch-term" value="'
            . $this->query . '">
          <div class="input-group-btn">
            <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span></button>
          </div>
        </div><br/>
       ' . Html::endForm();

        return $form;
    }

    public function getFriendlyName()
    {
        return 'Поисковая форма';
    }

    /**
     * @method getActionRoute()
     * @return mixed (string|bool) route to you contoller where you render form with settings and save it with WidgetizerModel or false if
     * your widget don`t needed in settings
     * @example '/admin/widgets/search-widget'  - first slash required!!!
     */
    public function getActionRoute()
    {
        return '/content/widget/search-widget-configure';
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