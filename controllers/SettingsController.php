<?php
namespace app\controllers;

use app\models\User;
use dektrium\user\controllers\AdminController as BaseAdminController;
use yii\base\ViewContextInterface;

/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 07.12.16
 * Time: 13:18
 */


class SettingsController extends \dektrium\user\controllers\SettingsController implements ViewContextInterface {

	/**
	 * Displays page where user can update account settings (username, email or password).
	 *
	 * @return string|\yii\web\Response
	 */
	public function actionAccount()
	{
		$this->view->title = 'Аккаунт';
		$model = User::findOne(['id' => \Yii::$app->user->id]);
		/** @var $model User */
		$event = $this->getFormEvent($model);

		$this->performAjaxValidation($model);

		$this->trigger(self::EVENT_BEFORE_ACCOUNT_UPDATE, $event);
		if ($model->load(\Yii::$app->request->post())) {
			if ($model->bithday) {
				$model->bithday = date('Y-m-d', strtotime($model->bithday));
			}
//			var_dump($model->bithday); die();
			if ($model->save()) {
				\Yii::$app->session->setFlash('success', \Yii::t('user', 'Your account details have been updated'));
				$this->trigger(self::EVENT_AFTER_ACCOUNT_UPDATE, $event);
				return $this->refresh();
			}
		}

		return $this->render('account', [
			'model' => $model,
		]);
	}

	public function getViewPath()
	{
		return \Yii::getAlias('@app/views/user/settings');
	}
}