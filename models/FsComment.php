<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fs_comment".
 *
 * @property integer $fs_id
 * @property integer $user_id
 * @property string $type
 * @property string $dt
 * @property string $comment
 *
 * @property Fs $fs
 * @property User $user
 */
class FsComment extends \yii\db\ActiveRecord
{
	const TYPE_PRIVATE = 'private';
	const TYPE_PUBLIC = 'public';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fs_comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fs_id', 'user_id', 'comment'], 'required'],
            [['fs_id', 'user_id'], 'integer'],
            [['type', 'comment'], 'string'],
            [['dt'], 'safe'],
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
            'type' => 'Type',
            'dt' => 'Dt',
            'comment' => 'Comment',
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
