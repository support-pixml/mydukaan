<?php

namespace Config;

use CodeIgniter\Validation\CreditCardRules;
use CodeIgniter\Validation\FileRules;
use CodeIgniter\Validation\FormatRules;
use CodeIgniter\Validation\Rules;

class Validation
{
	//--------------------------------------------------------------------
	// Setup
	//--------------------------------------------------------------------

	/**
	 * Stores the classes that contain the
	 * rules that are available.
	 *
	 * @var string[]
	 */
	public $ruleSets = [
		Rules::class,
		FormatRules::class,
		FileRules::class,
		CreditCardRules::class,
	];

	/**
	 * Specifies the views that are used to display the
	 * errors.
	 *
	 * @var array<string, string>
	 */
	public $templates = [
		'list'   => 'CodeIgniter\Validation\Views\list',
		'single' => 'CodeIgniter\Validation\Views\single',
	];

	//--------------------------------------------------------------------
	// Rules
	//--------------------------------------------------------------------

	public $signup = [
        'name'		   => [
							'rules'  => 'required|min_length[3]',
							'errors' => [
								'required' => 'Please enter proper name.',
								'min_length' => '{field} must be {param} or more characters'
							]
						],
		'password'     => [
							'rules'  => 'required|min_length[8]',
							'errors' => [
								'required' => 'Password must be 8 or more characters.',
								'min_length' => '{field} must be {param} or more characters'
							]
						],
        'pass_confirm' => [
							'rules'  => 'required|matches[password]',
							'errors' => [
								'required' => 'Password must match with confirm password.',
								'matches' => 'Password must match with confirm password.'
							]
						],
        'email'        => [
							'rules'  => 'required|valid_email|is_unique[users.email]',
							'errors' => [
								'required' => 'Please enter proper email.',
								'valid_email' => 'Please enter proper email.',
								'is_unique' => 'Email Address already registered.'
							]
						]
    ];

	public $signin = [
		'email'        => [
							'rules'  => 'required|valid_email',
							'errors' => [
								'required' => 'Please enter proper email.',
								'valid_email' => 'Please enter proper email.'
							]
							],
		'password'     => [
							'rules'  => 'required|min_length[8]',
							'errors' => [
								'required' => 'Password must be 8 or more characters.',
								'min_length' => '{field} must be {param} or more characters'
							]
						],
    ];

	public $category = [
        'name'		   => [
							'rules'  => 'required|min_length[3]',
							'errors' => [
								'required' => 'Please enter proper name.',
								'min_length' => '{field} must be {param} or more characters'
							]
						],
        // 'image'        => [
		// 					'rules'  => 'max_size[image,1024]',
		// 					'errors' => [
		// 						'max_size' => 'Image size should be < 1 MB.',
		// 					]
		// 				]
    ];

	public $update_category = [
        'name'		   => [
							'rules'  => 'required|min_length[3]',
							'errors' => [
								'required' => 'Please enter proper name.',
								'min_length' => '{field} must be {param} or more characters'
							]
						]
    ];

	public $product = [
        'name'		   => [
							'rules'  => 'required|min_length[3]',
							'errors' => [
								'required' => 'Please enter proper name.',
								'min_length' => '{field} must be {param} or more characters'
							]
						],
		'image'        => [
							'rules'  => 'uploaded[image]|max_size[image,1024]',
							'errors' => [
								'max_size' => 'Image size should be < 1 MB.',
							]
						],
		'category_id'		   => [
							'rules'  => 'required|numeric',
							'errors' => [
								'required' => 'Please enter proper {field}.',
								'numeric' => '{field} must be {value}'
							]
						],
		'price'		   => [
							'rules'  => 'required|numeric',
							'errors' => [
								'required' => 'Please enter proper {field}.',
								'min_length' => '{field} must be {value}'
							]
						],
		'stock'		   => [
							'rules'  => 'required|numeric',
							'errors' => [
								'required' => 'Please enter proper {field}.',
								'min_length' => '{field} must be {value}'
							]
						],
		'description'		   => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Please enter proper {field}.',
							]
						],
    ];

	public $order = [
        'cust_name'		   => [
							'rules'  => 'required|min_length[3]',
							'errors' => [
								'required' => 'Please enter proper name.',
								'min_length' => '{field} must be {param} or more characters'
							]
						],
		'image'        => [
							'rules'  => 'uploaded[image]|max_size[image,1024]',
							'errors' => [
								'max_size' => 'Image size should be < 1 MB.',
							]
						],
		'category_id'		   => [
							'rules'  => 'required|numeric',
							'errors' => [
								'required' => 'Please enter proper {field}.',
								'numeric' => '{field} must be {value}'
							]
						],
		'price'		   => [
							'rules'  => 'required|numeric',
							'errors' => [
								'required' => 'Please enter proper {field}.',
								'min_length' => '{field} must be {value}'
							]
						],
		'stock'		   => [
							'rules'  => 'required|numeric',
							'errors' => [
								'required' => 'Please enter proper {field}.',
								'min_length' => '{field} must be {value}'
							]
						],
		'description'		   => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Please enter proper {field}.',
							]
						],
    ];

}
