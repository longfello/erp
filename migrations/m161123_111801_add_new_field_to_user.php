<?php

use yii\db\Migration;
use yii\db\mysql\Schema;

class m161123_111801_add_new_field_to_user extends Migration
{
	public function up()
	{
		$this->addColumn('{{%user}}', 'first_name', Schema::TYPE_STRING);
		$this->addColumn('{{%user}}', 'last_name', Schema::TYPE_STRING);
		$this->addColumn('{{%user}}', 'middle_name', Schema::TYPE_STRING);
		$this->addColumn('{{%user}}', 'bithday', Schema::TYPE_DATE);
		$this->addColumn('{{%user}}', 'phone', Schema::TYPE_STRING);
		$this->addColumn('{{%user}}', 'position_id', Schema::TYPE_INTEGER);
		$this->addColumn('{{%user}}', 'theme', Schema::TYPE_STRING);
	}

	public function down()
	{
		$this->dropColumn('{{%user}}', 'first_name');
		$this->dropColumn('{{%user}}', 'last_name');
		$this->dropColumn('{{%user}}', 'middle_name');
		$this->dropColumn('{{%user}}', 'bithday');
		$this->dropColumn('{{%user}}', 'phone');
		$this->dropColumn('{{%user}}', 'position_id');
		$this->dropColumn('{{%user}}', 'theme');
	}
}
