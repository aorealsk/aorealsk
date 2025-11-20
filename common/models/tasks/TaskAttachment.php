<?php
namespace common\models\tasks;

use yii\db\ActiveRecord;

class TaskAttachment extends ActiveRecord
{
    public static function tableName()
    {
        return 'task_attachment'; // create this table in DB
    }

    public function rules()
    {
        return [
            [['taskId', 'fileName', 'filePath'], 'required'],
            [['taskId'], 'integer'],
            [['uploadedAt'], 'safe'],
            [['fileName', 'filePath', 'uploadedBy'], 'string', 'max' => 255],
        ];
    }

    public function getTask()
    {
        return $this->hasOne(Tasks::class, ['id' => 'taskId']);
    }
}
