<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Categories extends Migration
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
			'name'         => [
				'type'           => 'VARCHAR',
				'constraint'     => '255',
				'null'	      => false,
			],
			'slug'         => [
				'type'           => 'VARCHAR',
				'constraint'     => '255',
				'unique' => true,
				'null'	      => false,
			],
			'image'     => [
				'type'             => 'VARCHAR',
				'constraint'   => '255',
				'null'	      => true,
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
		$this->forge->createTable('categories');
	}

	public function down()
	{
		//
		$this->forge->dropTable('categories');
	}
}
