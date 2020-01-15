<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $info string */
/* @var $model \app\models\Fs */

$this->title = 'Создание архива';
?>

<div class="well">
	  Запрошенный архив создается, ожидайте... <div class="dl-info"><?= $info ?></div>
</div>

<?php

  $this->registerJs("
var dlintrvl = setInterval(function(){
  $.get(document.location.href, function(data){
    if (data.complete){
      clearInterval(dlintrvl);
      $('.dl-info').html('<div class=\"alert alert-success\" role=\"alert\">Готово!</div>');
      document.location.reload();
    } else {
      $('.dl-info').html(data.info);
    }
  }, 'json');
}, 3000);
");

?>
