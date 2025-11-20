<?php
namespace common\models\majstery;

use yii\db\ActiveRecord;
use common\models\User;
use common\models\schools\StudyField;

class Majstery extends ActiveRecord
{
    public static function tableName()
    {
        // pontosan a DB tábla neve (Linuxon case-sensitive lehet)
        return 'Majstery';
    }

    public function rules()
    {
        return [
            [['FirstName','LastName','EmailAddress','PhoneNumber'], 'required'],
            [['UserID','TraineeFor'], 'integer'],
            [['BirthDate'], 'safe'],
            [['Height','Weight','FootSize','ShirtSize'], 'number'],
            [[
                'FirstName','LastName','Gender','WaistLine','TrouserLenght','IBAN',
                'PrimaryLanguage','Languages','ContactStreet','ContactBuildingNr',
                'ContactTown','ContactTownID','ContactCountry','EmailAddress','PhoneNumber',
                'LastFinishedSchool'
            ], 'string', 'max' => 255],
            [['EmailAddress'], 'email'],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'UserID']);
    }

    public function getStudyField()
    {
        // nem kötelező kapcsolat, de kényelmes
        return $this->hasOne(StudyField::class, ['id' => 'TraineeFor']);
    }
}
