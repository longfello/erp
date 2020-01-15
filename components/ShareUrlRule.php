<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 30.05.16
 * Time: 11:44
 */

namespace app\components;

use app\models\FileshareFile;
use app\models\Fs;
use app\models\FsFile;
use yii\web\UrlRuleInterface;
use yii\base\Object;

class ShareUrlRule extends Object implements UrlRuleInterface {

  public function createUrl($manager, $route, $params){
  	return false;
  }

  public function parseRequest($manager, $request)
  {
	  $info = $request->getPathInfo();

	  if ($info) {
		  $chains = explode('/', $info);
		  if (count($chains) == 2 && $chains[0] == 'files'){
			  $hash = $chains[1];
			  $model = FileshareFile::findOne(['share_hash' => $hash]);
			  if ($model){
			    return ['site/sharefile', ['model' => $model]];
			  }
		  }

		  list($hash, $action) = explode('/', $info.'/view');
		  $action = $action?$action:'view';

		  $modelMain = Fs::findOne(['share_hash' => $hash]);
		  $modelAll  = Fs::findOne(['share_hash_plus' => $hash]);

		  if ($modelMain || $modelAll) {
			  $type  = $modelMain?FsFile::TYPE_MAIN:FsFile::TYPE_ALL;
			  $model = $modelMain?$modelMain:$modelAll;
			  return ['site/share', ['type' => $type, 'model' => $model, 'action' => $action]];
		  }
	  }
      return false;  // данное правило не применимо
  }
}