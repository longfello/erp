<?php
/**
 *
 * @var \yii\web\View $this
 * @var \app\models\Fs $model
 * @var \yii\bootstrap\ActiveForm $form
 */

$previews = \app\models\FsPreview::find()->where(['fs_id' => $model->id])->all();
?>
<?php
  $files = glob(Yii::getAlias('@webroot/svg').'/*.svg');
  $names = [];
  foreach ($files as $file){
	  $names[basename($file)] = ucfirst(basename($file,'.svg'));
  }
?>
<?= $form->field($model, 'livicon_preview')->dropDownList([ null => '--- По-умолчанию ---'] + ['Иконки LivIcons' => $names]) ?>

<div class="preview-wrapper row uploaded-files-preview">
<?php
foreach ($previews as $one){
	/** @var $one \app\models\FsPreview */
	echo "<div class='col-xs-3 img-preview'>";
	echo \yii\bootstrap\Html::img($one->getUrl());
	echo "<a href='#' class='btn-remove-preview' data-id='{$one->fs_id}' data-filename='{$one->filename}'><i class='fa fa-remove'></i></a>";
	echo "</div>";
}

$uploadModel = new \app\models\FsPreview();
?>

<?php $widget = \dosamigos\fileupload\FileUpload::widget([
	'plus' => true,
	'useDefaultButton' => false,
	'model' => $uploadModel,
	'attribute' => 'file',
	'url' => ['knowledge/upload', 'id' => $model->id, 'type' => 'preview'], // your url, this is just for demo purposes,
  'clientOptions' => [
	  'maxChunkSize' => 1000000, // 1 MB
	  'maxFileSize' => 400000000,
	  'dropZone' => new \yii\web\JsExpression('$(".dropbox")'),
	  'singleFileUploads' => true,
    'options' => ['accept' => 'image/*'],
  ],
  'options' => [
	  'id'    => 'files-preview',
	  'class' => 'file-download',
	  'multiple' => 'multiple'
  ],
	// Also, you can specify jQuery-File-Upload events
	// see: https://github.com/blueimp/jQuery-File-Upload/wiki/Options#processing-callback-options
	'clientEvents' => [
		'fileuploaddone' => 'function(e, data) {
                                var result = jQuery.parseJSON(data.result)
                                console.log(result);
                                for(var i in result.files){
                                  $(".preview-wrapper").append("<div class=\"col-xs-3 img-preview\"><img src=\""+ result.files[i].url +"\"><a href=\'#\' class=\'btn-remove-preview\' data-id=\''.$model->id.'\' data-filename=\'"+ result.files[i].name +"\'><i class=\'fa fa-remove\'></i></a></div>");
                                }
                            }',
		'fileuploadfail' => 'function(e, data) {
                                // console.log(e);
                                // console.log(data);
                                alert("При загрузке произошла ошибка");
                            }',
	'fileuploadprogressall' => 'function(e, data) {
                              var progress = parseInt(data.loaded / data.total * 100, 10);
                              $(".uploaded-files-preview .progress").show();
                              $(".uploaded-files-preview .progress .bar").css("width", progress + "%");
                              if (progress == 100) $(".uploaded-files-preview .progress").hide();
                            }'
	],
]);?>
	<div class="clearfix"></div>
  <div class="dropbox">
    <p class="drop-here">Перетащите файл сюда или нажмите на плюс</p>
    <label class="wrap-input-file">
      <span class="icon-plus"></span>
		<?= $widget ?>
    </label>
  </div>
  <div class="progress"><div class="bar" style="width: 0%;"></div></div>

</div>

<?php

$this->registerJs("
$(document).on('click', 'a.btn-remove-preview', function(e){
  e.preventDefault();
  var self = this;
  $.post('".\yii\helpers\Url::to('/backend/knowledge/delete-preview')."', $(this).data(), function(data){
    if (data && data.result) {
      $(self).parents('.img-preview').remove();
    }
  }, 'json');
});
");


