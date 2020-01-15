<?php
/**
 *
 * @var \app\models\Fs[] $items
 */
$editor = Yii::$app->user->can('admin');
?>

<?php if ($editor) { echo $this->render('_fs_popups', ['model' => new \app\models\Fs()]); } ?>
<ul class="root-category-items">
	<?php foreach($items as $item){ ?>
    <?php if ($item->checkAccessRecursive(\app\models\Fs::ACCESS_VIEW)){ ?>
      <li>
        <a class="ajaxify" href="<?= \yii\helpers\Url::to(['/backend/knowledge/index', 'root' => $item->id ]) ?>">
          <?= $item->getIcon("folder-img", $item->name); ?>
          <span class="folder-name" title="<?= \yii\bootstrap\Html::encode($item->name); ?>"><?= $item->name ?></span>
        </a>
        <?php if ($item->checkAccess(\app\models\Fs::ACCESS_EDIT)){ ?>
          <ul class="functions">
            <li><a href="<?= \yii\helpers\Url::to(['/backend/knowledge/update', 'id'=>$item->id]) ?>" title="Редактировать"><i class="fa fa-pencil"></i></a></li>
            <li><a data-pjax="0" data-method="post" data-confirm="Удалить папку вместе со всем содержимым？" aria-label="Удалить" title="Удалить" href="<?= \yii\helpers\Url::to(['/backend/knowledge/delete', 'id'=>$item->id]) ?>"><i class="fa fa-remove"></i></a></li>
          </ul>
        <?php } ?>
      </li>
    <?php } ?>
  <?php } ?>
  <?php if ($editor) { ?>
    <li><a href="#" class="add-folder wrap-add-plus"  data-toggle="modal" data-target=".modal-create-category" title="Создать папку"><span class="add-plus">+</span><span class="add-text">Добавить</span></a></li>
  <?php } ?>
</ul>