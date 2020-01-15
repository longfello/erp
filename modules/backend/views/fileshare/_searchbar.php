<?php
  /** @var $model \app\models\Fs */
	$q = Yii::$app->request->get('q', '');
?>
<form action="<?= \yii\helpers\Url::to(['/backend/fileshare/search']); ?>" method="get" class="wrap-search-input div-inline">
    <div class="wrap-label">
		<?= \kartik\typeahead\TypeaheadBasic::widget([
			'name' => 'q',
	    'value' => $q,
			'data' => [''] + \yii\helpers\ArrayHelper::map(\app\models\FileshareFile::find()->all(), 'id', 'name'),
			'pluginOptions' => ['highlight' => true],
			'options' => ['placeholder' => 'Поиск по названию, описанию или тегу', 'class'=>'search-input'],
		]) ?>
		<input type="hidden" name="root" value="<?= $model?$model->id:null ?>">
		<input type="hidden" name="in" value="<?= $model?$model->id:null ?>">
		<button type="submit" class="submit"></button>
        <p class="hidden-clues">Поиск по названию</p>
    </div>
</form>
