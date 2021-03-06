<?php

namespace app\forms;

use yii\base\Model;

class RequestForm extends Model
{
	public $user_id;
	public $query;

	public function init() {
		$userData   = \Yii::$app->session->get('user-info', []);
		$this->user_id = isset($userData['id'])?$userData['id']:false;
		parent::init(); // TODO: Change the autogenerated stub
	}

	public function attributeLabels() {
		return [
			'query' => 'Комментарий'
		];
	}

	public function rules()
	{
		return [
			[['user_id'], 'required'],
			[['query'], 'required'],
			[['query'], 'string', 'max' => 255],
			[['user_id', 'query'], 'safe']
		];
	}
}
