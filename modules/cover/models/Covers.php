<?php

namespace insolita\content\modules\cover\models;

use insolita\content\models\Article;
use insolita\content\models\News;
use insolita\content\modules\cover\CoverModule;
use insolita\things\helpers\Helper;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use insolita\things\components\SActiveRecord;
use yii\imagine\Image;

/**
 * This is the model class for table "vg_covers".
 *
 * @property integer               $id
 * @property integer               $filename
 * @property \yii\web\UploadedFile $image
 * @property string                $filesize
 * @property string                $conttype
 * @property News[]                $news
 * @property Article[]             $articles
 */
class Covers extends SActiveRecord
{
    public static $titledAttribute = 'filename';
    public $gridDefaults = ['id', 'filename'];
    public $ignoredAttributes = [];

    public $image;
    /**
     * @var \insolita\content\modules\cover\CoverModule $module
     **/
    private $_module;

    public function init()
    {
        $this->_module = CoverModule::getInstance();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%covers}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filename'], 'string'],
            ['image', 'image'],
            ['conttype', 'safe'],
            [
                'image', 'image', 'extensions' => $this->_module->extensions,
                'mimeTypes' => $this->_module->mimeTypes,
                'maxFiles'=>1,
                'maxSize'=>$this->_module->maxFilesize
            ]
        ];
    }

    public function scenarios()
    {
        return [
            'default' => ['filename', 'image'],
            'create' => ['filename', 'image'],
            'update' => ['filename', 'image'],
            'delete' => ['id']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filename' => 'Файл',
            'image' => 'Титульное изображение',
            'filesize' => 'Размер файла',
            'updated' => 'Обновлено'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticles()
    {
        return $this->hasMany(Article::className(), ['cover_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasMany(News::className(), ['cover_id' => 'id']);
    }

    public function beforeSave($insert)
    {
        if ($this->image != null) {
            $fname = Yii::$app->security->generateRandomString(15) . '.' . $this->image->getExtension();
            $this->filename = $fname;
            $this->filesize = Yii::$app->formatter->asSize($this->image->size);
        }
        return parent::beforeSave($insert);
    }

    public function formattedErrors()
    {
        $errs = [];
        foreach ($this->getFirstErrors() as $error) {
            $errs[] = Html::encode($error);
        }
        return implode("\n", $errs);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->image->saveAs($this->_module->cover_origpath . $this->filename);
        Image::thumbnail(
            $this->_module->cover_origpath . $this->filename,
            $this->_module->cover_wsize,
            $this->_module->cover_hsize
        )
            ->save($this->_module->cover_path . $this->filename);
        Yii::$app->image->load($this->_module->cover_origpath . $this->filename)
            ->resize($this->_module->cover_midsize, $this->_module->cover_midsize, Yii\image\drivers\Image::AUTO)
            ->save($this->_module->cover_midpath . $this->filename);
        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeDelete()
    {
        /**@var \insolita\content\modules\cover\CoverModule $module * */
        $module = Yii::$app->getModule('content')->getModule('cover');
        @unlink($module->cover_path . $this->filename);
        @unlink($module->cover_origpath . $this->filename);
        @unlink($module->cover_midpath . $this->filename);
        return parent::beforeDelete();
    }

    public function removeBadfiles()
    {
        /**@var \insolita\content\modules\cover\CoverModule $module * */
        $module = Yii::$app->getModule('content')->getModule('cover');
        $covers = self::find()->all();
        foreach ($covers as $cover) {
            /**@var Covers $cover * */
            if (!file_exists($this->_module->cover_origpath . $cover->filename) or !file_exists(
                    $this->_module->cover_origpath . $cover->filename
                )
            ) {
                Helper::logs(['Bad cover ', $cover->getAttributes()]);
                if ($cover->news != null) {
                    News::updateAll(['cover_id' => new Expression('NULL')], ['cover_id' => $cover->id]);
                }
                if ($cover->articles != null) {
                    Article::updateAll(['cover_id' => new Expression('NULL')], ['cover_id' => $cover->id]);
                }
                $cover->delete();
            }
        }
    }

    public function removeUnusable()
    {

        $covers = self::find()->all();
        foreach ($covers as $cover) {
            /**@var Covers $cover * */
            if ($cover->news == null && $cover->articles == null) {
                Helper::logs(['Unused cover ', $cover->getAttributes()]);
                $cover->delete();
            } else {
                Helper::logs(ArrayHelper::map($cover->news, 'id', 'cover_id'));
            }
        }
    }

    public function rebuildThumb()
    {

        /**@var \insolita\content\modules\cover\CoverModule $module * */
        $module = Yii::$app->getModule('content')->getModule('cover');
        if (file_exists($module->cover_origpath . $this->filename)) {
            @unlink($module->cover_path . $this->filename);
            @unlink($module->cover_midpath . $this->filename);

            Image::thumbnail(
                $module->cover_origpath . $this->filename,
                $module->cover_wsize,
                $module->cover_hsize
            )
                ->save($module->cover_path . $this->filename);
            Yii::$app->image->load($module->cover_origpath . $this->filename)
                ->resize($module->cover_midsize, $module->cover_midsize, Yii\image\drivers\Image::AUTO)
                ->save($module->cover_midpath . $this->filename);
            Helper::logs('rebuilded ' . $module->cover_path . $this->filename);
        } else {
            Helper::logs('NOT FOUND! ' . $module->cover_path . $this->filename);
        }

    }

}
