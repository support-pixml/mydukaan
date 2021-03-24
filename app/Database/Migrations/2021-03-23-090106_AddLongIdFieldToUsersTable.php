<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLongIdFieldToUsersTable extends Migration
{
	public function up()
	{
		//
		$fields = array(
			'long_id' => array(
				'type' 			=> 'VARCHAR',
				'constraint'     => '255'
			)
		);

		$this->forge->addColumn('users', $fields);
	}

	public function down()
	{
		//
		$this->forge->dropColumn('users', 'long_id');
	}
}
