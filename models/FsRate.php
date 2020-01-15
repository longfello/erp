<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fs_rate".
 *
 * @property integer $fs_id
 * @property integer $user_id
 * @property integer $rate
 *
 * @property Fs $fs
 * @property User $user
 */
class FsRate extends \yii\db\ActiveRecord
{
	public static $verbalRates = [
		0 => 'Оценка отсутствует',
		1 => 'Безполезно',
		2 => 'Сомнительно безполезно',
		3 => 'Сомнительно полезно',
		4 => 'Полезно',
		5 => 'Крайне полезно',
	];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fs_rate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fs_id', 'user_id'], 'required'],
            [['fs_id', 'user_id', 'rate'], 'integer'],
            [['fs_id'], 'exist', 'skipOnError' => true, 'targetClass' => Fs::className(), 'targetAttribute' => ['fs_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fs_id' => 'Fs ID',
            'user_id' => 'User ID',
            'rate' => 'Rate',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFs()
    {
        return $this->hasOne(Fs::className(), ['id' => 'fs_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
