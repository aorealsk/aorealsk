<?php
namespace frontend\models\client;

use common\models\client\ClientContact;
use common\models\client\ClientPersonalInfo;
use common\models\sys\SysLog;
use Yii;
use yii\base\Model;
use common\models\client\Client;

class SignupForm extends Model
{
    public $email;
    public $password;
    public $passwordConfirm;
    public $firstName;
    public $lastName;
    public $phone;
    public $country;
    private $_client = null;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\client\Client', 'message' => Yii::t('app','Tento email už bol použitý na registráciu. Použite prosím druhý.')],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['passwordConfirm', 'required'],
            ['passwordConfirm', 'string', 'min' => 6],
            ['passwordConfirm', 'compare', 'compareAttribute' => 'password', 'message' => Yii::t('app','Zadané heslá sa nezhodujú')],

            ['firstName', 'trim'],
            ['firstName', 'required'],
            ['firstName', 'string', 'max' => 255],

            ['lastName', 'trim'],
            ['lastName', 'required'],
            ['lastName', 'string', 'max' => 255],

            ['phone', 'trim'],
            ['phone', 'required'],

            ['country', 'trim'],
            ['country', 'required'],
        ];
    }

    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        $tr = Yii::$app->db->beginTransaction();
        $pid = getmypid();

        try{
            $this->_client = new Client();
            $this->_client->email = $this->email;
            $this->_client->setPassword($this->password);
            $this->_client->generateAuthKey();
            $result = $this->_client->save();
            if ($result) {
                $personalInfo = new ClientPersonalInfo();
                $personalInfo->client_id = $this->_client->id;
                $personalInfo->first_name = $this->firstName;
                $personalInfo->last_name = $this->lastName;
                $personalInfo->save();
                $contact = new ClientContact();
                $contact->client_id = $this->_client->id;
                $contact->mobile_area_code = $this->country;
                $contact->mobile = $this->phone;
                $contact->save();
            }
            $tr->commit();
            SysLog::WriteInfo($pid, __CLASS__, "Client was saved.");
        } catch (\Exception $e) {
            $tr->rollBack();
            SysLog::WriteError($pid,__CLASS__, $e->getTraceAsString());
            return null;
        }

        return $result ? $this->_client : null;
    }
}