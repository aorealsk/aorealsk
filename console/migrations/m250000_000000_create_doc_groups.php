<?php

use yii\db\Migration;

class m250000_000000_create_doc_groups extends Migration
{
    public function safeUp()
    {
        // Csoport tábla
        $this->createTable('{{%doc_group}}', [
            'id'          => $this->primaryKey(),
            'name'        => $this->string(128)->notNull()->unique(),
            'description' => $this->text(),
            'created_at'  => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'  => $this->dateTime()->null(),
            'created_by'  => $this->integer()->null(),
            'updated_by'  => $this->integer()->null(),
        ]);

        // Kapcsoló tábla (csoport-tagok)
        $this->createTable('{{%doc_group_user}}', [
            'group_id' => $this->integer()->notNull(),
            'user_id'  => $this->integer()->notNull(),
            'added_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->addPrimaryKey('pk_doc_group_user', '{{%doc_group_user}}', ['group_id','user_id']);
        $this->addForeignKey('fk_dgu_group', '{{%doc_group_user}}', 'group_id', '{{%doc_group}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_dgu_user',  '{{%doc_group_user}}', 'user_id',  '{{%user}}',       'id', 'CASCADE', 'CASCADE');

        $this->createIndex('idx_dgu_user',  '{{%doc_group_user}}', 'user_id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_dgu_user',  '{{%doc_group_user}}');
        $this->dropForeignKey('fk_dgu_group', '{{%doc_group_user}}');
        $this->dropTable('{{%doc_group_user}}');
        $this->dropTable('{{%doc_group}}');
    }
}
