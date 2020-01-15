<?php
$uploadModel = new \app\models\FileshareFile();
$widget = \dosamigos\fileupload\FileUpload::widget([
	'plus' => true,
	'useDefaultButton' => false,
	'model' => $uploadModel,
	'attribute' => 'file',
	'url' => ['fileshare/upload', 'id' => $model?$model->id:0, 'type' => 'file'], // your url, this is just for demo purposes,
	'options' => [
		'id'    => 'fileshare',
		'class' => 'file-download',
		'multiple' => 'multiple'
	],
	'clientOptions' => [
		'maxChunkSize' => 1000000, // 1 MB
		'maxFileSize' => 2*1024*1024*1024,
		'dropZone' => new \yii\web\JsExpression('$(".dropbox")'),
		'singleFileUploads' => true,
    'messages' => [
	    'maxNumberOfFiles' => 'Достигнуто максимальное количество файлов',
      'acceptFileTypes' => 'Данный тип файла не допускается',
      'maxFileSize' => 'Файл превышает допустимый размер',
      'minFileSize' => 'Файл слишком маленький',
      'uploadedBytes' => 'Загружено больше, чем размер файла'
    ]
	],
	'clientEvents' => [
	  'fileuploadadd' => 'function(e, data){
	    $(".dropbox").trigger("progress", data);
//	    $(".dropbox-details").removeClass("hidden");
	  }',
    'fileuploadprocessfail' => 'function (e, data) {
        window._file_share.setError(data.files[data.index]);
        alert(data.files[data.index].name + "\n" + data.files[data.index].error);
    }',
	  'fileuploadprogress' => 'function(e, data){
	    $(".dropbox-details").removeClass("hidden");
	    $(".dropbox").trigger("progress", data);
	  }',
		'fileuploadfail' => 'function(e, data) {
		  if (data.errorThrown != "abort") {
        if (data.errorThrown) {
          alert("При загрузке произошла ошибка: " + data.errorThrown);
        }
		  }
    }',
		'fileuploadprogressall' => 'function(e, data) {
      var progress = parseInt(data.loaded / data.total * 100, 10);
      if (data.loaded == data.total) {
  	    $(".dropbox-details").addClass("hidden");
        $(".dropbox").addClass("hidden");
        setTimeout(function(){ document.location.href = document.location.href; }, 1000);
      }
    }'
	],
]);
?>

<div class="dropbox hidden dropbox__file-share">
  <div class="dropbox-inner">
    <p class="drop-here">Перетащите файл сюда или нажмите на плюс</p>
    <label class="wrap-input-file">
      <span class="icon-plus"></span>
      <?= $widget ?>
    </label>
  </div>
  <div class="dropbox-details hidden">
    <p class="file-load"><span class="file-load__bold-text">Файлы загружаются</span>, подождите...</p>
    <div class="wrap-upload-files"></div>
    <div class="fileshare-upload-file hidden" id="fileshare-upload-file-template">
      <img src="/img/fs/file.png" alt="" class="fileshare-upload-file__img">
      <span class="fileshare-upload-file__name">Rotonda.ttf</span>
      <a class="fileshare-upload-file__cancel"><i class="fa fa-times"></i></a>
      <div class="wrap-progress-bar">
        <div class="wrap-progress-bar__band"></div>
      </div>
    </div>

  </div>
</div>
