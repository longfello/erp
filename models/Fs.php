<?php

namespace app\models;

use app\components\Notification;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\HttpException;

/**
 * This is the model class for table "fs".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $type
 * @property string $share_hash
 * @property string $share_hash_plus
 * @property string $notes
 * @property string $name
 * @property string $additional_name
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property integer $project_id
 * @property integer $size
 * @property integer $user_id
 * @property string $livicon_preview
 * @property integer $sort_order
 *
 * @property Fs $parent
 * @property Fs[] $childs
 * @property Fs $project
 * @property FsComment[] $fsComments
 * @property FsComment $fsCommentPrivate
 * @property FsFile[] $fsFiles
 * @property FsFile[] $fsFilesMain
 * @property FsFile[] $fsFilesAdditional
 * @property FsPreview[] $fsPreviews
 * @property FsRate[] $fsRates
 * @property User[] $users
 * @property FsRights[] $fsRights
 * @property FsTag[] $fsTags
 * @property FsTags[] $tags
 * @property User $user
 */
class Fs extends \yii\db\ActiveRecord
{
	public $regenerateHash = false;
	public $regenerateHashPlus = false;

	const TYPE_CATEGORY = 'category';
	const TYPE_CARD = 'card';

	const ACCESS_VIEW  = 'view';
	const ACCESS_EDIT  = 'edit';
	const ACCESS_SHARE = 'share';

	const SORT_NAME = 'name';
	const SORT_TIME = 'updated_at';
	const SORT_SIZE = 'size';
	const SORT_DEFAULT = 'sort_order';

