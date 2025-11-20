<?php

namespace common\models\auth;

use yii\db\ActiveRecord;

class AuthAssignment extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'auth_assignment';
    }

    /**
     * @param int $userId
     * @return string
     */
    public function getGroupsByUserId(int $userId): string
    {
        $group = self::find()
            ->select(['item_name'])
            ->where(['=', 'user_id', $userId])
            ->asArray()
            ->all();
        if (is_null($group)) {
            return '';
        }

        $groups = [];
        array_walk($group, function ($value) use (&$groups) {
            $groups[] = $value['item_name'];
        });

        return implode(',', $groups);
    }
}
