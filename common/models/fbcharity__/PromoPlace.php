<?php

namespace common\models\fbcharity;

use yii\db\ActiveRecord;
use Yii;

class PromoPlace extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'promo_places';
    }

    public static function getDb()
    {
        return Yii::$app->db2;
    }

    public function getPromotion()
    {
        return $this->hasOne(Promo::class, ['id' => 'promo_id']);
    }
}