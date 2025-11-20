<?php
use yii\db\Migration;

class m251001_000001_create_mentor_profile_team extends Migration
{
    public function up()
    {
        $db     = $this->db;
        $schema = $db->schema;

        // mentor_profile
        $this->createTable('{{%mentor_profile}}', [
            'id'         => $this->primaryKey(),
            'user_id'    => $this->integer()->notNull(),
            'role'       => $this->string(32)->notNull(),     // teacher | supervisor | business_partner
            'org_name'   => $this->string(255)->null(),
            'phone'      => $this->string(32)->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
        $this->createIndex('ux_mentor_profile_user', '{{%mentor_profile}}', 'user_id', true);
        $this->addForeignKey('fk_mentor_profile_user', '{{%mentor_profile}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

        // team
        $this->createTable('{{%team}}', [
            'id'               => $this->primaryKey(),
            'mentor_profile_id'=> $this->integer()->notNull(),
            'name'             => $this->string(128)->notNull(),
            'description'      => $this->text()->null(),
            'created_at'       => $this->integer()->notNull(),
            'updated_at'       => $this->integer()->notNull(),
        ]);
        $this->createIndex('idx_team_owner', '{{%team}}', 'mentor_profile_id');
        $this->addForeignKey('fk_team_owner', '{{%team}}', 'mentor_profile_id', '{{%mentor_profile}}', 'id', 'CASCADE', 'CASCADE');

        // Resolve the real students table + PK type
        $studentsClass   = \common\models\schools\Students::class;
        $studentsTable   = $studentsClass::tableName();
        $studentsRawName = $schema->getRawTableName($studentsTable);
        $studentsSchema  = $schema->getTableSchema($studentsRawName, true);
        if ($studentsSchema === null) {
            throw new \yii\base\InvalidConfigException("Students table '$studentsRawName' not found.");
        }
        $studentPk  = $studentsSchema->primaryKey[0] ?? 'id';
        $studentCol = $studentsSchema->columns[$studentPk];

        if ($studentCol->type === 'bigint') {
            $studentFkCol = $this->bigInteger();
        } else {
            $studentFkCol = $this->integer();
        }
        if ($studentCol->unsigned) {
            $studentFkCol = $studentFkCol->unsigned();
        }
        $studentFkCol = $studentFkCol->notNull();

        // team_student (junction)
        $this->createTable('{{%team_student}}', [
            'team_id'    => $this->integer()->notNull(),
            'student_id' => $studentFkCol,
            'PRIMARY KEY (team_id, student_id)',
        ]);

        $this->addForeignKey('fk_ts_team',    '{{%team_student}}', 'team_id',    '{{%team}}', 'id',           'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_ts_student', '{{%team_student}}', 'student_id',  $studentsTable, $studentPk,  'CASCADE', 'CASCADE');
    }

    public function down()
    {
        // Drop in reverse order
        $this->dropForeignKey('fk_ts_student', '{{%team_student}}');
        $this->dropForeignKey('fk_ts_team',    '{{%team_student}}');
        $this->dropTable('{{%team_student}}');

        $this->dropForeignKey('fk_team_owner', '{{%team}}');
        $this->dropTable('{{%team}}');

        $this->dropForeignKey('fk_mentor_profile_user', '{{%mentor_profile}}');
        $this->dropTable('{{%mentor_profile}}');
    }
}
