<?php
/* @var $this yii\web\View */
/* @var $model app\models\Fs */
/* @var $form yii\bootstrap\ActiveForm */
?>

<?= $form->field($model, 'parent_id')->dropDownList([null => '[ Корень ]'] + \yii\helpers\ArrayHelper::map(\app\models\Fs::find()->where(['type' => 'category'])->orderBy('name')->all(), 'id', 'name')) ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

<?php /* echo $form->field($model, 'type')->dropDownList([ 'card' => 'Карточка', 'category' => 'Категория', ]) */ ?>

<?= $form->field($model, 'notes')->textarea(['rows' => 6]) ?>

<?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'project_id')->dropDownList([null => '[ Нет ]'] + \yii\helpers\ArrayHelper::map(\app\models\Fs::find()->where(['type' => 'category'])->orderBy('name')->all(), 'id', 'name')) ?>

<?= $form->field($model, 'sort_order')->widget(\kartik\widgets\TouchSpin::class, [
	'pluginOptions' => ['verticalbuttons' => true]
]) ?>

<?php if ($model->type == $model::TYPE_CARD) { ?>

	<?php if (Yii::$app->user->can('admin')) { ?>
		<?= $form->field($model, 'regenerateHash')->checkbox() ?>
		<?= $form->field($model, 'regenerateHashPlus')->checkbox() ?>
    <?php } ?>

	<?= $this->render('_form_files', ['model' => $model, 'form' => $form]); ?>
<?php } ?>
