<?php
namespace common\models\client;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use common\models\client\ClientBusiness;
use yii\web\IdentityInterface;


class Client extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    const CLASS_SALT = 'xAOy0(N$/2:Ji3`Ab(93+|p6~s26|4Vf|0DD_[Vz^0#QL }J#*zx5}*73052|~|/';
    /**
     * @var string
     */

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'client';
    }

    public function updateAuthKey()
    {
        $this->auth_key = hash('md5',$this->email.self::CLASS_SALT);
        $this->update(false);
    }

    public function makeReferalCode()
    {
        $referalCode = "TIP";
        $birthDay = (new \DateTime($this->birth_date));

        $referalCode .= strtoupper($this->name_first);
        $referalCode .= strtoupper($this->name_last);
        $referalCode .= $birthDay->format('m');
        $referalCode .= $birthDay->format('d');

        $this->referal_code = $referalCode;
        $this->save();
    }

    public static function getClientMainFolder(int $clientId)
    {
        return 'clients/' . str_pad($clientId,10, "0");
    }

    public static function getClientDocumentFolder(int $clientId)
    {
        return static::getClientMainFolder($clientId) . "/document";
    }
   
    public function getClientContact() 
    {
        return $this->hasOne(ClientContact::class, ['client_id' => 'id']);
    }

    public function getClientBusinesses() 
    {
        return $this->hasMany(ClientBusiness::class, ['client_id' => 'id']);
    }

    public function getClientDetail() 
    {
        return $this->hasOne(ClientDetail::class, ['client_id' => 'id']);
    }

    public function getDocuments() 
    {
        return $this->hasMany(ClientDocuments::class, ['client_id' => 'id']);
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public function getCompanyInfo()
    {
        return $this->hasOne(ClientCompanyInfo::class, ['client_id' => 'id']);
    }

    /**
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return Client|null
     */
    public static function findByUsername(string $username)
    {
        return static::findOne(['email' => $username, 'status' => self::STATUS_ACTIVE]);
    }

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

    public function getPersonalInfo()
    {
        return $this->hasOne(ClientPersonalInfo::class, ['client_id' => 'id']);
    }

    public function getSocial()
    {
        return $this->hasOne(ClientSocial::class, ['client_id' => 'id']);
    }

    public function getSpolocnici(bool $isArray=false)
    {
        $spolocnici = ClientPersonalInfo::find()->andWhere(['=','client_id',$this->id])->andWhere(['=','client_type','spolocnik']);
        if ($isArray) {
            $spolocnici->asArray();
        }
        return $spolocnici->all();
    }

    public function getKonatelia(bool $isArray=false)
    {
        $konatelia = ClientPersonalInfo::find()->andWhere(['=','client_id',$this->id])->andWhere(['=','client_type','konatel']);
        if ($isArray) {
            $konatelia->asArray();
        }
        return $konatelia->all();
    }
}