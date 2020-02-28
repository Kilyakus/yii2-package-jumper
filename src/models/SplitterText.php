<?php
namespace kilyakus\package\splitter\models;

use Yii;
use kilyakus\validator\escape\EscapeValidator;

class JumperText extends \kilyakus\modules\components\ActiveRecord
{
    public $translations = [];

    public static function tableName()
    {
        return 'splittertext';
    }

    public function rules()
    {
        return [
            [['title', 'short', 'text', 'description'], 'trim'],
            [['title'], 'string', 'max' => 255],
            [['short', 'text', 'description'], 'safe'],
            [['title'], EscapeValidator::className()],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => Yii::t('easyii', 'Title'),
            'short' => Yii::t('easyii', 'Short'),
            'text' => Yii::t('easyii', 'Text'),
            'description' => Yii::t('easyii', 'Description'),
        ];
    }
}