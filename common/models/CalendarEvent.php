<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "calendar_event".
 *
 * Columns:
 *  id
 *  calendar_id
 *  user_id
 *  type
 *  title
 *  start
 *  end
 *  all_day
 *  location
 *  supervisors
 *  teammates
 *  company
 *  tools
 *  vehicles
 *  notes
 *  created_at
 *  updated_at
 */
class CalendarEvent extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%calendar_event}}';
    }

    public function rules()
    {
        return [
            // REQUIRED FIELDS
            [['calendar_id', 'type', 'title', 'start'], 'required'],

            // INTEGER FIELDS
            [['calendar_id', 'user_id', 'all_day', 'created_at', 'updated_at'], 'integer'],

            // DATE/TIME – allow any string, AR will pass it to DB; we normalise in controller/view
            [['start', 'end'], 'safe'],

            // LONG TEXT FIELDS
            [['location', 'supervisors', 'teammates', 'company', 'tools', 'vehicles', 'notes'], 'string'],

            // SHORT STRINGS
            [['type'], 'string', 'max' => 20],
            [['title'], 'string', 'max' => 255],

            // allow empty → NULL for optional text fields
            [['location', 'supervisors', 'teammates', 'company', 'tools', 'vehicles', 'notes'], 'default', 'value' => null],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'calendar_id'  => 'Kalendár',
            'user_id'      => 'Používateľ',
            'type'         => 'Typ',
            'title'        => 'Názov',
            'start'        => 'Začiatok',
            'end'          => 'Koniec',
            'all_day'      => 'Celý deň',
            'location'     => 'Miesto',
            'supervisors'  => 'Supervízori',
            'teammates'    => 'Členovia tímu',
            'company'      => 'Spoločnosť',
            'tools'        => 'Nástroje',
            'vehicles'     => 'Vozidlá',
            'notes'        => 'Poznámky',
            'created_at'   => 'Vytvorené',
            'updated_at'   => 'Upravené',
        ];
    }

    public function getCalendar()
    {
        return $this->hasOne(Calendar::class, ['id' => 'calendar_id']);
    }
}
