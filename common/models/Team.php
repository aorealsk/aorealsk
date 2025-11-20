<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Team extends ActiveRecord
{
    /** @var int[] IDs of selected students (used by the form) */
    public $studentIds = [];

    public static function tableName()
    {
        return '{{%team}}';
    }

    public function rules()
    {
        return [
            [['name', 'mentor_profile_id'], 'required'],
            ['description', 'string'],

            // strip hidden empty values from checkboxList
            ['studentIds', 'filter', 'filter' => function ($v) {
                $arr = array_filter((array)$v, static fn($x) => $x !== '' && $x !== null);
                return array_values($arr);
            }],
            // require at least one student
            ['studentIds', 'required', 'message' => 'Vyberte aspoň jedného študenta.'],
            // each must be integer
            ['studentIds', 'each', 'rule' => ['integer']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name'       => 'Názov tímu',
            'description'=> 'Popis',
            'studentIds' => 'Členovia tímu (študenti)',
        ];
    }

    /** Relation used in lists/detail */
    public function getStudents()
    {
        return $this->hasMany(\common\models\schools\Students::class, ['id' => 'student_id'])
            ->viaTable('{{%team_student}}', ['team_id' => 'id']);
    }

    /** Replace existing links with the provided list */
    public function saveStudents(array $ids): void
    {
        // normalize + dedupe + remove empties
        $ids = array_values(array_unique(array_filter($ids, static fn($x) => $x !== '' && $x !== null)));

        Yii::$app->db->createCommand()
            ->delete('{{%team_student}}', ['team_id' => $this->id])
            ->execute();

        if (!$ids) {
            return; // nothing to insert (safe when form is re-validated)
        }

        $rows = [];
        foreach ($ids as $sid) {
            $rows[] = [$this->id, (int)$sid];
        }

        Yii::$app->db->createCommand()
            ->batchInsert('{{%team_student}}', ['team_id','student_id'], $rows)
            ->execute();
    }
}
