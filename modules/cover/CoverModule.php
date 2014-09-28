<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 07.08.14
 * Time: 13:06
 */

namespace insolita\content\modules\cover;


use yii\base\InvalidConfigException;
use yii\base\Module;

class CoverModule extends Module
{
    public $controllerNamespace = 'insolita\content\modules\cover\controllers';

    public $cover_url;
    public $cover_path;
    public $cover_origurl;
    public $cover_origpath;
    public $cover_midurl;
    public $cover_midpath;

    public $cover_hsize;
    public $cover_wsize;

    public $cover_midsize;

    public $mimeTypes=['image/jpg','image/jpeg', 'image/gif', 'image/png'];

    public $extensions=['jpg', 'jpeg', 'gif', 'png'];

    public $maxFilesize=2097152;

    public function init()
    {
        parent::init();
        if (!$this->cover_url || !$this->cover_path || !$this->cover_origpath || !$this->cover_origurl) {
            throw new InvalidConfigException('Корявая конфигурация модуля Cover');
        }
    }
} 