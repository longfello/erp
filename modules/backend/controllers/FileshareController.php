<?php

namespace app\modules\backend\controllers;
use app\components\Controller;
use app\components\UploadHandler;
use app\models\FileshareCategory;
use app\models\FileshareFile;
use app\models\Fs;
use app\models\FsComment;
use app\models\FsFile;
use app\models\FsPreview;
use app\models\FsRate;
use app\models\FsRights;
use app\models\FsTag;
use app\models\FsTags;
use app\models\User;
use app\components\Notification;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use yii\base\Exception;
use yii\base\Response;
use yii\data\Pagination;
use yii\data\Sort;
use yii\db\Expression;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\HttpException;
use yii\web\UploadedFile;

/**
 * Default controller for the `backend` module
 */
class FileshareController extends Controller
{
	const SORT_NAME = 'name';
	const SORT_TIME = 'updated_at';
	const SORT_SIZE = 'size';

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'deletePreview' => ['POST'],
					'createCard' => ['POST'],
				],
			],
		];
	}

	public $layout = Controller::LAYOUT_BACKEND;
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex($root = null, $sort = null, $dir = null){
	    $this->view->title = 'Файлообменник';

	    $model = FileshareCategory::findOne(['id' => $root, 'user_id' => \Yii::$app->user->id]);
	    if ($model || is_null($root)) {
		    if ($model) {
			    $this->view->title .= ' - '.$model->name;
		    }

		    $this->view->params['breadcrumbs'] = [[
			    'url'   => '/backend/fileshare/index',
			    'label' => 'Файлообменник'
		    ]];

		    $sort = in_array($sort, [self::SORT_NAME, self::SORT_TIME, self::SORT_SIZE])?$sort:self::SORT_NAME;
		    $dir  = in_array($dir, [SORT_ASC, SORT_DESC])?$dir:SORT_ASC;

		    $categories = FileshareCategory::find()->where(['parent_id' => $root, 'user_id' => \Yii::$app->user->id])->orderBy([$sort => ($dir == SORT_ASC)?SORT_ASC:SORT_DESC])->all();
		    $files      = FileshareFile::find()->where(['parent_id' => $root, 'user_id' => \Yii::$app->user->id])->orderBy([$sort => ($dir == SORT_ASC)?SORT_ASC:SORT_DESC])->all();
		    $this->setBreadcrumbsPath($root);

		    return $this->render('category', [
			    'categories' => $categories,
			    'files'      => $files,
			    'model' => $model,
			    'sort' => $sort,
			    'dir' => $dir,
		    ]);
	    } else {
		    throw new HttpException(404, 'Страница не найдена');
	    }
    }
	public function actionFileRename($id, $name){
    	$model = FileshareFile::findOne(['id' => $id]);
		if($model && $model->user_id == \Yii::$app->user->id){
    		$model->name = $name;
    		$model->save();
	    }
	}
	public function actionCategoryRename($id, $name){
    	$model = FileshareCategory::findOne(['id' => $id]);
		if($model && $model->user_id == \Yii::$app->user->id){
    		$model->name = $name;
    		$model->save();
	    }
	}
	public function actionFileDelete($id){
		$this->view->title = 'Файлообменник - удаление';
		$model = FileshareFile::findOne(['id' => $id]);
		if($model && $model->user_id == \Yii::$app->user->id){
			$root = $model->parent_id;
			$model->delete();
			$this->redirect((isset(\Yii::$app->request->referrer) && \Yii::$app->request->referrer) ? \Yii::$app->request->referrer : \yii\helpers\Url::to(['/backend/fileshare/index', 'root' => $root ]));
		} else {
			throw new HttpException(404, 'Страница не найдена');
		}
	}
	public function actionCategoryDelete($id){
		$this->view->title = 'Файлообменник - удаление';
		$model = FileshareCategory::findOne(['id' => $id]);
		if($model && $model->user_id == \Yii::$app->user->id){
			$root = $model->parent_id;
			$model->delete();
			$this->redirect((isset(\Yii::$app->request->referrer) && \Yii::$app->request->referrer) ? \Yii::$app->request->referrer : \yii\helpers\Url::to(['/backend/fileshare/index', 'root' => $root ]));
		} else {
			throw new HttpException(404, 'Страница не найдена');
		}
	}
	public function actionCreateCategory($root = null){
		$this->view->title = 'Файлообменник - добавление категории';
		$root = $root?(int)$root:null;

		$fs = FileshareCategory::findOne(['id' => $root]);
		if (is_null($root) || ($fs && $fs->user_id == \Yii::$app->user->id)){
			$model = new FileshareCategory();
			$model->name = \Yii::$app->request->post('name');
			$model->parent_id = $root;
			$model->user_id = \Yii::$app->user->id;
			if (!$model->save()) var_dump($model->getErrors());

			if ($root) {
				$this->redirect(['/backend/fileshare/index', 'root' => $model->parent_id]);
			} else {
				$this->redirect(['/backend/fileshare/index']);
			}
		} else throw new HttpException(403, 'У вас нет прав для просмотра данной страницы');
	}
	public function actionUpload($id, $type){
		$this->view->title = 'Файлообменник - загрузка';
		$fs = FileshareCategory::findOne(['id' => $id]);
		if (!$id || $fs->user_id == \Yii::$app->user->id) {
			switch ($type){
				case 'file':
					$model = new FileshareFile();
					$model->parent_id = $id?$id:null;
					break;
				default:
					throw new HttpException(404, 'Страница не найдена');
			}
			$uploader = new UploadHandler([
				/** @var $model FileshareFile */
				'param_name' => \yii\helpers\StringHelper::basename(get_class($model))
			]);
			if (!$uploaded_file = $uploader->complete) \Yii::$app->end();

			if (file_exists($uploaded_file)) {
				$file_info = pathinfo($uploaded_file);
				$file_size = $uploader->get_file_size($uploaded_file);
				$file_info['extension'] = isset($file_info['extension'])?$file_info['extension']:'';
				if ($model->hasAttribute('size')){
					$model->size = $file_size;
				}
				if ($model->hasAttribute('original_filename')){
					$model->original_filename = $file_info['basename'];
				}
				if ($model->hasAttribute('name')){
					$model->name = $file_info['basename'];
				}


				if ($model->save()){
					$directory = $model->getPath();
					$filePath = $directory . $model->id;
					if (copy($uploaded_file, $filePath)) {
						unlink($uploaded_file);
						$path = $model->getUrl();
						return Json::encode([
							'files' => [[
								'name' => $model->name,
								'size' => $file_size,
								'sizeText' => \Yii::$app->formatter->asShortSize($file_size, 0),
								"url" => $path,
								"thumbnailUrl" => $model->getIcon('fileshare-upload-file__img'),
								"original_name" => $model->name,
							]]
						]);
					}
				} else {
					var_dump($model->getErrors()); die();
				}
			}
		}
		return '';
	}
	public function actionGeticon($ext){
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return [
		  'ext' => FileshareFile::getIconByExtension($ext, 'fileshare-upload-file__img'),
		];
	}
	public function actionFileDownload($id) {
		$this->view->title = 'Файлообменник - скачать файл';
		$model = FileshareFile::findOne(['id' => $id]);
		if ($model && $model->checkAccess(true)) {
			$file = $model->getPath().$model->id;

			$path_parts = pathinfo( $model->name );
			$ext        = strtolower(isset($path_parts['extension'])?$path_parts['extension']:'');
			$filename = Inflector::slug(isset($path_parts['filename'])?$path_parts['filename']:'').'.'.$ext;

			\Yii::$app->response->sendFile($file, $filename);
		} else  {
			throw new HttpException(404, 'Страница не найдена');
		}
	}
	public function actionShare($id){
		$this->view->title = 'Файлообменник';
		$this->layout = self::LAYOUT_LOGIN;

		$model = FileshareFile::findOne(['id' => $id]);
		if ($model && $model->checkAccess(true)) {

			if (\Yii::$app->request->isPost) {
				if (\Yii::$app->request->post('add_password', false)){
					$model->password = sha1(\Yii::$app->request->post('add_password'));
				} else {
					$model->password = null;
				}
				$model->save();
				$this->redirect(\Yii::$app->request->referrer);
			} else {
				\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

				if (!$model->share_hash) {
					$model->share_hash = $model->generateHash();
					$model->save();
				}

				$path_parts = pathinfo( $model->name );
				$ext        = isset($path_parts['extension'])?$path_parts['extension']:'';

				$data = [
					'password' => (bool)$model->password,
					'url'      => 'https://aprill.ru'.Url::to('/files/'.$model->share_hash),
					'id'       => $model->id,
					'size'     => \Yii::$app->formatter->asShortSize($model->size, 1),
					'ext'      => $ext?'.'.$ext:'',
					'action'   => \Yii::$app->request->url,
				];
				return $data;
			}
		}
	}
	public function actionMove(){
		if (\Yii::$app->request->isPost){
			$type = \Yii::$app->request->post('entity_type', null);
			$id   = \Yii::$app->request->post('entity_id', null);

			$model = false;
			switch($type){
				case 'file':
					$model = FileshareFile::findOne(['id' => $id]);
					break;
				case 'category':
					$model = FileshareCategory::findOne(['id' => $id]);
					break;
			}

			if ($model) {
				$moveToId = \Yii::$app->request->post('move-to', null);
				$moveToId = (int)$moveToId?(int)$moveToId:null;
				$moveTo = FileshareCategory::findOne(['id' => $moveToId]);

				/** @var $moveTo FileshareCategory */
				if (is_null($moveToId) || ($moveTo && $moveTo->checkAccess(true))) {
					$parent = $model->parent;
					$model->parent_id = $moveToId;
					$model->save();
					$model->recalcSize();
					if ($parent){
						$parent->recalcSize();
					}
					if ($moveTo){
						$moveTo->recalcSize();
					}
				}
			}
		}
		$this->redirect(\Yii::$app->request->referrer);
	}

	public function actionSearch($q = null, $root = null, $in = 0){
	    $in = (int)$in;
	    $this->view->title = 'Файлообменник - поиск';

	    $this->view->params['breadcrumbs'] = [
	    	[
			    'url'   => '/backend/fileshare/index',
			    'label' => 'Файлообменник'
	        ],
	    	[
			    'label' => 'Поиск'
	        ],
	    ];

	    $model = FileshareCategory::findOne(['id' => $root]);
	    $model = $model?$model:new FileshareCategory();

	    $inSql = ($in)?"AND fs.parent_id = {$in}":"";

        $tags = explode(' ', $q);
	    $tags = array_map(function($item){
		    $item = trim($item);
		    $item = $item?$item:uniqid();
		    return '*'.str_replace([" ","\t","\n","\r","\v","*","'", "`", "-", "+", "~", '@', '(', ')', '<', '>', ','], ' ', $item).'*';
	    }, $tags);
	    $tags = implode(' ', $tags);

	    $securityClause = \Yii::$app->user->can('admin')?"":"AND fs.user_id = ".\Yii::$app->user->id;

	    $sql = "
SELECT DISTINCT fs.*, MATCH (fs.name) AGAINST ('$tags' IN BOOLEAN MODE) AS relevance 
FROM fileshare_file fs
WHERE  
	MATCH (fs.name) AGAINST ('$tags' IN BOOLEAN MODE)
	{$securityClause}
	{$inSql}
ORDER BY relevance DESC	
";
	    $query = FileshareFile::findBySql($sql);


	    // делаем копию выборки
	    $countQuery = clone $query;
	    // подключаем класс Pagination, выводим по 10 пунктов на страницу
	    $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 10]);
	    // приводим параметры в ссылке к ЧПУ
	    $pages->pageSizeParam = false;

	    $query->sql .= " LIMIT {$pages->limit} OFFSET {$pages->offset} ";

	    $models = $query->all();

	    return $this->render('search', [
		    'models' => $models,
		    'pages' => $pages,
		    'model' => $model,
	    ]);
    }

    private function setBreadcrumbsPath($root){
	    $this->view->params['breadcrumbs'] = array_merge($this->view->params['breadcrumbs'], $this->getBCArray($root));
    }
    private function getBCArray($root, $iteration = 0){
	    $elements = [];
	    if ($iteration > 6) return $elements;
	    $item = FileshareCategory::findOne(['id' => $root]);
	    if ($item) {
		    $elements[] = [
			    'url'   => Url::to(['/backend/fileshare/index', 'root' => $item->id]),
			    'label' => $item->name
		    ];
		    $elements = array_merge($this->getBCArray($item->parent_id, $iteration+1), $elements);
	    }
	    return $elements;
    }
}
