<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\assets\AppAsset;

AppAsset::register($this);
$this->title = $this->title?$this->title.' - Jarvis':'Jarvis';

$theme = Yii::$app->user->isGuest?"light":Yii::$app->user->identity->theme;
$sidebarVar = isset($_COOKIE['sidebar-state'])?$_COOKIE['sidebar-state']:0;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>

    <link rel="icon" type="image/png" href="/favicon.png">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&amp;subset=cyrillic" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Exo+2:300,400&amp;subset=cyrillic" rel="stylesheet">
    <?php $this->head() ?>
  <!-- Yandex.Metrika counter -->
  <script type="text/javascript">
    (function (d, w, c) {
      (w[c] = w[c] || []).push(function() {
        try {
          w.yaCounter42217004 = new Ya.Metrika({
            id:42217004,
            clickmap:true,
            trackLinks:true,
            accurateTrackBounce:true,
            webvisor:true,
            ut:"noindex"
          });
        } catch(e) { }
      });

      var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
      s.type = "text/javascript";
      s.async = true;
      s.src = "https://mc.yandex.ru/metrika/watch.js";

      if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
      } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
  </script>
  <noscript><div><img src="https://mc.yandex.ru/watch/42217004?ut=noindex" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
  <!-- /Yandex.Metrika counter -->
  <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-37254489-7', 'auto');
    ga('send', 'pageview');

  </script>
</head>
<body class="<?= $theme ?>-theme <?= $sidebarVar?"open-menu":""?>">
<?php $this->beginBody() ?>
<div class="allWrapper flexBlockAll">
    <div class="main-wrapper page-fs">
        <header>
            <a href="#" class="nav-link div-inline">
                <span class="<?= $sidebarVar?"rotateIcon":""?>"></span>
            </a>
            <a href="#" class="logo div-inline">
                <img src="/img/logo.svg" alt="">
            </a>
            <p class="header-ticker div-inline">
                <img src="/img/fs/header-cloud.png" alt=""> <span class="ticker-text">в Москве 2<span class="temp-symbol">&ordm;С</span></span>
            </p>
            <a href="#" class="notice-link div-inline">
                <i class="fa fa-bell-o" aria-hidden="true"></i>
                <span class="notice-counter hidden"></span>
            </a>
            <div id="notifications"></div>
        </header>
        <main class="flexBlockAll">
          <aside class="left-nav <?= $sidebarVar?"open":""?>">
                <ul class="list-left-nav">
	                <?php if (Yii::$app->user->can('admin')){ ?>
                    <li <?= (Yii::$app->controller->id == "default")  ?"class='active'":"" ?>><a href="/backend" class="sidebar-main"><span class="icon"></span><span class="sidebar-text">Главная</span></a></li>
                    <li <?= (Yii::$app->controller->id == "agency")   ?"class='active'":"" ?>><a href="/backend/agency/index" class="agency"><span class="icon"></span><span class="sidebar-text">Агентство</span></a></li>
                    <li <?= (Yii::$app->controller->id == "chips")    ?"class='active'":"" ?>><a href="/backend/chips/index" class="chips"><span class="icon"></span><span class="sidebar-text">Фишки</span></a></li>
                  <?php } ?>
                  <li <?= (Yii::$app->controller->id == "knowledge")?"class='active'":"" ?>><a href="/backend/knowledge/index" class="knowledge-base"><span class="icon"></span><span class="sidebar-text">База знаний</span></a></li>
	                <?php if (Yii::$app->user->can('admin') || Yii::$app->user->can('user-aprill')) { ?>
                      <li <?= (Yii::$app->controller->id == "fileshare")?"class='active'":"" ?>><a href="/backend/fileshare/index" class="file-sharing"><span class="icon"></span><span class="sidebar-text">Обменник</span></a></li>
	                <?php } ?>
                  <?php if (Yii::$app->user->can('admin')){ ?>
                    <li <?= (in_array(Yii::$app->controller->module->id, ["rbac", 'user']))    ?"class='active'":"" ?>><a href="/user/admin/index" class="user"><span class="usersIcon roli-icon"></span><span class="sidebar-text">Роли</span></a></li>
                    <li <?= (Yii::$app->controller->id == "access-query")?"class='active'":"" ?>><a href="/backend/access-query/index" class="query"><span class="usersIcon zaprosy-icon"></span><span class="sidebar-text">Запросы</span></a></li>
                    <li <?= (Yii::$app->controller->id == "position")?"class='active'":"" ?>><a href="/backend/position/index" class="query"><span class="usersIcon dolgnosty-icon"></span><span class="sidebar-text">Должности</span></a></li>
                  <?php } ?>
                </ul>
                <div class="sidebar-bottom div-inline ">
                    <div class="pers-name-wrap div-inline">
                        <a href="/user/settings/account" class="pers-logo">
                            <?php
                            if (Yii::$app->user->identity && Yii::$app->user->identity->avatar) {
                                echo Html::img(Yii::$app->user->identity->avatar, ['alt' => Yii::$app->user->identity->initiales()]);
                            } else { ?>
                                <span class="user-initiales"><?= Yii::$app->user->identity ? Yii::$app->user->identity->initiales() : '' ?></span>
                            <?php } ?>
                        </a>
                        <p class="pers-name"><?= Yii::$app->user->identity ? Yii::$app->user->identity->first_name.' '.Yii::$app->user->identity->last_name : ' '?></p>
                        <a href="#" class="pers-mail"><?= Yii::$app->user->identity ? Yii::$app->user->identity->email : "" ?></a>
                    </div>
                    <ul class="settings flexBlockAll">
                        <li class="div-inline">
                            <a href="/user/settings/account">
                                <span class="icon-settings"></span>
                            </a>
                        </li>
                        <li class="div-inline">
                            <a href="/site/logout">
                                <span class="icon-logout"></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </aside>
            <section class="content"><?= $this->render('_internal', ['content' => $content]) ?></section>
        </main>
    </div>
</div>
<?php
  echo \machour\yii2\notifications\widgets\NotificationsWidget::widget([
        'theme' => \machour\yii2\notifications\widgets\NotificationsWidget::THEME_GROWL,
        'pollInterval' => 30000,
        'clientOptions' => [
	        'duration' => 30000,
            'location' => 'tr',
	        'fixed' => true,
        ],
        'counters' => [
            '.notice-counter',
        ],
        'listSelector' => '#notifications',
    ]);

  if (isset($this->params['appendFooter'])) echo $this->params['appendFooter'];
?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
