<?php
namespace common\models\tasks;

use yii\db\ActiveRecord;

class TaskIssueLink extends ActiveRecord
{
    public static function tableName()
    {
        return 'task_issue_link'; // create this table in DB
    }

    public function rules()
    {
        return [
            [['taskId', 'type', 'issueKey'], 'required'],
            [['taskId'], 'integer'],
            [['createdAt'], 'safe'],
            [['type', 'issueKey'], 'string', 'max' => 100],
        ];
    }

    public function getTask()
    {
        return $this->hasOne(Tasks::class, ['id' => 'taskId']);
    }
}
