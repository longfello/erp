<?php
  /**
   * @var $this \yii\web\View
   */

  \app\modules\backend\assets\FileShareAsset::register($this);
?>
<div class="category-functions-wrapper fileshare-functions hidden">
	<a href="#" class="btn btn-function btn-category-function-delete"   data-href="<?= \yii\helpers\Url::to(['/backend/fileshare/category-delete', 'id' => '#']) ?>"><i class="icon icon-delete"></i> Удалить</a>
	<a href="#" class="btn btn-function btn-category-function-rename"   data-href="<?= \yii\helpers\Url::to(['/backend/fileshare/category-rename', 'id' => '#']) ?>"><i class="icon icon-rename"></i> Переименовать</a>
	<a href="#" class="btn btn-function btn-category-function-move"     data-href="<?= \yii\helpers\Url::to(['/backend/fileshare/category-move', 'id' => '#']) ?>"><i class="icon icon-move"></i> Переместить</a>
</div>