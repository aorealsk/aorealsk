<?php
use yii\db\Migration;

class m251001_000002_fix_team_student_fk extends Migration
{
    // non-transactional to avoid MySQL DDL implicit commits
    public function up()
    {
        $db     = $this->db;
        $schema = $db->schema;

        // Resolve real students table + pk/type
        $studentsClass   = \common\models\schools\Students::class;
        $studentsTable   = $studentsClass::tableName();
        $studentsRawName = $schema->getRawTableName($studentsTable);
        $studentsSchema  = $schema->getTableSchema($studentsRawName, true);
        if ($studentsSchema === null) {
            throw new \yii\base\InvalidConfigException("Students table '$studentsRawName' not found.");
        }
        $studentPk  = $studentsSchema->primaryKey[0] ?? 'id';
        $studentCol = $studentsSchema->columns[$studentPk];

        // Build matching FK column type/sign
        $studentFkCol = ($studentCol->type === 'bigint') ? $this->bigInteger() : $this->integer();
        if ($studentCol->unsigned) $studentFkCol = $studentFkCol->unsigned();
        $studentFkCol = $studentFkCol->notNull();

        // Drop old table (if any)
        if ($schema->getTableSchema('{{%team_student}}', true) !== null) {
            try { $this->dropForeignKey('fk_ts_student', '{{%team_student}}'); } catch (\Throwable $e) {}
            try { $this->dropForeignKey('fk_ts_team',    '{{%team_student}}'); } catch (\Throwable $e) {}
            $this->dropTable('{{%team_student}}');
        }

        $tableOptions = ($db->driverName === 'mysql') ? 'ENGINE=InnoDB DEFAULT CHARSET='.$db->charset : null;

        // Recreate junction table with correct type
        $this->createTable('{{%team_student}}', [
            'team_id'    => $this->integer()->notNull(),
            'student_id' => $studentFkCol,
            'PRIMARY KEY (team_id, student_id)',
        ], $tableOptions);

        // Add FKs
        $this->addForeignKey('fk_ts_team', '{{%team_student}}', 'team_id', '{{%team}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_ts_student', '{{%team_student}}', 'student_id', $studentsTable, $studentPk, 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $schema = $this->db->schema;
        if ($schema->getTableSchema('{{%team_student}}', true) !== null) {
            try { $this->dropForeignKey('fk_ts_student', '{{%team_student}}'); } catch (\Throwable $e) {}
            try { $this->dropForeignKey('fk_ts_team',    '{{%team_student}}'); } catch (\Throwable $e) {}
            $this->dropTable('{{%team_student}}');
        }
    }
}
