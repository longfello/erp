<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 25.11.16
 * Time: 14:43
 */

namespace app\controllers;


use app\components\Controller;
use app\models\Notification;
use dektrium\user\controllers\SecurityController;

class UserController extends SecurityController  {
	public $layout = Controller::LAYOUT_LOGIN;

	public function actionLogin(){
		if (!\Yii::$app->user->isGuest) {
			\Yii::$app->user->logout();
		}
		$this->redirect('/');
	}

	public function actionBlocked(){
		return $this->render('//user/blocked');
	}
}