<?php

namespace app\controllers;

use app\components\Controller;
use app\forms\RequestForm;
use app\models\AccessQuery;
use app\models\FileshareFile;
use app\models\Fs;
use app\models\FsFile;
use app\models\Notification;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\HttpException;
use yii\web\Response;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
//                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
	    if (Yii::$app->user->isGuest){
		    $this->layout = self::LAYOUT_LOGIN;
		    return $this->render('index');
	    } else {
		    $this->redirect('/backend');
	    }
    }

    public function actionSharefile($model){
    	/** @var $model FileshareFile */

    	$action = Yii::$app->request->get('act', 'view');

    	if ($action == 'dl'){
		    if (Yii::$app->session->get('enable_download_'.$model->id, false)){
			    $file = $model->getPath().$model->id;

			    $path_parts = pathinfo( $model->name );
			    $ext        = strtolower(isset($path_parts['extension'])?$path_parts['extension']:'');
			    $filename = Inflector::slug(isset($path_parts['filename'])?$path_parts['filename']:'').'.'.$ext;

			    return \Yii::$app->response->sendFile($file, $filename);
		    } else {
			    return $this->redirect(Url::to(['/files/'.$model->share_hash]));
		    }
	    } else {
		    $error = false;
		    if (Yii::$app->request->isPost){
			    if ($model->password){
				    $pass = sha1(Yii::$app->request->post('password', ''));
				    if ($pass == $model->password){
					    Yii::$app->session->set('enable_download_'.$model->id, true);
					    return $this->redirect(Url::to(['/files/'.$model->share_hash, 'act' => 'dl']));
				    } else {
					    $error = 'Неверный пароль';
					    Yii::$app->session->set('enable_download_'.$model->id, false);
					    return $this->render('sharefile', ['model' => $model, 'error' => $error]);
				    }
			    }
			    Yii::$app->session->set('enable_download_'.$model->id, true);
			    return $this->redirect(Url::to(['/files/'.$model->share_hash, 'act' => 'dl']));
		    }
		    return $this->render('sharefile', ['model' => $model, 'error' => $error]);
	    }
    }

    public function actionRequest(){
	    $this->view->title = 'Запрос доступа';

	    $this->layout = self::LAYOUT_LOGIN;

	    $userData   = Yii::$app->session->get('user-info', []);

	    if ($userData) {
		    $model = new RequestForm();

		    $query = AccessQuery::findOne(['user_id' => $model->user_id]);
		    if (!$query) {
			    $query = new AccessQuery();
			    if ($model->load(Yii::$app->request->post()) && $model->validate()) {
				    // данные в $model удачно проверены
				    $query->user_id = $model->user_id;
				    $query->query   = $model->query;
				    $query->save();
				    return $this->render( 'request-sended');
			    } else {
				    return $this->render( 'request', [
					    'userData' => $userData,
					    'model'    => $model
				    ] );
			    }
		    } else {
			    return $this->render( 'request-already', [
				    'query'    => $query
			    ] );
		    }
	    } else {
		    return $this->redirect('/');
	    }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionDownload(){
	    $this->view->title = 'Загрузка';

	    return $this->render('download');
    }
    public function actionShare($type, $model, $action){
	    $this->view->title = 'Поделиться';
    	/** @var $model Fs */
	    if (!$model || ($model->type != Fs::TYPE_CARD)){
		    throw new HttpException(404, 'Карточка не найдена');
	    }

	    $this->view->title .= ' - '.$model->name;

	    if (!$model->fsFiles) {
		    throw new HttpException(404, 'Карточка не содержит файлов');
	    }

	    switch($action) {
		    case 'dl':
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
						    'info'     => $model->getDownloadInfo($type)
					    ];
				    } else {
					    return $this->render('download-wait', [
						    'info' => $model->getDownloadInfo($type),
						    'model' => $model
					    ]);
				    }
			    }
			    break;
		    default:
			    $hash = ($type == FsFile::TYPE_MAIN)?$model->share_hash:$model->share_hash_plus;
			    return $this->render('download', ['model' => $model, 'hash' => $hash, 'type' => $type]);
	    }
    }

	public function actionNotifySeen($type, $id){
		switch($type){
			case 'file':
				Notification::updateAll([
					'seen' => 1
				],[
					'key' => 'new_file',
					'key_id' => $id,
					'user_id' => \Yii::$app->user->id
				]);
				break;
		}

		Yii::$app->response->format = Response::FORMAT_JSON;
		return [
			'count' => count(Notification::findAll(['seen' => 0, 'user_id' => \Yii::$app->user->id]))
		];
	}

}
