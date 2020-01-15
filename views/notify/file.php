<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 30.11.16
 * Time: 16:56
 *
 * @var $this \yii\web\View
 * @var $model \app\models\Fs
 */
if ($model) {
  if ($model->user){ ?>
    <div class="dashboard-link-name notification-file-<?= $model->id ?>">
      <div class="icon-block">
        <?php
        if ($model->user->avatar) {
          echo \yii\bootstrap\Html::img($model->user->avatar, ['alt' => $model->user->initiales()]);
        } else { ?>
          <span class="user-initiales"><?= $model->user->initiales() ?></span>
        <?php } ?>
      </div>
      <div class="text-link-name">
        <p><a href="#"><?= $model->user->first_name ?></a> добавил <a href="<?= \yii\helpers\Url::to(['/backend/knowledge/index', 'root'=>$model->id]) ?>"><?= $model->name ?></a></p>
        <p class="dashboard-time-text"><?= Yii::$app->formatter->asRelativeTime(date(DateTime::W3C, strtotime($model->created_at))) ?></p>
      </div>
      <a class="close-notification" data-type="file" data-id="<?= $model->id ?>">×</a>
    </div>
  <?php }
}