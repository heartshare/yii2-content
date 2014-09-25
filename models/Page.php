<?php

namespace insolita\content\models;

use insolita\things\behaviors\MetaModelBeh;
use insolita\things\behaviors\SlugModelBeh;
use insolita\menu\models\Menu;
use insolita\uploader\models\Attach;
use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use insolita\things\components\SActiveRecord;


/**
 * This is the model class for table "vg_page".
 *
 * @property integer $id
 * @property string  $name
 * @property string  $slug
 * @property string  $full
 * @property string  $full_parsed
 * @property integer $views
 * @property string  $updated
 * @property integer $bymanager
 * @property string  $metak
 * @property string  $metadesc
 */

/**
 * @var \insolita\menu\models\Menu $menu
 */
class Page extends SActiveRecord
{
    public static $titledAttribute = 'name';
    public $gridDefaults = ['id', 'name', 'views', 'addtomenu', 'updated',];
    public $ignoredAttributes = ['bymanager', 'metak', 'metadesc', 'full', 'full_parsed'];

    public $addtomenu;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%page}}';
    }

    public static function modelTitle($type = 'plural')
    {
        $titles = [
            'single' => 'Страница',
            'plural' => 'Страницы',
            'rod' => 'Страниц',
            'vin' => 'Страницу'
        ];
        return isset($titles[$type]) ? $titles[$type] : $titles['plural'];
    }

    public function behaviors()
    {
        return [
            'slug' => [
                'class' => SlugModelBeh::className(),
                'source_attribute' => 'name',
                'slug_attribute' => 'slug'
            ],
            'meta' => [
                'class' => MetaModelBeh::className(),
                'metakey_attribute' => 'metak',
                'metadesc_attribute' => 'metadesc',
                'source_attributes' => ['name', 'full']
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'full'], 'required'],
            [['full', 'full_parsed'], 'string'],
            [['views', 'bymanager'], 'integer'],
            [['updated'], 'safe'],
            [['name'], 'string', 'max' => 200],
            [['slug', 'metak', 'metadesc'], 'string', 'max' => 255],
        ];
    }

    public function scenarios()
    {
        return [
            'create' => [
                'id',
                'name',
                'slug',
                'full',
                'full_parsed',
                'views',
                'updated',
                'bymanager',
                'metak',
                'metadesc'
            ],
            'update' => [
                'id',
                'name',
                'slug',
                'full',
                'full_parsed',
                'views',
                'updated',
                'bymanager',
                'metak',
                'metadesc'
            ], 'viewupdate' => ['views'],
            'delete' => ['active']
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenu()
    {
        return $this->hasOne(Yii::$app->getModule('menu')->getMenuClass(), ['id' => 'menuid']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'slug' => 'SEO-Ссылка(автоматом)',
            'full' => 'Текст',
            'full_parsed' => 'Обработанный текст',
            'views' => 'Просмотров',
            'updated' => 'Обновлено',
            'bymanager' => 'Bymanager',
            'metak' => 'SEO-ключи',
            'metadesc' => 'SEO-описание',
            'addtomenu' => 'Ссылка для меню'
        ];
    }

    public function prepareFulltext()
    {
        if ($this->full && $this->full != $this->getOldAttribute('full')) {
            preg_match_all('#\"ATTACH_IMAGE\":\"(.+?)\"#', $this->full, $imgids);
            preg_match_all('#{\"ATTACH_FILE\":\"(.+?)\"}#', $this->full, $fileids);
            $images = !count($imgids)
                ? []
                : Attach::find()->where(['type' => Attach::TYPE_IMAGE, 'id' => $imgids[1]])->indexBy('id')->asArray()
                    ->all();
            $files = !count($fileids)
                ? []
                : Attach::find()->where(['type' => Attach::TYPE_FILE, 'id' => $fileids[1]])->indexBy('id')->asArray()
                    ->all();
            if (empty($images) && empty($files)) {
                $this->full_parsed = $this->full;
            }
            $this->full_parsed = preg_replace_callback(
                '#\[\[(.+?)\]\]#siu',
                function ($m) use ($images, $files) {
                    $bb = Json::decode($m[1]);
                    if (isset($bb['ATTACH_FILE'])) {
                        $fileid = (int)$bb['ATTACH_FILE'];
                        if (!isset($files[$fileid])) {
                            return '';
                        } else {
                            return strtr(
                                \Yii::$app->getModule('uploader')->fileHtmlTemplate,
                                [
                                    '{attach_id}' => $fileid,
                                    '{filetitle}' => $files[$fileid]['filetitle'],
                                    '{fileurl}' =>
                                        \Yii::$app->getModule('uploader')->file_url . $files[$fileid]['filename'],
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
                                    \Yii::$app->getModule('uploader')->imagePreviewTemplate,
                                    [
                                        '{attach_id}' => $imgid,
                                        '{cssclass}' => $bb['CSS'],
                                        '{align}' => \Yii::$app->getModule('uploader')->getAlignStyle($bb['ALIGN']),
                                        '{alt}' => $bb['ALT'],
                                        '{size}' => $bb['SIZE'],
                                        '{imgurl}' => \Yii::$app->getModule('uploader')->getImageUrlBySize($bb['SIZE'])
                                            . $images[$imgid]['filename'],
                                        '{fullurl}' => \Yii::$app->getModule('uploader')->getImageUrlBySize('big')
                                            . $images[$imgid]['filename'],

                                    ]
                                )
                                    : strtr(
                                    \Yii::$app->getModule('uploader')->imageHtmlTemplate,
                                    [
                                        '{attach_id}' => $imgid,
                                        '{cssclass}' => $bb['CSS'],
                                        '{align}' => \Yii::$app->getModule('uploader')->getAlignStyle($bb['ALIGN']),
                                        '{alt}' => $bb['ALT'],
                                        '{size}' => $bb['SIZE'],
                                        '{imgurl}' => \Yii::$app->getModule('uploader')->getImageUrlBySize($bb['SIZE'])
                                            . $images[$imgid]['filename'],
                                    ]
                                );
                        }
                    } else {
                        return '';
                    }
                },
                $this->full
            );
        }
    }


    public function beforeSave($insert)
    {
        $this->prepareFulltext();
        if ($this->scenario !== 'viewupdate') {
            $this->updated = date('Y-m-d H:i:s', time());
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {

        parent::afterSave($insert, $changedAttributes);
    }

    public function afterFind()
    {
        if (Yii::$app->id == 'app-backend') {
            $this->addtomenu = Yii::$app->fronturlManager->createAbsoluteUrl(
                ['/content/front/page', 'slug' => $this->slug]
            );
        } else {
            $this->addtomenu = Yii::$app->urlManager->createAbsoluteUrl(['/content/front/page', 'slug' => $this->slug]);
        }
        parent::afterFind();
    }

    public function onView($event)
    {
        if (Yii::$app->params['viewscount']) {
            $event->sender->scenario = 'viewupdate';
            $event->sender->updateCounters(['views' => 1]);
        }
    }

    public function getUrl()
    {
        return Yii::$app->id == 'app-backend' ?
            Yii::$app->fronturlManager->createAbsoluteUrl(['content/front/page', 'slug' => $this->slug])
            : Yii::$app->urlManager->createAbsoluteUrl(['content/front/page', 'slug' => $this->slug]);
    }

    public static function getLastModify()
    {
        return (new \yii\db\Query())->select('max(updated) as max')
            ->from(self::tableName())
            ->scalar();
    }
}
