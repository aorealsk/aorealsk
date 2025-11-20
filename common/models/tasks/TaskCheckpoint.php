<?php

namespace common\models\tasks;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "task_checkpoint".
 *
 * @property int $id
 * @property int $taskId
 * @property string $label
 * @property int $isDone
 * @property int $order
 * @property string|null $createdAt
 *
 * @property Tasks $task
 */
class TaskCheckpoint extends ActiveRecord
{
    public static function tableName()
    {
        return 'task_checkpoint';
    }

    public function rules()
    {
        return [
            [['taskId', 'label'], 'required'],
            [['taskId', 'order'], 'integer'],
            [['isDone'], 'boolean'],
            [['createdAt'], 'safe'],
            [['label'], 'string', 'max' => 255],

            // FK validáció (nem kötelező, de hasznos)
            [
                ['taskId'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Tasks::class,
                'targetAttribute' => ['taskId' => 'id']
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'        => 'ID',
            'taskId'    => 'Task ID',
            'label'     => 'Label',
            'isDone'    => 'Done',
            'order'     => 'Order',
            'createdAt' => 'Created At',
        ];
    }

    /**
     * Kapcsolat a Tasks modellhez
     */
    public function getTask()
    {
        return $this->hasOne(Tasks::class, ['id' => 'taskId']);
    }
}
