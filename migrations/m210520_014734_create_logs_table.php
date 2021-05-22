<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%logs}}`.
 */
class m210520_014734_create_logs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%logs}}', [
            'id' => $this->primaryKey(),
            'ip' => $this->string(),
            'date_time' => $this->string(),
            'timestamp' => $this->integer(),
            'url' => $this->string(),
            'user_agent' => $this->string(),
            'operation' => $this->string(),
            'architecture' => $this->string(),
            'browser' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%logs}}');
    }
}
