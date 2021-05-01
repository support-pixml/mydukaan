<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Productoptions extends Migration
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
			'product_id'     => [
				'type'             => 'INT',
				'constraint'   => '11',
				'unsigned'  => true,
			],
			'option_name'         => [
				'type'           => 'VARCHAR',
				'constraint'     => '255',
				'null'	      => false,
			],
			'option_price' => [
				'type'           => 'INT',
				'constraint' => 11,
				'unsigned'  => true,
				'null' => false
			],
			'option_stock' => [
				'type'           => 'INT',
				'constraint' => 11,
				'unsigned'  => true,
				'null' => false
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
		$this->forge->createTable('product_options');
	}

	public function down()
	{
		//
		$this->forge->dropTable('product_options');
	}
}
