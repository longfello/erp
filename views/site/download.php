<div class="allWrapper flexBlockAll download-wrap">
    <div class="main-download-wrap">
        <?= $model->getIcon('download-file-type', $model->name);?>
        <p class="download-file-name"><?= $model->name ?><span class="download-file-extension"></span></p>
        <p class="download-file-size"><?= Yii::$app->formatter->asShortSize((($type == \app\models\FsFile::TYPE_MAIN)?$model->getFsMainSize():$model->size), 0) ?></p>
        <a href="<?= \yii\helpers\Url::to(["/{$hash}/dl"]) ?>" class="download-file">Скачать</a>
    </div>
</div>