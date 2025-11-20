<?php
namespace frontend\controllers;

use common\models\client\ClientContact;
use common\models\client\ClientPersonalInfo;
use common\models\client\ClientSocial;
use common\models\client\form\ClientLoginForm;
use common\models\sys\SysLog;
use frontend\models\client\SignupForm;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\helpers\HtmlPurifier;
use common\models\client\Client;
use common\models\Stat;
use common\models\client\ClientLogin;

class ClientController extends Controller
{
    /**
     * @return array
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'my-services' => [
                'class' => 'frontend\actions\client\MyServicesAction'
            ],
        ];
    }

    /**
     * @return void|\yii\web\Response
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(Url::to(['/client/login']));
        }
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(Url::to(['/client/my-profile']));
        }
        $model = new ClientLoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            $log = new ClientLogin();
            $log->client_id = Yii::$app->user->getId();
            $log->log_ip = Yii::$app->getRequest()->getUserIP();
            $log->save();

            return $this->redirect(Url::to(['/client/my-profile']));
            //return $this->goBack();
        } else {
            $model->password = '';
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(Url::to('/'));
    }

    /**
     * @return string
     */
    public function actionPasswordReset()
    {
        if(Yii::$app->request->isPost)
        {
            $email = Yii::$app->request->post('email');
            $client = Client::findOne(['email' => $email]);

            if($client === null){
                Yii::$app->session->setFlash('error', Yii::t('app','Neplatný email. Skúste znova'));
                return $this->redirect('/client/password-reset');
            }

            Yii::$app->mailer->compose()
                ->setFrom('info@aoreal.sk')
                ->setTo($email)
                ->setSubject('Obnovenie hesla')
                ->setHtmlBody("<a href=\"https://aoreal.sk/client/reset-form/$client->auth_key\">Odkaz na obnovenie hesla</a>")
                ->send();

            Yii::$app->session->setFlash('success', Yii::t('app','Email bol úspešne odoslaný'));
        }

        return $this->render('reset');
    }

    public function actionResetForm($id)
    {
        if(Yii::$app->request->isPost){
            $password = Yii::$app->request->post('password');
            $passwordConfirmation = Yii::$app->request->post('password_confirmation');

            if(strlen($password) < 6) 
            {
                Yii::$app->session->setFlash('passwordLength', Yii::t('app','Heslo musí byť dlhšie ako 6 znakov'));
                return $this->redirect("/client/reset-form/$id");
            } elseif($password !== $passwordConfirmation)
            {
                Yii::$app->session->setFlash('error', Yii::t('app','Nesprávne heslo'));
                return $this->redirect("/client/reset-form/$id");
            }
            $client = Client::findOne(['auth_key' => $id]);
            $client->setPassword($password);
            $client->save();

            return $this->redirect('/client/login');
        }
        return $this->render('reset-form');
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            $client = $model->signup();
            if (!is_null($client)) {
                Yii::$app->mailer
                    ->compose(['html' => 'accountActivation-html'],[
                        'client' => $client
                    ])
                    ->setFrom('info@aoreal.sk')
                    ->setTo($client->email)
                    ->setSubject(Yii::t('app','Aktivácia účtu na aoreal.sk'))
                    ->send();
                return $this->redirect(Url::to(['/client/account-created']));
            }
        }

