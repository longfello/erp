<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 08.12.16
 * Time: 11:29
 *
 * @var $model \app\models\Fs
 */
?>

<div class="admin-panel div-inline">
	<?php if ($model->id) { ?>
  	<a href="#" class="add-folder"  data-toggle="modal" data-target=".modal-create-category">Создать папку</a>
    <a href="#" class="upload-file" data-toggle="modal" data-target=".modal-download-file">Загрузить файл</a>
  <?php } ?>
	<!--
		  <a href="#" class="download"></a>
		  <a href="#" class="edit"></a>
		  <a href="#" class="delete"></a>
		  <a href="#" class="copy"></a>
		  -->
</div>


<?php

	$uploadModel = new \app\models\FsFile();
	$widget = \dosamigos\fileupload\FileUpload::widget([
		'plus' => true,
		'useDefaultButton' => false,
		'model' => $uploadModel,
		'attribute' => 'file',
		'url' => ['knowledge/upload', 'id' => $model->id, 'type' => 'temporary'], // your url, this is just for demo purposes,
		'options' => [
			'id'    => 'files-main',
			'class' => 'file-download',
			'multiple' => 'multiple'
		],
		'clientOptions' => [
			'maxChunkSize' => 1000000, // 1 MB
			'maxFileSize' => 400000000,
			'dropZone' => new \yii\web\JsExpression('$(".dropbox")'),
			'singleFileUploads' => true,
		],
		'clientEvents' => [
			'fileuploaddone' => 'function(e, data) {
                                var result = jQuery.parseJSON(data.result)
                                console.log(result);
                                for(var i in result.files){
                                  $(".uploaded-files").append(\'<div class="uploaded-file"><span class="icon"><img src="\'+result.files[i].thumbnailUrl+\'"></span><span class="name">\'+ result.files[i].original_name +\'</span><span class="size">\'+ result.files[i].sizeText +\'</span><a href="#" class="delete-uploaded-file"><i class="fa fa-remove"></i></a><input type="hidden" name="filename[]" value="\'+ result.files[i].name +\'"><input type="hidden" name="original_filename[]" value="\'+ result.files[i].original_name +\'"></div>\');
                                }
                            }',
			'fileuploadfail' => 'function(e, data) {
                                alert("При загрузке произошла ошибка: " + data.errorThrown);
                            }',
			'fileuploadprogressall' => 'function(e, data) {
                              var progress = parseInt(data.loaded / data.total * 100, 10);
                              $(".uploaded-files .progress").show();
                              $(".uploaded-files .progress .bar").css("width", progress + "%");
                              if (progress == 100) {
                                $(".uploaded-files .progress").hide();
                                $(".dropbox").hide();
                              }
                            }'

		],
	]);
	$widgetAdditional = \dosamigos\fileupload\FileUpload::widget([
		'plus' => true,
		'useDefaultButton' => false,
		'model' => $uploadModel,
		'attribute' => 'file',
		'url' => ['knowledge/upload', 'id' => $model->id, 'type' => 'temporary-additional'], // your url, this is just for demo purposes,
		'options' => [
			'id'    => 'files-additional',
			'class' => 'file-download',
			'multiple' => 'multiple'
		],
		'clientOptions' => [
			'maxChunkSize' => 1000000, // 1 MB
			'maxFileSize' => 400000000,
			'dropZone' => new \yii\web\JsExpression('$(".dropbox-additional")'),
			'singleFileUploads' => true,
		],
		'clientEvents' => [
			'fileuploaddone' => 'function(e, data) {
                                var result = jQuery.parseJSON(data.result)
                                console.log(result);
                                for(var i in result.files){
                                  $(".uploaded-files-additional").append(\'<div class="uploaded-file"><span class="icon"><img src="\'+result.files[i].thumbnailUrl+\'"></span><span class="name">\'+ result.files[i].original_name +\'</span><a href="#" class="delete-uploaded-file"><i class="fa fa-remove"></i></a><input type="hidden" name="filename[]" value="\'+ result.files[i].name +\'"><input type="hidden" name="original_filename[]" value="\'+ result.files[i].original_name +\'"></div>\');
                                }
                            }',
			'fileuploadfail' => 'function(e, data) {
                                alert("При загрузке произошла ошибка: " + data.errorThrown);
                            }',
			'fileuploadprogressall' => 'function(e, data) {
                              var progress = parseInt(data.loaded / data.total * 100, 10);
                              $(".uploaded-files-additional .progress").show();
                              $(".uploaded-files-additional .progress .bar").css("width", progress + "%");
                              if (progress == 100) {
                                $(".uploaded-files-additional .progress").hide();
                                $(".dropbox-additional").hide();
                              }
                            }'
		],
	]);
	$action = \yii\helpers\Url::to(['/backend/knowledge/create-card', 'root' => $model->id]);
	$actionCP = \yii\helpers\Url::to(['/backend/knowledge/create-category', 'root' => $model->id]);
	$csrf = '<input id="form-token" type="hidden" name="'.Yii::$app->request->csrfParam.'" value="'.Yii::$app->request->csrfToken.'"/>';

  $access = \kartik\select2\Select2::widget([
    'name' => 'rights',
    'hideSearch' => true,
    'data' => [
      0 => 'Публичный материал',
      'fs_internal' => 'Внутренний материал',
    ],
    'options' => ['class' => 'access-type'],
  ]);
  $notify = \kartik\select2\Select2::widget([
    'name' => 'notify',
    'hideSearch' => true,
    'data' => [
      1 => 'Оповестить всех',
      0 => 'Не оповещать',
    ],
    'options' => ['class' => 'notify-type'],
  ]);

