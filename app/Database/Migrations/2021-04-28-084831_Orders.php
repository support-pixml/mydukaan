<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Orders extends Migration
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
			'long_id'     => [
				'type'             => 'VARCHAR',
				'constraint'   => '255',
				'unique' => true
			],
			'customer_name'     => [
				'type'             => 'VARCHAR',
				'constraint'   => '255',
				null     => false
			],
			'customer_company'     => [
				'type'             => 'VARCHAR',
				'constraint'   => '255',
			],
			'address'     => [
				'type'             => 'TEXT',
			],
			'customer_email'     => [
				'type'             => 'VARCHAR',
				'constraint'   => '255',
				null     => false
			],
			'customer_phone'     => [
				'type'             => 'VARCHAR',
				'constraint'   => '255',
				null     => false
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
			'order_total'     => [
				'type'             => 'FLOAT',
				'constraint'   => '11',
			],
			'order_status'     => [
				'type'             => 'INT', // 1 = pending, 2 = confirmed, 3 = delivered, 4 = cancelled
				'default'        => '1',
			],
			'orderBy' => [
				'type'           => 'INT',
				'constraint' => 11,
				'unsigned'  => true,
				'null' => false
			],	
			'note'     => [
				'type'      => 'TEXT',
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
		$this->forge->createTable('orders');
	}

	public function down()
	{
		//
		$this->forge->dropTable('orders');
	}
}
