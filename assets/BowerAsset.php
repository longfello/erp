<?php
namespace app\assets;
use yii\web\AssetBundle;
/**
 * @author forecho <caizhenghai@gmail.com>
 */
class BowerAsset extends AssetBundle
{
	public $sourcePath = '@bower';
	public $baseUrl = '@bower';
	public $css = [];
	public $js = [
		'twemoji/2/twemoji.min.js',
		'mark.js/dist/jquery.mark.min.js',
//		'clipboard/dist/clipboard.min.js',
	];
}