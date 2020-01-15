<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fs_tag".
 *
 * @property integer $fs_id
 * @property integer $tag_id
 *
 * @property Fs $fs
 * @property FsTags $tag
 */
class FsTag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fs_tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fs_id', 'tag_id'], 'required'],
            [['fs_id', 'tag_id'], 'integer'],
            [['fs_id'], 'exist', 'skipOnError' => true, 'targetClass' => Fs::className(), 'targetAttribute' => ['fs_id' => 'id']],
            [['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => FsTags::className(), 'targetAttribute' => ['tag_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fs_id' => 'Fs ID',
            'tag_id' => 'Tag ID',
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
    public function getTag()
    {
        return $this->hasOne(FsTags::className(), ['id' => 'tag_id']);
    }
}
