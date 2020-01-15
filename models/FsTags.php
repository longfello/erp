<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fs_tags".
 *
 * @property integer $id
 * @property string $name
 *
 * @property FsTag[] $fsTags
 * @property Fs[] $fs
 */
class FsTags extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fs_tags';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
	        ['name', 'unique', 'targetAttribute' => ['name'], 'message' => 'Название должно быть уникальным.'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFsTags()
    {
        return $this->hasMany(FsTag::className(), ['tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFs()
    {
        return $this->hasMany(Fs::className(), ['id' => 'fs_id'])->viaTable('fs_tag', ['tag_id' => 'id']);
    }
}
