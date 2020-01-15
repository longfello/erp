<?php

use yii\db\Migration;

class m161123_113321_add_fk_user_to_position extends Migration
{
    public function up()
    {
	    // add foreign key for table `user`
	    $this->addForeignKey(
		    'user_position_fk',
		    'user',
		    'position_id',
		    'position',
		    'id',
		    null,
		    'CASCADE'
	    );
    }

    public function down()
    {
        $this->dropForeignKey('user_position_fk', 'user');
        return false;
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
