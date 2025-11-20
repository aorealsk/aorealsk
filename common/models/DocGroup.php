<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * @property int         $id
 * @property string      $name
 * @property string|null $description
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property User[]        $users
 * @property DocGroupUser[] $members
 */
class DocGroup extends ActiveRecord
{
    public static function tableName()
    {
        // works with or without DB table prefix
        return '{{%doc_group}}';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 191],
            [['description'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'name'       => 'Názov',
            'description'=> 'Popis',
            'created_at' => 'Vytvorené',
            'updated_at' => 'Upravené',
        ];
    }

    public function getMembers()
    {
        return $this->hasMany(DocGroupUser::class, ['group_id' => 'id']);
    }

    public function getUsers()
    {
        // users via pivot doc_group_member
        return $this->hasMany(User::class, ['id' => 'user_id'])->via('members');
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        // our MySQL table already defaults these, but set them explicitly too
        if ($insert && !$this->created_at) {
            $this->created_at = new Expression('CURRENT_TIMESTAMP');
        }
        $this->updated_at = new Expression('CURRENT_TIMESTAMP');
        return true;
    }
}
