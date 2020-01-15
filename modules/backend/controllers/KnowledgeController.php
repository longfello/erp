<?php

namespace app\modules\backend\controllers;
use app\components\Controller;
use app\components\UploadHandler;
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
class KnowledgeController extends Controller
{
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['POST'],
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
	    $this->view->title = 'База знаний';

	    $model = Fs::findOne(['id' => $root]);
	    if ($model || is_null($root)) {
		    if (!is_null($root) && !$model->checkAccessRecursive(Fs::ACCESS_VIEW)) throw new HttpException(403, 'У вас нет прав для просмотра данной страницы');

		    if ($model) {
			    $this->view->title .= ' - '.$model->name;
		    }

		    $this->view->params['breadcrumbs'] = [[
			    'url'   => '/backend/knowledge/index',
			    'label' => 'База знаний'
		    ]];

		    if (is_null($root) || $model->type == Fs::TYPE_CATEGORY) {

			    $sort = in_array($sort, [Fs::SORT_NAME, Fs::SORT_TIME, Fs::SORT_SIZE])?$sort:Fs::SORT_DEFAULT;
			    $dir  = in_array($dir, [SORT_ASC, SORT_DESC])?$dir:SORT_ASC;

			    $items = Fs::find()->where(['parent_id' => $root])->orderBy(["type" => SORT_DESC, $sort => ($dir == SORT_ASC)?SORT_ASC:SORT_DESC, 'name' => SORT_ASC])->all();
			    $this->setBreadcrumbsPath($root);

			    if ($root) {
				    return $this->render('category', [
					    'items' => $items,
					    'model' => $model,
					    'sort' => $sort,
					    'dir' => $dir,
				    ]);
			    } else {
				    return $this->render('root_category', [
					    'items' => $items,
				    ]);
			    }

		    } else {
			    $this->setBreadcrumbsPath($root);
			    return $this->render('card', [
				    'model' => $model
			    ]);
		    }
	    } else {
		    throw new HttpException(404, 'Страница не найдена');
	    }
    }
    public function actionSearch($q = null, $root = null, $in = 0){
	    $in = (int)$in;
	    $this->view->title = 'База знаний - поиск';

	    $this->view->params['breadcrumbs'] = [
	    	[
			    'url'   => '/backend/knowledge/index',
			    'label' => 'База знаний'
	        ],
	    	[
			    'label' => 'Поиск'
	        ],
	    ];

	    $model = Fs::findOne(['id' => $root]);
	    $model = $model?$model:new Fs();

	    $type = \Yii::$app->request->get('type', false);

	    $inSql = ($in)?"AND fs.parent_id = {$in}":"";

	    switch ($type){
		    case 'tag':
			    $sql = "
SELECT DISTINCT fs.* FROM fs
LEFT JOIN fs_tag tag ON tag.fs_id = fs.id
LEFT JOIN fs_tags tags ON tags.id = tag.tag_id
LEFT JOIN fs_file file ON file.fs_id = fs.id
WHERE fs.type = '".Fs::TYPE_CARD."' AND 
(
	   tags.name = :keywords 
) $inSql
ORDER BY fs.name
";
			    $query = Fs::findBySql($sql, [':keywords' => $q]);
		    	break;
		    default:
		    	$tags = explode(' ', $q);
			    $tags = array_map(function($item){
				    $item = trim($item);
				    $item = $item?$item:uniqid();
				    return '*'.str_replace([" ","\t","\n","\r","\v","*","'", "`", "-", "+", "~", '@', '(', ')', '<', '>', ','], '', $item).'*';
			    }, $tags);
			    $tags = implode(' ', $tags);
// SELECT * FROM `fs` WHERE MATCH (name,description) AGAINST ('*удлиняем* *кло*' IN BOOLEAN MODE);
				/*
			    $sql = "
SELECT DISTINCT fs.* FROM fs
LEFT JOIN fs_tag tag ON tag.fs_id = fs.id
LEFT JOIN fs_tags tags ON tags.id = tag.tag_id
LEFT JOIN fs_file file ON file.fs_id = fs.id
WHERE fs.type = '".Fs::TYPE_CARD."' AND 
(
	   tags.name LIKE :keywords 
	OR fs.name LIKE :keywords
	OR fs.description LIKE :keywords
	OR file.original_filename LIKE :keywords
) $inSql
ORDER BY tags.name LIKE :keywords DESC, fs.name LIKE :keywords DESC, fs.description LIKE :keywords DESC
";
			    */
			    $sql = "
SELECT DISTINCT fs.* FROM fs
LEFT JOIN fs_tag tag ON tag.fs_id = fs.id
LEFT JOIN fs_tags tags ON tags.id = tag.tag_id
LEFT JOIN fs_file file ON file.fs_id = fs.id
WHERE fs.type = '".Fs::TYPE_CARD."' AND 
(
    MATCH (tags.name) AGAINST ('$tags' IN BOOLEAN MODE)
	OR MATCH (fs.name, fs.description) AGAINST ('$tags' IN BOOLEAN MODE)
	OR MATCH (file.original_filename) AGAINST ('$tags' IN BOOLEAN MODE)
) $inSql
";
			    $query = Fs::findBySql($sql);
	    }


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
    public function actionUpdate($id){
	    $this->view->title = 'База знаний - редактирование';
	    $this->view->params['breadcrumbs'] = [[
		    'url'   => '/backend/knowledge/index',
		    'label' => 'База знаний'
	    ]];
	    $model = Fs::findOne(['id' => $id]);
	    if ($model){
		    $this->setBreadcrumbsPath($id);
		    if ($model->checkAccess(Fs::ACCESS_EDIT)){
			    $root = $model->parent_id;
			    if (\Yii::$app->request->isPost){
				    if ($model->load($_POST) && $model->save()) {
				    	if ($model->regenerateHash){
				    		$model->share_hash = $model->generateHash(FsFile::TYPE_MAIN);
				    		$model->save();
					    }
				    	if ($model->regenerateHashPlus){
				    		$model->share_hash_plus = $model->generateHash(FsFile::TYPE_ALL);
				    		$model->save();
					    }

					    $model->recalcSize();

					    // Сохранение тэгов
					    FsTag::deleteAll(['fs_id' => $model->id]);
					    $tags = \Yii::$app->request->post('tag', []);
					    foreach ($tags as $tag){
						    $tag = FsTags::findOne(['id' => $tag]);
						    if ($tag) {
							    $tagModel = new FsTag();
							    $tagModel->fs_id = $model->id;
							    $tagModel->tag_id = $tag->id;
							    if (!$tagModel->save()) {
								    throw new HttpException(403, print_r($tagModel->getErrors(), true));
							    }
						    }
					    }

					    // Сохраниение прав
					    FsRights::deleteAll(['fs_id' => $model->id]);

					    $read  = \Yii::$app->request->post('righttoview', []);
					    $edit  = \Yii::$app->request->post('righttoedit', []);
					    $share = \Yii::$app->request->post('righttoshare', false);
					    $share = $share?[$share]:[];

					    $max = max(count($read), count($edit), count($share));
					    for($i = 0; $i < $max; $i++){
						    $rightModel = new FsRights();
						    $rightModel->fs_id = $model->id;
						    $rightModel->view  = array_pop($read);
						    $rightModel->edit  = array_pop($edit);
						    if ($share) {
						    	$value = array_pop($share);
						    	if ($value == 'fs_internal'){
								    $rightModel->share = $value;
							    }
						    }
						    if (!$rightModel->save()) {
							    throw new HttpException(403, print_r($rightModel->getErrors(), true));
						    }
					    }
					    $this->redirect(\yii\helpers\Url::to(['/backend/knowledge/index', 'root' => $root ]));
					} else {
					    throw new HttpException(403, print_r($model->getErrors(), true));
				    }
			    }
			    return $this->render('update', [
			    	'model' => $model
			    ]);
		    } else {
			    throw new HttpException(403, 'У вас нет прав для просмотра данной страницы');
		    }
	    } else {
		    throw new HttpException(404, 'Страница не найдена');
	    }
    }
    public function actionDelete($id){
	    $this->view->title = 'База знаний - удаление';
	    $model = Fs::findOne(['id' => $id]);
	    if ($model){
		    if ($model->checkAccess(Fs::ACCESS_EDIT)){
			    $root = $model->parent_id;
			    $model->delete();
			    $this->redirect(\yii\helpers\Url::to(['/backend/knowledge/index', 'root' => $root ]));
		    } else {
			    throw new HttpException(403, 'У вас нет прав для просмотра данной страницы');
		    }
	    } else {
		    throw new HttpException(404, 'Страница не найдена');
	    }
    }
	public function actionUpload($id, $type, $mode = FsFile::TYPE_MAIN){
		$this->view->title = 'База знаний - загрузка';
		$fs = Fs::findOne(['id' => $id]);
		if ($fs->checkAccess(Fs::ACCESS_EDIT)) {
			switch ($type){
				case 'temporary':
					$model = new FsFile();
					$model->fs_id = $id;
					$model->type = FsFile::TYPE_MAIN;
					$model->directory = 'temporary';
					break;
				case 'temporary-additional':
					$model = new FsFile();
					$model->fs_id = $id;
					$model->type = FsFile::TYPE_ADDITIONAL;
					$model->directory = 'temporary';
					break;
				case 'file':
					$model = new FsFile();
					$model->type = $mode;
					$model->fs_id = $id;
					break;
				case 'preview':
					$model = new FsPreview();
					$model->fs_id = $id;
					break;
				default:
					throw new HttpException(404, 'Страница не найдена');
			}

			$directory = $model->getPath();

			$uploader = new UploadHandler([
				/** @var $model FsFile|FsPreview */
				'param_name' => \yii\helpers\StringHelper::basename(get_class($model))
			]);
			if (!$uploaded_file = $uploader->complete) \Yii::$app->end();

			if (file_exists($uploaded_file)) {
				$file_info = pathinfo($uploaded_file);
				$file_size = $uploader->get_file_size($uploaded_file);
				$file_info['extension'] = isset($file_info['extension'])?$file_info['extension']:'';
				$model->filename = $model->generateFilename($file_info['basename'], $file_info['extension']);
				if ($model->hasAttribute('size')){
					$model->size = $file_size;
				}
				if ($model->hasAttribute('original_filename')){
					$model->original_filename = $file_info['basename'];
				}


				$filePath = $directory . $model->filename;
				if (copy($uploaded_file, $filePath)) {
					unlink($uploaded_file);
					if ($model->save()){
						$path = $model->getUrl();
						return Json::encode([
							'files' => [[
								'name' => $model->filename,
								'size' => $file_size,
								'sizeText' => \Yii::$app->formatter->asShortSize($file_size, 0),
								"url" => $path,
								"thumbnailUrl" => $model->getIcon(),
								"original_name" => $file_info['basename'],
							]]
						]);
					}
				}
			}
		}
		return '';
	}
	public function actionDeletePreview(){
		$this->view->title = 'База знаний - предпросмотр';
		$id       = \Yii::$app->request->post('id', false);
		$filename = \Yii::$app->request->post('filename', false);
		$model    = FsPreview::findOne(['fs_id' => $id, 'filename' => $filename]);
		$responce = [
			'result' => false
		];
		if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost){
			if ($model && $model->fs->checkAccess(Fs::ACCESS_EDIT)) {
				$responce['result'] = $model->delete();
			}
		}
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $responce;
	}
	public function actionDeleteFile(){
		$this->view->title = 'База знаний - удаление';

		$id       = \Yii::$app->request->post('id', false);
		$filename = \Yii::$app->request->post('filename', false);
		$model    = FsFile::findOne(['fs_id' => $id, 'filename' => $filename]);
		$responce = [
			'result' => false
		];
		if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost){
			if ($model) {
				if ($model->fs->checkAccess(Fs::ACCESS_EDIT)) {
					$responce['result'] = $model->delete();
				}
			}
		}
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $responce;
	}
	public function actionCreateCard($root){
		$this->view->title = 'База знаний - добавление карточки';

		$fs = Fs::findOne(['id' => $root]);
		if ($fs->checkAccess(Fs::ACCESS_EDIT)){
			$model = new Fs();
			$model->name = \Yii::$app->request->post('name');
			$model->additional_name = \Yii::$app->request->post('add-name', null);
			$model->description = \Yii::$app->request->post('description');
			$model->parent_id = $fs->id;
			$model->type = Fs::TYPE_CARD;
			$model->user_id = \Yii::$app->user->id;
			if (!$model->save()) var_dump($model->getErrors());

			$model->recalcSize();

			$right = \Yii::$app->request->post('rights', false);
			if ($right && $right == 'fs_internal' ) {
				$rmodel = new FsRights();
				$rmodel->fs_id = $model->id;
				$rmodel->share  = $right;
				$rmodel->save();
			}

			// Сохранение тэгов
			$tags = \Yii::$app->request->post('tags');
			$tags = explode(',', $tags);
			foreach ($tags as $tag){
				$tag = trim($tag);
				$tmodel = FsTags::findOne(['name' => $tag]);
				if (!$tmodel) {
					$tmodel = new FsTags();
					$tmodel->name = $tag;
					$tmodel->save();
				}
				$ttmodel = new FsTag();
				$ttmodel->tag_id = $tmodel->id;
				$ttmodel->fs_id = $model->id;
				$ttmodel->save();
			}

			// Сохранение файлов
			$files    = \Yii::$app->request->post('filename');
			$original = \Yii::$app->request->post('original_filename');
			foreach ($files as $key => $file){
				$fmodel = FsFile::findOne(['filename' => $file, 'original_filename' => $original[$key], 'fs_id' => $model->parent_id]);

				if (!$fmodel) { continue; }
				$fmodel->fs_id = $model->id;
				$fmodel->save();
				$fmodel->moveTemporaryFile();
			}

			// $notify
			$notify = \Yii::$app->request->post('notify');
			$targets = [];
			switch ($notify){
				case '1':
					$targets = User::find()->all();
					break;
			}
			foreach ($targets as $one){
				$id = ($one instanceof User)?$one->id:$one;
				Notification::notify(Notification::KEY_NEW_FILE, $id, $model->id);
			}
			$this->redirect(['/backend/knowledge/index', 'root' => $model->parent_id]);
		} else throw new HttpException(403, 'У вас нет прав для просмотра данной страницы');
	}
	public function actionCreateCategory($root = null){
		$this->view->title = 'База знаний - добавление категории';

		$fs = Fs::findOne(['id' => $root]);
		if (is_null($root) || $fs->checkAccess(Fs::ACCESS_EDIT)){
			$model = new Fs();
			$model->name = \Yii::$app->request->post('name');
			$model->description = \Yii::$app->request->post('description');
			$model->parent_id = $root;
			$model->type = Fs::TYPE_CATEGORY;
			$model->user_id = \Yii::$app->user->id;
			if (!$model->save()) var_dump($model->getErrors());

			// Сохранение тэгов
			$tags = \Yii::$app->request->post('tags');
			$tags = explode(',', $tags);
			foreach ($tags as $tag){
				$tag = trim($tag);
				$tmodel = FsTags::findOne(['name' => $tag]);
				if (!$tmodel) {
					$tmodel = new FsTags();
					$tmodel->name = $tag;
					$tmodel->save();
				}
				$ttmodel = new FsTag();
				$ttmodel->tag_id = $tmodel->id;
				$ttmodel->fs_id = $model->id;
				$ttmodel->save();
			}

			if ($root) {
				$this->redirect(['/backend/knowledge/index', 'root' => $model->parent_id]);
			} else {
				$this->redirect(['/backend/knowledge/index']);
			}
		} else throw new HttpException(403, 'У вас нет прав для просмотра данной страницы');
	}
	public function actionLoadComments($id, $page = 1){
		$this->view->title = 'База знаний - загрузка комментариев';

		$query = FsComment::find()->where(['fs_id' => $id, 'type' => 'public'])->orderBy(['dt' => SORT_DESC]);
		$countQuery = clone $query;
		$pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 4]);
		$pages->pageSizeParam = false;

		$models = $query->offset($pages->offset)
		                ->limit($pages->limit)
		                ->all();

		krsort($models);

		$html = '';
		foreach($models as $comment){
			$html.= '<div class="comments-all-style">
		<div class="circle-position-style">
			<div class="icon-block-circle bg-color-1">
				<h4>'.$comment->user->getAvatar().'</h4>
			</div>
		</div>
		<div class="text-comments">
			<p><span>'.$comment->user->getName().' </span>'.$comment->comment.'</p>
			<div class="bottom-text-navs">
				<a class="reply" href="#" data-href="@'.$comment->user->getName().': ">Ответить</a>
				<p class="date-navs">'.\Yii::$app->formatter->asRelativeTime($comment->dt).'</p>
			</div>
		</div>
	</div>';
		}

		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$count = $pages->totalCount - $page * $pages->pageSize;

		return [
			'content' => $html,
			'count'   => max(0, min(4, $count))
		];
	}
	public function actionPublishComment($id){
		$this->view->title = 'База знаний - добавить комментарий';
		$text = \Yii::$app->request->post('text');

    	$model = new FsComment();
		$model->fs_id = $id;
		$model->comment = $text;
		$model->type    = FsComment::TYPE_PUBLIC;
		$model->user_id = \Yii::$app->user->id;
		$model->save();
	}
	public function actionSavePrivateComment(){

		$text = \Yii::$app->request->post('text');
		$id = \Yii::$app->request->post('id');


    	$model = FsComment::findOne(['user_id' => \Yii::$app->user->id, 'fs_id' => $id, 'type' => FsComment::TYPE_PRIVATE]);
		if (!$model) {
			$model = new FsComment();
			$model->fs_id = $id;
			$model->user_id = \Yii::$app->user->id;
			$model->type    = FsComment::TYPE_PRIVATE;
		}
		$model->comment = $text;
		$model->save();
		$this->layout = Controller::LAYOUT_AJAX;
		return "Обновлено: ".\Yii::$app->formatter->asRelativeTime(date(\DateTime::W3C));
	}
	public function actionSaveRate(){
		$rate = \Yii::$app->request->post('rate');
		$id   = \Yii::$app->request->post('id');

		$model = FsRate::findOne(['fs_id' => $id, 'user_id' => \Yii::$app->user->id]);
		if (!$model){
			$model = new FsRate();
			$model->fs_id = $id;
			$model->user_id = \Yii::$app->user->id;
		}

		$rate = max(1, $rate);
		$rate = min(5, $rate);
		$model->rate = $rate;
		$model->save();

		$fs = Fs::findOne(['id' => $model->fs_id]);
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return [
			'rate' => round($fs->getOveralRate()),
			'text' => $fs->getOveralRateText(),
		];
	}
	public function actionDownload($type, $id){
		$this->view->title = 'База знаний - загрузка';
		$model = Fs::findOne(['id' => $id]);
		if (!$model || ($model->type != Fs::TYPE_CARD)){
			throw new HttpException(404, 'Карточка не найдена');
		}
		$this->view->title .= ' - '.$model->name;

		if (!$model->fsFiles) {
			throw new HttpException(404, 'Карточка не содержит файлов');
		}

		$file = $model->getDownloadFile($type);
		if ($file) {
			if (\Yii::$app->request->isAjax) {
				\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
				return [
					'complete' => true,
					'info'     => ''
				];
			} else {
				$path_parts = pathinfo( $file );
				$ext        = strtolower(isset($path_parts['extension'])?$path_parts['extension']:'');
				$filename = Inflector::slug($model->name).'.'.$ext;
				return \Yii::$app->response->sendFile($file, $filename);
			}
		} else {
			if (\Yii::$app->request->isAjax) {
				\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
				return [
					'complete' => false,
					'info'     => $model->getDownloadInfo($type),
                    'error'     => $model->getDownloadInfo($type, true)
				];
			} else {
				return $this->render('download-wait', [
					'info' => $model->getDownloadInfo($type),
					'model' => $model,
					'error'     => $model->getDownloadInfo($type, true)
				]);
			}
		}
	}
	public function actionShare($type, $id){
		$this->view->title = 'База знаний';

		if (\Yii::$app->request->isAjax && \Yii::$app->request->isPost) {
			$error = $link = $hash = '';
			$model = Fs::findOne(['id' => $id]);
			if (!$model || ($model->type != Fs::TYPE_CARD)){
				$error = 'Карточка не найдена';
			} else {
				if (!$model->checkAccess(Fs::ACCESS_SHARE)) {
					$error = 'Недостаточно прав';
				} else {
					if (!$model->fsFiles) {
						$error = 'Карточка не содержит файлов';
					} else {
						switch ($type){
							case FsFile::TYPE_MAIN:
								if (!$model->share_hash) {
									$model->share_hash = $model->generateHash(FsFile::TYPE_MAIN);
								}
								$hash = $model->share_hash;
								break;
							case FsFile::TYPE_ALL:
								if (!$model->share_hash_plus) {
									$model->share_hash_plus = $model->generateHash(FsFile::TYPE_ALL);
								}
								$hash = $model->share_hash_plus;
								break;
							default:
								$error = 'Тип шаринга не определен';
						}
						if (!$error) {
							if (!$model->save()) {
								$error = 'Ошибка сохранения ссылки: '.$model->getFirstErrors();
							} else {
								$link = Url::to(['/'.$hash], true);
						}
						}
					}
				}
			}

			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			return [
				'error' => $error,
				'link'  => $link
			];
		} else {
			throw new HttpException(405, 'Метод не разрешен');
		}

	}

    private function setBreadcrumbsPath($root){
	    $this->view->params['breadcrumbs'] = array_merge($this->view->params['breadcrumbs'], $this->getBCArray($root));
    }
    private function getBCArray($root, $iteration = 0){
	    $elements = [];
	    if ($iteration > 6) return $elements;
	    $item = Fs::findOne(['id' => $root]);
	    if ($item) {
		    $elements[] = [
			    'url'   => Url::to(['/backend/knowledge/index', 'root' => $item->id]),
			    'label' => $item->name
		    ];
		    $elements = array_merge($this->getBCArray($item->parent_id, $iteration+1), $elements);
	    }
	    return $elements;
    }
}
