<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\FsTags */

$this->title = 'Добавить тэг';
$this->params['breadcrumbs'][] = ['label' => 'Тэги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fs-tags-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
