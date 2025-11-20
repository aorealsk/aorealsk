<?php

namespace common\models\fbcharity;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property mixed|null $code
 * @property mixed|null $available_from
 * @property mixed|null $available_to
 * @property mixed|null $used_at
 * @property mixed|string|null $created_at
 */
class PromoCode extends ActiveRecord
{
    public const REFERRAL = 'referral';
    public const FREE = 'free';
    public static function tableName(): string
    {
        return 'promo_codes';
    }

    public static function getDb()
    {
        return Yii::$app->db2;
    }

    public function getFirstAvailableReferralCode(): self
    {
        return self::find()
            ->where(['code_type' => 'referral'])
            ->andWhere(['used_at' => null])
            ->andWhere(['assigned_to' => null])
            ->one();
    }

    public static function codeExistsForCustomer(string $customerName)
    {
        return static::find()
            ->where(['assigned_to' => $customerName])
            ->andWhere(['code_type' => 'referral'])
            ->one();
    }
}