	public $access = [
		self::ACCESS_VIEW => true,
		self::ACCESS_EDIT => true,
		self::ACCESS_SHARE => true,
	];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'project_id', 'size', 'user_id', 'sort_order'], 'integer'],
            [['type', 'notes'], 'string'],
            [['name'], 'required'],
            [['uploaded_at'], 'safe'],
            [['share_hash', 'share_hash_plus', 'name', 'additional_name', 'livicon_preview'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 300],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Fs::className(), 'targetAttribute' => ['parent_id' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Fs::className(), 'targetAttribute' => ['project_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
	        [['regenerateHash', 'regenerateHashPlus'], 'safe']
        ];
    }

	public function behaviors()
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
					ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
				],
				'value' => function() { return date(\DateTime::W3C); } // unix timestamp },
				],
			];
	}

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Родитель',
            'type' => 'Тип',
            'share_hash' => 'Хэш шаринга',
            'share_hash_plus' => 'Хэш шаринга с доп.материалами',
            'notes' => 'Замечания',
            'name' => 'Название',
            'additional_name' => 'Название дополнительных материалов',
            'description' => 'Описание',
            'uploaded_at' => 'Дата/время изменения',
            'project_id' => 'ID проекта',
            'size' => 'Размер',
            'user_id' => 'Владелец',
            'livicon_preview' => 'Иконка',
            'sort_order' => 'Порядок сортировки по-умолчанию',
	        'regenerateHash' => 'Пересоздать ссылку (основные файлы)',
	        'regenerateHashPlus' => 'Пересоздать ссылку (все файлы)',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Fs::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChilds()
    {
        return $this->hasMany(Fs::className(), ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Fs::className(), ['id' => 'project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFsComments()
    {
        return $this->hasMany(FsComment::className(), ['fs_id' => 'id'])->andOnCondition(['type' => 'public']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFsCommentPrivate()
    {
        return $this->hasOne(FsComment::className(), ['fs_id' => 'id'])->andOnCondition(['type' => 'private', 'user_id' => Yii::$app->user->id]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFsFiles()
    {
        return $this->hasMany(FsFile::className(), ['fs_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFsFilesMain()
    {
        return $this->hasMany(FsFile::className(), ['fs_id' => 'id'])->andOnCondition(['type' => FsFile::TYPE_MAIN]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFsFilesAdditional()
    {
        return $this->hasMany(FsFile::className(), ['fs_id' => 'id'])->andOnCondition(['type' => FsFile::TYPE_ADDITIONAL]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFsPreviews()
    {
        return $this->hasMany(FsPreview::className(), ['fs_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFsRates()
    {
        return $this->hasMany(FsRate::className(), ['fs_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFsRights()
    {
        return $this->hasMany(FsRights::className(), ['fs_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFsTags()
    {
        return $this->hasMany(FsTag::className(), ['fs_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(FsTags::className(), ['id' => 'tag_id'])->viaTable('fs_tag', ['fs_id' => 'id']);
    }

	public function checkAccess($rule){
		$this->access[self::ACCESS_VIEW]  = Yii::$app->user->can('fs_view');
		$this->access[self::ACCESS_EDIT]  = Yii::$app->user->can('fs_edit');
		$this->access[self::ACCESS_SHARE] = Yii::$app->user->can('fs_share');

		if ($this->fsRights) {
			$access = [
				self::ACCESS_VIEW  => null,
				self::ACCESS_EDIT  => null,
				self::ACCESS_SHARE => null
			];

			foreach ($this->fsRights as $one){
				if ($one->view){
					$access[self::ACCESS_VIEW]  |=  Yii::$app->user->can($one->view);
				}
				if ($one->edit){
					$access[self::ACCESS_EDIT]  |=  Yii::$app->user->can($one->edit);
				}

				if ($one->share && ($one->share === 'fs_internal')) $access[self::ACCESS_SHARE] = false;

			}

			if (!is_null($access[self::ACCESS_VIEW]))  $this->access[self::ACCESS_VIEW]  = $access[self::ACCESS_VIEW];
			if (!is_null($access[self::ACCESS_EDIT]))  $this->access[self::ACCESS_EDIT]  = $access[self::ACCESS_EDIT];
			if (!is_null($access[self::ACCESS_SHARE])) $this->access[self::ACCESS_SHARE] = $access[self::ACCESS_SHARE];
		}

		if ($rule !== self::ACCESS_SHARE) {
			$this->access[$rule] = $this->access[$rule] || Yii::$app->user->can('admin');
		}
		return isset($this->access[$rule])?$this->access[$rule]:false;
	}

	public function checkAccessRecursive($rule){
		if ($this->checkAccess($rule)){
			return $this->parent?$this->parent->checkAccessRecursive($rule):true;
		}
		return false;
	}

	public function getOveralRate(){
		$rate=0; $count=0;
		foreach($this->fsRates as $one){
			$rate += $one->rate;
			$count++;
		}
		return $count?$rate/$count:0;
	}

	public function getOveralRateText(){
		$rate=(int)round($this->getOveralRate());
		return isset(FsRate::$verbalRates[$rate])?FsRate::$verbalRates[$rate]:FsRate::$verbalRates[0];
	}

	public function recalcSize(){
		$this->size = 0;
		foreach ($this->childs as $one){
			$this->size += $one->size;
		}

		foreach ($this->fsFiles as $file){
			$this->size += $file->size;
		}
		$this->save();
		if ($this->parent) {
			$this->parent->recalcSize();
		}
	}

	public function getIcon($class='', $alt=''){
		$icon = '';
		if ($this->livicon_preview) {
			$icon="<div title='{$alt}' class='livicon-evo {$class}' data-options='name:{$this->livicon_preview}; style: filled; tryToSharpen: true; pathToFolder: /svg/'></div>";
		} else {
			$src = ($this->type == self::TYPE_CATEGORY)?'/img/fs/folder.png':'/img/fs/file.png';
			/*
			if ($this->fsPreviews){
				$preview = $this->fsPreviews[0];
				$src = $preview ->getUrl();
			}
			*/

			if ($this->type == self::TYPE_CARD) {
				if ($this->fsFiles) {
					$file       = $this->fsFiles[0];
					$path_parts = pathinfo( $file->original_filename );
					$ext        = isset($path_parts['extension'])?$path_parts['extension']:'';
					$file       = '/img/ext/'.$ext.'.svg';

					if (file_exists(Yii::getAlias( '@webroot' ).$file)){
						$src = $file;
					}
				}
			}
			$icon = "<img src='{$src}' class='{$class}' alt='{$alt}'>";
		}

		return $icon;
	}

	public function getPreviewThumbnail($file = false, $w = 288, $h = 168){
		// 288x168
		if (!$file || !file_exists($file)){
			$file = Yii::getAlias('@webroot/img/default-preview.png');
		}

		$out_filename = md5($file).sha1($file).'.png';
		$out_path     = '/data/preview/assets/';
		$out_path    .= substr($out_filename, 0, 2).'/';
		$out_path    .= substr($out_filename, 0, 4).'/';
		$out_path    .= $w.'x'.$h.'/';

		if (!is_dir(basedir.$out_path)) {
			mkdir(basedir.$out_path, 0777, true);
		}

		if (!file_exists(basedir.$out_path.$out_filename)) {
			$image=Yii::$app->image->load($file);
			$image->resize($w, $h, \yii\image\drivers\Image::ADAPT)->save(basedir.$out_path.$out_filename);
		}
		return $out_path.$out_filename;
	}

	public function getPreview(){
		$slides = [];
		if ($this->fsPreviews){
			foreach ($this->fsPreviews as $one){
				$url = $this->getPreviewThumbnail($one->getPath().$one->getAttribute('filename'));
				$slides[] = "<img src='".$url."' alt='preview slide'>";
			}
		} else {
			foreach ($this->fsFilesMain as $file) {
				$path_parts = pathinfo( $file->original_filename );
				$ext        = strtolower(isset($path_parts['extension'])?$path_parts['extension']:'');

				if (in_array($ext, ['jpg', 'png', 'gif', 'jpeg', 'bmp'])){
					if ($file->isPublic) {
						$url = $this->getPreviewThumbnail($file->getPath().$file->getAttribute('filename'));
						$slides[] = "<img src='".$url."' alt='preview slide'>";
					} else {
						// Resize img
						$url = $this->getPreviewThumbnail($file->getPath().$file->getAttribute('filename'));
						$slides[] = "<img src='".$url."' alt='preview slide'>";
					}
				}
			}
		}

		if (!$slides){
			$url = $this->getPreviewThumbnail(false);
			$slides[] = "<img src='".$url."' alt='preview slide'>";
		}

		return $slides;
	}

	public function getFsMainSize(){
		$size = 0;
		foreach($this->fsFilesMain as $one){
			$size += $one->size;
		}
		return $size;
	}

	public function getDownloadPath(){
		if (!$this->fsFiles) {
			return false;
		}

		$anyFile = $this->fsFiles[0];
		/** @var $anyFile FsFile */
		$path = $anyFile->getPath().'download/';
		if (!is_dir($path)) {
			mkdir($path, 0777, true);
		}
		return $path;
	}

	public function getDownloadInfo($type, $getIsError = false){
		$path = $this->getDownloadPath();
		$hash = $this->getDownloadHash($type);

		$lock_file     = $path.$hash.'.zip.lock';
        $error_file    = $path.$hash.'.zip.error';
        if ($getIsError) {
            if (file_exists($error_file)) {
                return ' '.file_get_contents($error_file);
            }
            return false;
        } else {
            if (file_exists($lock_file)){
                return file_get_contents($lock_file);
            } else return "";
        }
	}

	public function getDownloadFile($type = FsFile::TYPE_ALL){
		$path = $this->getDownloadPath();
		$hash = $this->getDownloadHash($type);

		if (count($this->fsFiles)>1){
            $error_file    = $path.$hash.'.zip.error';
			$lock_file     = $path.$hash.'.zip.lock';
			$archive_file  = $path.$hash.'.zip';
			if (file_exists($lock_file)){
				if (filemtime($lock_file) + 1*60*60 < time()){
					// 1 hour for create archive, else remove lock
					@unlink($lock_file);
				}
			}

			if (file_exists($error_file)){
				if (filemtime($error_file) + 1*60 < time()){
					// 1 minute for create archive, else remove lock
					@unlink($error_file);
				}
			}

			if (file_exists($archive_file) && !file_exists($lock_file)){
				return $archive_file;
			} else {
				if (!file_exists($lock_file)){
					// Запуск фонового создания архива
					shell_exec(Yii::getAlias('@app')."/yii download/create {$this->id} {$type}  >> {$lock_file} 2>&1 &");
				}
				return false;
			}
		} elseif (count($this->fsFiles) == 1) {
			$files = $this->fsFiles;
			$file = array_pop($files);
			/** @var $model FsFile */
			return $file->getPath().$file->filename;
		}
		return false;
	}

	public function getDownloadHash($type = FsFile::TYPE_ALL){
		$query = FsFile::find()->where(['fs_id' => $this->id])->orderBy(['filename' => SORT_ASC]);
		if ($type != FsFile::TYPE_ALL){
			$query = $query->andWhere(['type' => $type]);
		}
		$models = $query->all();
		$complex_name = '';
		foreach ($models as $one){
			/** @var $one FsFile */
			$complex_name .= $one->filename;
		}
		return md5($complex_name).'-'.sha1($complex_name);
	}

	public function generateHash($type = FsFile::TYPE_ALL, $length = 4){
		$hash = Yii::$app->getSecurity()->generateRandomString($length);
		$hash = substr($hash, 0, 1).$hash;

		/*
		$salt = mt_rand();
		$hash = (array) (md5($type.$salt).sha1($type.$salt).$this->id);
		usort($hash, function(){
			return mt_rand(-1,1);
		});
		*/
		if (Fs::findOne(['share_hash' => $hash]) || Fs::findOne(['share_hash_plus' => $hash])) {
			$hash = $this->generateHash($type, $length+1);
		}
//		return implode('', $hash);
		return $hash;
	}

	public function afterSave($insert, $changedAttributes){
		/*
		$delete = $add = [];
		if (isset($changedAttributes['share_hash']))      { $delete[] = $changedAttributes['share_hash'];      $add[] = $this->share_hash; }
		if (isset($changedAttributes['share_hash_plus'])) { $delete[] = $changedAttributes['share_hash_plus']; $add[] = $this->share_hash_plus; }

		foreach ($delete as $hash) {
			$this->unshare($hash);
		}
		foreach ($add as $hash) {
			$this->share($hash);
		}
		*/
		parent::afterSave($insert, $changedAttributes);

		if ($insert){
			if ($this->parent_id){
				$parent = Fs::findOne(['id' => $this->parent_id]);
				if ($parent){
					/** @var $parent Fs */
					foreach ($parent->fsRights as $right){
						$model = new FsRights();
						$model->fs_id = $this->id;
						$model->view  = $right->view;
						$model->edit  = $right->edit;
						$model->share  = $right->share;
						$model->save();
					}
				}
			}
		}
	}


	public function beforeDelete() {
		if ($result = parent::beforeDelete()){
			foreach ($this->fsFiles as $one){
				$one->delete();
			}

			foreach ($this->fsPreviews as $one){
				$one->delete();
			}

			Notification::deleteAll(['key' => Notification::KEY_NEW_FILE, 'key_id' => $this->id]);
		};
		return $result;
	}

	public function getParentsArray($root, $iteration = 0){
		$elements = [];
		if ($iteration > 6) return $elements;
		$item = Fs::findOne(['id' => $root]);
		if ($item) {
			$elements[] = [
				'url'   => Url::to(['/backend/knowledge/index', 'root' => $item->id]),
				'label' => $item->name
			];
			$elements = array_merge($this->getParentsArray($item->parent_id, $iteration+1), $elements);
		}
		return $elements;
	}

	public function relativeTime(){
		$time = $this->updated_at?$this->updated_at:$this->created_at;
		$time = Yii::$app->formatter->asTimestamp(date(\DateTime::W3C, strtotime($time)));
		if ((time() - $time) > 60*60*24) {
			return "<span title='".Yii::$app->formatter->asDatetime($time)."'>".Yii::$app->formatter->asDatetime($time, 'php:d.m.Y')."</span>";
		} else {
			return Yii::$app->formatter->asRelativeTime($time, time());
		}
	}

	/*
	public function unshare($hash){
		$path = basedir.'/data/share/'.$hash;
		if (is_dir($path)) {
			unlink($path);
		}
	}

	public function share($hash){
		if (!$this->fsFiles) {
			return false;
		}

		$anyFile = $this->fsFiles[0];
		$path = '/'.trim($anyFile->getPath(), '/');

		$to = basedir.'/data/share/'.$hash;
		return symlink($path, $to);
	}
	*/
}
