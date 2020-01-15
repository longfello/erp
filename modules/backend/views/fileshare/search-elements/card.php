<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\Fs
 * @var $editor bool
 * @var $update bool
 * @var $q string
 */
?>
<div class="fs-row fs-file flexBlockAll functions-link" data-id="<?= $model->id ?>" data-target="file-functions-wrapper">
  <div class="col-6 file-<?= $model->id ?>">
	  <?= $model->getIcon("file-img", "file-type"); ?>
    <span class="folder-name file-name-viewer" title="<?= \yii\bootstrap\Html::encode($model->name) ?>"><?= $model->name ?></span>
    <input type="text" class="file-name-editor" value="<?= $model->name ?>" data-type="file" autocomplete="off">
    <ul class="file-path">
      <li><span class="file-path-in">в</span></li>
	    <?php
	    $bc = $model->getParentsArray($model->parent_id);
	    if ($bc) {
		    foreach($bc as $one){ ?>
              <li>
                <a href="<?=$one['url']?>"><?= $one['label']?></a>
              </li>
		    <?php }
	    } else {
		    ?><a href="<?= \yii\helpers\Url::to('/backend/fileshare/index') ?>"> / </a><?
	    }

	    ?>
    </ul>

  </div>
  <div class="col-2">
    <p class="date-change"><?= $model->relativeTime(); ?></p>
  </div>
  <div class="col-1">
    <p class="size"><?= Yii::$app->formatter->asShortSize($model->size, 0) ?></p>
  </div>
  <div class="col-3 actions">
    <div class="wrap-file-was-share">
		<?php if ($model->share_hash){ ?>
          <img src="/img/file-was-sharing.svg" alt="" class="wrap-file-was-share__share">
			<?php if ($model->password){ ?>
            <span class="wrap-file-was-share__locked"><i class="icon-lock"></i></span>
			<?php } ?>
		<?php } ?>
    </div>
    <a href="<?= \yii\helpers\Url::to(['/backend/fileshare/share', 'id' => $model->id]); ?>" class="sharing-modal-btn link">Поделиться</a>
  </div>
</div>
