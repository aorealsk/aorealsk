<?php

namespace common\models;

use common\models\auth\AuthAssignment;
use common\models\users\UserAttendance;
use common\models\users\UserDetails;
use common\models\users\UserWork;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use common\models\StudyPlanType; // Study plan support

/**
 * User model
 *
 * @property int         $id
 * @property string      $username
 * @property string      $password_hash
 * @property string|null $password_reset_token
 * @property string|null $email
 * @property string      $auth_key
 * @property int         $status
 * @property int         $created_at
 * @property int         $updated_at
 * @property string|null $name_first
 * @property string|null $name_last
 * @property string|null $birthdate
 * @property string|null $shirt_size
 * @property string|null $pants_size
 * @property string|null $shoe_size
 * @property string|null $street
 * @property string|null $street_no
 * @property string|null $zip
 * @property string|null $city
 * @property string|null $phone
 * @property string|null $iban
 * @property string|null $userclassroom
 * @property int|null    $study_plan_type_id
 *
 * Virtual attributes:
 * @property string|null $newPassword
 * @property string|null $newPasswordRepeat
 *
 * Convenience (virtual) properties:
 * @property-read string $fullName
 * @property-read string $fullAddress
 */
class User extends ActiveRecord implements IdentityInterface
{
    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE  = 10;

    /** Virtual attributes used in forms */
    public $newPassword;
    public $newPasswordRepeat;

    // DO NOT declare public $userclassroom;  (it's a real DB column)

    public static function tableName(): string
    {
        return '{{%user}}';
    }

    public function behaviors(): array
    {
        return [TimestampBehavior::class];
    }

    /* ================= Relations ================ */

    public function getAgent()
    {
        return $this->hasOne(\common\models\Agent::class, ['user_id' => 'id']);
    }

    public function getGuardians()
    {
        return $this->hasMany(\common\models\UserGuardian::class, ['user_id' => 'id'])
            ->orderBy(['id' => SORT_ASC]);
    }

    public function getDetails(): ActiveQuery
    {
        return $this->hasOne(UserDetails::class, ['userId' => 'id']);
    }

    public function getWorkDetails(): ActiveQuery
    {
        return $this->hasOne(UserWork::class, ['userId' => 'id']);
    }

    public function getAttendance(): ActiveQuery
    {
        return $this->hasMany(UserAttendance::class, ['userId' => 'id']);
    }

    /** Study plan FK relation */
    public function getStudyPlanType(): ActiveQuery
    {
        return $this->hasOne(StudyPlanType::class, ['id' => 'study_plan_type_id']);
    }

    /* ============== Convenience getters (for templates / PDFs) ============== */

    /**
     * Full name helper: "First Last".
     */
    public function getFullName(): string
    {
        $first = $this->name_first ?? '';
        $last  = $this->name_last ?? '';
        return trim($first . ' ' . $last);
    }

    /**
     * Full postal address helper.
     * Example: "Street 12, 12345 City"
     */
    public function getFullAddress(): string
    {
        $parts = [];

        if (!empty($this->street) || !empty($this->street_no)) {
            $parts[] = trim((string)$this->street . ' ' . (string)$this->street_no);
        }

        if (!empty($this->zip) || !empty($this->city)) {
            $parts[] = trim((string)$this->zip . ' ' . (string)$this->city);
        }

        return trim(implode(', ', $parts));
    }

    /* ================= Rules / Scenarios ================ */

public function rules(): array
    {
    return [
        /* ======== EMAIL ======== */

        // Whitespace levágása
        [['email'], 'trim'],
        // Max. hossz
        [['email'], 'string', 'max' => 255],
        // Formai ellenőrzés (DE: nem unique!)
        [['email'], 'email'],

        /* ======== TRIEDA (userclassroom) ======== */

        // Orezanie bielych znakov
        [['userclassroom'], 'trim'],

        // Povolené znaky pre triedu (písmená, čísla, . _ / - :)
        [
            ['userclassroom'],
            'match',
            'pattern' => '/^[\p{L}0-9 ._\/:\-]+$/u',
            'message' => 'Trieda môže obsahovať iba písmená, čísla a znaky . _ / - :'
        ],

        // Dĺžka reťazca
        [['userclassroom'], 'string', 'max' => 255],

        // Konverzia prázdneho stringu na NULL
        [['userclassroom'], 'filter', 'filter' => static function ($v) {
            return $v === '' ? null : $v;
        }],

        /* ======== ŠTUDIJNÝ PLÁN (study_plan_type_id) ======== */

        // Bezpečná konverzia:
        //  - '' alebo null -> null
        //  - čokoľvek iné -> integer
        [['study_plan_type_id'], 'filter', 'filter' => static function ($v) {
            if ($v === '' || $v === null) {
                return null;
            }
            return (int)$v;
        }],

        // Integer validácia (funguje aj ak je NULL)
        [['study_plan_type_id'], 'integer'],

        // Existencia v tabuľke study_plan_types (FK)
        [
            ['study_plan_type_id'],
            'exist',
            'skipOnError' => true,
            'targetClass' => StudyPlanType::class,
            'targetAttribute' => ['study_plan_type_id' => 'id']
        ],

        /* ======== OSTATNÉ TEXTOVÉ POLIA ======== */

        [['name_first', 'name_last', 'street', 'city'], 'string', 'max' => 255],
        [['street_no', 'zip', 'phone', 'shirt_size', 'pants_size', 'shoe_size'], 'string', 'max' => 50],
        [['iban'], 'string', 'max' => 40],

        // Všetky tieto atribúty sú "safe" => load() ich načíta z POST
        [[
            'username', 'email', 'status',
            'name_first', 'name_last', 'birthdate',
            'shirt_size', 'pants_size', 'shoe_size',
            'street', 'street_no', 'zip', 'city', 'phone', 'iban',
            'userclassroom',
            'study_plan_type_id',
        ], 'safe'],

        // Konverzia prázdnych reťazcov na NULL (okrem study_plan_type_id – to riešime vyššie)
        [[
            'name_first', 'name_last', 'birthdate',
            'shirt_size', 'pants_size', 'shoe_size',
            'street', 'street_no', 'zip', 'city', 'phone', 'iban',
            'userclassroom',
            'email', // ide is nyugodtan NULL legyen, ha üres
        ], 'filter', 'filter' => static function ($v) {
            return $v === '' ? null : $v;
        }],

        /* ======== HESLO (virtuálne polia) ======== */

        [['newPassword', 'newPasswordRepeat'], 'string', 'min' => 6],
        [['newPassword', 'newPasswordRepeat'], 'required', 'on' => 'create'],
        ['newPasswordRepeat', 'compare', 'compareAttribute' => 'newPassword', 'message' => 'Heslá sa musia zhodovať.'],
    ];
    }


