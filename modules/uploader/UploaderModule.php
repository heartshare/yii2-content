<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 30.07.14
 * Time: 22:08
 */

namespace insolita\content\modules\uploader;


use yii\base\Module;
use yii\helpers\Url;

class UploaderModule extends Module
{
    public $controllerNamespace = 'insolita\content\modules\uploader\controllers';
    public $thumb_path;
    public $thumb_url;
    public $mid_path;
    public $mid_url;
    public $big_path;
    public $big_url;
    public $orig_path;
    public $orig_url;

    public $file_path;
    public $file_url;

    public $thumb_size = '200,200';
    public $mid_size = 500;
    public $big_size = 1000;


    public $deleteAllow = false;
    public $bbinsertAllow = true;
    public $htmlinsertAllow = true;
    public $fileUploadAllow = true;
    public $imageUploadAllow = true;

    /**@var array $imageClasses - Array of classes for images, which user choose* */
    public $imageClasses
        = [
            '' => 'no',
            'img-thumbnail' => 'img-thumbnail',
            'img-rounded' => 'img-rounded',
            'img-circle' => 'img-circle'
        ];

    public $leftAlignStyle = 'float:left;';
    public $rightAlignStyle = 'float:right;';

    /**@var string $imageHtmlTemplate - you can use vars {attach_id},{cssclass},{align},{alt},{imgurl},{size}
     **/
    public $imageHtmlTemplate = '<img src="{imgurl}" alt="{alt}" class="{cssclass}" style="{align}">';

    /**@var string $imagePreviewTemplate - you can use vars {attach_id},{cssclass},{align},{alt},{imgurl},{fullurl},{size}
     **/
    public $imagePreviewTemplate = '<a href="{fullurl}" rel="imagelink" class="imagelink" title="{alt}"><img src="{imgurl}" alt="{alt}" class="{cssclass}" style="{align}"></a>';

    /**@var string $fileHtmlTemplate - you can use vars {attach_id},{filetitle},{fileurl},{filesize}
     **/
    public $fileHtmlTemplate = '<a href="{fileurl}"  class="filelink">Скачать: {filetitle}({filesize})</a>';

    /**@var string $imageBBTemplate - you can use vars  {attach_id},{cssclass},{align},{alt},{size},{preview}
     **/
    public $imageBBTemplate = '[[{"ATTACH_IMAGE":"{attach_id}","PREVIEW":"{preview}","CSS":"{cssclass}","ALIGN":"{align}","SIZE":"{size}","ALT":"{alt}"}]]';

    /**@var string $fileBBTemplate - you can use vars  {attach_id}
     **/
    public $fileBBTemplate = '[[{"ATTACH_FILE":"{attach_id}"}]]';
    /**
     * @var integer $pjaxTimeout - pjaxTimeout option
     **/
    public $pjaxTimeout = 5000;

    public $mimeTypesImg=['image/jpg','image/jpeg', 'image/gif', 'image/png'];

    public $mimeTypesFiles=['application/msword','application/pdf','application/x-compressed','application/x-gzip'
        ,'application/x-tar','application/zip','application/rar','text/plain'
        ,'application/vnd.ms-excel', 'image/gif', 'image/png'];

    public $extensionsImg=['jpg', 'jpeg', 'gif', 'png'];
    public $extensionsFiles=['txt', 'zip', 'rar', 'doc','pdf','xls','gz','tar'];

    public $maxFilesizeImg=2097152;
    public $maxFilesizeFile=2097152;


