<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOptionNameFieldToOrderDetailsTable extends Migration
{
	public function up()
	{
		//
		$fields = array(
			'option_name' => array(
				'type' 			=> 'VARCHAR',
				'constraint'     => '255'
			)
		);

		$this->forge->addColumn('order_details', $fields);
	}

	public function down()
	{
		//
		$this->forge->dropColumn('order_details', 'option_name');
	}
}
