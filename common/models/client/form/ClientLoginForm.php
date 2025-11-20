<?php
namespace common\models\client\form;

use common\models\client\Client;
use Yii;
use yii\base\Model;

class ClientLoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_client = null;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            // username and password are both required
            ['username', 'required', 'message'=>Yii::t('app','Užívateľské meno nemôže byť prázdne')],
            ['password', 'required', 'message'=>Yii::t('app','Heslo nemôže byť prázdne')],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getClient(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }

        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return Client|null
     */
    protected function getClient(): ?Client
    {
        if ($this->_client === null) {
            $this->_client = Client::findByUsername($this->username);
        }

        return $this->_client;
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $client = $this->getClient();
            if (!$client || !$client->validatePassword($this->password)) {
                $this->addError($attribute, 'Neplatné heslo alebo užívateľ neexistuje');
            }
        }
    }
}