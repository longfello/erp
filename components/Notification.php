<?php

namespace app\components;

use app\models\Fs;
use machour\yii2\notifications\NotificationsModule;
use Yii;
use machour\yii2\notifications\models\Notification as BaseNotification;

class Notification extends BaseNotification
{
	/**
	 * A new message notification
	 */
	const KEY_NEW_FILE = 'new_file';

	/**
	 * @var array Holds all usable notifications
	 */
	public static $keys = [
		self::KEY_NEW_FILE,
	];

	/**
	 * @inheritdoc
	 */
	public function getTitle()
	{
		switch ($this->key) {
			case self::KEY_NEW_FILE:
				return 'Новый файл загружен';
		}
	}

	/**
	 * @inheritdoc
	 */
	public function getDescription()
	{
		switch ($this->key) {
			case self::KEY_NEW_FILE:
				$model = Fs::findOne($this->key_id);
				if ($model) {
					return Yii::$app->view->renderFile('@app/views/notify/file.php', ['model' => $model]);
				} else {
					$this->delete();
					return '';
				}
		}
	}

	/**
	 * @inheritdoc
	 */
	public function getRoute()
	{
		switch ($this->key) {
			case self::KEY_NEW_FILE:
				return ['/backend/knowledge/index', 'root' => $this->key_id];

		};
	}


	/**
	 * Creates a notification
	 *
	 * @param string $key
	 * @param integer $user_id The user id that will get the notification
	 * @param string $key_id The foreign instance id
	 * @param string $type
	 * @return bool Returns TRUE on success, FALSE on failure
	 * @throws \Exception
	 */
	public static function notify($key, $user_id, $key_id = null, $type = self::TYPE_DEFAULT)
	{
		if ($key == self::KEY_NEW_FILE) {
			if (!$model = Fs::findOne(['id' => $key_id])){
				Notification::deleteAll(['key' => self::KEY_NEW_FILE, 'key_id' => $key_id]);
				return '';
			}
		}
		return parent::notify($key, $user_id, $key_id, $type);
	}
}