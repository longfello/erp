<?php
  /**
   * @var $this \yii\web\View
   */

  \app\modules\backend\assets\FileShareAsset::register($this);
?>
<div class="file-functions-wrapper fileshare-functions hidden">
	<a href="#" class="btn btn-function btn-file-function-download" data-href="<?= \yii\helpers\Url::to(['/backend/fileshare/file-download', 'id' => '#']) ?>"><i class="icon icon-download"></i> Скачать</a>
	<a href="#" class="btn btn-function btn-file-function-delete"   data-href="<?= \yii\helpers\Url::to(['/backend/fileshare/file-delete', 'id' => '#']) ?>"><i class="icon icon-delete"></i> Удалить</a>
	<a href="#" class="btn btn-function btn-file-function-rename"   data-href="<?= \yii\helpers\Url::to(['/backend/fileshare/file-rename', 'id' => '#']) ?>"><i class="icon icon-rename"></i> Переименовать</a>
	<a href="#" class="btn btn-function btn-file-function-move"     data-href="<?= \yii\helpers\Url::to(['/backend/fileshare/file-move', 'id' => '#']) ?>"><i class="icon icon-move"></i> Переместить</a>
</div>