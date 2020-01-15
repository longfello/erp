<?php
/**
 *
 * @var \app\models\AccessQuery $query
 */
?>

<div class="row">
	<div class="col-xs-6 col-xs-offset-3">
		<h1 class="text-center"><a href="/"><img src="/img/logo.png" alt="Jarvis Logo"></a></h1>
		<div class="alert alert-danger text-center">
			<br>
			<p>Вы уже запрашивали доступ.</p>
			<?php if ($query->answer) { ?>
				<p>Ответ администратора: <?= $query->answer ?></p>
			<?php } else { ?>
				<p>Администратор еще не рассмотрел ваш запрос.</p>
			<?php } ?>
			<br>
		</div>
	</div>
</div>
