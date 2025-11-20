<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "study_plan_items".
 *
 * Columns:
 *  - id        (PK)
 *  - type_id   (FK to study_plan_types.id)
 *  - month     (1-12)
 *  - position  (int order within month)
 *  - item      (text / description of work-plan)
 */
class StudyPlanItem extends ActiveRecord
{
    public static function tableName()
    {
        return 'study_plan_items';
    }

    public function rules()
    {
        return [
            // these 3 must be integers
            [['type_id', 'month', 'position'], 'integer'],

            // we expect all fields to be present for a valid plan row
            [['type_id', 'month', 'position', 'item'], 'required'],

            // month in valid range
            ['month', 'integer', 'min' => 1, 'max' => 12],

            // item is just a description we later put into CalendarEvent->title
            ['item', 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'       => 'ID',
            'type_id'  => 'Study plan type',
            'month'    => 'Month',
            'position' => 'Position',
            'item'     => 'Item',
        ];
    }
}
