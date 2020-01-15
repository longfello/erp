<?php
  /**
   * @var $model \app\models\FileshareFile
   * @var $error string
   * @var $this \yii\web\View
   */
?>
<div class="wrap-share-download">
	<form method="post">
    <?= $model->getIcon('wrap-share-download__file-type') ?>
		<p class="wrap-share-download__file-name"><?= $model->name ?></p>
		<p class="wrap-share-download__file-size"><?= Yii::$app->formatter->asShortSize($model->size, 0) ?></p>
    <?php if ($model->password) { ?>
      <div class="wrap-gener-pass">
        <label for="gener-pass" class="gener-pass-label">Пароль</label>
        <input type="password" name="password" id="gener-pass" class="gener-pass-input gener-pass-input_bg-trans">
        <button type='button' class="show-pass-btn show-pass-btn_download"></button>
        <?php if ($error){ ?>
          <p class="error-pass-text"><?= $error ?></p>
        <?php } ?>
      </div>
    <?php } ?>
    <input name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" type="hidden">
		<button type="submit" class="download-btn">Скачать</button>
	</form>
</div>

<?php

  $this->registerJs("
$(document).on('click', '.show-pass-btn', function(e){
    e.preventDefault();
    var el = $(this).siblings('input');
    if (el){
      if ($(el).attr('type') == 'text') {
        $(el).attr('type', 'password');
      } else {
        $(el).attr('type', 'text');
      }
    }
  });
");