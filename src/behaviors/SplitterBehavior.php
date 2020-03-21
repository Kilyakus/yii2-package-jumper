<?php
namespace kilyakus\package\splitter\behaviors;

use Yii;
use yii\db\ActiveRecord;
use kilyakus\package\splitter\models\SplitterText;

class SplitterBehavior extends \yii\base\Behavior
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
        self::beforeSplitter();
    }

    public function afterInsert()
    {
        self::afterSplitter();
    }

    public function beforeUpdate()
    {
        self::beforeSplitter();
    }

    public function afterUpdate()
    {
        self::afterSplitter();
    }

    public function beforeSplitter()
    {
        // if($this->splitterText->load(Yii::$app->request->post())){

        //     if($post = Yii::$app->request->post('SplitterText')['splitter']){

        //     }
        // }
    }

    public function afterSplitter()
    {
        if($this->splitterText->load(Yii::$app->request->post())){

            if($post = Yii::$app->request->post('SplitterText')['splitter']){

                $ownerClass = $this->owner;

                foreach ($post as $splitterKey => $data)
                {
                    if($data['title'] || $data['short'] || $data['text'] || $data['description']){
                        if($splitterKey == 0 || !($splitter = SplitterText::find()->where(['splittertext_id' => $splitterKey, 'class' => $ownerClass::className(), 'item_id' => $this->owner->primaryKey])->one())){
                            $splitter = new SplitterText();
                        }
                        $splitter->load(['SplitterText' => $data]);
                        $splitter->class = $ownerClass::className();
                        $splitter->item_id = $this->owner->primaryKey;
                        $splitter->save();
                    }
                }

                foreach ($post as $splitterKey => $data)
                {
                    $splitter = SplitterText::find()->where(['splittertext_id' => $splitterKey, 'class' => $ownerClass::className(), 'item_id' => $this->owner->primaryKey])->one();

                    if($splitter && (empty($data['short']) && empty($data['text']) && empty($data['description'])) || $splitter && empty($data['title'])){
                        $splitter->delete();
                    }
                }
            }
        }
    }

    public function afterDelete()
    {
        SplitterText::deleteAll(['class' => get_class($this->owner), 'item_id' => $this->owner->primaryKey]);
    }

    public function getSplitter()
    {
        $splitter = $this->owner->hasOne(SplitterText::className(), ['item_id' => $this->owner->primaryKey()[0]])->where(['class' => get_class($this->owner)]);

        if(!$splitter->one()){
            $splitter = $this->owner;
        }

        return $splitter;
    }

    public function getSplitterText()
    {
        if(!$this->_model)
        {
            $this->_model = $this->owner->splitter;
            if(!$this->_model){
                $this->_model = new SplitterText([
                    'class' => get_class($this->owner),
                    'item_id' => $this->owner->primaryKey,
                ]);
            }
        }

        return $this->_model;
    }
}