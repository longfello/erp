<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Fs */

$this->title = 'Редактирование: ' . $model->name;
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="fs-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
