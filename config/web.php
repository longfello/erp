<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'jarvis',
	'language' => 'ru',
    'basePath' => dirname(__DIR__),
	'aliases' => [
		'webroot' => realpath(__DIR__.'/../htdocs'),
	],
    'bootstrap' => ['log', 'assetsAutoCompress'],
    'runtimePath'=> basedir.'/tmp/runtime',
	'modules' => [
		'user' => [
			'class' => 'dektrium\user\Module',
			'enableUnconfirmedLogin' => true,
			'confirmWithin' => 21600,
			'cost' => 12,
			'adminPermission' => 'admin',
			'enablePasswordRecovery' => false,
			'modelMap' => [
				'User' => [
					'class' => 'app\models\User',
				],
			],
			'controllerMap' => [
				'registration' => \dektrium\user\controllers\RegistrationController::className(),
				'security' => 'app\controllers\UserController',
				'settings' => 'app\controllers\SettingsController',
			],
		],
		'rbac' => [
			'class' => 'dektrium\rbac\RbacWebModule',
			'adminPermission' => 'admin',
		],
		'notifications' => [
			'class' => 'machour\yii2\notifications\NotificationsModule',
			// Point this to your own Notification class
			// See the "Declaring your notifications" section below
			'notificationClass' => 'app\components\Notification',
			// Allow to have notification with same (user_id, key, key_id)
			// Default to FALSE
			'allowDuplicate' => false,
			// This callable should return your logged in user Id
			'userId' => function() {
				return \Yii::$app->user->id;
			}
		],
		'backend' => 'app\modules\backend\Module',
	],
    'components' => [
	    'assetsAutoCompress' => [
		    'class'         => '\app\components\AssetsAutoCompressComponent',
	    ],
	    'view' => [
	    	/*
		    'class' => \rmrevin\yii\minify\View::class,
		    'base_path' => '@app/htdocs', // path alias to web base
		    'minify_path' => '@app/htdocs/minify', // path alias to save minify result
		    'force_charset' => 'UTF-8', // charset forcibly assign, otherwise will use all of the files found charset
		    'expand_imports' => true, // whether to change @import on content
		    //'css_linebreak_pos' => false,
	    	*/
		    'theme' => [
			    'pathMap' => [
				    '@dektrium/user/views' => '@app/views/user',
				    '@dektrium/rbac/views' => '@app/views/rbac'
			    ],
		    ],
	    ],
	    'assetManager' => [
		    'basePath'=> basedir.'/data/assets',
		    'baseUrl' => '@web/data/assets',
	    ],
	    'image' => [
		    'class' => 'yii\image\ImageDriver',
		    'driver' => 'GD',  //GD or Imagick
	    ],
    	'formatter' => [
    		'class' => \yii\i18n\Formatter::className(),
		    'defaultTimeZone' => '+3',
		    'sizeFormatBase'  => '1000'
	    ],
	    'authClientCollection' => [
		    'class'   => \yii\authclient\Collection::className(),
		    'clients' => [
			    'google' => [
				    'class'        => 'dektrium\user\clients\Google',
				    'clientId'     => '772224467996-s1fkcum298qr6d2v49nb0ctp820av9hn.apps.googleusercontent.com',
				    'clientSecret' => 'smvql5E6GYQMFQkuyxkGAb81',
			    ],
		    ],
	    ],
        'request' => [
            'cookieValidationKey' => 'Jf-bOspmO09uyovEImiKGn3SLwN7qfbL',
        ],
        'cache' => [
            'class' => 'yii\caching\ApcCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
	    'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
	            ['pattern'=>'logout', 'route'=>'site/logout'],
	            ['pattern'=>'site/<action:\w+>', 'route'=>'site/<action>'],
	            ['pattern'=>'user/<action:\w+>', 'route'=>'user/security/<action>'],
	            ['pattern'=>'profile/<action:\w+>', 'route'=>'user/settings/<action>'],

	            '<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>/<id>',
	            '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
	            '<module:\w+>/<action:\w+>/<id:\d+>' => '<module>/default/<action>/<id>',

	            ['class' => 'app\components\ShareUrlRule'],

	            '<module:\w+>/<action:\w+>' => '<module>/default/<action>',
	            '<module:\w+>' => '<module>/default/index',
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['192.168.1.*'],
    ];
}

use dektrium\user\controllers\SecurityController;

\yii\base\Event::on(\dektrium\user\controllers\RegistrationController::class, \dektrium\user\controllers\RegistrationController::EVENT_BEFORE_CONNECT, function (\dektrium\user\events\ConnectEvent $e) {
	/** @var $e \dektrium\user\events\ConnectEvent */
	$account = $e->getAccount();
	/** @var $user \app\models\User */
	$user    = $e->getUser();

	$data    = $e->getAccount()->decodedData;
	/** @var $data array */
	if(isset($data['name'])){
		if (isset($data['name']['familyName'])) $user->last_name  = $data['name']['familyName'];
		if (isset($data['name']['givenName']))  $user->first_name = $data['name']['givenName'];
	}
	if (isset($data['image']) && isset($data['image']['url'])) $user->avatar = $data['image']['url'];

	$user->username = $user->email;

	if ($user->create()) {
		$account->connect($user);
		Yii::$app->controller->trigger(\dektrium\user\controllers\RegistrationController::EVENT_AFTER_CONNECT, $e);

		$auth =  Yii::$app->authManager;
		if(!$auth->getAssignment(\app\models\User::ROLE_USER, $user->id)){
			$auth->assign($auth->getRole(\app\models\User::ROLE_USER), $user->id);
		}

		$domain = '@aprill.ru';
		if (mb_substr($user->email, -mb_strlen($domain), mb_strlen($domain)) !== $domain){
			$user->blocked_at = new \yii\db\Expression('NOW()');
			$user->save();
			Yii::$app->session->set('user-info', $user->attributes);
			Yii::$app->response->redirect('/site/request');
		} else {
			Yii::$app->user->login($user, 1209600); // 2 weeks
			Yii::$app->response->redirect('/');
		}
	}
});
\yii\base\Event::on(SecurityController::class, SecurityController::EVENT_BEFORE_AUTHENTICATE, function (\dektrium\user\events\AuthEvent $e) {
	if ($e->account->user === null) {
		return;
	}

	if($e->account->user->isBlocked){
		Yii::$app->session->set('user-info', $e->account->user->attributes);
		Yii::$app->response->redirect('/site/request');
	}
});
\yii\base\Event::on(SecurityController::class, SecurityController::EVENT_AFTER_AUTHENTICATE, function (\dektrium\user\events\AuthEvent $e) {
	// if user account was not created we should not continue
	if ($e->account->user === null) {
		return;
	}
	switch ($e->client->getName()) {
		case 'google':
			$data    = $e->getAccount()->decodedData;
			/** @var $data array */
			$update_data = [];
			if(isset($data['name'])){
				if (isset($data['name']['familyName'])) $update_data['last_name']  = $data['name']['familyName'];
				if (isset($data['name']['givenName']))  $update_data['first_name'] = $data['name']['givenName'];
			}
			if (isset($data['image']) && isset($data['image']['url'])) $update_data['avatar'] = $data['image']['url'];

			if($update_data){
				$e->account->user->updateAttributes($update_data);
			}
	}

	// after saving all user attributes will be stored under account model
	// Yii::$app->identity->user->accounts['facebook']->decodedData
});

return $config;
