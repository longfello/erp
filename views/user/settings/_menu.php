<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\helpers\Html;
use yii\widgets\Menu;

/** @var dektrium\user\models\User $user */
$user = Yii::$app->user->identity;
$networksVisible = count(Yii::$app->authClientCollection->clients) > 0;

?>

<div class="panel panel-default panel-user-ava">
    <div class="panel-heading">
        <h3 class="panel-title"> <?php
            if (Yii::$app->user->identity && Yii::$app->user->identity->avatar) {
              echo Html::img(Yii::$app->user->identity->avatar, ['alt' => Yii::$app->user->identity->initiales(), 'class' => 'img-rounded']);
            } else { ?>
                <span class="user-initiales"><?= Yii::$app->user->identity ? Yii::$app->user->identity->initiales() : '' ?></span>
            <?php } ?>
            <p class="to-acc">Привязано к аккаунту</p>
            <a href="#" class="acc-link"><?= $user->username ?></a>
        </h3>
    </div>
    <div class="panel-body ">
        <?= Menu::widget([
            'options' => [
                'class' => 'nav nav-pills nav-stacked',
            ],
            'items' => [
                // ['label' => Yii::t('user', 'Profile'), 'url' => ['/user/settings/profile']],
                ['label' => Yii::t('user', 'Profile'), 'url' => ['/user/settings/account']],
                [
                    'label' => Yii::t('user', 'Networks'),
                    'url' => ['/user/settings/networks'],
                    'visible' => $networksVisible
                ],
            ],
        ]) ?>
    </div>
</div>
