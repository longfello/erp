<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
if ($exception->statusCode == 404) $message = 'Запрошенная вами страница не найдена';
?>
<div class="site-error">
    <div class="logo logo-error">
        <h1><a href="/"><img src="/img/error-img.svg" alt="Jarvis Logo"></a></h1>
    </div>
<!--    <h1 class="error-titles">--><?//= Html::encode($this->title) ?><!--</h1>-->

    <div class="alert alert-danger error-number">
        <span>Ошибка</span> <span><?= $exception->statusCode ?></span>
    </div>

    <p class="err-some-text"><?= nl2br(Html::encode($message)) ?></p>

</div>
