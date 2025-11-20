<?php

namespace backend\controllers;

use common\models\cache\UserMatrixCache;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\users\UsersStats;
use backend\models\RegistrationForm;

class SiteController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'register', 'error'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'index', 'testnotice'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['login', 'register'],
                        'allow' => false,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['get', 'post'],
                ],
            ],
        ];
    }

    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login: redirects to testnotice if test not yet completed
     */
    public function actionLogin()
    {
        $this->layout = 'login';

        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['site/index']);
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // bookkeeping
            $this->userStats(UsersStats::ACTION_LOGIN);
            Yii::$app->user->identity->loadUserGroups();
            $this->loadRole((int)Yii::$app->user->identity->getId());
            if (!UserMatrixCache::isLoaded()) {
                UserMatrixCache::load();
            }

            // check test status
            $userId = Yii::$app->user->id;
            $testCompleted = $this->isStudentTestCompleted($userId);

            if (!$testCompleted) {
                return $this->redirect(['site/testnotice']);
            }

            return $this->redirect(['site/index']);
        }

        Yii::$app->user->setReturnUrl(\yii\helpers\Url::to(['site/index']));
        $model->password = '';
        return $this->render('login', ['model' => $model]);
    }

    /**
     * Logout
     */
    public function actionLogout(): Response
    {
        $userId = (int)Yii::$app->user->getId();
        $this->userStats(UsersStats::ACTION_LOGOUT);
        UserMatrixCache::clear();
        Yii::$app->cache->delete('role_' . $userId);
        Yii::$app->session->destroy();
        Yii::$app->user->logout();
        return $this->redirect(['site/login']);
    }

    /**
     * Registration (same logic as login)
     */
    public function actionRegister()
    {
        $this->layout = 'login';

        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['site/index']);
        }

        $model = new RegistrationForm();

        if ($model->load(Yii::$app->request->post()) && ($user = $model->register())) {
            Yii::$app->user->login($user);

            $this->userStats(UsersStats::ACTION_LOGIN);
            Yii::$app->user->identity->loadUserGroups();
            $this->loadRole((int)Yii::$app->user->identity->getId());
            if (!UserMatrixCache::isLoaded()) {
                UserMatrixCache::load();
            }

            $userId = Yii::$app->user->id;
            $testCompleted = $this->isStudentTestCompleted($userId);

            if (!$testCompleted) {
                return $this->redirect(['site/testnotice']);
            }

            return $this->redirect(['site/index']);
        }

        return $this->render('register', ['model' => $model]);
    }

    /**
     * New internal test notice page
     */
    public function actionTestnotice()
    {
        $userId = Yii::$app->user->id ?? null;
        if (!$userId) {
            return $this->redirect(['site/login']);
        }

        if ($this->isStudentTestCompleted($userId)) {
            return $this->redirect(['site/index']);
        }

        // Link to the real frontend test
        $frontendTestUrl = '/studenttest.php?uid=' . $userId;

        return $this->render('testnotice', [
            'frontendTestUrl' => $frontendTestUrl,
        ]);
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    private function userStats(string $action): void
    {
        $stats = new UsersStats();
        $stats->userAction = $action;
        $stats->userId = Yii::$app->user->getId();
        $stats->userIp = Yii::$app->getRequest()->getUserIP();
        $stats->save();
    }

    private function loadRole(int $userId): void
    {
        $sql = "SELECT item_name FROM auth_assignment WHERE user_id=:uid";
        $role = Yii::$app->db->createCommand($sql)->bindValues([':uid' => $userId])->queryScalar();
        Yii::$app->cache->set('role_' . $userId, strtolower((string)$role));
    }

    private function isStudentTestCompleted(int $userId): bool
    {
        $path = Yii::getAlias('@webroot/studenttests/' . $userId . '.done');
        return file_exists($path);
    }
}
