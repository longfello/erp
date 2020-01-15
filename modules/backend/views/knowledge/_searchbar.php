<?php
  /** @var $model \app\models\Fs */
	$q = Yii::$app->request->get('q', '');
?>
<form action="<?= \yii\helpers\Url::to(['/backend/knowledge/search']); ?>" method="get" class="wrap-search-input div-inline">
    <div class="wrap-label wrap-focus-input">
		<?= \kartik\typeahead\TypeaheadBasic::widget([
			'name' => 'q',
	    'value' => $q,
			'data' => \yii\helpers\ArrayHelper::map(\app\models\FsTags::find()->all(), 'id', 'name'),
			'pluginOptions' => ['highlight' => true],
			'options' => ['placeholder' => '', 'class'=>'search-input focus-input'],
		]) ?>
        <label class="focus-input-label" for="w0">Поиск по названию, описанию или тегу</label>
		<input type="hidden" name="root" value="<?= $model->id ?>">
		<input type="hidden" name="in" value="<?= $model->id ?>">
		<button type="submit" class="submit"></button>
    </div>
</form>
