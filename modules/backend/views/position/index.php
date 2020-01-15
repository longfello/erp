<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Должности';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="position-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
	    <?= Html::a('Показать все', ['index', 'all'=>'all'], ['class' => 'btn btn-info pull-left']) ?>
      <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success pull-right']) ?>
    </p>
  <div class="clearfix"></div>
<?php Pjax::begin(); ?>    <?= \richardfan\sortable\SortableGridView::widget([
        'dataProvider' => $dataProvider,
        'sortUrl' => \yii\helpers\Url::to(['sortItem']),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
