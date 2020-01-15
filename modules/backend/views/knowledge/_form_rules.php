<?php
/**
 *
 * @var \app\models\Fs $model
 * @var \yii\bootstrap\ActiveForm $form
 */

$read  = $write = [];
$share = 'fs_external';

$rightsList   = Yii::$app->authManager->getRoles();

if ($model->fsRights){
	foreach ($model->fsRights as $one){
		if ($one->view)  $read[] = $one->view;
		if ($one->edit)  $write[] = $one->edit;
		if ($one->share && $one->share == 'fs_internal') $share = 'fs_internal';
	}
}

if (!$read){
	foreach ($rightsList as $role){
		/** @var $role \yii\rbac\Role */
		$permissions = Yii::$app->authManager->getPermissionsByRole($role->name);
		if (isset($permissions['fs_view'])) $read[] = $role->name;
	}
}

if (!$write){
	foreach ($rightsList as $role){
		/** @var $role \yii\rbac\Role */
		$permissions = Yii::$app->authManager->getPermissionsByRole($role->name);
		if (isset($permissions['fs_edit'])) $write[] = $role->name;
	}
}

$dropdownData = \yii\helpers\ArrayHelper::map($rightsList, 'name', 'name');

?>
<br>
<div class="well">
	Если не выбрана ни одна роль, доступ к элементу считается согласно умолчания. <br>Выбрать несколько ролей можно с помощью удержания кнопки Ctrl
</div>
<div class="row">
	<div class="col-xs-4">Просмотр</div>
	<div class="col-xs-4">Редактирование</div>
	<div class="col-xs-4">Шаринг</div>
</div>
<div class="row">
	<div class="col-xs-4">
		<?= \yii\bootstrap\Html::dropDownList('righttoview', $read, $dropdownData, ['multiple' => 'multiple', 'size' => 15, 'class' => 'col-xs-12', 'autocompete' => 'off']) ?>
	</div>
	<div class="col-xs-4">
		<?= \yii\bootstrap\Html::dropDownList('righttoedit', $write, $dropdownData, ['multiple' => 'multiple', 'size' => 15, 'class' => 'col-xs-12', 'autocompete' => 'off']) ?>
	</div>
	<div class="col-xs-4">
		<?= \yii\bootstrap\Html::dropDownList('righttoshare', $share, [
		  'fs_external' => 'публичный материал',
		  'fs_internal' => 'внутренний материал'
    ], ['size' => 15, 'class' => 'col-xs-12', 'autocompete' => 'off']) ?>
	</div>
</div>