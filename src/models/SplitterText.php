<?php
namespace kilyakus\package\splitter\models;

use Yii;
use kilyakus\package\gui\behaviors\GuiBehavior;
use kilyakus\package\translate\behaviors\TranslateBehavior;
use kilyakus\validator\escape\EscapeValidator;
use bin\admin\models\Photo;

class SplitterText extends \kilyakus\modules\components\ActiveRecord
{
    public $splitter = [];

    public $module;

    public static function tableName()
    {
        return 'splittertext';
    }

    public function behaviors()
    {
        return [
            'guiBehavior' => [
                'class' => GuiBehavior::className(),
                'model' => Photo::className(),
                'isRoot' => IS_MODER,
                'identity' => Yii::$app->user->identity->id,
            ],
            // 'translateBehavior' => TranslateBehavior::className(),
        ];
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