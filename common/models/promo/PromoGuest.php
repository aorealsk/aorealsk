<?php
namespace common\models\promo;

class PromoGuest extends \yii\db\ActiveRecord
{
    const VALIDATION_SALT = 'keyforpromoclass202301260724';
    const ORDER_SALT = 'ordervalidationsaltkey202301260725';
    public static function tableName(): string
    {
        return 'promo_guest';
    }

    public function getFullName(): string
    {
        return implode(' ',[$this->name_first,$this->name_last]);
    }

    public function generateValidationHash(): string
    {
        $hash = sha1($this->name_first.$this->name_last.$this->email.self::VALIDATION_SALT.uniqid());
        $this->validation_hash = $hash;
        $this->save();
        return $hash;
    }

    public function generateOrderHash(): string
    {

        $hash = sha1($this->name_first.$this->name_last.$this->email.self::ORDER_SALT.uniqid());
        $this->order_hash = $hash;
        $this->save();
        return $hash;
    }

    public function validated()
    {
        $this->validated=1;
        $this->save();
    }

    public function getPromo()
    {
        return $this->hasOne(Promo::class,['id'=>'promo_id']);
    }
}