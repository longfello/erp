<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AccessQuery */
/* @var $form yii\widgets\ActiveForm */

$user = \app\models\User::findOne($model->user_id);
$username = $user?$user->email:'?';
?>

<div class="access-query-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="alert alert-info">
        <p>Пользователь <?= $username ?> обратился с запросом на разрешение доступа с комментарием:</p>
        <p><?= $model->query?$model->query:"---" ?></p>
    </div>

    <?= $form->field($model, 'answer')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'allow')->dropDownList(['0' => 'Запретить', '1' => 'Разрешить']) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
