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
 * This is the model class for table "vg_article".
 *
 * @property integer  $id
 * @property integer  $cat_id
 * @property integer  $cover_id
 * @property string   $name
 * @property string   $slug
 * @property string   $anons
 * @property string   $full
 * @property string   $full_parsed
 * @property integer  $active
 * @property integer  $views
 * @property string   $created
 * @property string   $updated
 * @property string   $metak
 * @property string   $metadesc
 * @property string   $publishto
 * @var Category      $cat
 *
 * @property Tagged[] $taggeds
 * @property Tags     tags
 */
class Article extends SActiveRecord
{
    public static $titledAttribute = 'name';
    public $gridDefaults = ['cat_id', 'cover_id','addtomenu', 'name', 'active', 'views', 'publishto', 'created'];
    public $ignoredAttributes = ['full', 'full_parsed', 'nocover', 'selcover'];
    public $taglist;
    public $nocover = 0;
    public $selcover;
    public $addtomenu;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%article}}';
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
            'single' => 'Статья',
            'plural' => 'Статьи',
            'rod' => 'Статьи',
            'vin' => 'Статью'
        ];
        return isset($titles[$type]) ? $titles[$type] : $titles['plural'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_id', 'name', 'anons'], 'required'],
            [['cat_id', 'cover_id', 'active', 'views'], 'integer'],
            [['anons', 'full', 'full_parsed'], 'string'],
            [['created', 'updated', 'publishto'], 'safe'],
            [['name'], 'string', 'max' => 200, 'min' => 3],
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
            ['cover_id', 'default', 'value' => null]
        ];
    }

    public function scenarios()
    {
        return [
            'create' => [
                'cat_id',
                'cover_id',
                'selcover',
                'name',
                'slug',
                'anons',
                'taglist',
                'full',
                'full_parsed',
                'active',
                'views',
                'created',
                'updated',
                'metak',
                'metadesc',
                'publishto'
            ],
            'update' => [
                'cat_id',
                'cover_id',
                'selcover',
                'name',
                'slug',
                'anons',
                'taglist',
                'full',
                'full_parsed',
                'active',
                'views',
                'created',
                'updated',
                'metak',
                'metadesc',
                'publishto'
            ],
            'delete' => ['id'],
            'viewupdate' => ['views'],
            'toggle' => ['active']
        ];
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
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cat_id' => 'Категория',
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
            'selcover' => 'Выбрать из загруженых',
            'bymanager' => 'Автор',
            'addtomenu' => 'Ссылка для меню'
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
        return $this->hasMany(Tagged::className(), ['contid' => 'id'])->andOnCondition('conttype="art"');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCat()
    {
        return $this->hasOne(Category::className(), ['id' => 'cat_id']);
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
            Tagged::deleteAll(['contid' => $this->id, 'conttype' => 'art']);
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
     * @var \insolita\content\models\Covers $cover
     **/


    public function prepareFulltext()
    {
        if ($this->full && $this->full != $this->getOldAttribute('full')) {
            // Helper::logs('startFullParsed');
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
            if ($this->active) {
                $this->cat->updateCounters(['cnt' => 1]);
            }
            if (!$this->publishto) {
                $this->publishto = date('Y-m-d H:i:s', time());
            }
        }
        if ($this->scenario !== 'viewupdate') {
            $this->updated = date('Y-m-d H:i:s', time());
        }
        $this->prepareFulltext();

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->scenario == 'update' or $this->scenario == 'create') {
            $this->prepareTags();

        }
        if ($this->scenario == 'toggle' || $this->scenario == 'update') {
            if ($this->active == 1 && $changedAttributes['active'] == 0) {
                $this->cat->updateCounters(['cnt' => 1]);
            }
            if ($this->active == 0 && $changedAttributes['active'] == 1) {
                $this->cat->updateCounters(['cnt' => -1]);
            }
        }
        $relatedRecords = $this->getRelatedRecords();
        if (isset($relatedRecords['tags'])) {
            foreach ($relatedRecords['tags'] as $tag) {
                $this->link('tags', $tag, ['conttype' => 'art']);
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
        $this->addtomenu = $this->getUrl();
    }

    public function onView($event)
    {
        if (Yii::$app->params['viewscount']) {
            $event->sender->scenario = 'viewupdate';
            $event->sender->updateCounters(['views' => 1]);
        }
    }

    public function beforeDelete()
    {
        //Category::updateAllCounters(['cnt'=>-1],['id'=>$this->cat_id]);
        if ($this->cat && $this->active == 1) {
            $this->cat->updateCounters(['cnt' => -1]);
        }
        return parent::beforeDelete();
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
                    'class' => ($addclass ? ' ' . $addclass : ''),
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

    public function getUrl()
    {
        return Yii::$app->id == 'app-backend'
            ?
            Yii::$app->fronturlManager->createAbsoluteUrl(
                ['content/front/article', 'slug' => $this->slug, 'category' => $this->cat->slug]
            )
            :
            Yii::$app->urlManager->createAbsoluteUrl(
                ['content/front/article', 'slug' => $this->slug, 'category' => $this->cat->slug]
            );
    }

    public static function getLastModify()
    {
        return (new \yii\db\Query())->select('max(updated) as max')
            ->from(self::tableName())
            ->where(['active' => 1])
            ->andWhere(new Expression('publishto<=NOW()'))
            ->scalar();
    }

    public function metaGenerateByDate($date, $page = 0)
    {
        $cache = Yii::$app->cache->get('meta_artsbd' . $date . $page);
        if ($cache == null) {
            $metadata = MetaData::find()->where(
                ['model' => self::className(), 'model_id' => 'articles-bydate/' . $date . $page]
            )->one();
            if (!$metadata) {
                $metadata = new  MetaData();
                $metadata->scenario = 'create';
                $metadata->model = self::className();
                $metadata->model_id = 'articles-bydate/' . $date . $page;
            } else {
                $metadata->scenario = 'update';
            }
            $limit = Yii::$app->params['articles_pp'];
            $offset = $limit * $page;
            $data = Article::find()->select(['id', 'name', 'anons', 'publishto'])->indexBy('id')
                ->where(
                    '{{%article}}.active=:a AND {{%article}}.publishto<=:b',
                    [':a' => 1, ':b' => date('Y-m-d H:i', time())]
                )->orderBy(['{{%article}}.publishto' => SORT_DESC])->offset($offset)->limit($limit);
            $depth = explode('-', $date);
            if (count($depth) == 2) {
                $res = $data->andWhere(
                    ['between', '{{%article}}.publishto', $date . '-01 00:00:00', $date . '-31 23:59:59']
                )
                    ->asArray()->all();
            } else {
                $res = $data->andWhere(['between', '{{%article}}.publishto', $date . ' 00:00:00', $date . ' 23:59:59'])
                    ->asArray()->all();
            }
            $titles = ArrayHelper::map($res, 'id', 'name');
            $texts = ArrayHelper::map($res, 'id', 'anons');
            $data = array_reduce($titles, function ($res, $data) { return $res .= $data . ' '; }) . array_reduce(
                    $texts,
                    function ($res, $data) { return $res .= $data . ' '; }
                );

            $metadata->metadata = $data;
            $metadata->save(false);
            Yii::$app->cache->set(
                'meta_artsbd' . $date . $page,
                ['metaKey' => $metadata->metak, 'metaDesc' => $metadata->metad],
                3600
            );
            return ['metaKey' => $metadata->metak, 'metaDesc' => $metadata->metad];
        }
        return $cache;
    }

}
