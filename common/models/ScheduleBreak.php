<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * @property int      $id
 * @property string   $title
 * @property string   $from_time
 * @property string   $to_time
 * @property int|null $break_min
 * @property int      $created_at
 */
class ScheduleBreak extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%schedule_break}}';
    }

    public function rules()
    {
        return [
            [['title','from_time','to_time'], 'required'],
            [['title'], 'string', 'max' => 255],
            [['break_min'], 'integer', 'min' => 0, 'max' => 300],
            [['from_time','to_time'], 'match', 'pattern' => '/^\d{1,2}:\d{2}$/'],
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert && $this->created_at === null) {
            $this->created_at = time();
        }
        return parent::beforeSave($insert);
    }
}
