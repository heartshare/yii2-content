<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 28.07.14
 * Time: 9:31
 */

namespace insolita\content;


use insolita\content\modules\cover\CoverModule;
use yii\base\BootstrapInterface;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;

class ContentModule extends \yii\base\Module implements BootstrapInterface
{

    public $controllerNamespace = 'insolita\content\controllers';

    public $logDir = '@app/runtime/logs/';

    public $defaultRoute = 'content/index';

    public $cover_url = '@web/uploads/covers/prepared/';
    public $cover_path = '@webroot/uploads/covers/prepared/';
    public $cover_origurl = '@web/uploads/covers/orig/';
    public $cover_origpath = '@webroot/uploads/covers/orig/';

    public $mainuploadpath;

    public $adminModel = null;

    private $adminList;

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $app->getUrlManager()->addRules(
            [
                '/page/<slug:[\w\-]+>' => 'content/front/page',
                '/news/<slug:[\w\-]+>' => 'content/front/news',
                '/newsby/<date:[\w\-]+>' => 'content/front/news-bydate',
                '/articlesby/<date:[\w\-]+>' => 'content/front/articles-bydate',
                '/news/<slug:[\w\-]+>/<page:[\d+]>' => 'content/front/news',
                '/article/<category:[\w\-]+>/<slug:[\w\-]+>' => 'content/front/article',
                '/category/<slug:[\w\-]+>' => 'content/front/category',
                '/category/<slug:[\w\-]+>/<page:[\d+]>' => 'content/front/category',
                '/content'=>'content/front/category',
                '/content/<page:[\d+]>'=>'content/front/category',
                '/showtag/<name:[\w\-]+>' => 'content/front/showtag',
                '/search' => 'content/front/search',
                '/feedback' => 'content/front/feedback',
                '/captcha' => 'content/front/captcha',
                '/newslist' => 'content/front/newslist',
                '/novosti' => 'content/front/novosti',
                '/pages/<slug:\w+>' => 'content/page/look',
                '/newslook/<slug:\w+>' => 'content/news/look',
                '/articles/<slug:\w+>' => 'content/article/look',
                '/uploader/<_a:[\w\-]+>' => 'content/uploader/default/<_a>',
                '/coverize/<_a:[\w\-]+>' => 'content/cover/default/<_a>',
            ],
            true
        );
    }

    public function init()
    {
        parent::init();
        if (!isset(\Yii::$app->params['thumb_size']) or !isset(\Yii::$app->params['uploadpath'])
            or !isset(\Yii::$app->params['siteurl'])
        ) {
            throw new InvalidCallException('Некорректная настройка параметров для модуля Контент');
        }
        $covsizes = explode(',', \Yii::$app->params['thumb_size']);
        if (count($covsizes) < 2) {
            throw new InvalidConfigException('Некорректная настройка параметра thumb_size');
        }
        $this->mainuploadpath = (isset($this->mainuploadpath))
            ? \Yii::getAlias($this->mainuploadpath)
            : \Yii::getAlias(
                \Yii::$app->params['uploadpath']
            );

        $pu = str_replace('@frontweb/', '', \Yii::$app->params['uploadpath']);
        //$pu = str_replace('web', '', $pu);
        $baseurlpath = \Yii::$app->id == 'app-backend'
            ? \Yii::$app->fronturlManager->baseUrl . '/' . $pu
            :
            \Yii::$app->urlManager->baseUrl . '/' . $pu;
        $this->modules = [
            'cover' => [
                'class' => CoverModule::className(),
                'cover_url' => $baseurlpath . '/covers/prepared/',
                'cover_path' => \Yii::getAlias($this->mainuploadpath . '/covers/prepared/'),
                'cover_origurl' => $baseurlpath . '/covers/orig/',
                'cover_origpath' => \Yii::getAlias($this->mainuploadpath . '/covers/orig/'),
                'cover_midurl' => $baseurlpath . '/covers/mid/',
                'cover_midpath' => \Yii::getAlias($this->mainuploadpath . '/covers/mid/'),
                'cover_wsize' => $covsizes[0],
                'cover_hsize' => $covsizes[1],
                'cover_midsize' => \Yii::$app->params['mid_size'],
            ],
            'uploader' => [
                'class' => 'insolita\content\modules\uploader\UploaderModule',
                'thumb_path' => \Yii::getAlias($this->mainuploadpath . '/content/thumbs/'),
                'big_path' => \Yii::getAlias($this->mainuploadpath . '/content/big/'),
                'mid_path' => \Yii::getAlias($this->mainuploadpath . '/content/mid/'),
                'orig_path' => \Yii::getAlias($this->mainuploadpath . '/content/orig/'),
                'thumb_url' => $baseurlpath . '/content/thumbs/',
                'big_url' => $baseurlpath . '/content/big/',
                'mid_url' => $baseurlpath . '/content/mid/',
                'orig_url' => $baseurlpath . '/content/orig/',
                'deleteAllow' => true,
                'thumb_size' => $covsizes[0],
                'mid_size' => \Yii::$app->params['mid_size'],
                'big_size' => \Yii::$app->params['big_size'],
            ],
        ];
    }

    public function getAdminList()
    {
        if (!$this->adminList) {
            if ($this->adminModel) {
                $admModel = $this->adminModel;
                $this->adminList = $admModel::getList();
            } else {
                return [];
            }
        }
        return $this->adminList;
    }

    public function getConfig()
    {
        return FileHelper::normalizePath(__DIR__ . '/data/addconfig.php');
    }

} 