    public function init()
    {
        parent::init();
        $this->thumb_path = !$this->thumb_path
            ? \Yii::getAlias('@webroot/uploads/thumbs/')
            : \Yii::getAlias(
                $this->thumb_path
            );
        $this->mid_path = !$this->mid_path ? \Yii::getAlias('@webroot/uploads/mid/') : \Yii::getAlias($this->mid_path);
        $this->big_path = !$this->big_path ? \Yii::getAlias('@webroot/uploads/big/') : \Yii::getAlias($this->big_path);
        $this->orig_path = !$this->orig_path
            ? \Yii::getAlias('@webroot/uploads/orig/')
            : \Yii::getAlias(
                $this->orig_path
            );

        $this->thumb_url = !$this->thumb_url
            ? \Yii::getAlias('@web/uploads/thumbs/')
            : \Yii::getAlias(
                $this->thumb_url
            );
        $this->mid_url = !$this->mid_url ? \Yii::getAlias('@web/uploads/mid/') : \Yii::getAlias($this->mid_url);
        $this->big_url = !$this->big_url ? \Yii::getAlias('@web/uploads/big/') : \Yii::getAlias($this->big_url);
        $this->orig_url = !$this->orig_url ? \Yii::getAlias('@web/uploads/orig/') : \Yii::getAlias($this->orig_url);

        $this->file_path = !$this->file_path
            ? \Yii::getAlias('@webroot/uploads/files/')
            : \Yii::getAlias(
                $this->file_path
            );
        $this->file_url = !$this->file_url ? \Yii::getAlias('@web/uploads/files/') : \Yii::getAlias($this->file_url);
    }

    public function getRedactorSettings()
    {
        $setts = ['managerUrl' => Url::toRoute(['/content/uploader/default/index'])];
        if ($this->deleteAllow) {
            $setts['deleteUrl'] = Url::toRoute(['/content/uploader/default/delete']);
        }
        if ($this->fileUploadAllow) {
            $setts['fileUploadUrl'] = Url::toRoute(['/content/uploader/default/fileupl']);
            $setts['fileListUrl'] = Url::toRoute(['/content/uploader/default/filelist']);
        }
        if ($this->imageUploadAllow) {
            $setts['imageUploadUrl'] = Url::toRoute(['/content/uploader/default/imageupl']);
            $setts['imageListUrl'] = Url::toRoute(['/content/uploader/default/imagelist']);
        }
        if ($this->fileBBTemplate) {
            $setts['fileBBTemplate'] = $this->fileBBTemplate;
        }
        if ($this->imageBBTemplate) {
            $setts['imageBBTemplate'] = $this->imageBBTemplate;
        }
        if ($this->fileHtmlTemplate) {
            $setts['fileHtmlTemplate'] = $this->fileHtmlTemplate;
        }
        if ($this->imagePreviewTemplate) {
            $setts['imagePreviewTemplate'] = $this->imagePreviewTemplate;
        }
        if ($this->imageHtmlTemplate) {
            $setts['imageHtmlTemplate'] = $this->imageHtmlTemplate;
        }
        $setts['previewUrl'] = Url::toRoute(['/content/uploader/default/preview']);

        $setts['bbinsertAllow'] = $this->bbinsertAllow;
        $setts['htmlinsertAllow'] = $this->htmlinsertAllow;

        $setts['leftAlignStyle'] = $this->leftAlignStyle ? : 'float:left;';
        $setts['rightAlignStyle'] = $this->rightAlignStyle ? : 'float:right;';
        $setts['pjaxTimeout'] = $this->pjaxTimeout ? : 5000;

        return $setts;
    }

    public function getImageUrlBySize($size = 'orig')
    {
        if ($size == 'orig') {
            return $this->orig_url;
        } elseif ($size == 'mid') {
            return $this->mid_url;
        } elseif ($size == 'big') {
            return $this->big_url;
        } else {
            return $this->thumb_url;
        }
    }

    public function getImagePathBySize($size = 'orig')
    {
        if ($size == 'orig') {
            return $this->orig_path;
        } elseif ($size == 'mid') {
            return $this->mid_path;
        } elseif ($size == 'big') {
            return $this->big_path;
        } else {
            return $this->thumb_path;
        }
    }

    public function getAlignStyle($style)
    {
        if ($style == 'no') {
            return '';
        } elseif ($style == 'left') {
            return $this->leftAlignStyle;
        } else {
            return $this->rightAlignStyle;
        }
    }

} 