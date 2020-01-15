<ul class="dashboard-information"></ul>
<?php if (isset($this->params['breadcrumbs'])){ ?>
	<div class="breadscrambs-wrap">

		<?= \yii\widgets\Breadcrumbs::widget([
			'encodeLabels' => false,
			'options' => [
				'class' => 'breadscrambs'
			],
			'itemTemplate' => "<li>{link}</li>\n", // template for all links
			'homeLink' => [
				'label' => '<i class="fa fa-home" aria-hidden="true"></i>Главная',
				'url' => Yii::$app->homeUrl,
			],
			'links' => $this->params['breadcrumbs'],
		])
		?>
	</div>
<?php } ?>
<div class="content-wrapper  container-fluid">
	<?= $content ?>
</div>
