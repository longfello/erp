<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

/**
 * @var yii\bootstrap\ActiveForm    $form
 * @var dektrium\user\models\User   $user
 */
?>

<?= $form->field($user, 'first_name')->textInput(['maxlength' => 255]) ?>
<?= $form->field($user, 'last_name')->textInput(['maxlength' => 255]) ?>
<?= $form->field($user, 'middle_name')->textInput(['maxlength' => 255]) ?>
<?= $form->field($user, 'email')->textInput(['maxlength' => 255]) ?>
<?= $form->field($user, 'bithday')->widget(\kartik\date\DatePicker::classname(), [
	'pluginOptions' => [
		'format' => 'yyyy-mm-dd',
		'todayHighlight' => true,
	],
	'options' => [
		'value' => $user->bithday?Yii::$app->formatter->asDate(strtotime($user->bithday), 'php:Y-m-d'):''
	]
]) ?>
<?= $form->field($user, 'phone')->widget(\borales\extensions\phoneInput\PhoneInput::className(), [
	'jsOptions' => [
		'allowExtensions' => true,
		'onlyCountries' => ['ru'],
		'allowDropdown' => false,
		'autoHideDialCode' => false,
		'nationalMode' => false,
		'utilsScript' => 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/9.2.6/js/utils.js'
	],
	'defaultOptions' => ['class' => 'form-control']
]); ?>
<?= $form->field($user, 'position_id')->dropDownList(\yii\helpers\ArrayHelper::map(\app\models\Position::find()->orderBy(['sort_order' => SORT_ASC])->all(), 'id', 'name')) ?>
<?= $form->field($user, 'theme')->dropDownList(['light' => 'Светлая', 'dark' => 'Тёмная']) ?>
