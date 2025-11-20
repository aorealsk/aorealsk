<?php
namespace backend\models\forms;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\UserGuardian;

/**
 * Form model for editing a user's profile + up to 2 guardians (if under 18).
 */
class UserProfileForm extends Model
{
    // user table fields
    public $username;
    public $email;
    public $name_first;
    public $name_last;
    public $birthdate;     // Y-m-d
    public $shirt_size;
    public $pants_size;
    public $shoe_size;
    public $street;
    public $street_no;
    public $zip;
    public $city;
    public $phone;
    public $iban;
    public $userclassroom;   // NEW: classroom
    public $password;        // optional (set only if filled)

    /**
     * Guardians as array of rows:
     * [
     *   ['name'=>'', 'phone'=>'', 'email'=>'', 'street'=>'', 'street_no'=>'', 'zip'=>'', 'city'=>''],
     * ]
     */
    public $guardians = [];

    /** @var User */
    private $user;

    public static function fromUser(User $user): self
    {
        $m = new self();
        $m->user = $user;

        // map user fields -> form
        foreach ([
            'username','email','name_first','name_last','birthdate',
            'shirt_size','pants_size','shoe_size',
            'street','street_no','zip','city','phone','iban',
            'userclassroom', // NEW
        ] as $attr) {
            $m->$attr = $user->$attr;
        }

        // preload guardians
        $rows = UserGuardian::find()
            ->where(['user_id' => (int)$user->id])
            ->orderBy(['id'=>SORT_ASC])
            ->asArray()->all();

        $m->guardians = array_values(array_map(function($g){
            return [
                'name'      => (string)($g['name'] ?? ''),
                'phone'     => (string)($g['phone'] ?? ''),
                'email'     => (string)($g['email'] ?? ''),
                'street'    => (string)($g['street'] ?? ''),
                'street_no' => (string)($g['street_no'] ?? ''),
                'zip'       => (string)($g['zip'] ?? ''),
                'city'      => (string)($g['city'] ?? ''),
            ];
        }, $rows));

        // keep at most 2 slots in the UI
        while (count($m->guardians) < 2) { $m->guardians[] = ['name'=>'','phone'=>'','email'=>'','street'=>'','street_no'=>'','zip'=>'','city'=>'']; }
        if (count($m->guardians) > 2) { $m->guardians = array_slice($m->guardians, 0, 2); }

        return $m;
    }

    public function rules(): array
    {
        return [
            [['username','email','name_first','name_last','birthdate'], 'required'],
            [[
                'username','name_first','name_last','shirt_size','pants_size','shoe_size',
                'street','street_no','zip','city','phone','iban','userclassroom'
            ], 'string', 'max' => 255],
            ['email', 'email'],
            ['birthdate', 'date', 'format' => 'php:Y-m-d'],
            ['password', 'string', 'min' => 6],

            // guardians validation: if under 18 -> at least 1 guardian with name+phone required
            ['guardians', 'validateGuardians'],
        ];
    }

    public function validateGuardians()
    {
        if ($this->isMinor()) {
            $valid = 0;
            foreach ((array)$this->guardians as $g) {
                $name  = trim((string)($g['name']  ?? ''));
                $phone = trim((string)($g['phone'] ?? ''));
                if ($name !== '' && $phone !== '') { $valid++; }
            }
            if ($valid < 1) {
                $this->addError('guardians', Yii::t('app',
                    'Ak máte menej ako 18 rokov, je potrebné pridať aspoň jedného zákonného zástupcu (meno a telefón).'
                ));
            }
        }
    }

    public function attributeLabels(): array
    {
        return [
            'username'      => 'Prihlasovacie meno',
            'email'         => 'E-mail',
            'name_first'    => 'Meno',
            'name_last'     => 'Priezvisko',
            'birthdate'     => 'Dátum narodenia',
            'shirt_size'    => 'Veľkosť trička',
            'pants_size'    => 'Veľkosť nohavíc',
            'shoe_size'     => 'Veľkosť obuvi',
            'street'        => 'Ulica',
            'street_no'     => 'Číslo',
            'zip'           => 'PSČ',
            'city'          => 'Mesto',
            'phone'         => 'Telefón',
            'iban'          => 'IBAN',
            'userclassroom' => 'Trieda',          // NEW
            'password'      => 'Nové heslo (voliteľné)',
        ];
    }

    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }
        $u = $this->user;
        if (!$u) return false;

        foreach ([
            'username','email','name_first','name_last','birthdate',
            'shirt_size','pants_size','shoe_size',
            'street','street_no','zip','city','phone','iban',
            'userclassroom', // NEW
        ] as $attr) {
            $u->$attr = $this->$attr;
        }

        if ($this->password) {
            $u->setPassword($this->password);
        }

        if (!$u->save(false)) {
            $this->addError('username', 'Nepodarilo sa uložiť používateľa.');
            return false;
        }

        // Guardians
        $uid = (int)$u->id;
        if ($this->isMinor()) {
            UserGuardian::deleteAll(['user_id' => $uid]);
            $rows = 0;
            foreach ((array)$this->guardians as $g) {
                $name  = trim((string)($g['name']  ?? ''));
                $phone = trim((string)($g['phone'] ?? ''));
                $email = trim((string)($g['email'] ?? ''));
                if ($name === '' && $phone === '') continue;
                $gu = new UserGuardian();
                $gu->user_id   = $uid;
                $gu->name      = $name;
                $gu->phone     = $phone;
                $gu->email     = $email;
                $gu->street    = trim((string)($g['street']    ?? ''));
                $gu->street_no = trim((string)($g['street_no'] ?? ''));
                $gu->zip       = trim((string)($g['zip']       ?? ''));
                $gu->city      = trim((string)($g['city']      ?? ''));
                $gu->save(false);
                $rows++;
                if ($rows >= 2) break;
            }
        } else {
            UserGuardian::deleteAll(['user_id' => $uid]);
        }

        return true;
    }

    private function isMinor(): bool
    {
        $d = trim((string)$this->birthdate);
        if ($d === '') return false;
        $ts = strtotime($d);
        if ($ts === false) return false;
        $age = (int)floor((time() - $ts) / (365.2425 * 24 * 3600));
        return $age < 18;
    }
}
