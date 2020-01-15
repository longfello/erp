<?php
// подключаем виджет постраничной разбивки
use yii\widgets\LinkPager;

  /**
   * @var $this \yii\web\View
   * @var $pages \yii\data\Pagination
   * @var $model \app\models\Fs
   * @var $models \app\models\Fs[]
   * @var $dir string
   * @var $sort string
   */
$in = Yii::$app->request->get('in', Yii::$app->request->get('root',0));
$q = Yii::$app->request->get('q', '');
?>
<div class="search-wrap search-wrap_file-share">
  <form action="<?= \yii\helpers\Url::to(['/backend/fileshare/search']); ?>" method="get" class="wrap-search-input">
    <div class="wrap-label">
		<?= \kartik\typeahead\TypeaheadBasic::widget([
			'name' => 'q',
			'value' => $q,
			'data' => \yii\helpers\ArrayHelper::map(\app\models\FsTags::find()->all(), 'id', 'name'),
			'pluginOptions' => ['highlight' => true],
			'options' => ['placeholder' => 'Поиск по названию, описанию или тегу', 'class'=>'search-input'],
		]) ?>
      <input type="hidden" name="root" value="<?= $model->id ?>">
      <button type="submit" class="submit"></button>
      <p class="hidden-clues">Поиск по названию, описанию или тегу</p>
    </div>
    <div class="tabs-wrap">
      <div class="fs-tabs list">
        <div class="fs-row flexBlockAll header-row search-nav">
          <div class="col-1">
            <p class="search-in">Искать в:</p>
          </div>
          <div class="col-4">
            <?php if ($model->id){ ?>
              <p class="div-inline search-filter">
                <input type="radio" id="search-this" name="in" value="<?= $model?$model->id:0 ?>" <?= ($model->id == $in)?'checked="checked"':'' ?> >
                <label for="search-this">В папке "<?= $model->name ?>"</label>
              </p>
            <?php } ?>
            <p class="div-inline search-filter">
                <input type="radio" id="search-all" name="in"  value="0"  <?= (0 == $in)?'checked="checked"':'' ?>>
                <label for="search-all">Везде</label>
            </p>
            <p class="div-inline"></p>
          </div>
          <div class="col-7">
            <p class="search-result"><span class="search-number">
                <?= \Yii::$app->i18n->messageFormatter->format(
	                '{n, plural, one{Найден # результат} few{Найдено # результата} many{Найдено # результатов} other{Найдено # результата}}',
	                ['n' => $pages->totalCount],
	                \Yii::$app->language
                ) ?>

                </span>в файлах</p>
          </div>
        </div>
      </div>
    </div>
  </form>
	<?php echo $this->render('_fs_popups', ['model' => $model]); ?>
</div>
<div class="tabs-wrap tabs-wrap_file-sharing tabs-wrap_result-search">
  <div class="functions-wrap">
	  <?php echo $this->render('elements/file-functions', ['model' => $model]); ?>
	  <?php echo $this->render('elements/category-functions', ['model' => $model]); ?>
  </div>
  <div class="fs-tabs list search-results">
    <div class="fs-row flexBlockAll header-row">
      <div class="col-6"><p>Название</p></div>
      <div class="col-2"><p>Дата изменения</p></div>
      <div class="col-1"><p>Размер</p></div>
      <div class="col-3"><p>Функции</p></div>
    </div>
		<?php foreach($models as $item){ ?>
			<?= $this->render('search-elements/card', ['model' => $item, 'update' => false, 'editor' => false, 'q' => $q]) ?>
		<?php } ?>
	</div>
  <?= LinkPager::widget([
  'pagination' => $pages,
  ]); ?>
</div>


<?php

$this->registerJs("
  $('.search-results .col-info').mark('".\yii\bootstrap\Html::encode($q)."');
");
