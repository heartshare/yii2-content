<?php

namespace insolita\content\models;

use insolita\things\behaviors\MetaModelBeh;
use insolita\things\behaviors\SlugModelBeh;
use insolita\content\modules\cover\models\Covers;
use insolita\content\modules\uploader\models\Attach;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use insolita\things\components\SActiveRecord;

/**
 * This is the model class for table "vg_news".
 *
 * @property integer $id
 * @property integer $cover_id
 * @property string  $name
 * @property string  $slug
 * @property string  $anons
 * @property string  $full
 * @property string  $full_parsed
 * @property integer $active
 * @property integer $views
 * @property string  $created
 * @property string  $updated
 * @property string  $metak
 * @property string  $metadesc
 * @property string  $publishto
 */
class News extends SActiveRecord
{
    public static $titledAttribute = 'name';
    public $gridDefaults = ['cover_id', 'name', 'anons', 'active', 'views', 'publishto', 'created'];
    public $ignoredAttributes = ['full', 'full_parsed', 'nocover', 'selcover'];
    public $taglist;
    public $nocover = 0;
    public $selcover;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%news}}';
    }

    public function transactions()
    {
        return [
            // scenario name => operation (insert, update or delete)
            'create' => self::OP_INSERT | self::OP_UPDATE,
            'update' => self::OP_INSERT | self::OP_UPDATE,
        ];
    }

    public static function modelTitle($type = 'plural')
    {
        $titles = [
            'single' => 'Новость',
            'plural' => 'Новости',
            'rod' => 'Новости',
            'vin' => 'Новость'
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
                'source_attributes' => ['name', 'anons', 'full_parsed']
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'anons'], 'required', 'message' => 'Праметр обязателен!'],
            [['active', 'views'], 'integer'],
            [['anons', 'full', 'full_parsed'], 'string'],
            [['created', 'updated'], 'safe'],
            [
                ['name'],
                'string',
                'max' => 200,
                'min' => 2,
                'tooShort' => 'Слишком длинное',
                'tooLong' => 'Слишком короткое'
            ],
            [['slug', 'metak', 'metadesc'], 'string', 'max' => 255],
            [
                ['slug'],
                'match',
                'pattern' => '/[^A-Za-z0-9\-_]/us',
                'not' => true,
                'message' => 'Допустимо использование только символов латинского алфавита и цифр'
            ],
            ['taglist', 'string'],
            [
                'taglist',
                'match',
                'pattern' => '/[^A-Za-zА-Яа-яЁё0-9\s\-\_,]/us',
                'not' => true,
                'message' => 'Допустимо использование только символов русского, английского алфавита, цифр и пробелов'
            ],
            ['selcover', 'exist', 'targetAttribute' => 'id', 'targetClass' => Covers::className()],
            ['nocover', 'in', 'range' => [0, 1]],
            ['active', 'default', 'value' => 1],
            ['publishto', 'default', 'value' => date('Y-m-d H:i:s', time())]
        ];
    }

    public function scenarios()
    {
        return [
            'create' => [
                'cover_id',
                'name',
                'slug',
                'active',
                'selcover',
                'taglist',
                'anons',
                'full',
                'full_parsed',
                'nocover',
                'metak',
                'metadesc',
                'publishto',
                'updated'
            ],
            'update' => [
                'cover_id',
                'name',
                'slug',
                'active',
                'selcover',
                'anons',
                'taglist',
                'full',
                'full_parsed',
                'nocover',
                'metak',
                'metadesc',
                'publishto', 'updated'
            ],
            'viewupdate' => ['views'],
            'toggle' => ['active'],
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
            'cover_id' => 'Обложка',
            'name' => 'Название',
            'slug' => 'SEO-Ссылка(автоматом)',
            'anons' => 'Анонс',
            'full' => 'Текст',
            'full_parsed' => 'Обработанный текст',
            'active' => 'Активно?',
            'views' => 'Просмотров',
            'created' => 'Создано',
            'updated' => 'Обновлено',
            'metak' => 'SEO-ключи',
            'metadesc' => 'SEO-описание',
            'publishto' => 'Опубликовать в ',
            'nocover' => 'Без обложки',
            'taglist' => 'Метки',
            'bymanager' => 'Автор',
            'selcover' => 'Выбрать из загруженых'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCover()
    {
        return $this->hasOne(Covers::className(), ['id' => 'cover_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaggeds()
    {
        return $this->hasMany(Tagged::className(), ['contid' => 'id'])->andOnCondition('{{%tagged}}.conttype="news"');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tags::className(), ['tag_id' => 'tagid'])->via('taggeds');
    }

    public function setTags($tags)
    {
        $this->populateRelation('tags', $tags);
        // $this->tags_count = count($tags);
    }

    public function prepareTags()
    {

        $tags = [];
        if (count($this->tags)) {
            foreach ($this->tags as $tag) {
                $tag->freeqDecr();
            }
            Tagged::deleteAll(['contid' => $this->id, 'conttype' => 'news']);
        }
        if (!empty($this->taglist)) {
            $taglist = explode(',', $this->taglist);
            foreach ($taglist as $name) {
                $tag = Tags::findTag($name);
                if ($tag) {
                    $tags[] = $tag;
                } else {
                    $tag = new Tags();
                    $tag->scenario = 'create';
                    $tag->tagname = $name;
                    if ($tag->save()) {
                        $tags[] = $tag;
                    }
                }
            }
        }

        $this->setTags($tags);
    }

    public function setCover($cover)
    {
        $this->populateRelation('cover', $cover);
    }

    /**
     * @var \insolita\content\modules\cover\models\Covers $cover
     **/


    public function prepareFulltext()
    {
        if ($this->full && $this->full != $this->getOldAttribute('full')) {
            //Helper::logs('start prepareFulltext');
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
                                \Yii::$app->getModule('content')->getModule('uploader')->fileHtmlTemplate,
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
                                    \Yii::$app->getModule('content')->getModule('uploader')->imagePreviewTemplate,
                                    [
                                        '{attach_id}' => $imgid,
                                        '{cssclass}' => $bb['CSS'],
                                        '{align}' => \Yii::$app->getModule('content')->getModule('uploader')
                                            ->getAlignStyle($bb['ALIGN']),
                                        '{alt}' => (trim($bb['ALT']) && $bb['ALT'] != '-')
                                            ? $bb['ALT']
                                            :
                                            'Фото к материалу:'
                                            . Html::encode($this->name),
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
                                        '{align}' => \Yii::$app->getModule('content')->getModule('uploader')
                                            ->getAlignStyle($bb['ALIGN']),
                                        '{alt}' => (trim($bb['ALT']) && $bb['ALT'] != '-')
                                            ? $bb['ALT']
                                            :
                                            'Фото к материалу:'
                                            . Html::encode($this->name),
                                        '{size}' => $bb['SIZE'],
                                        '{imgurl}' =>
                                            \Yii::$app->getModule('content')->getModule('uploader')->getImageUrlBySize(
                                                $bb['SIZE']
                                            ) . $images[$imgid]['filename'],
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

    public function beforeValidate()
    {
        if ($this->nocover) {
            if ($this->cover) {
                $this->unlink('cover', $this->cover);
            }
        } elseif ($this->selcover) {
            $this->cover_id = $this->selcover;
        }

        return parent::beforeValidate();
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
        }
        $this->prepareFulltext();
        if ($this->scenario !== 'viewupdate') {
            $this->updated = date('Y-m-d H:i:s', time());
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->scenario == 'update' or $this->scenario == 'create') {
            $this->prepareTags();

        }

        $relatedRecords = $this->getRelatedRecords();
        if (isset($relatedRecords['tags'])) {
            foreach ($relatedRecords['tags'] as $tag) {
                $this->link('tags', $tag, ['conttype' => 'news']);
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterFind()
    {
        parent::afterFind();
        if ($this->scenario !== 'usersearch') {
            $this->taglist = ($this->tags !== null)
                ? implode(',', \yii\helpers\ArrayHelper::getColumn($this->tags, 'tagname', false)) : '';
        }
    }

    public function onView($event)
    {
        if (Yii::$app->params['viewscount']) {
            $event->sender->scenario = 'viewupdate';
            $event->sender->updateCounters(['views' => 1]);
        }
    }

    public function showCover($addclass = 'imgingrid', $placeholder = true)
    {
        if ($this->cover) {
            return Html::img(
                Yii::$app->getModule('content')->getModule('cover')->cover_url . $this->cover->filename,
                [
                    'class' => 'img-thumbnail' . ($addclass ? ' ' . $addclass : ''),
                    'alt' => Html::encode($this->name)
                ]
            );
        } elseif ($placeholder) {
            return Html::img(
                'http://placehold.it/' . str_replace(',', 'x', \Yii::$app->params['thumb_size']) . '&text=No foto',
                [
                    'class' => 'img-thumbnail' . ($addclass ? ' ' . $addclass : ''),
                    'alt' => Html::encode($this->name)
                ]
            );
        } else {
            return '';
        }
    }

    public function showCoverMid($addclass = 'imgingrid', $placeholder = false)
    {
        if ($this->cover) {
            return Html::img(
                Yii::$app->getModule('content')->getModule('cover')->cover_midurl . $this->cover->filename,
                [
                    'class' => 'img-thumbnail' . ($addclass ? ' ' . $addclass : ''),
                    'alt' => Html::encode($this->name)
                ]
            );
        } elseif ($placeholder) {
            return Html::img(
                'http://placehold.it/' . str_replace(',', 'x', \Yii::$app->params['mid_size']) . '&text=No foto',
                [
                    'class' => 'img-thumbnail' . ($addclass ? ' ' . $addclass : ''),
                    'alt' => Html::encode($this->name)
                ]
            );
        } else {

        }
    }

    public function metaGenerate($page = 0)
    {
        $cache = Yii::$app->cache->get('meta_newslist' . $page);
        if ($cache == null) {
            $metadata = MetaData::find()->where(['model' => self::className(), 'model_id' => 'newslist/' . $page])->one(
            );
            if (!$metadata) {
                $metadata = new  MetaData();
                $metadata->scenario = 'create';
                $metadata->model = self::className();
                $metadata->model_id = 'newslist/' . $page;
            } else {
                $metadata->scenario = 'update';
            }
            $limit = Yii::$app->params['news_pp'];
            $offset = $limit * $page;
            $data = News::find()->select(['id', 'name', 'anons'])->indexBy('id')->where(
                '{{%news}}.active=:a AND {{%news}}.publishto<=:b',
                [':a' => 1, ':b' => date('Y-m-d H:i', time())]
            )->orderBy(['publishto' => SORT_DESC])->offset($offset)->limit($limit)->asArray()->all();
            $titles = ArrayHelper::map($data, 'id', 'name');
            $texts = ArrayHelper::map($data, 'id', 'anons');
            $data = array_reduce($titles, function ($res, $data) { return $res .= ' ' . $data; }) . array_reduce(
                    $texts,
                    function ($res, $data) { return $res .= ' ' . $data; }
                );
            $metadata->metadata = $data;
            $metadata->save(false);
            Yii::$app->cache->set(
                'meta_newslist' . $page,
                ['metaKey' => $metadata->metak, 'metaDesc' => $metadata->metad],
                3600
            );
            return ['metaKey' => $metadata->metak, 'metaDesc' => $metadata->metad];
        }
        return $cache;
    }

    public function metaGenerateByDate($date, $page = 0)
    {
        $cache = Yii::$app->cache->get('meta_newsbd' . $page);
        if ($cache == null) {
            $metadata = MetaData::find()->where(['model' => self::className(), 'model_id' => 'news-bydate/' . $page])
                ->one();
            if (!$metadata) {
                $metadata = new  MetaData();
                $metadata->scenario = 'create';
                $metadata->model = self::className();
                $metadata->model_id = 'news-bydate/' . $page;
            } else {
                $metadata->scenario = 'update';
            }
            $limit = Yii::$app->params['news_pp'];
            $offset = $limit * $page;
            $data = News::find()->select(['id', 'name', 'anons'])->indexBy('id')
                ->where(
                    '{{%news}}.active=:a AND {{%news}}.publishto<=:b',
                    [':a' => 1, ':b' => date('Y-m-d H:i', time())]
                )
                ->orderBy(['publishto' => SORT_DESC])->offset($offset)->limit($limit);
            $depth = explode('-', $date);
            if ($depth == 1) {
                $res = $data->andWhere(
                    ['between', '{{%news}}.publishto', $date . '-01 00:00:00', $date . '-31 23:59:59']
                )
                    ->asArray()->all();
            } else {
                $res = $data->andWhere(['between', '{{%news}}.publishto', $date . ' 00:00:00', $date . ' 23:59:59'])
                    ->asArray()->all();
            }

            $titles = ArrayHelper::map($res, 'id', 'name');
            $texts = ArrayHelper::map($res, 'id', 'anons');
            $data = array_reduce($titles, function ($res, $data) { return $res .= ' ' . $data; }) . array_reduce(
                    $texts,
                    function ($res, $data) { return $res .= ' ' . $data; }
                );
            $metadata->metadata = $data;
            $metadata->save(false);
            Yii::$app->cache->set(
                'meta_newsbd' . $page,
                ['metaKey' => $metadata->metak, 'metaDesc' => $metadata->metad],
                3600
            );
            return ['metaKey' => $metadata->metak, 'metaDesc' => $metadata->metad];
        }
        return $cache;
    }

    public function getUrl()
    {
        return Yii::$app->id == 'app-backend' ? Yii::$app->fronturlManager->createAbsoluteUrl(
            ['content/front/news', 'slug' => $this->slug]
        )
            : Yii::$app->urlManager->createAbsoluteUrl(['content/front/news', 'slug' => $this->slug]);

    }

    public static function getLastModify()
    {
        return (new \yii\db\Query())->select('max(updated) as max')
            ->from(self::tableName())
            ->where(['active' => 1])
            ->andWhere(new Expression('publishto<=NOW()'))
            ->scalar();
    }
}
