<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 20.08.14
 * Time: 4:39
 */

namespace insolita\content\controllers;

use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use yii\web\Controller;

class InstallController extends Controller
{

    public $layout = '//dash';
    public $icon = 'cog';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['god'],
                    ],
                ]

            ],
        ];
    }

    public function actionIndex()
    {
        /**
         * @var \insolita\content\ContentModule $cont
         */
        $cont = \Yii::$app->getModule('content');
        $basepath = $cont->mainuploadpath;
        echo $basepath;
        FileHelper::createDirectory($basepath);

        FileHelper::createDirectory(FileHelper::normalizePath($basepath . '/covers/prepared/'));
        \Yii::$app->session->setFlash('info2', $basepath . '/covers/prepared/');
        FileHelper::createDirectory(FileHelper::normalizePath($basepath . '/covers/mid/'));
        \Yii::$app->session->setFlash('info2', $basepath . '/covers/mid/');
        FileHelper::createDirectory(FileHelper::normalizePath($basepath . '/covers/orig/'));
        \Yii::$app->session->setFlash('info3', $basepath . '/covers/orig/');
        FileHelper::createDirectory(FileHelper::normalizePath($basepath . '/content/orig/'));
        \Yii::$app->session->setFlash('info4', $basepath . '/content/orig/');
        FileHelper::createDirectory(FileHelper::normalizePath($basepath . '/content/thumbs/'));
        \Yii::$app->session->setFlash('info5', $basepath . '/content/thumbs/');
        FileHelper::createDirectory(FileHelper::normalizePath($basepath . '/content/big/'));
        \Yii::$app->session->setFlash('info6', $basepath . '/content/big/');
        FileHelper::createDirectory(FileHelper::normalizePath($basepath . '/content/mid/'));
        \Yii::$app->session->setFlash('info1', $basepath . '/content/mid/');
       // return $this->redirect(['/config/index']);
    }
}