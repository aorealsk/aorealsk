<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;

class RegistrationForm extends Model
{
    public $username;
    public $password;
    public $email;
    public $name_first;
    public $name_last;
    public $birthdate; // YYYY-MM-DD
    public $phone;

    public function rules(): array
    {
        return [
            // Required fields
            [['username', 'password', 'email', 'name_first', 'name_last', 'birthdate'], 'required'],

            // Trim inputs
            [['username', 'email', 'name_first', 'name_last', 'phone'], 'trim'],

            // Length & format
            [['username', 'name_first', 'name_last'], 'string', 'min' => 2, 'max' => 100],
            ['password', 'string', 'min' => 6, 'max' => 255],
            ['email', 'email'],

            // Uniqueness against the common User AR
            ['username', 'unique', 'targetClass' => User::class, 'targetAttribute' => 'username'],
            ['email',    'unique', 'targetClass' => User::class, 'targetAttribute' => 'email'],

            // Birthdate as Y-m-d
            ['birthdate', 'date', 'format' => 'php:Y-m-d'],

            // Phone (optional, fairly loose)
            ['phone', 'match', 'pattern' => '/^\+?[0-9\s\-()]{7,20}$/', 'message' => 'Invalid phone number.'],
            ['phone', 'string', 'max' => 32],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username'   => 'Username',
            'password'   => 'Password',
            'email'      => 'Email',
            'name_first' => 'First Name',
            'name_last'  => 'Last Name',
            'birthdate'  => 'Birthdate',
            'phone'      => 'Phone',
        ];
    }

    /**
     * Creates and returns a saved User AR on success, or null on failure.
     */
    public function register(): ?User
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username   = $this->username;
        $user->email      = $this->email;
        $user->name_first = $this->name_first;
        $user->name_last  = $this->name_last;
        $user->birthdate  = $this->birthdate; // must match your DB column type (DATE recommended)
        $user->phone      = $this->phone;

        // Hash password and set auth key via helpers on your User AR
        $user->setPassword($this->password);
        if ($user->hasAttribute('auth_key')) {
            $user->generateAuthKey();
        }

        // Status default is handled by User::rules(); TimestampBehavior fills created_at/updated_at

        if ($user->save()) {
            return $user;
        }

        // Bubble AR errors up to the form for display
        $this->addErrors($user->getErrors());
        return null;
    }
}
