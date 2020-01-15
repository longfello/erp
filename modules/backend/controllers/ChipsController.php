<?php

namespace app\modules\backend\controllers;
use app\components\Controller;

/**
 * Default controller for the `backend` module
 */
class ChipsController extends Controller
{
	public $layout = Controller::LAYOUT_BACKEND;
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
	    $this->view->title = 'Фишки';

	    $this->view->params['breadcrumbs'] = ['Фишки'];
        return $this->render('index');
    }
}
