<?php

namespace common\models\schools; // Visszaállítva erre a helyes névtérre!

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "student_legal_representative".
 */
class StudentLegalRepresentative extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'studentLegalRepresentative';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['studentId'], 'integer'],
            [['firstName', 'lastName', 'email', 'phoneCountry', 'phoneNumber'], 'string', 'max' => 255],
            [['studentId'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\schools\Students::class, 'targetAttribute' => ['studentId' => 'id']],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(\common\models\schools\Students::class, ['id' => 'studentId']);
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