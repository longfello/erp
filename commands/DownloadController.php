<?php
namespace app\commands;

use app\models\Fs;
use app\models\FsFile;
use yii\console\Controller;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class DownloadController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionCreate($id, $type)
    {
    	$model = Fs::findOne(['id' => $id]);
    	if ($model){
		    $zip = new \ZipArchive();

		    $hash = $model->getDownloadHash($type);
		    $path = $model->getDownloadPath();
		    $lock_file     = $path.$hash.'.zip.lock';
		    $archive_file  = $path.$hash.'.zip';
		    touch($lock_file);

		    $query = FsFile::find()->where(['fs_id' => $id])->orderBy(['filename' => SORT_ASC]);
		    if ($type != FsFile::TYPE_ALL){
			    $query = $query->andWhere(['type' => $type]);
		    }
		    $models = $query->all();
		    $cnt = count($models);
		    $i = 0;

		    file_put_contents($lock_file, '0%');

		    $size_limit  = 50*1024*1024;
		    $count_limit = 100;

		    $sizel = $countl = 0;
		    $zip_closed = false;

		    foreach ( $models as $one ) {
			    if ($zip->open($archive_file, \ZipArchive::CREATE)!==TRUE) {
				    exit("Невозможно открыть архив\n");
			    }
			    $zip_closed = false;
		    	/** @var $one FsFile */
			    $zip->addFile($one->getPath().$one->filename, $one->original_filename);
			    $i++;
			    $countl++;
			    $sizel += filesize($one->getPath().$one->filename);

			    $percent = round(100*$i/$cnt);

			    $html = <<<HTML
<div class="progress">
  <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="{$percent}" aria-valuemin="0" aria-valuemax="100" style="width: {$percent}%;">
    <span>{$percent}% выполнено</span>
  </div>
</div>
HTML;

			    file_put_contents($lock_file, $html);

			    if ($sizel > $size_limit || $countl > $count_limit) {
				    $zip->close();
				    $zip_closed = true;
				    $countl = $sizel = 0;
			    }
		    }
		    if (!$zip_closed) $zip->close();
		    unlink($lock_file);

		    echo $id . "\n";
	    } else {
		    exit("Отсутствует модель\n");
	    }
    }
}
