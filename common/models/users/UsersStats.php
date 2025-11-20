<?php

namespace common\models\users;

use yii\db\ActiveRecord;

/**
 * @property int $userId
 * @property string $userAction
 * @property mixed|string|null $userIp
 */

class UsersStats extends ActiveRecord
{
    public const ACTION_LOGIN = 'login';
    public const ACTION_LOGOUT = 'logout';

    public static function tableName(): string
    {
        return 'usersStats';
    }
}
