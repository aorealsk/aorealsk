<?php

namespace common\models\cache;

use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

class UserMatrixCache implements CacheWrap
{
    public static function isLoaded(): bool
    {
        $userMatrix = Yii::$app->cache->get('user_matrix');
        return !empty($userMatrix) && is_array($userMatrix);
    }

    /**
     * @throws Exception
     */
    public static function load(): void
    {
        $sql = 'SELECT CONCAT(pu.`group`,"_",pu.userId,"_",p.`name`) as X' .
            ' FROM privilegesUsers pu JOIN `privileges` p ON p.id=pu.privilegesId WHERE pu.`status`=1';
        $rows = Yii::$app->db->createCommand($sql)->queryAll();
        $privileges = ArrayHelper::getColumn($rows, 'X');
        Yii::$app->cache->set('user_matrix', $privileges);
    }

    public static function clear(): void
    {
        Yii::$app->cache->delete('user_matrix');
    }

    public static function inCache(string $privilege)
    {
        $privileges = Yii::$app->cache->get('user_matrix');
        //TODO: itt lehet hogy maskepp kell majd megoldani!!!
        if (!is_array($privileges)) {
            return false;
        }
        return in_array($privilege, $privileges);
    }
}
