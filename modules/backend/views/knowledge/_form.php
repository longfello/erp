<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Fs */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="fs-form">

    <?php $form = \yii\bootstrap\ActiveForm::begin(); ?>


    <?php
    echo \yii\bootstrap\Tabs::widget([
    'items' => [
        [
            'label' => 'Основное',
            'content' => $this->render('_form_basic', ['model' => $model, 'form' => $form]),
            'active' => true
        ],
        [
            'label' => 'Тэги',
            'content' => $this->render('_form_tags', ['model' => $model, 'form' => $form]),
        ],
        [
            'label' => 'Предпросмотр',
            'content' => $this->render('_form_preview', ['model' => $model, 'form' => $form]),
        ],
        [
            'label' => 'Права',
            'content' => $this->render('_form_rules', ['model' => $model, 'form' => $form]),
        ]
    ],
]);
    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
