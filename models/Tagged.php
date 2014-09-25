<?php

namespace insolita\content\models;

use Yii;
use \insolita\things\components\SActiveRecord;
/**
 * This is the model class for table "vg_tagged".
 *
 * @property integer $tagid
 * @property integer $contid
 * @property string  $conttype
 *
 * @property Article $cont
 * @property Tags    $tag
 */
class Tagged extends SActiveRecord
{
    public static $titledAttribute = 'name';
    public  $gridDefaults = ['tagid', 'contid', 'conttype'];
    public  $ignoredAttributes = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tagged}}';
    }

    public static function modelTitle($type = 'plural')
    {
        $titles = [
            'single' => 'Tagged',
            'plural' => 'Tagged',
            'rod' => 'Tagged',
            'vin' => 'Tagged'
        ];
        return isset($titles[$type]) ? $titles[$type] : $titles['plural'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tagid', 'contid', 'conttype'], 'required'],
            [['tagid', 'contid'], 'integer'],
            [['conttype'], 'string']
        ];
    }

    public function scenarios()
    {
        return [
            'create' => ['tagid', 'contid', 'conttype'],
            'update' => ['tagid', 'contid', 'conttype'],
            'delete' => ['active']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tagid' => 'Tagid',
            'contid' => 'Contid',
            'conttype' => 'Conttype',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCont()
    {
        return $this->hasOne(Article::className(), ['id' => 'contid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasOne(News::className(), ['id' => 'contid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(Tags::className(), ['tag_id' => 'tagid']);
    }

    public function beforeSave($insert)
    {
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {

        if ($insert) {
            $this->tag->freeqRecount();
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function beforDelete()
    {
        parent::beforeDelete();
    }

}
