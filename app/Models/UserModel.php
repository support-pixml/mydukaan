<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class UserModel extends Model
{
    protected $DBGroup = 'default'; 
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['name', 'phone', 'password'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    // public function findUserByEmailAddress(string $emailAddress)
    // {
    //     $user = $this
    //         ->asArray()
    //         ->where(['email' => $emailAddress])
    //         ->first();

    //     if (!$user) 
    //         throw new Exception('User does not exist for specified email address');

    //     return $user;
    // }
}