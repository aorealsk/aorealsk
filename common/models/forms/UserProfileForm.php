<?php
namespace backend\models\forms;

use Yii;
use yii\base\Model;
use backend\models\User;

class UserProfileForm extends Model
{
    public $username;
    public $email;
    public $password;

    public $name_first;
    public $name_last;
    public $birthdate;

    public $shirt_size;
    public $pants_size;
    public $shoe_size;

    public $street;
    public $street_no;
    public $zip;
    public $city;
    public $phone;
    public $iban;

    /** NEW */
    public $userclassroom;

    /** keep whatever structure you already used */
    public $guardians = [];

    public static function fromUser(User $u): self
    {
        $m = new self();
        $m->username      = $u->username;
        $m->email         = $u->email;

        $m->name_first    = $u->name_first;
        $m->name_last     = $u->name_last;
        $m->birthdate     = $u->birthdate;

        $m->shirt_size    = $u->shirt_size;
        $m->pants_size    = $u->pants_size;
        $m->shoe_size     = $u->shoe_size;

        $m->street        = $u->street;
        $m->street_no     = $u->street_no;
        $m->zip           = $u->zip;
        $m->city          = $u->city;
        $m->phone         = $u->phone;
        $m->iban          = $u->iban;

        /** NEW */
        $m->userclassroom = $u->userclassroom;

        // Populate guardians here if you already have storage for them
        return $m;
    }

    public function rules(): array
    {
        return [
            // text limits
            [['name_first','name_last','street','city','userclassroom'], 'string', 'max' => 255],
            [['street_no','zip','phone','shirt_size','pants_size','shoe_size'], 'string', 'max' => 50],
            [['iban'], 'string', 'max' => 40],
            [['username','email','password'], 'string', 'max' => 255],
            ['email', 'email'],

            // safe for mass-assignment
            [[
                'username','email','password',
                'name_first','name_last','birthdate',
                'shirt_size','pants_size','shoe_size',
                'street','street_no','zip','city','phone','iban',
                'userclassroom',
                'guardians',
            ], 'safe'],

            // convert empty strings to null (optional)
            [[
                'name_first','name_last','birthdate',
                'shirt_size','pants_size','shoe_size',
                'street','street_no','zip','city','phone','iban',
                'userclassroom',
            ], 'filter', 'filter' => static fn($v) => $v === '' ? null : $v],
        ];
    }

    public function save(): bool
    {
        /** @var User $u */
        $u = Yii::$app->user->identity;
        if (!$u instanceof User) {
            $this->addError('username', 'User not found.');
            return false;
        }

        $u->username      = $this->username;
        $u->email         = $this->email;

        $u->name_first    = $this->name_first;
        $u->name_last     = $this->name_last;
        $u->birthdate     = $this->birthdate;

        $u->shirt_size    = $this->shirt_size;
        $u->pants_size    = $this->pants_size;
        $u->shoe_size     = $this->shoe_size;

        $u->street        = $this->street;
        $u->street_no     = $this->street_no;
        $u->zip           = $this->zip;
        $u->city          = $this->city;
        $u->phone         = $this->phone;
        $u->iban          = $this->iban;

        /** NEW */
        $u->userclassroom = $this->userclassroom;

        // Change password only if provided (adapt to your auth logic)
        if ($this->password) {
            if (method_exists($u, 'setPassword')) {
                $u->setPassword($this->password);
            } else {
                $u->password = Yii::$app->security->generatePasswordHash($this->password);
            }
        }

        // TODO: persist $this->guardians if you have a storage for them

        return $u->save();
    }
}
