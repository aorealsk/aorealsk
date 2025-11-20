<?php
namespace common\models\schools;

use yii\db\ActiveRecord;

/**
 * @property string $email
 * @property string $firstName
 * @property string $lastName
 * @property string $phoneNumber
 * @property int $candidate
 * @property string $primarySchoolTown
 * @property string $primarySchoolName
 */
class Students extends ActiveRecord
{
    const STATUS_PENDING = 0;
    const STATUS_PROCESSED = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_NOTACCEPTED = 3;

    const CANDIDATE = 1;
    const STUDENT = 0;

    public static function tableName()
    {
        return 'student';
    }

    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }


    public function getSchool()
{
    return $this->hasOne(\common\models\schools\School::class, ['id' => 'schoolId']);
}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudyField()
    {
        return $this->hasOne(\common\models\schools\StudyField::class, ['id' => 'studyFieldId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentLegalRepresentatives()
    {
        return $this->hasMany(\common\models\schools\StudentLegalRepresentative::class, ['studentId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentLanguages()
    {
        // Feltételezve, hogy a StudentLanguage modell a common\models névtér alatt van.
        // Ha nem, javítsd az elérési utat.
        return $this->hasMany(\common\models\schools\StudentLanguage::class, ['studentId' => 'id'])->inverseOf('student');
    }

    /**
     * @return string
     */
    public function getStudentName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * @return string
     */
    public function getFormattedPhone()
    {
        if (empty($this->phoneNumber)) {
            return '-';
        }
        return str_replace('00', '+', $this->phoneCountry) . $this->phoneNumber;
    }

}