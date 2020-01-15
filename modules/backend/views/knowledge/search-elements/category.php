<?php
  /**
   * @var $this \yii\web\View
   * @var $model \app\models\Fs
   * @var $editor bool
   * @var $update bool
   */
?>
<div class="fs-row flexBlockAll">
	<div class="col-<?= $editor?8:9?>">
    <a class="ajaxify" href="<?= \yii\helpers\Url::to(['/backend/knowledge/index', 'root' => $model->id ]) ?>">
  		<?= $model->getIcon("folder-img", $model->name); ?>
		</a>
    <a class="ajaxify" href="<?= \yii\helpers\Url::to(['/backend/knowledge/index', 'root' => $model->id ]) ?>">
      <span class="folder-name" title="<?= \yii\bootstrap\Html::encode($model->name) ?>"><?= $model->name ?></span>
    </a>
	</div>
	<div class="col-2">
		<p class="date-change">
			<?= Yii::$app->formatter->asRelativeTime(max($model->updated_at, $model->created_at)) ?>
		</p>
	</div>
	<div class="col-1">
		<p class="size"><?= Yii::$app->formatter->asShortSize($model->size, 0) ?></p>
	</div>
	<?php if($editor) {?>
		<div class="col-1 actions">
			<?php if($update) {?>
				<a href="<?= \yii\helpers\Url::to(['/backend/knowledge/update', 'id'=>$model->id]) ?>"  title="Редактировать"><i class="fa fa-pencil"></i></a>
				<a data-pjax="0" data-method="post" data-confirm="Удалить папку вместе со всем содержимым？" aria-label="Удалить" title="Удалить" href="<?= \yii\helpers\Url::to(['/backend/knowledge/delete', 'id'=>$model->id]) ?>"><i class="fa fa-remove"></i></a>
			<?php } ?>
		</div>
	<?php } ?>
</div>
