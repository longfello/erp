<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this  yii\web\View
 * @var $form  yii\widgets\ActiveForm
 * @var $model \app\models\User
 */

$this->title = Yii::t('user', 'Account settings');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row row-center">
    <div class="col-md-5 col-sm-5">
        <?= $this->render('_menu') ?>
    </div>
    <div class="col-md-7 col-sm-7">
        <div class="panel panel-default panel-user">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'id'          => 'account-form',
                    'options'     => ['class' => 'form-horizontal'],
                    'fieldConfig' => [
                        'template'     => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-lg-9 user-account-error\">{error}\n{hint}</div>",
                        'labelOptions' => ['class' => 'col-lg-3 control-label focus-input-label'],
                    ],
                    'enableAjaxValidation'   => false,
                    'enableClientValidation' => false,
                ]); ?>

                <?= $form->field($model, 'last_name', ['options' => ['class' => 'form-group wrap-focus-input']])->textInput(['maxlength' => 255, 'placeholder' => '', 'class' => 'form-control focus-input']) ?>
	            <?= $form->field($model, 'first_name', ['options' => ['class' => 'form-group wrap-focus-input']])->textInput(['maxlength' => 255, 'placeholder' => '', 'class' => 'form-control focus-input']) ?>
	            <?= $form->field($model, 'middle_name', ['options' => ['class' => 'form-group wrap-focus-input']])->textInput(['maxlength' => 255, 'placeholder' => '', 'class' => 'form-control focus-input']) ?>
	            <?= $form->field($model, 'bithday' , ['options' => ['class' => 'form-group wrap-focus-input']])->widget(\kartik\date\DatePicker::classname(), [
	              'pluginOptions' => [
		              'format' => 'dd.mm.yyyy',
		              'todayHighlight' => true,
                ],
                'options' => [
	                'value' => $model->bithday?Yii::$app->formatter->asDate(strtotime($model->bithday), 'php:d.m.Y'):'', 'placeholder' => ' ', 'class' => 'form-control focus-input'
                ]
              ]) ?>
	            <?= $form->field($model, 'phone', ['options' => ['class' => 'form-group wrap-focus-input']])->widget(\borales\extensions\phoneInput\PhoneInput::className(), [
		            'jsOptions' => [
			            'allowExtensions' => true,
			            'onlyCountries' => ['ru'],
		              'allowDropdown' => false,
                  'autoHideDialCode' => false,
                  'nationalMode' => false,
                  'utilsScript' => 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/9.2.6/js/utils.js'
		            ],
		            'defaultOptions' => ['class' => 'form-control focus-input', 'placeholder' => ' ']
	            ]); ?>
	            <?= $form->field($model, 'position_id')->widget(\kartik\select2\Select2::className(), [
	                    'data' => [null => 'Должность'] + \yii\helpers\ArrayHelper::map(\app\models\Position::find()->orderBy(['sort_order' => SORT_ASC])->all(), 'id', 'name'),
                        'language' => 'ru',
                        'theme' => \kartik\select2\Select2::THEME_BOOTSTRAP
                ]) ?>
	            <?= $form->field($model, 'theme')->radioList(['light' => 'Светлая тема', 'dark' => 'Тёмная тема']) ?>

                <div class="form-group">
                    <div class="col-lg-9">
                        <?= Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-block btn-success']) ?><br>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>

        <?php if ($model->module->enableAccountDelete): ?>
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Yii::t('user', 'Delete account') ?></h3>
                </div>
                <div class="panel-body">
                    <p>
                        <?= Yii::t('user', 'Once you delete your account, there is no going back') ?>.
                        <?= Yii::t('user', 'It will be deleted forever') ?>.
                        <?= Yii::t('user', 'Please be certain') ?>.
                    </p>
                    <?= Html::a(Yii::t('user', 'Delete account'), ['delete'], [
                        'class'        => 'btn btn-danger',
                        'data-method'  => 'post',
                        'data-confirm' => Yii::t('user', 'Are you sure? There is no going back'),
                    ]) ?>
                </div>
            </div>
        <?php endif ?>
    </div>
</div>
