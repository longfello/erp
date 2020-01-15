<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 29.11.16
 * Time: 11:42
 */

namespace app\components;


use yii\db\ActiveRecord;

class ModelWithFile extends ActiveRecord {
	public $isPublic  = false;
	public $directory = 'common';

	public function getPath(){
		if ($this->isPublic) {
			$rootPath = basedir.'/data';
		} else {
			$rootPath = basedir.'/storage';
		}
		$path = $rootPath.$this->getRelativePath();
		if (!is_dir($path)) {
			mkdir($path, 0777, true);
		}
		return $path;
	}

	public function getRelativePath(){
		$path = '/'.$this->directory.'/';
		if ($this->hasAttribute('fs_id')){
			$path .= $this->getIdSubPath($this->getAttribute('fs_id')).'/';
		} elseif ($this->hasAttribute('id')){
			$path .= $this->getIdSubPath($this->getAttribute('id')).'/';
		}
		return $path;
	}

	public function generateFilename($name, $extension){
		$uid = uniqid(time(), true);
		$fileName = $uid . '_'.md5($name).'.' . $extension;
		return $fileName;
	}

	public function getUrl(){
		if ($this->isPublic) {
			return '/data'.$this->getRelativePath().$this->getAttribute('filename');
		} else return false;
	}

	public function getIdSubPath($id){
		$path = [];
		$path[] = substr($id, 0, 2);
		$path[] = substr($id, 0, 4);
		$path[] = $id;
		return implode('/', $path);
	}

	public function getIcon($class='', $alt=''){
		$src = $filename = '/img/fs/file.png';
		$file       = $this;
		if(isset($this->original_filename)){
			$path_parts = pathinfo( $file->original_filename );
			$ext        = isset($path_parts['extension'])?$path_parts['extension']:'';
			$filename       = '/img/ext/'.$ext.'.svg';
		}

		if (file_exists(\Yii::getAlias( '@webroot' ).$filename)){
			$src = $filename;
		}
		return $src;
	}
}