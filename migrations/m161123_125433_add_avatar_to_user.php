<?php

use yii\db\Migration;

class m161123_125433_add_avatar_to_user extends Migration
{
    public function up()
    {
	    $this->addColumn('{{%user}}', 'avatar', \yii\db\mysql\Schema::TYPE_STRING);
    }

    public function down()
    {
	    $this->dropColumn('{{%user}}', 'avatar');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
