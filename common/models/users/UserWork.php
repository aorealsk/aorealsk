<?php
namespace common\models\users;

use yii\db\ActiveRecord;

/**
 * @property int|mixed|null $userId
 * @property mixed|null $workType
 * @property mixed|null $basicWorktime
 */
class UserWork extends ActiveRecord
{
    public static $workTypes = [
        1 => 'Trvalý pracovný pomer',
        2 => 'Dual prax 1. roč. SŠ/SOŠ',
        3 => 'Dual prax 2. roč. SŠ/SOŠ',
        4 => 'Dual prax 3. roč. SŠ/SOŠ',
        5 => 'Dual prax 4. roč. SŠ/SOŠ',
        6 => 'Brigáda',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'userWork';
    }
}