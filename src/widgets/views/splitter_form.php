<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Tabs;
use kilyakus\package\translate\widgets\TranslateForm;
use kilyakus\package\splitter\models\SplitterText;
use kilyakus\widget\flag\Flag;
use kilyakus\widget\redactor\Redactor;
?>

<?php 
$activeTab = true;
foreach ($splitters as $splitterKey => $splitter){

    // foreach (Yii::$app->urlManager->languages as $key => $translation){

        $model = SplitterText::findOne($splitterKey);
        $content = '';
        foreach ($attributes as $attribute) {
            $content .= $form->field($model, 'splitter['.$splitterKey.']['.$attribute.']')->widget(Redactor::className(),array_merge($redactorOptions,['options' => ['value' => $model->{$attribute}]]))->label(Yii::t('easyii',ucwords($attribute)));
        }
        $content .= \bin\admin\widgets\ModulePhotos\ModulePhotos::widget(['model' => $model]);

        // $languages[$key] = [
        //     'label' => Flag::widget(['pluginSupport' => false, 'flag' => $translation, 'options' => ['class' => 'img-circle', 'width' => 22, 'height' => 22]]),
        //     'content' => $content,
        //     'active' => $translation == Yii::$app->language
        // ];

        // $content = Html::tag('div', Tabs::widget(['encodeLabels' => false, 'items' => $languages]), ['class' => 'language-tabs']);
    // }

    $splitters[$splitterKey] = [
        'label' => $form->field($model, 'splitter['.$splitterKey.'][title]')->textInput(['value' => $model->title])->label(Yii::t('easyii',ucwords('title'))),
        'content' => $content,
        'active' => $activeTab == true
    ];

    $activeTab = false;
}

// foreach (Yii::$app->urlManager->languages as $key => $translation){

    $model = new SplitterText();

    $content = '';
    foreach ($attributes as $attribute) {
        $content .= $form->field($model, 'splitter[new]['.$attribute.']')->widget(Redactor::className(),$redactorOptions)->label(Yii::t('easyii',ucwords($attribute)));
    }
    $content .= \bin\admin\widgets\ModulePhotos\ModulePhotos::widget(['model' => $model]);

    // $languages[$key] = [
    //     'label' => Flag::widget(['pluginSupport' => false, 'flag' => $translation, 'options' => ['class' => 'img-circle', 'width' => 22, 'height' => 22]]),
    //     'content' => $content,
    //     'active' => $translation == Yii::$app->language
    // ];

    // $content = Html::tag('div', Tabs::widget(['encodeLabels' => false, 'items' => $languages]), ['class' => 'language-tabs']);
// }

$splitters['new'] = [
    'label' => $form->field($model, 'splitter[new][title]')->label(Yii::t('easyii',ucwords('title'))),
    'content' => $content,
    'active' => $activeTab == true
];

echo Html::tag('div', Tabs::widget(['encodeLabels' => false, 'items' => $splitters]), ['class' => 'splitter-tabs']);

$css = <<< CSS
.splitter-tabs {width:100%;position:relative;}
.splitter-tabs > .nav-tabs {margin:0;border-bottom:1px solid #ebedf2;}
.splitter-tabs > .nav-tabs > li > a {padding:10px 10px 0 10px;}
.splitter-tabs > .nav-tabs > li > a .form-group {margin:0px;}
.splitter-tabs > .nav-tabs > li > a:hover {background-color:#ebedf2;border-color:transparent transparent #ebedf2;}
.splitter-tabs > .nav-tabs > li.active > a,
.splitter-tabs > .nav-tabs > li.active > a:hover,
.splitter-tabs > .nav-tabs > li.active > a:focus {background:#f7f8fa;border-color:#ebedf2;border-bottom-color:transparent;}
.splitter-tabs > .tab-content {margin-bottom:2rem;padding:1rem;background:#f7f8fa;border:1px solid #ebedf2;border-top:0;border-radius:0 0 4px 4px;box-shadow:0px 0px 13px 0px rgba(82, 63, 105, 0.05);}
.splitter-tabs > .tab-content .language-tabs > .tab-content {margin-bottom:0;box-shadow:none;}
.splitter-tabs > .tab-content .form-group {margin-bottom:0;}
CSS;
$this->registerCss($css, ["type" => "text/css"], "behavior-split" ); ?>