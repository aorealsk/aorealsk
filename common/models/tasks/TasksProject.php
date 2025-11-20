<?php

namespace common\models\tasks;

use Yii;
use yii\db\ActiveRecord;

class TasksProject extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;


    public static function tableName()
    {
        return 'tasksProject';
    }

    public function rules()
    {
        return [
            [['name', 'code', 'color'],'required'],
            [['name', 'code', 'color'],'string'],
            [['status'], 'string', 'max' => 1],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE]
        ];
    }

    public static function getBackgroundColor(string $ticketNumber): string
    {
        list($code,$id) = explode('-',$ticketNumber);

        $sql = "select color from tasksProject where code='{$code}'";
        $result = Yii::$app->db->createCommand($sql)->queryScalar();
        if (!$result) {
            $result = '#fff';
        }

        return $result;
    }
}