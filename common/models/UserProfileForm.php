<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use yii\db\Expression;
use common\models\User;

/**
 * UserProfileForm: edit logged user's profile + guardians
 */
class UserProfileForm extends Model
{
    public $id;

    // user table
    public $username;
    public $password;           // optional (if set -> rehash)
    public $email;
    public $name_first;
    public $name_last;
    public $birthdate;          // Y-m-d
    public $shirt_size;
    public $pants_size;
    public $shoe_size;
    public $street;
    public $street_no;
    public $zip;
    public $city;
    public $phone;
    public $iban;

    // guardians: up to 2
    public $guardians = [];     // each: ['name'=>..., 'phone'=>..., 'email'=>..., 'street'=>..., 'street_no'=>..., 'zip'=>..., 'city'=>...]

    public static function fromUser(User $u): self
    {
        $m = new self();
        $m->id          = (int)$u->id;
        $m->username    = (string)$u->username;
        $m->email       = (string)$u->email;
        $m->name_first  = (string)$u->name_first;
        $m->name_last   = (string)$u->name_last;
        $m->birthdate   = (string)$u->birthdate;
        $m->shirt_size  = (string)$u->shirt_size;
        $m->pants_size  = (string)$u->pants_size;
        $m->shoe_size   = (string)$u->shoe_size;
        $m->street      = (string)$u->street;
        $m->street_no   = (string)$u->street_no;
        $m->zip         = (string)$u->zip;
        $m->city        = (string)$u->city;
        $m->phone       = (string)$u->phone;
        $m->iban        = (string)$u->iban;

        $m->guardians = Yii::$app->db->createCommand("
            SELECT name, phone, email, street, street_no, zip, city
            FROM user_guardian WHERE user_id = :uid ORDER BY id ASC
        ", [':uid' => $u->id])->queryAll() ?: [];

        // normalize to max 2
        $m->guardians = array_slice($m->guardians, 0, 2);
        while (count($m->guardians) < 2) $m->guardians[] = ['name'=>'','phone'=>'','email'=>'','street'=>'','street_no'=>'','zip'=>'','city'=>''];

        return $m;
    }

    public function rules(): array
    {
        return [
            [['username','email','name_first','name_last'], 'required'],
            [['username','name_first','name_last','street','street_no','zip','city','shirt_size','pants_size','shoe_size','phone','iban'], 'string', 'max' => 255],
            ['email','email'],
            ['birthdate','date','format'=>'php:Y-m-d'],
            ['password','string','min'=>6],
            ['iban','match','pattern'=>'/^[A-Z]{2}\d{2}[A-Z0-9]{1,30}$/i','message'=>'Zadajte platný IBAN.','skipOnEmpty'=>true],
            // unique username/email – ignore current user
            ['username','validateUniqueUsername'],
            ['email','validateUniqueEmail'],
            // guardians array safe
            ['guardians','safe'],
            // If <18 → at least 1 guardian with name + phone
            ['guardians','validateGuardiansIfMinor'],
        ];
    }

    public function validateUniqueUsername()
    {
        $exists = (bool)Yii::$app->db->createCommand("
            SELECT 1 FROM user WHERE username = :u AND id <> :id LIMIT 1
        ", [':u'=>$this->username, ':id'=>$this->id])->queryScalar();
        if ($exists) $this->addError('username','Používateľské meno už existuje.');
    }

    public function validateUniqueEmail()
    {
        $exists = (bool)Yii::$app->db->createCommand("
            SELECT 1 FROM user WHERE email = :e AND id <> :id LIMIT 1
        ", [':e'=>$this->email, ':id'=>$this->id])->queryScalar();
        if ($exists) $this->addError('email','E-mail už existuje.');
    }

    public function isMinor(): bool
    {
        if (!$this->birthdate) return false;
        $bd = \DateTime::createFromFormat('Y-m-d', $this->birthdate);
        if (!$bd) return false;
        $now = new \DateTime('today');
        $age = (int)$bd->diff($now)->y;
        return $age < 18;
    }

    public function validateGuardiansIfMinor()
    {
        if (!$this->isMinor()) return;

        $countValid = 0;
        foreach ((array)$this->guardians as $g) {
            $name  = trim((string)($g['name']  ?? ''));
            $phone = trim((string)($g['phone'] ?? ''));
            if ($name !== '' && $phone !== '') $countValid++;
        }
        if ($countValid < 1) {
            $this->addError('guardians', 'Ak máte menej ako 18 rokov, je potrebné zadať aspoň 1 zákonného zástupcu (meno + telefón).');
        }
    }

    public function attributeLabels(): array
    {
        return [
            'username'   => 'Používateľské meno',
            'password'   => 'Heslo (zmeniť)',
            'email'      => 'E-mail',
            'name_first' => 'Meno',
            'name_last'  => 'Priezvisko',
            'birthdate'  => 'Dátum narodenia',
            'shirt_size' => 'Veľkosť trička',
            'pants_size' => 'Veľkosť nohavíc',
            'shoe_size'  => 'Veľkosť topánok',
            'street'     => 'Ulica',
            'street_no'  => 'Číslo domu',
            'zip'        => 'PSČ',
            'city'       => 'Mesto',
            'phone'      => 'Telefón',
            'iban'       => 'IBAN',
        ];
    }

    public function save(): bool
    {
        $u = User::findOne($this->id);
        if (!$u) { $this->addError('username','Používateľ neexistuje.'); return false; }

        $u->username   = $this->username;
        $u->email      = $this->email;
        $u->name_first = $this->name_first;
        $u->name_last  = $this->name_last;
        $u->birthdate  = $this->birthdate ?: null;
        $u->shirt_size = $this->shirt_size;
        $u->pants_size = $this->pants_size;
        $u->shoe_size  = $this->shoe_size;
        $u->street     = $this->street;
        $u->street_no  = $this->street_no;
        $u->zip        = $this->zip;
        $u->city       = $this->city;
        $u->phone      = $this->phone;
        $u->iban       = $this->iban;

        if (!empty($this->password)) {
            $u->setPassword($this->password); // assumes common\models\User::setPassword()
        }

        $db = Yii::$app->db;
        $tx = $db->beginTransaction();

        try {
            if (!$u->save(false)) throw new \RuntimeException('Uloženie používateľa zlyhalo.');

            // replace guardians
            $db->createCommand()->delete('user_guardian', ['user_id' => $u->id])->execute();

            $rows = [];
            foreach ((array)$this->guardians as $g) {
                $name = trim((string)($g['name'] ?? ''));
                $phone = trim((string)($g['phone'] ?? ''));
                $email = trim((string)($g['email'] ?? ''));
                $street = trim((string)($g['street'] ?? ''));
                $street_no = trim((string)($g['street_no'] ?? ''));
                $zip = trim((string)($g['zip'] ?? ''));
                $city = trim((string)($g['city'] ?? ''));
                if ($name === '' && $phone === '' && $email === '' && $street === '' && $city === '') continue; // skip empty
                $rows[] = [$u->id, $name, $phone, $email, $street, $street_no, $zip, $city];
            }
            if ($rows) {
                $db->createCommand()->batchInsert(
                    'user_guardian',
                    ['user_id','name','phone','email','street','street_no','zip','city'],
                    $rows
                )->execute();
            }

            $tx->commit();
            return true;
        } catch (\Throwable $e) {
            if ($tx->isActive) $tx->rollBack();
            $this->addError('username', $e->getMessage());
            return false;
        }
    }
}
