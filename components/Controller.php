<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 21.11.16
 * Time: 16:30
 */

namespace app\components;


use yii\base\InlineAction;

class Controller extends \yii\web\Controller {
	const LAYOUT_AJAX    = '/ajax';
	const LAYOUT_LOGIN   = '/login';
	const LAYOUT_BACKEND = '/main';

	public $layout = self::LAYOUT_LOGIN;

	public function beforeAction($action){
		/** @var $action InlineAction */
		if (\Yii::$app->user->isGuest) {
			if (!(($this->id == 'site') && (in_array($action->id, ['index', 'request', 'share', 'download', 'sharefile']))) && !(($this->id == 'site') && (\Yii::$app->request->isAjax))){
				\Yii::$app->response->redirect('/');
			}
		}

		$this->layout = (\Yii::$app->request->isAjax)?self::LAYOUT_AJAX:$this->layout;

		return parent::beforeAction($action);
	}
}