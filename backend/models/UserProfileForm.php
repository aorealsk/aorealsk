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
    public $userclassroom;        // Trieda
    public $study_plan_type_id;   // Študijný plán
    public $password;             // optional (set only if filled)

    /**
     * Guardians as array of rows:
     * [
     *   ['name'=>'', 'phone'=>'', 'email'=>'', 'street'=>'', 'street_no'=>'', 'zip'=>'', 'city'=>''],
     * ]
     */
    public $guardians = [];

    /** @var User|null */
    private $user;

    /**
     * Betölti az adatokat egy létező User-ből a form modellbe.
     */
    public static function fromUser(User $user): self
    {
        $m = new self();
        $m->user = $user;

        // map user fields -> form
        foreach ([
            'username','email','name_first','name_last','birthdate',
            'shirt_size','pants_size','shoe_size',
            'street','street_no','zip','city','phone','iban',
            'userclassroom','study_plan_type_id',
        ] as $attr) {
            $m->$attr = $user->$attr;
        }

        // preload guardians from DB
        $rows = UserGuardian::find()
            ->where(['user_id' => (int)$user->id])
            ->orderBy(['id' => SORT_ASC])
            ->asArray()
            ->all();

        $m->guardians = array_values(array_map(function ($g) {
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
        while (count($m->guardians) < 2) {
            $m->guardians[] = [
                'name'      => '',
                'phone'     => '',
                'email'     => '',
                'street'    => '',
                'street_no' => '',
                'zip'       => '',
                'city'      => '',
            ];
        }
        if (count($m->guardians) > 2) {
            $m->guardians = array_slice($m->guardians, 0, 2);
        }

        return $m;
    }

    public function rules(): array
    {
        return [
            [['username', 'email', 'name_first', 'name_last', 'birthdate'], 'required'],

            [[
                'username','name_first','name_last','shirt_size','pants_size','shoe_size',
                'street','street_no','zip','city','phone','iban','userclassroom'
            ], 'string', 'max' => 255],

            ['email', 'email'],
            ['birthdate', 'date', 'format' => 'php:Y-m-d'],
            ['password', 'string', 'min' => 6],

            // study plan id – optional, must be integer or null
            ['study_plan_type_id', 'integer'],

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
                if ($name !== '' && $phone !== '') {
                    $valid++;
                }
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
            'username'          => 'Prihlasovacie meno',
            'email'             => 'E-mail',
            'name_first'        => 'Meno',
            'name_last'         => 'Priezvisko',
            'birthdate'         => 'Dátum narodenia',
            'shirt_size'        => 'Veľkosť trička',
            'pants_size'        => 'Veľkosť nohavíc',
            'shoe_size'         => 'Veľkosť obuvi',
            'street'            => 'Ulica',
            'street_no'         => 'Číslo',
            'zip'               => 'PSČ',
            'city'              => 'Mesto',
            'phone'             => 'Telefón',
            'iban'              => 'IBAN',
            'userclassroom'     => 'Trieda',
            'study_plan_type_id'=> 'Študijný plán',
            'password'          => 'Nové heslo (voliteľné)',
        ];
    }

    /**
     * Elmenti a bejelentkezett felhasználó profilját és a gondviselőket.
     */
    public function save(): bool
    {
        // Form szintű validáció (kötelező mezők, formátumok, stb.)
        if (!$this->validate()) {
            return false;
        }

        // Mindig az aktuálisan bejelentkezett usert töltjük be
        $uid = (int)Yii::$app->user->id;

        /** @var User|null $u */
        $u = User::findOne($uid);
        if (!$u) {
            $this->addError('username', 'Používateľ neexistuje.');
            return false;
        }
        $this->user = $u;

        // ---- Form -> User AR mezők mapelése ----
        $u->username   = $this->username;
        $u->email      = $this->email;
        $u->name_first = $this->name_first;
        $u->name_last  = $this->name_last;
        $u->birthdate  = $this->birthdate;
        $u->shirt_size = $this->shirt_size;
        $u->pants_size = $this->pants_size;
        $u->shoe_size  = $this->shoe_size;
        $u->street     = $this->street;
        $u->street_no  = $this->street_no;
        $u->zip        = $this->zip;
        $u->city       = $this->city;
        $u->phone      = $this->phone;
        $u->iban       = $this->iban;

        // Trieda: prázdny string -> NULL
        $u->userclassroom = ($this->userclassroom === '' ? null : $this->userclassroom);

        // Študijný plán: '' / null -> NULL, egyébként int
        if ($this->study_plan_type_id === '' || $this->study_plan_type_id === null) {
            $u->study_plan_type_id = null;
        } else {
            $u->study_plan_type_id = (int)$this->study_plan_type_id;
        }

        // ---- Jelszó frissítés, ha megadták ----
        if ($this->password) {
            $u->setPassword($this->password);
        }

        // Mentés: skip AR validáció (form már validált, és nem akarjuk az esetleges unique-email rule-t)
        if (!$u->save(false)) {
            $this->addError('username', 'Nepodarilo sa uložiť používateľa.');
            return false;
        }

        // Frissítjük az AR objektumot és a bejelentkezett identitást
        $u->refresh();
        Yii::$app->user->setIdentity($u);

        // ---- Zákonní zástupcovia ----
        if ($this->isMinor()) {
            // előbb törlünk minden régi guardian sort ehhez a userhez
            UserGuardian::deleteAll(['user_id' => $uid]);

            $rows = 0;
            foreach ((array)$this->guardians as $g) {
                $name  = trim((string)($g['name']  ?? ''));
                $phone = trim((string)($g['phone'] ?? ''));
                $email = trim((string)($g['email'] ?? ''));

                // teljesen üres sorokat kihagyjuk
                if ($name === '' && $phone === '' && $email === '') {
                    continue;
                }

                $gu = new UserGuardian();
                $gu->user_id   = $uid;
                $gu->name      = $name;
                $gu->phone     = $phone;
                $gu->email     = $email;
                $gu->street    = trim((string)($g['street']    ?? ''));
                $gu->street_no = trim((string)($g['street_no'] ?? ''));
                $gu->zip       = trim((string)($g['zip']       ?? ''));
                $gu->city      = trim((string)($g['city']      ?? ''));

                // itt is skip-peljük az AR validációt, mert a form oldalán vizsgáljuk
                $gu->save(false);

                $rows++;
                if ($rows >= 2) {
                    break; // csak max. 2 guardian
                }
            }
        } else {
            // nagykorú: töröljük az esetleges régi guardianokat
            UserGuardian::deleteAll(['user_id' => $uid]);
        }

        return true;
    }

    /**
     * Egyszerű életkor-számítás: < 18 → kiskorú.
     */
    private function isMinor(): bool
    {
        $d = trim((string)$this->birthdate);
        if ($d === '') {
            return false;
        }
        $ts = strtotime($d);
        if ($ts === false) {
            return false;
        }
        $age = (int)floor((time() - $ts) / (365.2425 * 24 * 3600));
        return $age < 18;
    }
}
