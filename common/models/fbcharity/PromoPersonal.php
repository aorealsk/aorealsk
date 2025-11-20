<?php

namespace common\models\fbcharity;

use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use Yii;
use yii\web\IdentityInterface;

class PromoPersonal extends ActiveRecord implements IdentityInterface
{
    public static function tableName(): string
    {
        return 'promo_personals';
    }
    public static function getDb()
    {
        return Yii::$app->db2;
    }

    public function getFullName(): string
    {
        return sprintf('%s %s', $this->name_first, $this->name_last);
    }

    public function getPlace()
    {
        return $this->hasOne(PromoPlace::class, ['id' => 'place_id'])->one();
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        // throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
        return true;
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        //throw new NotSupportedException('"getAuthKey" is not implemented.');
        return true;
    }

    public function validateAuthKey($authKey)
    {
        //throw new NotSupportedException('"validateAuthKey" is not implemented.');
        return true;
    }

    public function getWorkingPlace()
    {
        return $this->hasOne(PromoPlace::class, ['id' => 'place_id'])->one();
    }
}
