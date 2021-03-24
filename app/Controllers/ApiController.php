<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\CategoryModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\Files\UploadedFile;

class ApiController extends Controller
{
    use ResponseTrait;
    protected $helpers = ['form', 'text'];
	// public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	// {
	// 	// Do Not Edit This Line
	// 	parent::initController($request, $response, $logger);

	// 	//--------------------------------------------------------------------
	// 	// Preload any models, libraries, etc, here.
	// 	//--------------------------------------------------------------------
	// 	// E.g.: $this->session = \Config\Services::session();
	// }

    function __construct()
    {
        // $db      = \Config\Database::connect();
    }

    // public function index()
    // {
    //     echo 'hello';
    // }

    public function signup()
    {
        $validation =  \Config\Services::validation();
        if(!$this->validate($validation->getRuleGroup('signup')))
        {
            return $this->fail($validation->getErrors(), 400);
        }
        else
        {
            $json = $this->request->getJSON();

            $long_id = random_string('alnum', 12);

            $data = [
                'name' => $json->name,
                'email' => $json->email,
                'password' => md5($json->password),
                'long_id' => $long_id,
            ];
            $db      = \Config\Database::connect();
            $db->table('users')->insert($data);
            $message = 'User Created';
            return $this->respondCreated($message, 201);
        }
    }

    public function signin()
    {
        $validation =  \Config\Services::validation();
        if(!$this->validate($validation->getRuleGroup('signin')))
        {
            return $this->fail($validation->getErrors(), 400);
        }
        else
        {
            $json = $this->request->getJSON();
            
            $data = [
                'email' => $json->email,
                'password' => md5($json->password)
            ];
            $db      = \Config\Database::connect();
            $query = $db->table('users')->select('id, long_id, email, role, name')->where($data)->get();
            $user_data = $query->getRow();
            if(is_null($user_data))
            {
                $message = 'Email or Password does\'nt match';
                return $this->fail($message, 400);
            }
            else
            {
                return $this->respond($user_data, 200);
            }
        }
    }

    public function get_users()
    {
        $db      = \Config\Database::connect();
        $query = $db->table('users')->get();
        $users = $query->getResult();
        if(is_null($users))
        {
            $message = 'No User Found.';
            return $this->fail($message, 400);
        }
        else
        {
            return $this->respond($users, 200);
        }
    }

    public function get_categories()
    {
        $db      = \Config\Database::connect();
        $query = $db->table('categories')->get();
        $categories = $query->getResult();
        if(is_null($categories))
        {
            $message = 'No Category Found.';
            return $this->fail($message, 400);
        }
        else
        {
            return $this->respond($categories, 200);
        }
    }

    public function add_category()
    {
        $validation = \Config\Services::validation();
        if(!$this->validate($validation->getRuleGroup('category')))
        {
            return $this->fail($validation->getErrors(), 400);
        }
        else
        {
            $post = $this->request->getPost();

            $long_id = random_string('alnum', 12);
            $slug = $this->slugify($post['name']);

            $db      = \Config\Database::connect();
            $query = $db->table('categories')->where('slug', $slug)->get();
            $result = $query->getRow();
            if($result)
            {
                $message = 'Category name is taken.';
                return $this->fail($message, 400);
            }

            $data = [
                'long_id' => $long_id,
                'name' => $post['name'],
                'slug' => $slug,
            ];

            $file = $this->request->getFile('image');
            $newName = $file->getRandomName();
            // $file->move(WRITEPATH.'uploads', $newName);
            $path = $file->store('categories/', $newName);

            $data['image'] = $newName;
            
            $db->table('categories')->insert($data);

            $message = 'Category Created';
            return $this->respondCreated($message, 201);
        }
    }

    public function update_category($category_long_id)
    {
        $db      = \Config\Database::connect();
        $query = $db->table('categories')->where('long_id', $category_long_id)->get();
        $result = $query->getRow();
        if(!$result)
        {
            $message = 'Category not found.';
            return $this->fail($message, 400);
        }

        $category_id = $result->id;

        $validation = \Config\Services::validation();
        if(!$this->validate($validation->getRuleGroup('update_category')))
        {
            return $this->fail($validation->getErrors(), 400);
        }

        $post = $this->request->getPost();
        $slug = $this->slugify($post['name']);

        $db      = \Config\Database::connect();
        $query = $db->table('categories')->where('slug', $slug)->get();
        $result = $query->getRow();
        if($result)
        {
            $message = 'Category name is taken.';
            return $this->fail($message, 400);
        }

        $data = [
                'name' => $post['name'],
                'slug' => $slug,
            ];
        
        if($this->request->getFile('image') != null)
        {
            $file = $this->request->getFile('image');
            $newName = $file->getRandomName();
            // $file->move(WRITEPATH.'uploads', $newName);
            $path = $file->store('categories/', $newName);
    
            $data['image'] = $newName;
        }

        $query = $db->table('categories');
        $query->set($data);
        $query->where('id', $category_id);
        $query->update();

        $message = 'Category Updated';
        return $this->respondCreated($message, 201);
    }

    public function add_product()
    {
        $validation = \Config\Services::validation();
        if(!$this->validate($validation->getRuleGroup('product')))
        {
            return $this->fail($validation->getErrors(), 400);
        }
        else
        {
            $post = $this->request->getPost();

            $long_id = random_string('alnum', 12);
            $slug = $this->slugify($post['name']);

            $db      = \Config\Database::connect();
            $query = $db->table('products')->where('slug', $slug)->get();
            $result = $query->getRow();
            if($result)
            {
                $message = 'Product name is taken.';
                return $this->fail($message, 400);
            }

            $data = [
                'long_id' => $long_id,
                'name' => $post['name'],
                'slug' => $slug,
                'category_id' => $post['category_id'],
                'price' => $post['price'],
                'stock' => $post['stock'],
                'description' => $post['description']
            ];

            $file = $this->request->getFile('image');
            $newName = $file->getRandomName();
            // $file->move(WRITEPATH.'uploads', $newName);
            $path = $file->store('products/', $newName);

            $data['image'] = $newName;
            
            $db->table('products')->insert($data);

            $message = 'Product Created';
            return $this->respondCreated($message, 201);
        }
    }

    private function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}