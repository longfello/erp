<?php
/**
 *
 */
?>

<div class="row">
	<div class="col-xs-6 col-xs-offset-3">
		<h1 class="text-center"><a href="/"><img src="/img/logo.png" alt="Jarvis Logo"></a></h1>
		<div class="well text-center">
			<br>
			<p>Доступ данного пользователя не был разрешен администратором. Вы можете подать заявку на предоставление доступа.</p>
			<br>
			<br>

			<?php $form = \yii\bootstrap\ActiveForm::begin([
				'action' => '/site/request'
			]); ?>

			<?= $form->field($model, 'query', ['template' => '{input}{error}{hint}'])->textarea(['placeholder' => 'Комментарий']); ?>
			<?= $form->field($model, 'user_id', ['template' => '{input}'])->hiddenInput(); ?>

			<div class="form-group">
				<?php echo \yii\bootstrap\Html::submitButton("Отправить заявку", ['class' => 'btn btn-success']) ?>
			</div>

			<?php $form::end(); ?>
		</div>
	</div>
</div>