    public function attributeLabels(): array
    {
        return [
            'userclassroom'      => 'Trieda',
            'study_plan_type_id' => 'Študijný plán',
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        foreach (['create', 'update'] as $s) {
            $base = $scenarios[$s] ?? [];
            $scenarios[$s] = array_unique(array_merge($base, [
                'username', 'email', 'status',
                'name_first', 'name_last', 'birthdate',
                'shirt_size', 'pants_size', 'shoe_size',
                'street', 'street_no', 'zip', 'city', 'phone', 'iban',
                'userclassroom',
                'study_plan_type_id',
                'newPassword', 'newPasswordRepeat',
            ]));
        }

        return $scenarios;
    }

    /* ================= Normalization ================ */

    public function beforeValidate()
    {
        if (!empty($this->birthdate)) {
            $b = trim((string)$this->birthdate);

            // dd.mm.yyyy
            if (preg_match('~^\d{1,2}\.\d{1,2}\.\d{4}$~', $b)) {
                $parts = explode('.', str_replace(' ', '', $b));
                $d = (int)$parts[0];
                $m = (int)$parts[1];
                $y = (int)$parts[2];
                if (checkdate($m, $d, $y)) {
                    $this->birthdate = sprintf('%04d-%02d-%02d', $y, $m, $d);
                }

            // dd/mm/yyyy
            } elseif (preg_match('~^\d{1,2}/\d{1,2}/\d{4}$~', $b)) {
                $ts = strtotime(str_replace('/', '-', $b));
                if ($ts) {
                    $this->birthdate = date('Y-m-d', $ts);
                }
            }
        }

        return parent::beforeValidate();
    }

    /* ================= IdentityInterface ================ */

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne(['password_reset_token' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire    = Yii::$app->params['user.passwordResetTokenExpire'];

        return $timestamp + $expire >= time();
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getUserName()
    {
        return $this->username;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /* ================= Security helpers ================ */

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /* ================= Domain helpers ================ */

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert && empty($this->auth_key)) {
            $this->generateAuthKey();
        }

        if (!empty($this->newPassword)) {
            $this->setPassword($this->newPassword);
        }

        return true;
    }

    public function hasRole($role)
    {
        $sql = "SELECT COUNT(user_id) FROM auth_assignment WHERE user_id=:uid AND item_name=:role";

        return Yii::$app->db->createCommand($sql)
            ->bindValues([':uid' => $this->id, ':role' => $role])
            ->queryScalar();
    }

    public function updateProfileData(array $data)
    {
        if (isset($data['email']) && $this->email !== trim($data['email'])) {
            $this->email = trim($data['email']);
        }
    }

    public function getProfilePicture()
    {
        $pic = Yii::getAlias('@web') . "/assets/images/users/nouser.png";

        if ($this->details !== null && $this->details->profilePic !== '') {
            $pic = Yii::getAlias('@web') . "/../media/profiles/{$this->id}/{$this->details->profilePic}";
        }

        return $pic;
    }

    public function isPresent($date)
    {
        return (int)$this->getAttendance()->andWhere(['uaDate' => $date])->count() > 0;
    }

    public function hasAbsence($date)
    {
        return (int)$this->getAttendance()
                ->andWhere(['uaDate' => $date])
                ->andWhere(['>', 'uaType', 1])
                ->count() > 0;
    }

    public function getAge()
    {
        if (empty($this->birthdate)) {
            return null;
        }

        $ts = strtotime($this->birthdate);
        if (!$ts) {
            return null;
        }

        $now = new \DateTimeImmutable();
        $bd  = (new \DateTimeImmutable())->setTimestamp($ts);

        return (int)$bd->diff($now)->y;
    }

    public function getIsMinor()
    {
        $age = $this->getAge();

        return $age !== null && $age < 18;
    }

    public function loadUserGroups()
    {
        $userGroups = strtolower((new AuthAssignment())->getGroupsByUserId($this->id));
        Yii::$app->session->set('my_groups_' . $this->id, $userGroups);
    }
}
