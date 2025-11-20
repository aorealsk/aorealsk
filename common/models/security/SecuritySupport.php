<?php

namespace common\models\security;

use common\models\cache\UserMatrixCache;
use Yii;

class SecuritySupport
{
    /**
     * @param string|array $rule
     * @return bool
     */
    public static function canDo($rule): bool
    {
        $userId = Yii::$app->user->id;
        if (!empty(Yii::$app->cache->get('role_' . $userId)) && Yii::$app->cache->get('role_' . $userId) === 'admin') {
            return true;
        }
        if (is_string($rule)) {
            return static::canDoItem($rule);
        }
        foreach ($rule as $item) {
            if (static::canDoItem($item)) {
                return true;
            }
        }
        return false;
    }

    private static function canDoItem(string $rule): bool
    {
        if ($rule === '*') {
            return true;
        }
        $userId = (Yii::$app->user->identity->id);
        $userGroups = Yii::$app->session->get('my_groups_' . $userId);
        $privileges = static::createPrivilegesForSearch($userGroups, $userId, $rule);
        foreach ($privileges as $privilege) {
            if (UserMatrixCache::inCache($privilege)) {
                return true;
            }
        }
        return false;
    }

    private static function createPrivilegesForSearch(string $userGroups, int $userId, string $rule): array
    {
        $privilegesItems = [];
        $groups = explode(',', $userGroups);
        foreach ($groups as $group) {
            $row[0] = $group;
            $row[1] = $userId;
            $row[2] = $rule;
            $privilegesItems[] = implode('_', $row);
            $row[1] = '0';
            $privilegesItems[] = implode('_', $row);
            unset($row);
        }
        return $privilegesItems;
    }
}
