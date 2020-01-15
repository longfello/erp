<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 08.12.16
 * Time: 11:29
 *
 * @var $model \app\models\FileshareCategory
 */
?>

<div class="admin-panel div-inline">
  <a href="#" class="add-folder"  data-toggle="modal" data-target=".modal-create-category">Создать папку</a>
  <?php $used = \app\models\FileshareCategory::getUserSize() ?>
  <?php if ($used < \app\models\FileshareCategory::PER_USER_SIZE_LIMIT) { ?>
  <a href="#" class="upload-file btn-upload-file">Загрузить файл</a>
  <?php } else { ?>
      <p class="error">Вы превысили доступный объем и не можете загружать файлы.</p>
  <?php } ?>
  <p class="info">(доступно <?= Yii::$app->formatter->asShortSize(\app\models\FileshareCategory::PER_USER_SIZE_LIMIT - $used, 1) ?> из <?= Yii::$app->formatter->asShortSize(\app\models\FileshareCategory::PER_USER_SIZE_LIMIT, 1) ?>)</p>
</div>


<?php

	$action = \yii\helpers\Url::to(['/backend/fileshare/create-card', 'root' => $model?$model->id:0]);
	$actionCP = \yii\helpers\Url::to(['/backend/fileshare/create-category', 'root' => $model?$model->id:0]);
  $actionMove = \yii\helpers\Url::to(['/backend/fileshare/move']);
	$csrf  = '<input id="form-token" type="hidden" name="'.Yii::$app->request->csrfParam.'" value="'.Yii::$app->request->csrfToken.'"/>';
	$tree  = \app\models\FileshareCategory::getTree();

$this->params['appendFooter'] = <<<HTML
	<div class="modal fade modal-create-category" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content modal-download-content dropbox-content">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h2 class="title">Создать папку</h2>
				<form action="{$actionCP}" class="wrap-dropbox wrap-create-category" method="post">
					{$csrf}
					<label>
						<input type="text" class="fname" name="name" placeholder="Назовите папку" maxlength="255">
						<span class="error"></span>
					</label>
					<input type="submit" class="submit-dropbox" value="Создать">
				</form>
			</div>
		</div>
	</div>
	<div class="modal fade modal-move" role="dialog" aria-labelledby="mySmallModalLabel" id="modal-move">
		<div class="modal-dialog" role="document">
			<div class="modal-content modal-download-content dropbox-content">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h2 class="title">Переместить</h2>
				<form action="{$actionMove}" class="wrap-dropbox wrap-create-category" method="post">
					{$csrf}
						<select name="move-to" class="modal-select" size="10">
						  {$tree}
            </select>
					<input type="hidden" name="entity_id"   class="entity_id"   value="">
					<input type="hidden" name="entity_type" class="entity_type" value="">
					<input type="submit" class="submit-dropbox" value="Переместить">
				</form>
			</div>
		</div>
	</div>
	<div class="modal fade sharing-password-modal" id="sharing-pass-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content modal-download-content dropbox-content">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h2 class="title">Поделиться</h2>
				<form action="" method="post">
          <div class="wrap-focus-input">
						<input type="text" class="focus-input" name="name" placeholder="" maxlength="255" id="share-pass-input" readonly>
						<label class="share-pass-input focus-input-label" for="download-name"></label>
					</div>	
					<div class="wrap-checkbox">
					    <input class="add-pass-input" type="checkbox" id="add-pass" name="add_password" value="1"><label for="add-pass" class="add-pass-label">Доступ по паролю</label>
                    </div>	
                    <div class="wrap-gener-pass">
                        <label for="gener-pass" name="password" class="gener-pass-label">Установите пароль</label>
                        <input type="password" id="gener-pass" class="gener-pass-input" name="add_password">
                        <button type='button' class="show-pass-btn"></button>                   
                        <button type='button' class="gener-pass-btn">Генерировать</button>                   
                    </div>
                    <input type="hidden" id="fspopup_csrf">		
                    <button type="submit" class="save-share-pass">Сохранить изменения</button>		
                </form>
			</div>
		</div>
	</div>

HTML;
	$this->registerJs("
$('form.wrap-create-category').on('submit', function(e){
  var er = false;
  if (($('.fname', this).val()).length < 2){
    er = true;
    $('.fname', this).addClass('error').siblings('.error').html('Слишком короткое название');
  }
  if (er){
    e.preventDefault();
    return false;
  } else {
    return true;
  }
});		
");



