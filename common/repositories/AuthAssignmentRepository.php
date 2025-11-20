<?php

namespace common\repositories;

use common\models\auth\AuthAssignment;
use Yii;

final class AuthAssignmentRepository
{
    public static function save(int $userId, array $data): bool
    {
        $result = false;
        if (!empty($data['auth_assignment'])) {

            $sql = 'delete from `auth_assignment` where `user_id` = :user_id';
            Yii::$app->db->createCommand($sql)->bindValue(':user_id', $userId)->execute();
            $model = new AuthAssignment();
            $model->user_id = $userId;
            $model->item_name = $data['auth_assignment'];
            $result = $model->save();
        }
        return $result;
    }

    public static function getUserIdByGroup(string $group): array
    {
        $sql = 'select user_id from `auth_assignment` where item_name=:group';
        $users = Yii::$app->db->createCommand($sql)->bindValue(':group', $group)->queryAll();
        $ids = [];
        array_map(function ($user) use (&$ids) {
            $ids[] = $user['user_id'];
        }, $users);
        return $ids;
    }

    public static function getByRole(string $role)
    {
        $sql = "
            select
                aa.user_id,
                concat(a.name_first, ' ', name_last) as full_name
            from
                auth_assignment aa
            join    
                agent a on a.user_id=aa.user_id
            where
                aa.item_name=:role    
        ";
        return Yii::$app->db->createCommand($sql)->bindValue(':role', $role)->queryAll();
    }
}