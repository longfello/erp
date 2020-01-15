<?php

namespace app\models;

use app\components\ModelWithFile;
use Yii;

/**
 * This is the model class for table "fs_file".
 *
 * @property integer $fs_id
 * @property string $filename
 * @property string $original_filename
 * @property integer $size
 * @property string $type
 *
 * @property Fs $fs
 */
class FsFile extends ModelWithFile
{
    const TYPE_ALL = 'all';
    const TYPE_MAIN = 'main';
    const TYPE_ADDITIONAL = 'additional';

	public $isPublic = false;
	public $directory = 'files';
	public $file;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fs_file';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fs_id', 'filename', 'size'], 'required'],
            [['fs_id', 'size'], 'integer'],
            [['type'], 'in', 'range' => [self::TYPE_MAIN, self::TYPE_ADDITIONAL]],
            [['filename', 'original_filename'], 'string', 'max' => 255],
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
            'filename' => 'Internal Filename',
            'original_filename' => 'Original Filename',
            'size' => 'Size',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFs()
    {
        return $this->hasOne(Fs::className(), ['id' => 'fs_id']);
    }

    public function moveTemporaryFile(){
	    $origin = $this->directory;
	    $origin_fsid = $this->fs_id;
	    $this->directory = 'temporary';
	    $this->fs_id = $this->fs->parent_id;
	    $source = $this->getPath().$this->filename;

        $this->directory = $origin;
	    $this->fs_id = $origin_fsid;
	    $target = $this->getPath().$this->filename;

	    if (file_exists($source)) {
		    if (copy($source, $target)){
			    $this->size = filesize($target);
			    $this->save(false);
			    $this->fs->recalcSize();
			    unlink($source);
			    return true;
		    }
	    }

	    return false;
    }

    public function beforeDelete() {
	    if ($result = parent::beforeDelete()){
	    	$file = $this->getPath().$this->filename;
	    	if (file_exists($file)){
	    		@unlink($file);
		    }
	    }
	    return $result;
    }
}
