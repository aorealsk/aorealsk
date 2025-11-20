<?php

namespace common\repositories;

use common\models\Agent;

final class AgentRepository
{
    public static function getByUserId(int $userId): ?Agent
    {
        return Agent::findOne(['user_id' => $userId]);
    }

    public static function getAllByUserId(int $userId): array
    {
        return Agent::find()->where(['=', 'user_id', $userId])->all();
    }

    public static function getByUserIdOfficeId(int $userId, int $officeId): ?Agent
    {
        return Agent::findOne(['user_id' => $userId, 'office_id' => $officeId]);
    }
    public static function save(int $userId, array $data): bool
    {
        $result = false;

        foreach ($data['office_id'] as $officeId) {
            // save to agent table
            $agent = static::getByUserIdOfficeId($userId, $officeId);
            if (!$agent) {
                $agent = new Agent();
            }
            $agent->user_id = $userId;
            $agent->office_id = $officeId;
            $agent->phone = trim($data['phone']);
            $agent->email = trim($data['email']);
            $agent->name_first = trim($data['name_first']);
            $agent->name_last = trim($data['name_last']);
            $agent->save();
        }

        return $result;
    }
}