<?php
namespace kilyakus\package\translate\behaviors;

use Yii;
use yii\db\ActiveRecord;
use kilyakus\package\translate\models\JumperText;

class JumperBehavior extends \yii\base\Behavior
{
    private $_model;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    public function beforeInsert()
    {
        self::beforeJumper();
    }

    public function afterInsert()
    {
        self::afterJumper();
    }

    public function beforeUpdate()
    {
        self::beforeJumper();
    }

    public function afterUpdate()
    {
        self::afterJumper();
    }

    public function beforeJumper()
    {
        if($this->translateText->load(Yii::$app->request->post())){

            if($post = Yii::$app->request->post('JumperText')['translations']){

                $current = $post[Yii::$app->language];

                if($current['title']){$this->owner->title = $current['title'];}
                if($current['short']){$this->owner->short = $current['short'];}
                if($current['text']){$this->owner->text = $current['text'];}
                if($current['description']){$this->owner->description = $current['description'];}

                foreach ($post as $lang => $translation)
                {

                    if(empty($current['title']) && !empty($translation['title'])){
                        $this->owner->title = $translation['title'];
                    }

                    if(empty($current['short']) && !empty($translation['short'])){
                        $this->owner->short = $translation['short'];
                    }

                    if(empty($current['text']) && !empty($translation['text'])){
                        $this->owner->text = $translation['text'];
                    }

                    if(empty($current['description']) && !empty($translation['description'])){
                        $this->owner->description = $translation['description'];
                    }
                }
            }
        }
    }

    public function afterJumper()
    {
        if($this->translateText->load(Yii::$app->request->post())){

            if($post = Yii::$app->request->post('JumperText')['translations']){

                foreach ($post as $lang => $translation)
                {
                    if(!$translate = JumperText::find()->where(['class' => $this->owner::className(), 'item_id' => $this->owner->primaryKey, 'lang' => $lang])->one()){
                        $translate = new JumperText();
                    }

                    $translate->load(['JumperText' => $translation]);
                    $translate->class = $this->owner::className();
                    $translate->item_id = $this->owner->primaryKey;
                    $translate->lang = $lang;
                    $translate->save();
            
                }

                foreach ($post as $lang => $translation)
                {
                    $translate = JumperText::find()->where(['class' => $this->owner::className(), 'item_id' => $this->owner->primaryKey, 'lang' => $lang])->one();
                    
                    if($translate && !$translation['title'] && isset($this->owner->title)){
                        $translate->title = $this->owner->title;
                        $translate->update();
                    }
                }
            }
        }
    }

    public function afterDelete()
    {
        JumperText::deleteAll(['class' => get_class($this->owner), 'item_id' => $this->owner->primaryKey]);
    }

    public function getJumper()
    {
        $translate = $this->owner->hasOne(JumperText::className(), ['item_id' => $this->owner->primaryKey()[0]])->where(['class' => get_class($this->owner), 'lang' => Yii::$app->language]);

        if(!$translate->one()){
            $translate = $this->owner;
        }

        return $translate;
    }

    public function getJumperText()
    {
        if(!$this->_model)
        {
            $this->_model = $this->owner->translate;
            if(!$this->_model){
                $this->_model = new JumperText([
                    'class' => get_class($this->owner),
                    'item_id' => $this->owner->primaryKey,
                    'lang' => Yii::$app->language
                ]);
            }
        }

        return $this->_model;
    }
}