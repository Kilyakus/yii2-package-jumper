<?php
namespace kilyakus\package\splitter\widgets;

use Yii;
use yii\base\Widget;
use yii\base\InvalidConfigException;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Tabs;
use kilyakus\package\splitter\models\SplitterText;
use kilyakus\widget\redactor\Redactor;

class SplitterForm extends Widget
{
    public $form;

    public $model;

    public $attributes = [
        'short',
        'text',
        'description'
    ];

    public $redactorOptions = [
        // 'usePresets' => false,
        'pluginOptions' => [
            'tabsize' => 2,
            'minHeight' => 150,
            'maxHeight' => 400,
            'focus' => true,
            // 'toolbar' => [
            //     ['style1', ['style', 'clear', 'hr']],
            //     ['insert', ['link', 'picture', 'video', 'table']],
            // ],
        ],
    ];

    public $uploadUrl;

    protected $redactorPresets = [
        'theme' => Redactor::THEME_SIMPLE,
        'fullscreen' => true,
        'codemirror' => true,
        'emoji' => true,
        'pluginOptions' => [
            'tabsize' => 2,
            'minHeight' => 150,
            'maxHeight' => 400,
            'focus' => true,
        ],
    ];

    public function init()
    {
        parent::init();

        if (empty($this->model)) {
            throw new InvalidConfigException('Required `model` param isn\'t set.');
        }

        if (empty($this->attributes)) {
            throw new InvalidConfigException('Required `attributes` param isn\'t set.');
        }

        if(!empty($this->redactorOptions)){

            $redactorOptions = $this->redactorOptions;

            foreach ($this->redactorPresets as $attribute => $option) {
                if($attribute == 'toolbar' && $this->redactorOptions[$attribute]){
                    foreach ($this->redactorOptions[$attribute] as $pluginKey => $pluginAttribute) {
                        foreach ($option as $optionKey => $optionAttribute) {
                            if($pluginAttribute[0] == $optionAttribute[0]){
                                $this->redactorPresets[$attribute][$optionKey] = $this->redactorOptions[$attribute][$pluginKey];
                                unset($this->redactorOptions[$attribute][$pluginKey]);
                            }
                        }
                    }
                }
            }

            $this->redactorOptions = ArrayHelper::merge($this->redactorPresets, $this->redactorOptions);
        }

        $this->redactorOptions['uploadUrl'] = Url::to(['/redactor/upload/images', 'dir' => '/redactor/' . Yii::$app->user->id . '/images']);
    }

    public function run()
    {
        $splitters = SplitterText::find()->where(['class' => $this->model::className(), 'item_id' => $this->model->primaryKey])->asArray()->all();
        $splitters = ArrayHelper::index($splitters, 'splittertext_id');

        if(get_class($this->model->splitterText) == get_class(new SplitterText())){
            $model = $this->model->splitterText;
        }else{
            $model = new SplitterText();
        }

        $model->splitter = $splitters;

        echo $this->render('splitter_form', [
            'form' => $this->form,
            'model' => $model,
            'splitters' => $splitters,
            'attributes' => $this->attributes,
            'redactorOptions' => $this->redactorOptions,
        ]);
    }

}