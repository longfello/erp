<?php

namespace app\models;

use app\components\ModelWithFile;
use Yii;

/**
 * This is the model class for table "fs_preview".
 *
 * @property integer $fs_id
 * @property string $filename
 * @property string $type
 *
 * @property Fs $fs
 */
class FsPreview extends ModelWithFile
{
	public $file;
	public $isPublic = true;
	public $directory = 'preview';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fs_preview';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fs_id', 'filename'], 'required'],
            [['fs_id'], 'integer'],
            [['filename'], 'string', 'max' => 255],
            [['fs_id'], 'exist', 'skipOnError' => true, 'targetClass' => Fs::className(), 'targetAttribute' => ['fs_id' => 'id']],
	        [['file'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fs_id' => 'Fs ID',
            'filename' => 'filename',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFs()
    {
        return $this->hasOne(Fs::className(), ['id' => 'fs_id']);
    }

	public function beforeDelete() {
		if ($result = parent::beforeDelete()){
			$file = $this->getPath().$this->filename;
			if (file_exists($file)){
				unlink($file);
			}
		}
		return $result;
	}
}
