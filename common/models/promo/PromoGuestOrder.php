<?php
namespace common\models\promo;

use yii\db\ActiveQuery;

class PromoGuestOrder extends \yii\db\ActiveRecord
{
    const NEW = 'new';
    const PROCESSING = 'processing';
    const PAID = 'paid';
    const CLOSED = 'closed';
    const DELETED = 'deleted';

    public static function tableName(): string
    {
        return 'promo_guest_order';
    }

    public function getGuest(): ActiveQuery
    {
        return $this->hasOne(PromoGuest::class, ['id'=>'promo_guest_id']);
    }

    public function getItems(): ActiveQuery
    {
        return $this->hasMany(PromoGuestOrderItem::class, ['promo_guest_order_id'=>'id']);
    }

}