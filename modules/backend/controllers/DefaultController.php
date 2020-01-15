<?php

namespace app\modules\backend\controllers;
use app\components\Controller;

/**
 * Default controller for the `backend` module
 */
class DefaultController extends Controller
{
	public $layout = Controller::LAYOUT_BACKEND;
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
	    if (!\Yii::$app->user->can('admin')){
	    	return $this->redirect('/backend/knowledge/index');
	    }
	    $this->view->title = 'Главная';
        return $this->render('index');
    }
}
