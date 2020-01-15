<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fs_rights".
 *
 * @property integer $fs_id
 * @property string $view
 * @property string $share
 * @property string $edit
 *
 * @property Fs $fs
 * @property AuthItem $view0
 * @property AuthItem $share0
 * @property AuthItem $edit0
 */
class FsRights extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fs_rights';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fs_id'], 'required'],
            [['fs_id'], 'integer'],
            [['view', 'share', 'edit'], 'string', 'max' => 64],
            [['fs_id'], 'exist', 'skipOnError' => true, 'targetClass' => Fs::className(), 'targetAttribute' => ['fs_id' => 'id']],
            [['view'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::className(), 'targetAttribute' => ['view' => 'name']],
            [['share'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::className(), 'targetAttribute' => ['share' => 'name']],
            [['edit'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::className(), 'targetAttribute' => ['edit' => 'name']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fs_id' => 'Fs ID',
            'view' => 'View',
            'share' => 'Share',
            'edit' => 'Edit',
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
    public function getView0()
    {
        return $this->hasOne(AuthItem::className(), ['name' => 'view']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShare0()
    {
        return $this->hasOne(AuthItem::className(), ['name' => 'share']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEdit0()
    {
        return $this->hasOne(AuthItem::className(), ['name' => 'edit']);
    }
}
