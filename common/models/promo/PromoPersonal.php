<?php
namespace common\models\promo;

use yii\base\NotSupportedException;
use yii\db\ActiveQuery;
use yii\web\IdentityInterface;

class PromoPersonal extends \yii\db\ActiveRecord implements IdentityInterface
{
    public static function tableName(): string
    {
        return 'promo_personal';
    }

    public function getDetail(): ActiveQuery
    {
        return $this->hasOne(Personal::class,['id'=>'personal_id']);
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public function validateAuthKey($authKey)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }
}