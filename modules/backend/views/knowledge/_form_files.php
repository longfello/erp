<?php
use dosamigos\fileupload\FileUpload;

/* @var $this yii\web\View */
/* @var $model app\models\Fs */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="uploaded-files main">
  <h3>Файлы (основные)</h3>
	<ul class="files-list main">
		<?php foreach ($model->fsFilesMain as $file){ ?>
			<li class="highlight">
				<div class="col-xs-9"><?= $file->original_filename ?></div>
				<div class="col-xs-2"><?= Yii::$app->formatter->asShortSize($file->size, 0) ?></div>
				<div class="col-xs-1 pull-right"><a href="#" class="f-remove" data-filename="<?= $file->filename ?>" data-id="<?= $model->id ?>"><i class="fa fa-remove"></i></a></div>
			</li>
		<?php } ?>
	</ul>
	<?php
	$uploadModel = new \app\models\FsFile();
	?>
	<?php $widget = FileUpload::widget([
		'plus' => true,
		'useDefaultButton' => false,
		'model' => $uploadModel,
		'attribute' => 'file',
		'url' => ['knowledge/upload', 'id' => $model->id, 'type' => 'file', 'mode' => \app\models\FsFile::TYPE_MAIN], // your url, this is just for demo purposes,
		'clientOptions' => [
			'maxChunkSize' => 1000000, // 1 MB
			'maxFileSize' => 400000000,
			'dropZone' => new \yii\web\JsExpression('$(".dropbox.main")'),
			'singleFileUploads' => true,
		],
		'options' => [
			'id'    => 'files-main',
			'class' => 'file-download',
			'multiple' => 'multiple'
		],
		'clientEvents' => [
			'fileuploaddone' => 'function(e, data) {
                                var result = jQuery.parseJSON(data.result)
                                console.log(result);
                                for(var i in result.files){
                                  $("ul.files-list.main").append("<li class=\'highlight\'><div class=\"col-xs-9\">"+ result.files[i].original_name +"</div><div class=\"col-xs-2\">"+ result.files[i].sizeText+"</div><div class=\"col-xs-1 pull-right\"><a href=\"#\" class=\"f-remove\"><i class=\"fa fa-remove\" data-filename=\""+result.files[i].filename+"\" data-id=\""+'. $model->id .'+"\"></i></a></div></li>");
                                }
                            }',
			'fileuploadfail' => 'function(e, data) {
                                alert("При загрузке произошла ошибка");
                            }',
			'fileuploadprogressall' => 'function(e, data) {
                              var progress = parseInt(data.loaded / data.total * 100, 10);
                              $(".uploaded-files.main .progress").show();
                              $(".uploaded-files.main .progress .bar").css("width", progress + "%");
                              if (progress == 100) $(".uploaded-files.main .progress").hide();
                            }'

		],
	]);?>

  <div class="dropbox main">
    <p class="drop-here">Перетащите файл сюда или нажмите на плюс</p>
    <label class="wrap-input-file">
      <span class="icon-plus"></span>
		<?= $widget ?>
    </label>
  </div>
  <div class="progress"><div class="bar" style="width: 0%;"></div></div>
</div>
<div class="uploaded-files additional">
  <h3>Файлы (дополнительные)</h3>
	<ul class="files-list additional">
		<?php foreach ($model->fsFilesAdditional as $file){ ?>
			<li class="highlight">
				<div class="col-xs-9"><?= $file->original_filename ?></div>
				<div class="col-xs-2"><?= Yii::$app->formatter->asShortSize($file->size, 0) ?></div>
				<div class="col-xs-1 pull-right"><a href="#" class="f-remove" data-filename="<?= $file->filename ?>" data-id="<?= $model->id ?>"><i class="fa fa-remove"></i></a></div>
			</li>
		<?php } ?>
	</ul>
	<?php
	$uploadModel = new \app\models\FsFile();
	?>
	<?php $widget = FileUpload::widget([
		'plus' => true,
		'useDefaultButton' => false,
		'model' => $uploadModel,
		'attribute' => 'file',
		'url' => ['knowledge/upload', 'id' => $model->id, 'type' => 'file', 'mode' => \app\models\FsFile::TYPE_ADDITIONAL], // your url, this is just for demo purposes,
		'clientOptions' => [
			'maxChunkSize' => 1000000, // 1 MB
			'maxFileSize' => 400000000,
			'dropZone' => new \yii\web\JsExpression('$(".dropbox.additional")'),
			'singleFileUploads' => true,
		],
		'options' => [
			'id'    => 'files-additional',
			'class' => 'file-download',
			'multiple' => 'multiple'
		],
		'clientEvents' => [
			'fileuploaddone' => 'function(e, data) {
                                var result = jQuery.parseJSON(data.result)
                                console.log(result);
                                for(var i in result.files){
                                  $("ul.files-list.additional").append("<li class=\'highlight\'><div class=\"col-xs-9\">"+ result.files[i].original_name +"</div><div class=\"col-xs-2\">"+ result.files[i].sizeText+"</div><div class=\"col-xs-1 pull-right\"><a href=\"#\" class=\"f-remove\"><i class=\"fa fa-remove\" data-filename=\""+result.files[i].filename+"\" data-id=\""+'. $model->id .'+"\"></i></a></div></li>");
                                }
                            }',
			'fileuploadfail' => 'function(e, data) {
                                alert("При загрузке произошла ошибка");
                            }',
			'fileuploadprogressall' => 'function(e, data) {
                              var progress = parseInt(data.loaded / data.total * 100, 10);
                              $(".uploaded-files.additional .progress").show();
                              $(".uploaded-files.additional .progress .bar").css("width", progress + "%");
                              if (progress == 100) $(".uploaded-files.additional .progress").hide();
                            }'

		],
	]);?>

  <div class="dropbox additional">
    <p class="drop-here">Перетащите файл сюда или нажмите на плюс</p>
    <label class="wrap-input-file">
      <span class="icon-plus"></span>
		<?= $widget ?>
    </label>
  </div>
  <div class="progress additional"><div class="bar" style="width: 0%;"></div></div>
</div>

<?php

  $this->registerJs("
$(document).on('click', '.files-list a.f-remove', function(e){
  e.preventDefault();
  var self = this;
  $.post('".\yii\helpers\Url::to('/backend/knowledge/delete-file')."', $(this).data(), function(data){
    if (data && data.result) {
      $(self).parents('li.highlight').remove();
    }
  }, 'json');
});  
");

?>

