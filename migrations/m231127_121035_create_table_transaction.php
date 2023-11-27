<?php

use yii\db\Migration;

/**
 * Class m231127_121035_create_table_transaction
 */
class m231127_121035_create_table_transaction extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('transaction', [
            'id' => $this->primaryKey(),
            'sender_wallet_id' => $this->integer()->notNull(),
            'receiver_wallet_id' => $this->integer()->notNull(),
            'amount_from' => $this->float()->unsigned()->notNull(),
            'currency_from' => $this->string(4)->notNull(),
            "amount_to" => $this->float()->unsigned()->notNull(),
            'currency_to' => $this->string(4)->notNull(),
            'timestamp' => $this->integer()->notNull(),

        ]);
        $this->createIndex(
            'idx-sender_wallet_id',
            'transaction',
            'sender_wallet_id'
        );

        $this->addForeignKey(
            'fk-sender_wallet_id',
            'transaction',
            'sender_wallet_id',
            'wallet',
            'id',
            'CASCADE'
        );
        $this->createIndex(
            'idx-receiver_wallet_id',
            'transaction',
            'receiver_wallet_id'
        );

        $this->addForeignKey(
            'fk-receiver_wallet_id',
            'transaction',
            'receiver_wallet_id',
            'wallet',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'idx-sender_wallet_id',
            'transaction',
        );

         $this->dropForeignKey(
            'fk-sender_wallet_id',
            'transaction',
        );

        $this->dropIndex(
            'idx-receiver_wallet_id',
            'transaction',
        );

         $this->dropForeignKey(
            'fk-receiver_wallet_id',
            'transaction',
        );

        $this->dropTable('transaction');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231127_121035_create_table_transaction cannot be reverted.\n";

        return false;
    }
    */
}
