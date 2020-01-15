<?php
/**
 *
 * @var \app\models\Fs $model
 * @var \yii\bootstrap\ActiveForm $form
 */

$tags = \app\models\FsTags::find()->orderBy('name')->all();
?><div class='row well'><?php
foreach ($tags as $tag){
	echo "<div class='col-xs-3'>";
	$checked = \app\models\FsTag::findOne(['fs_id' => $model->id, 'tag_id' => $tag->id]);
	echo \yii\bootstrap\Html::checkbox('tag[]', (bool)$checked, [ 'value' => $tag->id ]);
	echo \yii\bootstrap\Html::label($tag->name);
	echo "</div>";
}
?></div>
<a class="btn btn-default pull-right" href="<?= \yii\helpers\Url::to('/backend/tags/index') ?>">Управление тэгами</a>



