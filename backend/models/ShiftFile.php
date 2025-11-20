<?php
namespace backend\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $shift_id
 * @property int $user_id
 * @property string $file_path
 * @property string $mime
 * @property string $uploaded_at
 */
class ShiftFile extends ActiveRecord
{
    public static function tableName() { return 'shift_files'; }

    public function rules()
    {
        return [
            [['shift_id','user_id','file_path','mime','uploaded_at'], 'required'],
            [['shift_id','user_id'], 'integer'],
            [['uploaded_at'], 'safe'],
            [['file_path','mime'], 'string', 'max' => 255],
        ];
    }
}