$this->params['appendFooter'] = <<<HTML
	<div class="modal fade modal-download-file" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content modal-download-content dropbox-content">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h2 class="title">Загрузить файл</h2>
				<form action="{$action}" class="wrap-dropbox wrap-upload-file" method="post">
					{$csrf}
					<div class="dropbox">
						<p class="drop-here">Перетащите файл сюда или нажмите на плюс</p>
						<label class="wrap-input-file">
							<span class="icon-plus"></span>
							{$widget}
						</label>
					</div>
					<span class="error"></span>
				    <div class="uploaded-files">
				    <div class="progress"><div class="bar" style="width: 0%;"></div></div>
            </div>
					<div class="wrap-focus-input">
						<input type="text" class="fname focus-input" name="name" placeholder="" maxlength="255" id="download-name">
						<span class="error"></span>
						<label class="focus-input-label" for="download-name">Назовите файл</label>
					</div>
					<div class="wrap-textarea wrap-focus-input">
						<textarea class="upload-description limit-category focus-input" name="description" id="download-description" placeholder=""></textarea>
						<span class="cnt-wrapper">Осталось <i class="chars-limit-category chars-limit">300</i> символов</span>
						<span class="error"></span>
						<label for="download-description" class="focus-input-label">Добавьте описание</label>
					</div>
					<div class="wrap-focus-input add-hashtag">
						<input type="text" class="hashtag focus-input" name="tags" placeholder="" data-role="tagsinput" id="download-hastag">
						<label class="focus-input-label" >Добавьте теги через запятую</label>
					</div>

          <fieldset>
            <a href="#additional-materials" class="show-more"><i class="fa fa-chevron-right"></i>Дополнительные материалы</a>
            <div id="additional-materials" class="hidden">
              
    
              <label></label>
              <div class="dropbox-additional">
                <p class="drop-here">Перетащите файл сюда или нажмите на плюс</p>
                <label class="wrap-input-file">
                  <span class="icon-plus"></span>
                  {$widgetAdditional}
                </label>
              </div>
              <span class="error"></span>
              <div class="uploaded-files-additional">
              				    <div class="progress"><div class="bar" style="width: 0%;"></div></div>
              </div>
              <div class="wrap-focus-input ">
                <input type="text" class="add-fname focus-input" name="add-name" placeholder="" id="download-add-name">
                <span class="error"></span>
				<label class="focus-input-label" for="download-add-name">Назовите дополнительные материалы</label>
              </div>
            </div>
          </fieldset>  

					<label class="wrap-select-access public">
						<span class="lock-img"><img src="/img/fs/unlock.png" alt=""></span>
						{$access}
					</label>
					<label class="wrap-select-notify">
						<span class="icon-people"></span>
						{$notify}
					</label>

					<input type="submit" class="submit-dropbox" value="Добавить">
				</form>
			</div>
		</div>
	</div>
	<div class="modal fade modal-create-category" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content modal-download-content dropbox-content">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h2 class="title">Создать папку</h2>
				<form action="{$actionCP}" class="wrap-dropbox wrap-create-category" method="post">
					{$csrf}
					
					<div class="wrap-focus-input">
						<input type="text" class="fname focus-input" name="name" placeholder="" maxlength="255" id="download-name2">
						<span class="error"></span>
						<label class="focus-input-label" for="download-name2">Назовите файл</label>
					</div>
					<div class="wrap-textarea wrap-focus-input">
						<textarea class="upload-description limit-file focus-input" name="description" id="download-description2" placeholder=""></textarea>
						<span class="cnt-wrapper">Осталось <i class="chars-limit chars-limit-file">300</i> символов</span>
						<span class="error"></span>
						<label for="download-description2" class="focus-input-label">Добавьте описание</label>
					</div>
					<div class="wrap-focus-input add-hashtag">
						<input type="text" class="hashtag focus-input" name="tags" placeholder="" data-role="tagsinput" id="download-hastag2">
						<label class="focus-input-label" >Добавьте теги через запятую</label>
					</div>
					<input type="submit" class="submit-dropbox" value="Создать">
				</form>
			</div>
		</div>
	</div>
HTML;
	$this->registerJs("
$('a.show-more').on('click', function(e){
  e.preventDefault();
  $($(this).attr('href')).toggleClass('hidden');
  $('i.fa', this).toggleClass('fa-chevron-down fa-chevron-right');
});		
$('textarea.limit-category').limit('300','.chars-limit-category');		
$('textarea.limit-file').limit('300','.chars-limit-file');		
$('form.wrap-upload-file').on('submit', function(e){
  var er = false;
  if (($('.fname', this).val()).length < 2){
    er = true;
    $('.fname', this).addClass('error').siblings('.error').html('Слишком короткое название');
  }
  if (($('.upload-description', this).val()).length < 2){
    er = true;
    $('.upload-description', this).addClass('error').siblings('.error').html('Слишком короткое описание');
  }
  if ($('.uploaded-file', this).size() < 1){
    er = true;
    $('.dropbox, this').addClass('error').siblings('.error').html('Нужно загрузить не менее одного файла');
  }
  
  if (er){
    e.preventDefault();
    return false;
  } else {
    return true;
  }
});		
$('form.wrap-create-category').on('submit', function(e){
  var er = false;
  if (($('.fname', this).val()).length < 2){
    er = true;
    $('.fname', this).addClass('error').siblings('.error').html('Слишком короткое название');
  }
  if (($('.upload-description', this).val()).length < 2){
    er = true;
    $('.upload-description', this).addClass('error').siblings('.error').html('Слишком короткое описание');
  }
  if (er){
    e.preventDefault();
    return false;
  } else {
    return true;
  }
});		
$(document).on('click', 'a.delete-uploaded-file', function(e){
  e.preventDefault();
  $(this).parents('.uploaded-file').remove();
});
");



