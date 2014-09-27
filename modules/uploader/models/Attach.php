<?php

namespace insolita\content\modules\uploader\models;

use insolita\things\helpers\Helper;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * This is the model class for table "vg_images".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $filename
 * @property string  $filesize
 */
class Attach extends ActiveRecord
{
    const TYPE_IMAGE = 0;
    const TYPE_FILE = 1;
    /**
     * @var \yii\web\UploadedFile $imgfile
     */
    public $imgfile;
    /**
     * @var \yii\web\UploadedFile $file
     */
    public $file;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%attachments}}';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['filetitle', 'trim'],
            ['filetitle', 'string', 'min' => 2, 'max' => 200],
            [
                'filetitle',
                'match',
                'pattern' => '/[^A-Za-zА-Яа-яЁё0-9\s\-\_]/us',
                'not' => true,
                'message' => 'Имя -  Допустимо использование только символов русского, английского алфавита, цифр и пробелов'
            ],
            [['imgfile'], 'required', 'on' => 'imgupload'],
            ['filetitle', 'default', 'value' => '-', 'on' => 'imgupload'],
            [['file', 'filetitle'], 'required', 'on' => 'fileupload'],
            ['imgfile', 'image', 'on' => 'imgupload'],
            ['file', 'file', 'on' => 'fileupload'],
            ['filename', 'string', 'min' => 2, 'max' => 200],
        ];
    }

    public function scenarios()
    {
        return [
            'imgupload' => ['imgfile', 'filetitle', 'filename'],
            'fileupload' => ['file', 'filetitle', 'filename'],
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
            'filename' => 'Filename',
            'type' => 'Тип файла',
            'imgfile' => 'Изображение',
            'file' => 'Файл',
            'filetitle' => 'Название',
            'filesize' => 'Размер файла'
        ];
    }

    public function uploadImage()
    {
        if (isset($this->imgfile->name)) {
            $fname = Yii::$app->security->generateRandomString(15) . '.' . $this->imgfile->getExtension();
            $this->filename = $fname;
            $this->filesize = Yii::$app->formatter->asSize($this->imgfile->size);
            return true;
        } else {
            $this->addError('imgfile', 'Изображение не было загружено');
            return false;
        }
    }

    public function uploadFile()
    {
        if (isset($this->file->name)) {
            $fname = Yii::$app->security->generateRandomString(15) . '.' . $this->file->getExtension();
            $this->filename = $fname;
            $this->filesize = Yii::$app->formatter->asSize($this->file->size);
            return true;
        } else {
            $this->addError('file', 'Файл не был загружен');
            return false;
        }
    }

    public function beforeSave($insert)
    {
        if ($this->type == self::TYPE_IMAGE) {
            $this->uploadImage();
        } else {
            $this->uploadFile();
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
        /**
         * @var \insolita\content\modules\uploader\UploaderModule $uploader
         **/
        $uploader = Yii::$app->getModule('content')->getModule('uploader');
        if ($this->type == self::TYPE_IMAGE) {
            $this->imgfile->saveAs($uploader->orig_path . $this->filename);

            Yii::$app->image->load($uploader->orig_path . $this->filename)
                ->resize($uploader->thumb_size, $uploader->thumb_size, Yii\image\drivers\Image::AUTO)
                ->save($uploader->thumb_path . $this->filename);

            Yii::$app->image->load($uploader->orig_path . $this->filename)
                ->resize($uploader->mid_size, $uploader->mid_size, Yii\image\drivers\Image::AUTO)
                ->save($uploader->mid_path . $this->filename);

            Yii::$app->image->load($uploader->orig_path . $this->filename)
                ->resize($uploader->big_size, $uploader->big_size, Yii\image\drivers\Image::AUTO)
                ->save($uploader->big_path . $this->filename);
        } else {
            $this->file->saveAs($uploader->file_path . $this->filename);
        }

        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeDelete()
    {
        /**
         * @var \insolita\content\modules\uploader\UploaderModule $uploader
         **/
        $uploader = Yii::$app->getModule('content')->getModule('uploader');
        if ($this->type == self::TYPE_IMAGE) {
            @unlink($uploader->orig_path . $this->filename);
            @unlink($uploader->thumb_path . $this->filename);
            @unlink($uploader->mid_path . $this->filename);
            @unlink($uploader->big_path . $this->filename);
        } else {
            @unlink($uploader->file_path . $this->filename);
        }
        return parent::beforeDelete();
    }

    public function rebuildThumb()
    {
        /**
         * @var \insolita\content\modules\uploader\UploaderModule $uploader
         **/
        if ($this->type == self::TYPE_IMAGE) {
            $uploader = Yii::$app->getModule('content')->getModule('uploader');
            Helper::logs(
                [
                    $uploader->orig_path . $this->filename,
                    $uploader->thumb_path . $this->filename,
                    $uploader->mid_path . $this->filename
                ]
            );
            if(file_exists($uploader->orig_path . $this->filename)){
                @unlink($uploader->thumb_path . $this->filename);
                @unlink($uploader->mid_path . $this->filename);
                @unlink($uploader->big_path . $this->filename);
                Yii::$app->image->load($uploader->orig_path . $this->filename)
                    ->resize($uploader->thumb_size, $uploader->thumb_size, Yii\image\drivers\Image::AUTO)
                    ->save($uploader->thumb_path . $this->filename);

                Yii::$app->image->load($uploader->orig_path . $this->filename)
                    ->resize($uploader->mid_size, $uploader->mid_size, Yii\image\drivers\Image::AUTO)
                    ->save($uploader->mid_path . $this->filename);

                Yii::$app->image->load($uploader->orig_path . $this->filename)
                    ->resize($uploader->big_size, $uploader->big_size, Yii\image\drivers\Image::AUTO)
                    ->save($uploader->big_path . $this->filename);
                Helper::logs('rebuilded ' . $uploader->thumb_path . $this->filename);
            }else{
                Helper::logs('Not extst file  ' . $uploader->orig_path . $this->filename);
            }

        }
    }
}
