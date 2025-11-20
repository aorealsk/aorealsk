<?php
use yii\db\Migration;

class m250920_000000_create_schedule_break_table extends Migration
{
    /** Non-transactional because MySQL DDL does implicit COMMITs. */
    public function up()
    {
        $table = $this->db->schema->getTableSchema('{{%schedule_break}}', true);

        if ($table === null) {
            // Fresh create
            $this->createTable('{{%schedule_break}}', [
                'id'         => $this->primaryKey(),
                'title'      => $this->string(255)->notNull()->defaultValue(''),
                'from_time'  => $this->string(5)->notNull(),
                'to_time'    => $this->string(5)->notNull(),
                'break_min'  => $this->integer()->null(),
                'created_at' => $this->integer()->notNull(),
            ]);
            $this->createIndex('idx_schedule_break_from_to', '{{%schedule_break}}', ['from_time', 'to_time']);
        } else {
            // Table already created by the failed transactional run — make sure it's complete.
            if (!isset($table->columns['title'])) {
                $this->addColumn('{{%schedule_break}}', 'title', $this->string(255)->notNull()->defaultValue(''));
            }
            // Create index if missing (ignore if it already exists)
            try {
                $this->createIndex('idx_schedule_break_from_to', '{{%schedule_break}}', ['from_time', 'to_time']);
            } catch (\Throwable $e) { /* index already there */ }
        }
        return true;
    }

    public function down()
    {
        $table = $this->db->schema->getTableSchema('{{%schedule_break}}', true);
        if ($table !== null) {
            // Drop index first (safe even if already dropped)
            try { $this->dropIndex('idx_schedule_break_from_to', '{{%schedule_break}}'); } catch (\Throwable $e) {}
            $this->dropTable('{{%schedule_break}}');
        }
        return true;
    }
}
