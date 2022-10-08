<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveStatusFieldFromOrdersTable extends Migration
{
	public function up()
	{
		//
		$this->forge->dropColumn('orders', 'order_status');
	}

	public function down()
	{
		//
		$fields = array(
			'order_status'     => [
				'type'             => 'INT', // 1 = pending, 2 = confirmed, 3 = delivered, 4 = cancelled
				'default'        => '1',
			],
		);
		$this->forge->addColumn('orders', $fields);
	}
}
