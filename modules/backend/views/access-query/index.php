<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Запросы доступа';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="access-query-index">

    <h1><?= Html::encode($this->title) ?></h1>

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'user_id' => [
                'label'  => 'Пользователь',
                'format' => 'raw',
                'value'  => function($data){
                    $model = \app\models\User::findOne($data->user_id);
                    if ($model) {
                        return $model->email;
                    } else return '?';
                },
            ],
            'query:ntext',
            'answer:ntext',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}&nbsp;{delete}'
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
