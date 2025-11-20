<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "study_plan_types".
 *
 * Columns:
 *  - id
 *  - name
 */
class StudyPlanType extends ActiveRecord
{
    public static function tableName()
    {
        return 'study_plan_types';
    }

    public function rules()
    {
        return [
            [['name'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'   => 'ID',
            'name' => 'Study plan name',
        ];
    }
}
