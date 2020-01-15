<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\Fs
 * @var $editor bool
 * @var $update bool
 * @var $q string
 */
?>
<div class="fs-row flexBlockAll">
	<div class="col-<?= $editor?9:10?> col-info">
    <a class="ajaxify" href="<?= \yii\helpers\Url::to(['/backend/knowledge/index', 'root' => $model->id ]) ?>">
		  <?= $model->getIcon("file-img", "file-type"); ?>
    </a>
    <div class="search-more-info div-inline">
        <a class="ajaxify" href="<?= \yii\helpers\Url::to(['/backend/knowledge/index', 'root' => $model->id ]) ?>">
            <span class="folder-name" title="<?= \yii\bootstrap\Html::encode($model->name) ?>"><?= $model->name ?></span>

        </a>
        <ul class="file-path">
            <li><span class="file-path-in">в</span></li>
            <?php
              $bc = $model->getParentsArray($model->parent_id);
              foreach($bc as $one){ ?>
                <li>
                  <a href="<?=$one['url']?>"><?= $one['label']?></a>
                </li>
              <?php } ?>
        </ul>
        <?php
          if ($pos = mb_strpos($model->description, $q)){
            $begin = max(0, $pos-20);
	          $len   = min(mb_strlen($model->description)-$begin, 50);
	          $desc  = mb_substr($model->description, $begin, $len);
	          $prepend = ($begin>0)?'<span class="start-quote">...</span>':'';
	          $append  = ($begin+$len<mb_strlen($model->description))?'<span class="end-quote">...</span>':'';
            ?>
              <p class="part-description"><?= $prepend ?><?= $desc ?><?= $append ?></p>
            <?php
          }
        ?>

        <?php if ($model->tags){ ?>
          <ul class="some-tags div-inline">
            <?php foreach($model->tags as $tag){ ?>
                    <li>
                      <a href="<?= \yii\helpers\Url::to('/backend/knowledge/search?q='.$tag->name.'&root='.$model->id.'&type=tag'); ?>"><?= $tag->name ?></a>
                    </li>
            <?php } ?>
              </ul>
        <?php } ?>
    </div>

	</div>
	<div class="col-2">
		<p class="date-change"><?= Yii::$app->formatter->asRelativeTime($model->updated_at?$model->updated_at:$model->created_at) ?></p>
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
