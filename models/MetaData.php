<?php

namespace insolita\content\models;

use insolita\things\behaviors\MetaModelBeh;
use insolita\things\components\SActiveRecord;
use Yii;

/**
 * This is the model class for table "vg_meta".
 *
 * @property integer $id
 * @property string  $route
 * @property string  $metak
 * @property string  $metad
 * @property string  $updated
 */
class MetaData extends SActiveRecord
{
    public static $titledAttribute = 'name';
    public  $gridDefaults = ['id', 'model_id', 'model', 'metak', 'metad', 'updated'];
    public  $ignoredAttributes = [];

    public $metadata;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%meta}}';
    }

    public static function modelTitle($type = 'plural')
    {
        $titles = [
            'single' => 'MetaData',
            'plural' => 'MetaData',
            'rod' => 'MetaData',
            'vin' => 'MetaData'
        ];
        return isset($titles[$type]) ? $titles[$type] : $titles['plural'];
    }

    public function behaviors()
    {
        return [
            'meta' => [
                'class' => MetaModelBeh::className(),
                'metakey_attribute' => 'metak',
                'metadesc_attribute' => 'metad',
                'source_attributes' => ['metadata']
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model', 'metak', 'metad'], 'required'],
            [['updated'], 'safe'],
            [['metak', 'metad', 'model', 'model_id',], 'string', 'max' => 255],
            [['model_id'], 'safe']
        ];
    }

    public function scenarios()
    {
        return [
            'create' => ['id', 'model', 'model_id', 'metak', 'metad', 'updated'],
            'update' => ['id', 'model', 'model_id', 'metak', 'metad', 'updated'],
            'delete' => ['active']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model_id' => 'Model ID',
            'model' => 'Model class',
            'metak' => 'Metak',
            'metad' => 'Metad',
            'updated' => 'Обновлено',
        ];
    }


    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {

        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {

        return parent::afterSave($insert, $changedAttributes);
    }

}
