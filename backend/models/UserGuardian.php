<?php
namespace common\models;

use yii\db\ActiveRecord;

class UserGuardian extends ActiveRecord
{
    public static function tableName(): string { return '{{%user_guardian}}'; }

    public function rules(): array
    {
        return [
            [['user_id','name'], 'required'],
            [['user_id'], 'integer'],
            [['name','relation','street','city'], 'string', 'max' => 255],
            [['street_no'], 'string', 'max' => 50],
            [['zip'], 'string', 'max' => 20],
            [['phone'], 'string', 'max' => 50],
            [['email'], 'email'],
        ];
    }
}
