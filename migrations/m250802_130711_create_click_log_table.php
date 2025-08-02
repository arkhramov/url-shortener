<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%click_log}}`.
 */
class m250802_130711_create_click_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('click_log', [
            'id' => $this->primaryKey(),
            'url_id' => $this->integer()->notNull(),
            'clicked_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey('fk_click_url', 'click_log', 'url_id', 'url', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_click_url', 'click_log');
        $this->dropTable('click_log');
    }
}
