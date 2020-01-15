<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "access_query".
 *
 * @property integer $user_id
 * @property string $query
 * @property string $answer
 */
class AccessQuery extends \yii\db\ActiveRecord
{
	public $allow = false;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'access_query';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'query'], 'required'],
            [['user_id'], 'integer'],
            [['query', 'answer'], 'string'],
            [['allow'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'Пользователь',
            'query'   => 'Запрос',
            'answer'  => 'Ответ',
            'allow'   => 'Доступ',
        ];
    }
}
