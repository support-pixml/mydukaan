<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveFieldsFromOrdersTable extends Migration
{
	public function up()
	{
		//

		$this->forge->dropColumn('orders', 'address');
		$this->forge->dropColumn('orders', 'city');
		$this->forge->dropColumn('orders', 'state');
		$this->forge->dropColumn('orders', 'country');
		$this->forge->dropColumn('orders', 'pincode');		
	}

	public function down()
	{
		//
		$fields = array(			
			'address'     => [
				'type'             => 'TEXT',
			],
			'city'     => [
				'type'             => 'VARCHAR',
				'constraint'   => '255',
				null     => false
			],
			'state'     => [
				'type'             => 'VARCHAR',
				'constraint'   => '255',
				null     => false
			],
			'country'     => [
				'type'             => 'VARCHAR',
				'constraint'   => '255',
				null     => false
			],
			'pincode'     => [
				'type'             => 'INT',
				'constraint'   => '6',
				null     => false
			],
			'order_status'     => [
				'type'             => 'INT',
				null     => false
			],
		);

		$this->forge->addColumn('orders', $fields);
	}
}
