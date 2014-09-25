<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 30.07.14
 * Time: 15:24
 */

namespace insolita\content\modules\uploader\controllers;


use insolita\things\helpers\Helper;
use insolita\content\modules\uploader\models\Attach;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class DefaultController extends Controller
{
    public $layout = '//simple';

    public function behaviors()
    {

        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'imageupl' => ['post'],
                    'fileupl' => ['post'],
                    'delete' => ['post'],
                    'preview' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $model = new Attach();
        $dpimg = \Yii::$app->getModule('content')->getModule('uploader')->imageUploadAllow ? new ActiveDataProvider([
            'query' => Attach::find()->where(['type' => Attach::TYPE_IMAGE]),
            'pagination' => [
                'pageSize' => 9,
            ],
            'sort' => [
                'defaultOrder' => ['updated' => SORT_DESC]
            ]
        ]) : false;
        $dpfile = \Yii::$app->getModule('content')->getModule('uploader')->fileUploadAllow ? new ActiveDataProvider([
            'query' => Attach::find()->where(['type' => Attach::TYPE_FILE]),
            'pagination' => [
                'pageSize' => 9,
            ],
            'sort' => [
                'defaultOrder' => ['updated' => SORT_DESC]
            ]
        ]) : false;
        return $this->renderAjax('attach_index', ['model' => $model, 'dpimg' => $dpimg, 'dpfile' => $dpfile]);
    }

    public function actionPreview()
    {
        $text = \Yii::$app->request->post('text', '');
        $parsedtext = $this->prepareText($text);
        return $this->renderAjax('preview', ['text' => $parsedtext]);
    }

    public function actionImageupl()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Attach();
        $model->scenario = 'imgupload';
        $model->type = Attach::TYPE_IMAGE;
        $model->load(\Yii::$app->request->post());
        $model->imgfile = UploadedFile::getInstance($model, 'imgfile');
        if ($model->save()) {

            return [
                'state' => true,
                'id' => $model->id,
                'filename' => $model->filename,
                'thumburl' => \Yii::$app->getModule('content')->getModule('uploader')->thumb_url
            ];
        } else {
            return ['state' => false, 'error' => $model->formattedErrors()];
        }
    }

    public function actionFileupl()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Attach();
        $model->scenario = 'fileupload';
        $model->type = Attach::TYPE_FILE;
        $model->load(\Yii::$app->request->post());
        $model->file = UploadedFile::getInstance($model, 'file');
        if ($model->save()) {
            return ['state' => true, 'id' => $model->id];
        } else {
            return ['state' => false, 'error' => $model->formattedErrors()];
        }
    }

    public function actionImagelist()
    {
        $model = new Attach();
        $provider = new ActiveDataProvider([
            'query' => Attach::find()->where(['type' => Attach::TYPE_IMAGE]),
            'pagination' => [
                'pageSize' => 9,
            ],
            'sort' => [
                'defaultOrder' => ['updated' => SORT_DESC]
            ]
        ]);
        return $this->renderAjax('_imagelist', ['model' => $model, 'dp' => $provider]);
    }

    public function actionFilelist()
    {
        $model = new Attach();
        $provider = new ActiveDataProvider([
            'query' => Attach::find()->where(['type' => Attach::TYPE_FILE]),
            'pagination' => [
                'pageSize' => 9,
            ],
            'sort' => [
                'defaultOrder' => ['updated' => SORT_DESC]
            ]
        ]);
        return $this->renderAjax('_filelist', ['model' => $model, 'dp' => $provider]);
    }

    public function actionDelete()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $id = \Yii::$app->request->post('id');
        if (!$id) {
            return ['state' => false];
        }
        $model = Attach::findOne($id);
        if ($model) {
            $model->delete();
            return ['state' => true];
        } else {
            return ['state' => false];
        }
    }

    public function prepareText($text)
    {
        if (!$text) return '';
        preg_match_all('#\"ATTACH_IMAGE\":\"(.+?)\"#', $text, $imgids);
        preg_match_all('#{\"ATTACH_FILE\":\"(.+?)\"}#', $text, $fileids);

        $images = !count($imgids)
            ? []
            : Attach::find()->where(['type' => Attach::TYPE_IMAGE, 'id' => $imgids[1]])->indexBy('id')->asArray()->all(
            );
        $files = !count($fileids)
            ? []
            : Attach::find()->where(['type' => Attach::TYPE_FILE, 'id' => $fileids[1]])->indexBy('id')->asArray()->all(
            );
        if (empty($images) && empty($files)) return $text;
        $textparsed = preg_replace_callback(
            '#\[\[(.+?)\]\]#siu',
            function ($m) use ($images, $files) {
                $bb = Json::decode($m[1]);
                Helper::logs($bb);
                if (isset($bb['ATTACH_FILE'])) {
                    $fileid = (int)$bb['ATTACH_FILE'];
                    if (!isset($files[$fileid])) {
                        return '';
                    } else {
                        return strtr(
                            \Yii::$app->getModule('content')->getModule('uploader')->fileHtmlTemplate,
                            [
                                '{attach_id}' => $fileid,
                                '{filetitle}' => $files[$fileid]['filetitle'],
                                '{fileurl}' => \Yii::$app->getModule('content')->getModule('uploader')->file_url
                                    . $files[$fileid]['filename'],
                                '{filesize}' => $files[$fileid]['filesize']
                            ]
                        );
                    }
                } elseif (isset($bb['ATTACH_IMAGE'])) {
                    $imgid = (int)$bb['ATTACH_IMAGE'];
                    if (!isset($images[$imgid])) {
                        return '';
                    } else {
                        return
                            $bb['PREVIEW']
                                ? strtr(
                                \Yii::$app->getModule('content')->getModule('uploader')->imagePreviewTemplate,
                                [
                                    '{attach_id}' => $imgid,
                                    '{cssclass}' => $bb['CSS'],
                                    '{align}' => \Yii::$app->getModule('content')->getModule('uploader')->getAlignStyle(
                                            $bb['ALIGN']
                                        ),
                                    '{alt}' => $bb['ALT'],
                                    '{size}' => $bb['SIZE'],
                                    '{imgurl}' =>
                                        \Yii::$app->getModule('content')->getModule('uploader')->getImageUrlBySize(
                                            $bb['SIZE']
                                        ) . $images[$imgid]['filename'],
                                    '{fullurl}' =>
                                        \Yii::$app->getModule('content')->getModule('uploader')->getImageUrlBySize(
                                            'big'
                                        ) . $images[$imgid]['filename'],

                                ]
                            )
                                : strtr(
                                \Yii::$app->getModule('content')->getModule('uploader')->imageHtmlTemplate,
                                [
                                    '{attach_id}' => $imgid,
                                    '{cssclass}' => $bb['CSS'],
                                    '{align}' => \Yii::$app->getModule('content')->getModule('uploader')->getAlignStyle(
                                            $bb['ALIGN']
                                        ),
                                    '{alt}' => $bb['ALT'],
                                    '{size}' => $bb['SIZE'],
                                    '{imgurl}' =>
                                        \Yii::$app->getModule('content')->getModule('uploader')->getImageUrlBySize(
                                            $bb['SIZE']
                                        ) . $images[$imgid]['filename'],
                                ]
                            );
                    }
                }
            },
            $text
        );
        return $textparsed;
    }

} 