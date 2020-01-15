<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Fs */

$this->title = 'Create Fs';
$this->params['breadcrumbs'][] = ['label' => 'Fs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fs-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
