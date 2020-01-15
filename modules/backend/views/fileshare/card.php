<?php
/**
 * @var $this \yii\web\View
 * @var \app\models\Fs $model
 */
\app\assets\CommentAsset::register($this);
?>
<div class="item-content-left item-content" data-id="<?= $model->id ?>">
	<div class="item-img-monitor">
		<div class="slick-gallery">
			<?php if ($slides = $model->getPreview()){ ?>
			    <?php foreach($slides as $slide){ ?>
					<div><?= $slide ?></div>
				<?php } ?>
			<?php } else { ?>
				<div style="background:#ccf3ff"></div>
			<?php } ?>
		</div>
		<div class="img-monitor-text">
			<p><?= $model->name ?></p>
		</div>
	</div>
	<div class="block-date-person">
		<div class="date-block">
			<span><?= Yii::$app->formatter->asDate($model->updated_at, 'php:d.m.Y') ?></span>
		</div>
		<div class="person-block">
			<span><?= $model->user?$model->user->getName():'' ?></span>
		</div>
	</div>
	<div class="block-button-item <?php if (!$model->checkAccess(\app\models\Fs::ACCESS_SHARE)){ ?>share-internal<?php } ?>">
    <?php if ($model->checkAccess(\app\models\Fs::ACCESS_SHARE)){ ?>
		  <button class="share-button" data-toggle="modal" data-target=".modal-share">Поделиться</button>
    <?php } ?>
		<button class="download-button" data-toggle="modal" data-target=".modal-download">Скачать</button>
	</div>
	<div class="clearfix-content"></div>
	<div class="note-block">
		<div class="note-background">
			<p class="text-note">Здесь можете оставить заметку. Она будет видна только вам</p>
			<textarea name="" id="" cols="30" rows="10" class="textarea-notes" autocomplete="off"><?= $model->fsCommentPrivate?$model->fsCommentPrivate->comment:'' ?></textarea>
			<?php if ($model->fsCommentPrivate){ ?>
				<span class="update-text">Обновлено: <?= Yii::$app->formatter->asRelativeTime($model->fsCommentPrivate->dt) ?></span>
			<?php } ?>
		</div>
	</div>
</div>
<div class="item-content-right item-content" data-id="<?= $model->id ?>">
	<?php if ($model->notes){ ?>
		<div class="block-notification">
			<p><strong>Важно:</strong> <?= $model->notes; ?></p>
		</div>
	<?php } ?>
	<div class="clearfix-content"></div>
	<h2 class="title-materials"><?= $model->name ?></h2>
	<div class="evaluation-item">
		<ul class="evaluation rate-wrapper rate-<?= round($model->getOveralRate()); ?>">
			<li class="rate" data-value="1"><span class="evaluation-bg"></span></li>
			<li class="rate" data-value="2"><span class="evaluation-bg"></span></li>
			<li class="rate" data-value="3"><span class="evaluation-bg"></span></li>
			<li class="rate" data-value="4"><span class="evaluation-bg"></span></li>
			<li class="rate" data-value="5"><span class="evaluation-bg"></span></li>
		</ul>
		<ul class="evaluation">
			<li><p class="useful"><?= $model->getOveralRateText() ?></p></li>
			<li class="hidden set-rate"><a href="#" class="evaluation-now">Оценить сейчас</a></li>
		</ul>
	</div>
	<p class="some-text"><?= $model->description ?></p>
	<div class="clearfix-content"></div>
	<?php if ($model->tags){ ?>
		<div class="desc-tags">
			<ul class="some-tags">
				<?php foreach($model->tags as $tag){ ?>
					<li>
						<a href="<?= \yii\helpers\Url::to('/backend/fileshare/search?q='.$tag->name.'&root='.$model->id.'&type=tag'); ?>"><?= $tag->name ?></a>
					</li>
				<?php } ?>
			</ul>
		</div>
	<?php } ?>
	<div class="comments-item-block" data-id="<?= $model->id ?>">
		<a href='#' class="even-comments">Посмотреть еще <span class="event-cnt"></span> комментария</a>
		<div class="card-comments"></div>

		<div class="addit-comment-block">
			<div class="circle-position-style">
				<div class="icon-block-circle bg-color-3 color-title">
					<h4><?= Yii::$app->user->identity->getAvatar(); ?></h4>
				</div>
			</div>
			<form class="addit-text-comment addit-text-comment-form">
				<input type="text" name="comment" class="comment-input" placeholder="Ваш комментарий">
				<button type="submit" class="fa fa-planerBlock"></button>
			</form>
		</div>
	</div>
