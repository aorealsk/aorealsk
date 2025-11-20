<?php

namespace common\models\fbcharity;

use yii\db\ActiveRecord;
use Yii;

class Product extends ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->db2;
    }

    public static function tableName(): string
    {
        return 'products';
    }

    public function getDetails(string $lang = 'sk')
    {
        return $this->hasOne(ProductLang::class, ['product_id' => 'id'])->where(['lang' => $lang]);
    }
}
