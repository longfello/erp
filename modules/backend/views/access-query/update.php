<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AccessQuery */

$this->title = 'Разрешение запроса доступа';
$this->params['breadcrumbs'][] = ['label' => 'Запросы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Разрешение';
?>
<div class="access-query-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
