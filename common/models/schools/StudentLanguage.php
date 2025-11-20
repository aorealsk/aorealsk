<?php
namespace common\models\schools;

use yii\db\ActiveRecord;

class StudentLanguage extends ActiveRecord
{
    public static function tableName()
    {
        return 'studentLanguage';
    }

    public function getJazyk()
    {
        // A lényeg a '\' jel az elején, ami a gyökér névtérből indul.
        // Így a 'common\models' mappában fogja keresni a Jazyk modellt.
        return $this->hasOne(\common\models\Jazyk::class, ['id' => 'languageId']);
    }

    // A másik irányú reláció is hasznos lehet a Student modell számára
    public function getStudent()
    {
        return $this->hasOne(\common\models\schools\Students::class, ['id' => 'studentId']);
    }
}