<?php

namespace common\models\schools;

use yii\db\ActiveRecord;

class StudentCourse extends ActiveRecord
{
    public static function tableName()
    {
        return 'student_course';
    }
}
