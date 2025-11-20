<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'baseUrl'   => '/'
        ],
        /*'user' => [
            'identityClass' => 'common\models\client\Client',
            'enableAutoLogin' => false,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true, 'path' => '/'],
		'loginUrl' => ['/client/login'],
            'enableSession' => true,
            'authTimeout' => 3600,
        ],*/
	  'user' => [
            'identityClass' => 'common\models\fbcharity\PromoPersonal',
            'enableAutoLogin' => false,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true, 'path' => '/'],
            'loginUrl' => ['/promo/login'],
            'enableSession' => true,
            'authTimeout' => 3600,
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                ],
            ],
        ],
	'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@frontend/messages',
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                    ],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            //'urlFormat'=>'path',
            'baseUrl'   => '/',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => array(
		'open-days' => 'open-days/index',
                'open-days/<action:[a-zA-Z0-9-]+>' => 'open-days/<action>',
		'dual' => 'dual/index',
		'dual/<action:[a-zA-Z0-9-]+>' => 'dual/<action>',
		'tiper' => 'tiper/index',
                'tiper/<u:[0-9a-zA-Z]+>/<s:[a-zA-Z]+>' => 'tiper/index',
		'reservation' => 'reservation/index',
		'financial-request' => 'financial-request/index',
		'dev-test' => 'dev-test/index',
            'dev-test/<action:[a-zA-Z0-9-]+>' => 'dev-test/<action>',
		'promo/<action:[a-zA-Z0-9-]+>' => 'promo/<action>',
		'promo/<action:[a-zA-Z0-9-]+>/<pro:[0-9]+>' => 'promo/<action>',
		'promo/<action:[a-zA-Z0-9-]+>/<l:[a-z]{2}>' => 'promo/<action>',
            'promo/<action:[a-zA-Z0-9-]+>/<l:[a-z]{2}>/<k:[0-9a-zA-Z]+>' => 'promo/<action>',
            'promo/<slug:[0-9a-zA-Z\-]+>/<action:[a-zA-Z0-9-]+>' => 'promo/index',
            'promo/<slug:[0-9a-zA-Z\-]+>' => 'promo/index',
		'test-mail' => 'test-mail/index',
		'spatna-vazba' => 'response/index',
		# 'client' => 'client/index',
        # 'client/<action:[a-zA-Z0-9-]+>/<id>' => 'client/<action>',
            # 'client/<action:[a-zA-Z0-9-]+>' => 'client/<action>',
		# 'client/<action:[a-zA-Z0-9-]+>/<t:[a-zA-Z0-9-_]+>/<id:\d+>' => 'client/<action>',
            'dotaznik-zalozenie-sro' => 'questionnaire/index',
            'questionnaire' => 'questionnaire/form-submit',
            //  'questionnaire/document-upload' => 'questionnaire/document-upload',
            'questionnaire/<action:[a-zA-Z0-9-]+>' => 'questionnaire/<action>',
        'teacher'            => 'teacher/index',
        'teacher/thank-you'  => 'teacher/thank-you',
        'majster'           => 'majster/index',
      'majster/thank-you' => 'majster/thank-you',
		'students' => 'students/index',
		'students/<action:[a-zA-Z0-9-]+>' => 'students/<action>',
		'students/<action:[a-zA-Z0-9-]+>/<id:\d+>' => 'students/<action>',
		'customer'  =>  'customer/index',
                'customer/<action:[a-zA-Z0-9-]+>' => 'customer/<action>',
                'customer/<action:[a-zA-Z0-9-]+>/<id:\d+>' => 'customer/<action>',
                'applicant' => 'applicant/index',
                'applicant/<action:[a-zA-Z0-9-]+>' => 'applicant/<action>',
                'app-request' => 'app-request/index',
                'app-request/<action:[a-zA-Z0-9-]+>' => 'app-request/<action>',
                'financny-dotaznik/<l:[a-z]{2}>' => 'app-request/index', 
		'financny-dotaznik' => 'app-request/index',
                'financny-dotaznik/<action:[a-zA-Z0-9-]+>' => 'app-request/<action>',
		'app-request-eng' => 'app-request-eng/index',
		'financial-quest' => 'app-request-eng/index',
                'login' => 'site/login',
                'partner-login' => 'site/partner-login',
                //'pattern1'=>array('route1', 'urlSuffix'=>'.html', 'caseSensitive'=>false)
                '<action:(error|captcha)>' => 'site/<action>',
                //'nehnutelnost/<slug:[a-zA-Z0-9-]+>/'        => 'site/page',
                //'/' => 'site/index',
                '<view:(.*)>/<id:\d+>-<rewrite_url:[a-zA-Z0-9-]+>.html' => 'site/page',
                '<view:(.*)>' => 'site/page',
                //'<view:[a-zA-Z0-9-]+>/'                     => 'site/page',
                '<controller:\w+>/<id:\d+>'                 => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>'    => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>'             => '<controller>/<action>',
            ),
            //'scriptUrl' => '/index.php',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=mariadb103.r4.websupport.sk;port=3313;dbname=aoreal',
            'username' => 'aoreal',
            'password' => 'Op9YQ@WC3F',
            'charset' => 'utf8',
        ],
    ],
    'params' => $params,
];