        return $this->render('signup', [
            'model' => $model,
            'countries' => $this->getCountriesAreaCode()
        ]);
    }

    /**
     * @return array
     */
    private function getCountriesAreaCode(): array
    {
        $countries = Stat::find()->all();
        $countriesResult = [];
        array_walk($countries, function($key, $value) use (&$countriesResult){
            if ($key->predvolba != '') {
                $countriesResult[$key->predvolba] = "+{$key->predvolba} - {$key->international_name}";
            }
        });

        return $countriesResult;
    }


    /**
     * @return string
     */
    public function actionAccountCreated()
    {
        return $this->render('account-created');
    }

    /**
     * @return string
     */
    public function actionMyProfile()
    {
        if (is_null(Yii::$app->user->identity)) {
            $this->redirect(Url::to(['/client/login']));
            return false;
        }

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('Profile');
            if (Yii::$app->request->post('action') === 'profile') {
                $this->updateProfile($data);
            }
            if (Yii::$app->request->post('action') === 'pass') {
                $this->updatePassword($data);
            }
        }
        return $this->render('myprofile',[
            'client' => Client::findOne(['id'=>Yii::$app->user->getId()]),
            'countries' => $this->getCountriesAreaCode()
        ]);
    }

    /**
     * @param array $data
     * @return void
     */
    private function updateProfile(array $data)
    {
        $tr = Yii::$app->db->beginTransaction();
        try {
            // update phone number and name
            $personal = ClientPersonalInfo::findOne(['client_id' => Yii::$app->user->getId()]);
            $personal->first_name = $data['first_name'];
            $personal->last_name = $data['last_name'];
            $personal->save();
            $contact = ClientContact::findOne(['client_id' => Yii::$app->user->getId()]);
            if (is_null($contact)) {
                $contact = new ClientContact();
                $contact->client_id = Yii::$app->user->getId();
            }
            $contact->mobile_area_code = $data['phone']['countryCode'];
            $contact->mobile = $data['phone']['number'];
            $contact->save();
            // update social links
            $social = ClientSocial::findOne(['client_id'=>Yii::$app->user->getId()]);
            if (!$social) {
                $social = new ClientSocial();
                $social->client_id = Yii::$app->user->getId();
            }
            foreach($data['social'] as $key => $item) {
                $social->$key = $item;
            }
            $social->save();
            $tr->commit();
            Yii::$app->session->setFlash('success', Yii::t('app','Dáta boli úspešne aktualizované'));
        } catch (\Exception $e) {
            $tr->rollBack();
            SysLog::WriteError(0,__CLASS__,$e->getTraceAsString(),__LINE__);
            Yii::$app->session->setFlash('error', $e->getTraceAsString());
        }
    }

    /**
     * @param array $data
     * @return void
     */
    private function updatePassword(array $data): bool
    {
        $client = Client::findOne(['id'=>Yii::$app->user->getId()]);
        if (!$client->validatePassword($data['password']['old'])) {
            Yii::$app->session->setFlash('error', Yii::t('app','Staré heslo je neplatné!'));
            return false;
        }
        if ($data['password']['new'] != $data['password']['new_repeat']) {
            Yii::$app->session->setFlash('error', Yii::t('app','Nové heslá sa nezhodujú!'));
            return false;
        }
        $tr = Yii::$app->db->beginTransaction();
        try {
            $client = Client::findOne(['id'=>Yii::$app->user->getId()]);
            $client->setPassword($data['password']['new']);
            $client->save();
            $tr->commit();
            Yii::$app->session->setFlash('success', Yii::t('app','Heslo bolo úspešne zmenené!'));
        } catch (\Exception $e) {
            $tr->rollBack();
            SysLog::WriteError(0,__CLASS__, $e->getTraceAsString(), __LINE__);
        }
        return true;
    }

    /**
     * @param string $t
     * @param int $i
     * @return \yii\web\Response
     */
    public function actionActivateAccount()
    {
        $authKey = HtmlPurifier::process(Yii::$app->request->get('t'));
        $id = (int)(Yii::$app->request->get('id'));
        if ($id > 0) {
            $client = Client::findOne(['id'=>$id]);
            if ($client) {
                if ($client->validateAuthKey($authKey)) {
                    $client->status = Client::STATUS_ACTIVE;
                    $client->save();
                    return $this->redirect(Url::to(['/client/my-profile']));
                } else {
                    $status = 'invalid-key';
                }
            } else {
                $status = 'client-not-found';
            }
        } else {
            $status = 'wrong-id';
        }

        return $this->redirect(Url::to(['/client/account-status', 's' => $status, 'id' => $id]));
    }

    /**
     * @param string $s
     * @param int $id
     * @return string
     */
    public function actionAccountStatus(string $s, int $id)
    {
         return $this->render('account-status',[
            'client' => Client::findOne(['id'=>$id]),
            'status' => $s,
        ]);
    }

}