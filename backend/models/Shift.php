<?php
namespace backend\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $user_id
 * @property string $started_at
 * @property string $start_photo
 * @property string|null $ended_at
 * @property string|null $end_photo
 * @property string|null $note
 */
class Shift extends ActiveRecord
{
    public static function tableName() { return 'shifts'; }

    public function rules()
    {
        return [
            [['user_id','started_at','start_photo'], 'required'],
            [['user_id'], 'integer'],
            [['started_at','ended_at'], 'safe'],
            [['start_photo','end_photo','note'], 'string', 'max' => 255],
        ];
    }
}
