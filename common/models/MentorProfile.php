<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\User;
use common\models\Team;

/**
 * @property int $id
 * @property int $user_id
 * @property string $role
 * @property string|null $org_name
 * @property string|null $phone
 * @property int $created_at
 * @property int $updated_at
 *
 * @property-read User   $user
 * @property-read Team[] $teams
 * @property-read string $displayName
 * @property-read string|null $email
 */
class MentorProfile extends ActiveRecord
{
    public static function tableName() { return '{{%mentor_profile}}'; }

    public function rules()
    {
        return [
            [['user_id','role'], 'required'],
            [['user_id','created_at','updated_at'], 'integer'],
            [['role'], 'in', 'range' => ['teacher','supervisor','business_partner']],
            [['org_name'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 32],
        ];
    }

    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            $this->user_id = $this->user_id ?: Yii::$app->user->id;
        }
        return parent::beforeValidate();
    }

    public function beforeSave($insert)
    {
        $ts = time();
        if ($insert) $this->created_at = $ts;
        $this->updated_at = $ts;
        return parent::beforeSave($insert);
    }

    public function getUser()  { return $this->hasOne(User::class, ['id'=>'user_id']); }
    public function getTeams() { return $this->hasMany(Team::class, ['mentor_profile_id'=>'id']); }

    /**
     * Friendly name for listings. Tries common user attributes, falls back safely.
     */
    public function getDisplayName()
    {
        if ($this->user) {
            // prefer combined names if your User model has them
            foreach (['fullName','full_name','name'] as $attr) {
                if (!empty($this->user->$attr)) {
                    return (string)$this->user->$attr;
                }
            }
            // try first/last combo
            $first = $this->user->first_name ?? $this->user->firstname ?? null;
            $last  = $this->user->last_name  ?? $this->user->lastname  ?? null;
            if ($first || $last) {
                return trim($first.' '.$last);
            }
            // username fallback
            if (!empty($this->user->username)) {
                return (string)$this->user->username;
            }
        }
        return '#'.$this->user_id;
    }

    /** Convenience accessor for email in views */
    public function getEmail()
    {
        return $this->user->email ?? null;
    }
}
