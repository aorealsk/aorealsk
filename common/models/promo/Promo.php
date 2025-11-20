<?php
namespace common\models\promo;

use yii\db\ActiveQuery;

class Promo extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'promo';
    }

    public function getGuests(): ActiveQuery
    {
        return $this->hasMany(PromoGuest::class,['promo_id'=>'id']);
    }

    public function getOrders(): ActiveQuery
    {
        return $this->hasMany(PromoGuestOrder::class,['promo_id'=>'id']);
    }


}