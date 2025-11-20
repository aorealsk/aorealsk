<?php

namespace frontend\controllers;

use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\models\Aoreal;
use frontend\models\properties\ContactForm as PropertiesContactForm;

// ★ Add this: your registration form model
use frontend\models\RegistrationForm;

class SiteController extends Controller
{
    public function actionPage()
    {
        $lang_iso = 'sk';
        $view = Yii::$app->request->get('view');
        if ($view == 'ajax') {
            return $this->actionAjax();
        }
        $pageAlias = Aoreal::pageAlias();
        $page = $pageAlias[$lang_iso][$view];
        if (!isset($page) || empty($page)) {
            return $this->render('index');
        }
        if ($page[1] && !is_null($page[1])) {
            Yii::$app->view->title = $page[1];
            Yii::$app->view->params['breadcrumbs'][] = $page[1];
        }
        switch ($page[0]) {
            case 'contact':
                $model = new ContactForm();
                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                        Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
                    } else {
                        Yii::$app->session->setFlash('error', 'There was an error sending your message.');
                    }
                    return $this->refresh();
                } else {
                    return $this->render('contact', ['model' => $model]);
                }
                break;
            case 'properties':
                $searchForm = Yii::$app->request->get();
                if (isset($searchForm['submitPropertySearch'])) {
                    $properties = Aoreal::getProperties();
                } else {
                    $view = Yii::$app->request->get('view');
                    $exclusive = $view == 'exkluzivne-ponuky';
                    $newest = $view == 'najnovsie-ponuky';
                    $properties = Aoreal::getProperties($exclusive, $newest);
                }
                $model = new PropertiesContactForm();
                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    echo "ok";
                }
                return $this->render($page[0], [
                    'properties' => $properties,
                    'model' => $model,
                ]);
            break;
            case 'property':
                $id_property = Yii::$app->request->get('id');
                $property_details = Aoreal::getProperty($id_property);
                return $this->render($page[0], ['property_details' => $property_details]);
            break;
            default:
                return $this->render($page[0]);
        }
    }

    public function translang($string)
    {
        return 'ok';
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                // ★ Add 'register' so the rule below applies to it
                'only' => ['logout', 'signup', 'register'],
                'rules' => [
                    [
                        // ★ Guests may access signup + register
                        'actions' => ['signup', 'register'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $page = Aoreal::pageAlias()['sk'][Yii::$app->request->get('view')][0];
        if (isset($page) && $page == 'properties') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actions()
    {
        return [
            'error' => ['class' => 'yii\web\ErrorAction'],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionAjax()
    {
        $ajaxAction = Yii::$app->request->post('ajaxAction');
        $json_error = '';
        switch ($ajaxAction) {
            case 'getDisctrictsByRegion':
                $region_id = (int)Yii::$app->request->post('filter_id');
                $result_list = Aoreal::getDistricts($region_id, true);
                break;
            case 'getTownsByDistrict':
                $district_id = (int)Yii::$app->request->post('filter_id');
                $result_list = Aoreal::getTowns($district_id, null, true);
                break;
            case 'getTownsByChar':
                $q = Yii::$app->request->post('q');
                $result_list = Aoreal::getTownsByChar($q);
                break;
            case 'getProperties':
                $property_type = Yii::$app->request->post('property_type');
                $result_list = Aoreal::getProperties($property_type);
                break;
            case 'getPropertiesForMap':
                $property_type = Yii::$app->request->post('property_type');
                $result_list = Aoreal::getPropertiesForMap($property_type);
                break;
            default:
                $json_error = 'Wrong action';
        }
        $json_return = [
            'error' => $json_error,
            'list'  => $result_list
        ];
        die(json_encode($json_return));
    }

    public function actionIndex()
    {
        $page = Yii::$app->request->get('page');
        switch ($page) {
            case 'property':
                return $this->render('property');
            default:
                return $this->render('index');
        }
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';
            return $this->render('login', ['model' => $model]);
        }
    }

    public function actionPartnerLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';
            return $this->render('partner-login', ['model' => $model]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionKontakt()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }
            return $this->refresh();
        } else {
            return $this->render('contact', ['model' => $model]);
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }
        return $this->render('signup', ['model' => $model]);
    }

    // ★ Add this: the registration action used by your "Create an account" button
    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new RegistrationForm();
        if ($model->load(Yii::$app->request->post()) && ($user = $model->register())) {
            Yii::$app->user->login($user);
            return $this->goHome();
        }

        return $this->render('register', ['model' => $model]);
    }

    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', ['model' => $model]);
    }

    /**
     * Resets password.
     * @param string $token
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');
            return $this->goHome();
        }

        return $this->render('resetPassword', ['model' => $model]);
    }

    /* SK */
    public function actionCennik()
    {
        return $this->render('pricelist');
    }

    public function actionObchodnePodmienky()
    {
        return $this->render('terms-general');
    }

    public function actionReklamacnyPoriadok()
    {
        return $this->render('terms-complaint');
    }

    public function actionMakleri()
    {
        return $this->render('agents');
    }

    public function actionNehnutelnosti()
    {
        return $this->render('properties');
    }

    public function actionNehnutelnost()
    {
        return $this->render('property');
    }

    /* HU */
    public function actionKapcsolat()
    {
        return $this->actionKontakt();
    }

    public function actionArlista()
    {
        return $this->actionCennik();
    }

    public function actionSzerzodesiFeltetelek()
    {
        return $this->actionObchodnePodmienky();
    }

    public function actionReklamaciosFeltetelek()
    {
        return $this->actionReklamacnyPoriadok();
    }

    public function actionUgynokok()
    {
        return $this->actionMakleri();
    }

    public function actionIngatlanok()
    {
        return $this->actionNehnutelnosti();
    }

    public function actionIngatlan()
    {
        return $this->actionNehnutelnost();
    }
}
