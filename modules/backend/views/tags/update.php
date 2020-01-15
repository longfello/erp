<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\FsTags */

$this->title = 'Редактирование тэга: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Тэги', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="fs-tags-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
