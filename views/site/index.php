<?php

use dektrium\user\widgets\Connect;

/* @var $this yii\web\View */

$this->title = 'Jarvis';
?>
<div class="logo">
    <h1><a href="/"><img src="/img/logo.png" alt="Jarvis Logo"></a></h1>
</div>

<div id='login-form' class='formLogin flexBlockAll'>
    <div class="center-block">
        <?= Connect::widget(['baseAuthUrl' => ['/user/security/auth']]) ?>
    </div>
</div>

