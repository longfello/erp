<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
	    "/css/twemoji-picker.css",
        "/css/main.css",
        "/css/LivIconsEvo.css",
        "/css/slick.css",
        "/css/main.css",
        "/css/style-mark.css",
        "/css/style-pavel.css",
        "/css/bootstrap-theme-jarvis.css",
        "/css/framework/jquery.mCustomScrollbar.min.css",
	    "/js/framework/bootstrap-tagsinput-latest/dist/bootstrap-tagsinput.css",
    ];
    public $js = [
        "/js/tools/snap.svg-min.js",
        "/js/tools/TweenMax.min.js",
        "/js/tools/DrawSVGPlugin.min.js",
        "/js/tools/MorphSVGPlugin.min.js",
        "/js/tools/verge.min.js",
        "/js/LivIconsEvo.defaults.js",
        "/js/LivIconsEvo.min.js",
        "/js/jquery.limit.js",
        "/js/slick.min.js",
        "/js/framework/jquery.mCustomScrollbar.concat.min.js",
        "/js/main.js",
        "/js/twemoji-picker.js",
        "/js/share-this.js",
	    "/js/framework/bootstrap-tagsinput-latest/dist/bootstrap-tagsinput.js",
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
	    'yii\bootstrap\BootstrapPluginAsset',
	    'rmrevin\yii\fontawesome\AssetBundle',
	    '\wfcreations\simplelineicons\AssetBundle',
	    'mgcode\assets\HistoryJsAsset',
	    'mgcode\assets\JsCookieAsset',
	    'kartik\growl\GrowlAsset',
	    'app\assets\BowerAsset'
    ];
}
