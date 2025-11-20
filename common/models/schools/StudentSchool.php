<?php

namespace common\models\schools;

use yii\db\ActiveRecord;

/**
 * @property int $student_id
 * @property int $school_id
 * @property int $year_from
 * @property int $year_to
 * @property string $class
 */
class StudentSchool extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'student_school';
    }
}
