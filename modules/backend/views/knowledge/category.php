<?php
  /**
   * @var $items \app\models\Fs[]
   * @var $this \yii\web\View
   * @var $dir string
   * @var $sort string
   * @var $model null|\app\models\Fs
   */

$editor = $model->checkAccess(\app\models\Fs::ACCESS_EDIT);
?>
<div class="search-wrap">
	<?= $this->render('_searchbar', ['model' => $model]); ?>
	<?php if ($editor) { echo $this->render('_fs_popups', ['model' => $model]); } ?>
	<ul class="tabs-nav flexBlockAll viewStyleControl">
		<li>
			<a class="view-style view-style-tile" data-style="tile"><div></div></a>
		</li>
		<li>
			<a class="view-style view-style-list" data-style="list"><div></div></a>
		</li>
	</ul>
</div>

<div class="tabs-wrap">
	<div class="fs-tabs list">
		<div class="fs-row flexBlockAll header-row">
			<?php
			  $params = [
			  	'sort' => $sort,
			  	'dir' => $dir,
			    'root' => $model->id
			  ];
			?>
			<div class="col-<?= $editor?8:9?>">
				<?php $params0 = array_merge($params, ['sort' => \app\models\Fs::SORT_NAME, 'dir' => ($dir == SORT_ASC)?SORT_DESC:SORT_ASC]); ?>
				<a href="<?= \yii\helpers\Url::to(array_merge(['/backend/knowledge/index'], $params0)) ?>" class="ajaxify">
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
				<a href="<?= \yii\helpers\Url::to(array_merge(['/backend/knowledge/index'], $params0)) ?>" class="ajaxify">
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
				<a href="<?= \yii\helpers\Url::to(array_merge(['/backend/knowledge/index'], $params0)) ?>" class="ajaxify">
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
			<?php if($editor) {?>
				<div class="col-1">
					<p class="size">Функции</p>
				</div>
			<?php } ?>
		</div>
		<?php foreach($items as $item){ ?>
			<?php
        if ($item->checkAccessRecursive(\app\models\Fs::ACCESS_VIEW)){
	        $accessUpdate = $item->checkAccess(\app\models\Fs::ACCESS_EDIT);
	        echo $this->render('elements/'.$item->type, ['model' => $item, 'update' => $accessUpdate, 'editor' => $editor]);
        }
		}?>
	</div>
</div>
