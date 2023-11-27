<?php

use yii\db\Migration;

/**
 * Class m231127_112324_create_table_wallet
 */
class m231127_112324_create_table_wallet extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
        $this->createTable('wallet', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'currency' => $this->string(4)->notNull(),
            'balance' => $this->float()->unsigned()->defaultValue(0),
          //  'created_at' => $this->integer()->notNull(),
          //  'updated_at' => $this->integer()->notNull(),
            
        ]);

        $this->createIndex(
            'idx-wallet-user_id',
            'wallet',
            'user_id'
        );

        $this->addForeignKey(
            'fk-wallet-user_id',
            'wallet',
            'user_id',
            'user',
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
            'idx-wallet-user_id',
            'wallet',
        );

         $this->dropForeignKey(
            'fk-wallet-user_id',
            'wallet',
        );

        $this->dropTable('wallet');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231127_112324_create_table_wallet cannot be reverted.\n";

        return false;
    }
    */
}
