<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class UserAttendance extends ActiveRecord
{
    public static function tableName()
    {
        // must match your DB table name
        return 'userAttendance';
    }

    public function rules()
    {
        return [
            // numeric fields
            [['userId'], 'integer'],

            // everything else we just treat as safe so nothing breaks
            [
                [
                    'uaDate',
                    'inTime',
                    'outTime',
                    'note',
                    'diffTime',
                    'uaType',
                    'uaAction',
                    'inIP',
                    'outIP',
                    'ts',
                ],
                'safe',
            ],
        ];
    }
}
