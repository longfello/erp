<?php
  /**
   * @var $this \yii\web\View
   * @var $model \app\models\FileshareCategory
   */
?>
<div class="fs-row fs-category flexBlockAll functions-link" data-id="<?= $model->id ?>" data-target="category-functions-wrapper">
	<div class="col-6 category-<?= $model->id ?>">
    <a href="<?= \yii\helpers\Url::to(['/backend/fileshare/index', 'root' => $model->id ]) ?>" class="link">
  		<?= $model->getIcon("folder-img link", $model->name); ?>
		</a>
    <a href="<?= \yii\helpers\Url::to(['/backend/fileshare/index', 'root' => $model->id ]) ?>" class="link file-name-viewer">
      <span class="folder-name link" title="<?= \yii\bootstrap\Html::encode($model->name) ?>"><?= $model->name ?></span>
    </a>
    <input type="text" class="file-name-editor" value="<?= $model->name ?>" data-type="category" autocomplete="off">
	</div>
	<div class="col-2">
		<p class="date-change">
			<?= $model->relativeTime(); ?>
		</p>
	</div>
	<div class="col-1">
		<p class="size"><?= Yii::$app->formatter->asShortSize($model->size, 0) ?></p>
	</div>
  <div class="col-3 actions"></div>
</div>
