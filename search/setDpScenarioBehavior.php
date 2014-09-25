<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 23.08.14
 * Time: 2:09
 */

namespace insolita\content\search;


use insolita\content\models\News;
use insolita\things\helpers\Helper;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class setDpScenarioBehavior extends Behavior
{
    public $scenario;
    public $owner;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'onAfterFind',
            NewsSearch::EVENT_AFTER_FIND => 'onAfterFind'
        ];
    }

    public function onAfterFind()
    {
        Helper::logs('i`m onAfterFind set scenario Behavior and i set scenario ' . $this->scenario);
        $this->owner->setScenario = 'usersearchresult';
        Helper::logs($this->owner);
    }

    public function init()
    {
        parent::init();
        Helper::logs('i`m attached set scenario Behavior and i set scenario ' . $this->scenario);
        Helper::logs($this->owner);
    }


}