<?php
  /**
   * @var $categories \app\models\FileshareCategory[]
   * @var $files \app\models\FileshareFile[]
   * @var $this \yii\web\View
   * @var $dir string
   * @var $sort string
   * @var $model null|\app\models\FileshareCategory
   */
?>
<div class="search-wrap">
	<?= $this->render('_searchbar', ['model' => $model]); ?>
	<?php echo $this->render('_fs_popups', ['model' => $model]); ?>
  <?php if (Yii::$app->user->can('admin')) { ?>
    <div class="wrap-admin-mode">
        <input id="admin-mode" class="wrap-admin-mode__admin-input" type="checkbox" name="admin-mode"  checked hidden >
        <label for="admin-mode" class="wrap-admin-mode__admin-label">Режим администратора</label>
    </div>
  <?php } ?>
</div>


<?= $this->render('_uploader', ['model' => $model]);?>

<div class="tabs-wrap tabs-wrap_file-sharing">
  <div class="functions-wrap">
      <?php echo $this->render('elements/file-functions', ['model' => $model]); ?>
      <?php echo $this->render('elements/category-functions', ['model' => $model]); ?>
  </div>
	<div class="fs-tabs list">
    <div class="fs-row flexBlockAll header-row">
		<?php
		$params = [
			'sort' => $sort,
			'dir' => $dir,
			'root' => $model?$model->id:null
		];
		?>
      <div class="col-6">
		  <?php $params0 = array_merge($params, ['sort' => \app\models\Fs::SORT_NAME, 'dir' => ($dir == SORT_ASC)?SORT_DESC:SORT_ASC]); ?>
        <a href="<?= \yii\helpers\Url::to(array_merge(['/backend/fileshare/index'], $params0)) ?>" class="ajaxify">
          Название
			<?php if ($sort == \app\models\Fs::SORT_NAME){ ?>
				<?php if ($dir == SORT_ASC ){ ?>
                <i class="fa fa-chevron-up"></i>
				<?php } else { ?>
                <i class="fa fa-chevron-down"></i>
				<?php } ?>
			<?php } ?>
        </a>
      </div>
      <div class="col-2">
		  <?php $params0 = array_merge($params, ['sort' => \app\models\Fs::SORT_TIME, 'dir' => ($dir == SORT_ASC)?SORT_DESC:SORT_ASC]); ?>
        <a href="<?= \yii\helpers\Url::to(array_merge(['/backend/fileshare/index'], $params0)) ?>" class="ajaxify">
          Дата изменения
			<?php if ($sort == \app\models\Fs::SORT_TIME){ ?>
				<?php if ($dir == SORT_ASC ){ ?>
                <i class="fa fa-chevron-up"></i>
				<?php } else { ?>
                <i class="fa fa-chevron-down"></i>
				<?php } ?>
			<?php } ?>
        </a>
      </div>
      <div class="col-1">
		  <?php $params0 = array_merge($params, ['sort' => \app\models\Fs::SORT_SIZE, 'dir' => ($dir == SORT_ASC)?SORT_DESC:SORT_ASC]); ?>
        <a href="<?= \yii\helpers\Url::to(array_merge(['/backend/fileshare/index'], $params0)) ?>" class="ajaxify">
          Размер
			<?php if ($sort == \app\models\Fs::SORT_SIZE){ ?>
				<?php if ($dir == SORT_ASC ){ ?>
                <i class="fa fa-chevron-up"></i>
				<?php } else { ?>
                <i class="fa fa-chevron-down"></i>
				<?php } ?>
			<?php } ?>
        </a>
      </div>
        <div class="col-3">
          <p class="size">Функции</p>
        </div>
    </div>
		<?php foreach($categories as $item){ ?>
		  <?= $this->render('elements/category', ['model' => $item]);
		}?>
		<?php foreach($files as $item){ ?>
			<?= $this->render('elements/file', ['model' => $item]);
		}?>
	</div>
</div>
