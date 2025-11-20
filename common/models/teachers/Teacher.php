<?php
namespace common\models\teachers;

use yii\db\ActiveRecord;
use common\models\User;
use common\models\partners\Partners;

class Teacher extends ActiveRecord
{
    public static function tableName()
    {
        // A tábla neve nagy T-vel van megadva a sémában
        return 'Teacher';
    }

    public function rules()
    {
        return [
            [['FirstName', 'LastName', 'EmailAddress', 'PhoneNumber'], 'required'],
            [['UserID', 'SchoolID'], 'integer'],
            [['BirthDate'], 'safe'],
            [['Height', 'Weight', 'FootSize', 'ShirtSize'], 'number'],
            [['FirstName', 'LastName', 'Gender', 'WaistLine', 'TrouserLenght', 'IBAN', 'PrimaryLanguage',
              'Languages', 'ContactStreet', 'ContactBuildingNr', 'ContactTown', 'ContactTownID', 'ContactCountry',
              'EmailAddress', 'PhoneNumber', 'PrimaryStudy', 'LeaderOfClass'], 'string', 'max' => 255],
            [['EmailAddress'], 'email'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'FirstName' => 'Keresztnév',
            'LastName'  => 'Vezetéknév',
            'EmailAddress' => 'Email',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'UserID']);
    }

    public function getSchool()
    {
        return $this->hasOne(Partners::class, ['id' => 'SchoolID']);
    }
}
