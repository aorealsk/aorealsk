<?php

namespace common\models\fbcharity;

use yii\db\ActiveRecord;
use common\models\fbcharity\PromoPersonal;
use Yii;

class PromoOrder extends ActiveRecord
{
    public const NEW = 'new';
    public const PAID = 'paid';
    public const CANCELED = 'canceled';
    public const PROCESSING = 'processing';
    public const COMPLETED = 'completed';
    public const CLOSED = 'closed';
    public const DELETED = 'deleted';

    public static function tableName(): string
    {
        return 'promo_orders';
    }
    public static function getDb()
    {
        return Yii::$app->db2;
    }

    public function getGuest()
    {
        return $this->hasOne(Guest::class, ['id' => 'guest_id']);
    }

    public function getItems()
    {
        return $this->hasMany(PromoOrderItem::class, ['promo_order_id' => 'id']);
    }

    public function getPersonal()
    {
        return $this->hasOne(PromoPersonal::class, ['id' => 'promo_personal_id']);
    }
    public function label(): string
    {
        $status = [
            self::NEW => 'nová',
            self::PAID => 'zaplatená',
            self::CANCELED => 'storno',
            self::CLOSED => 'uzavretá',
            self::PROCESSING => 'sprocessovaná',
            self::DELETED => 'vymazaná',
            self::COMPLETED => 'skompletizovaná',
        ];

        return $status[$this->status];
    }

    public function getCssOptions(): string
    {
        $status = [
            self::NEW => '',
            self::PAID => 'background-color: #bdf0a2;',
            self::CANCELED => 'background-color: #7f8c8d; text-decoration: line-through; color: white;',
            self::CLOSED => 'info',
            self::PROCESSING => 'background-color:  #fad7a0;',
            self::DELETED => '',
            self::COMPLETED => '',
        ];

        return $status[$this->status];
    }
}