</div>

<?php
  $additional_name = $model->additional_name?$model->additional_name:'дополнительные материалы';
  $download_main = "<a href=".\yii\helpers\Url::to(['/backend/fileshare/download', 'type' => \app\models\FsFile::TYPE_MAIN, 'id' => $model->id], true)." target='_blank' class='download-link download-link-all'>Основной файл (". Yii::$app->formatter->asShortSize($model->getFsMainSize(), 0).")</a>";
  $download_additional = $model->fsFilesAdditional?"<a href=".\yii\helpers\Url::to(['/backend/fileshare/download', 'type' => \app\models\FsFile::TYPE_ALL, 'id' => $model->id], true)."  target='_blank' class='download-link-big download-link-all'>Все файлы (". Yii::$app->formatter->asShortSize($model->size, 0).")</a>":'';

  $share_main_text = $model->share_hash?"<p class=\"share-file-name main\">{$model->name} <span class=\"file-size\">(".Yii::$app->formatter->asShortSize($model->getFsMainSize(), 0).")</span></p>":'';
  $share_main = $model->share_hash?"<p class='link-text main'><input type='text' class='text-wrap' value='".\yii\helpers\Url::to("/{$model->share_hash}", true)."' title='".\yii\bootstrap\Html::encode("/{$model->share_hash}")."' readonly><a href='#' data-name='".\yii\bootstrap\Html::encode($model->name)."' data-url='".\yii\helpers\Url::to(['/'.$model->share_hash], true)."' class='link-btn-share'></a></p>":"<a href=".\yii\helpers\Url::to(['/backend/fileshare/share', 'type' => \app\models\FsFile::TYPE_MAIN, 'id' => $model->id], true)." class='share-this share-link share-link-all'><span class='link-name'>Основной файл</span> (". Yii::$app->formatter->asShortSize($model->getFsMainSize(), 0).")</a>";

  $share_additional_text = $share_additional = '';
  if ($model->fsFilesAdditional) {
	  $share_additional_text = $model->share_hash_plus?"<p class=\"share-file-name other\">{$additional_name}<span class=\"file-size\">(".Yii::$app->formatter->asShortSize($model->size, 0).")</span></p>":'';
	  $share_additional = $model->share_hash_plus?"<p class='link-text main'><input type='text' class='text-wrap' value='".\yii\helpers\Url::to("/{$model->share_hash_plus}", true)."' title='".\yii\bootstrap\Html::encode("/{$model->share_hash_plus}")."' readonly><a href='#' data-name='".\yii\bootstrap\Html::encode($additional_name)."' data-url='".\yii\helpers\Url::to(['/'.$model->share_hash_plus], true)."' class='link-btn-share'></a></p>":"<a href=".\yii\helpers\Url::to(['/backend/fileshare/share', 'type' => \app\models\FsFile::TYPE_ALL, 'id' => $model->id])." class='share-this share-link share-link-all'><span class='link-name'>{$additional_name}</span> (". Yii::$app->formatter->asShortSize($model->size, 0).")</a>";
  }

  $this->params['appendFooter'] = isset($this->params['appendFooter'])?$this->params['appendFooter']:"";
  if ($model->checkAccess(\app\models\Fs::ACCESS_SHARE)) {
	  $this->params['appendFooter'] .= <<<HTML
<div class="modal fade modal-share" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content modal-share-content">
            <div class="exit-block">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <h2 class="modalTitle">Поделиться</h2>
            <form class="share-form-modal">
                {$share_main_text}
                {$share_main}
                
                {$share_additional_text}
                {$share_additional}
                <div class="errors"></div>
            </form>
        </div>
    </div>
</div>
HTML;
  }

$this->params['appendFooter'] .= <<<HTML
<div class="modal fade modal-download" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content modal-download-content">
            <div class="exit-block">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <h2 class="modalTitle">Выберите вариант</h2>
            <form class="download-form-modal">
                {$download_main}
                {$download_additional}
            </form>
        </div>
    </div>
</div>
HTML;
?>