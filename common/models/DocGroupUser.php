<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Pivot: many-to-many (group ↔ user)
 *
 * @property int         $group_id
 * @property int         $user_id
 * @property string|null $added_at
 *
 * @property DocGroup $group
 * @property User     $user
 */
class DocGroupUser extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%doc_group_member}}';
    }

    public static function primaryKey()
    {
        return ['group_id', 'user_id'];
    }

    public function rules()
    {
        return [
            [['group_id', 'user_id'], 'required'],
            [['group_id', 'user_id'], 'integer'],
            [['added_at'], 'safe'],

            [['group_id', 'user_id'], 'unique',
                'targetAttribute' => ['group_id', 'user_id'],
                'message' => 'Tento používateľ už je členom skupiny.'
            ],

            // referential checks (skipOnError so it won’t block other validation)
            [['group_id'], 'exist', 'skipOnError' => true,
                'targetClass' => DocGroup::class, 'targetAttribute' => ['group_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($insert && !$this->added_at) {
            $this->added_at = new Expression('CURRENT_TIMESTAMP');
        }
        return true;
    }

    public function getGroup()
    {
        return $this->hasOne(DocGroup::class, ['id' => 'group_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
