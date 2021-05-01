<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Orderdetails extends Migration
{
	public function up()
	{
		//
		$this->forge->addField([
			'id'                => [
				'type'           => 'INT',
				'constraint' => 11,
				'unsigned'  => true,
				'auto_increment' => true,
			],
			'order_id'     => [
				'type'             => 'INT',
				null     => false
			],
			'product_id'     => [
				'type'             => 'INT',
				null     => false
			],
			'product_name'     => [
				'type'             => 'VARCHAR',
				'constraint'   => '255',
			],
			'product_price'     => [
				'type'             => 'FLOAT',
				null     => false
			],
			'quantity'     => [
				'type'             => 'INT',
				null     => false
			],
			'created_at'  => [
				'type'	      => 'DATETIME',
				'null'	      => true,
			],
			'updated_at' 	=> [
				'type'	     => 'DATETIME',
				'null'	     => true,
			]
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('order_details');
	}

	public function down()
	{
		//
		$this->forge->dropTable('order_details');
	}
}
