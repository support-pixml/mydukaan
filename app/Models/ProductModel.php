<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $DBGroup = 'default'; 
    protected $table      = 'products';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['name', 'slug', 'image', 'price', 'stock', 'description', 'category_id'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}