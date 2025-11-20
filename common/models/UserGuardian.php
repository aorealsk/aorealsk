<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\User;

/**
 * This is the model class for table "user_guardian".
 *
 * Columns (expected):
 *  - id (PK, auto-increment)
 *  - user_id
 *  - name
 *  - relation
 *  - phone
 *  - email
 *  - street
 *  - street_no
 *  - zip
 *  - city
 */
class UserGuardian extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_guardian';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // user_id should be an integer
            [['user_id'], 'integer'],

            // All these fields are allowed for mass assignment
            [['name', 'relation', 'phone', 'email', 'street', 'street_no', 'zip', 'city'], 'safe'],

            // (Optional) you could add simple string validators if you want, but "safe" is enough for now.
            // [['name', 'relation', 'phone', 'email', 'street', 'street_no', 'zip', 'city'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'        => 'ID',
            'user_id'   => 'User',
            'name'      => 'Name',
            'relation'  => 'Relation',
            'phone'     => 'Phone',
            'email'     => 'Email',
            'street'    => 'Street',
            'street_no' => 'Street No.',
            'zip'       => 'ZIP',
            'city'      => 'City',
        ];
    }

    /**
     * Relation to User (guardian belongs to one user).
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
