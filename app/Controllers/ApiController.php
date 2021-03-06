<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\CategoryModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\Files\UploadedFile;
use Exception;
use ReflectionException;

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
            return $this->respond(['message', $message], 200);
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
                return $this->getJWTForUser($user_data);
            }
        }
    }

    public function logout()
    {
        $message = 'Sign Out Successfully!';
        return $this->respond(['message' => $message], 200);
    }

    private function getJWTForUser($user_data, int $responseCode = ResponseInterface::HTTP_OK)
    {
        try {
            helper('jwt');

            return $this
                ->respond(
                    [
                        'message' => 'User authenticated successfully',
                        'user' => $user_data,
                        'access_token' => getSignedJWTForUser($user_data->email)
                    ]
                );
        } catch (Exception $exception) {
            return $this
                ->respond(
                    [
                        'error' => $exception->getMessage(),
                    ],
                    $responseCode
                );
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
        $query = $db->table('categories')->orderby('name', 'ASC')->get();
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
            // $post = $this->request->getJSON();

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
            $path = $file->move(FCPATH.'/uploads/categories/', $newName);

            $data['image'] = $newName;
            
            $db->table('categories')->insert($data);

            $message = 'Category Created';
            return $this->respond(['message' => $message], 200);
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
        // $query = $db->table('categories')->where('slug', $slug)->get();
        // $result = $query->getRow();
        // if($result)
        // {
        //     $message = 'Category name is taken.';
        //     return $this->fail($message, 400);
        // }

        $data = [
                'name' => $post['name'],
                'slug' => $slug,
            ];
        
        if($this->request->getFile('image') != null)
        {
            $file = $this->request->getFile('image');
            $newName = $file->getRandomName();
            $path = $file->move(FCPATH.'/uploads/categories/', $newName);
    
            $data['image'] = $newName;
        }

        $query = $db->table('categories');
        $query->set($data);
        $query->where('id', $category_id);
        $query->update();

        $message = 'Category Updated';
        return $this->respond(['message' => $message], 200);
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
            $path = $file->move(FCPATH.'/uploads/products/', $newName);

            $data['image'] = $newName;
            
            $db->table('products')->insert($data);
            $product_id = $db->insertID();
            
                        

            $message = 'Product Created';
            return $this->respond(['message' => $message], 200);
        }
    }

    public function get_all_products()
    {
        $db      = \Config\Database::connect();
        $query = $db->table('categories')->select('id, long_id, slug, name')->orderby('name', 'ASC')->get();
        $categories = $query->getResult();
        foreach($categories as $category)
        {
            $product_query = $db->table('products')->select('id, long_id, name, slug, image, price, stock')->where('category_id', $category->id)->orderby('name', 'ASC')->get();
            $products = $product_query->getResult();
            if(!$products)
                unset($category);
            else
            {
                foreach($products as $product)
                {
                    $product_option_query = $db->table('product_options')->select('id as product_option_id, product_id, option_name, option_stock, option_price as price, add_quantity')->where('product_id', $product->id)->get();
                    $product_options = $product_option_query->getResult();
                    foreach($product_options as $option)
                    {
                        $option->long_id = $product->long_id;
                        $option->product_name = $product->name;
                        $option->add_quantity = 0;
                    }
                    $product->product_options = $product_options;
                    unset($product->id);
                }
                $category->products = $products;
                $category->product_count = count($products);
            }
        }
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

    public function place_order()
    {
        // $validation = \Config\Services::validation();
        // if(!$this->validate($validation->getRuleGroup('product')))
        // {
        //     return $this->fail($validation->getErrors(), 400);
        // }
        // else
        // {
            $post = $this->request->getJSON();
            echo '<pre>'; print_r($post); echo '</pre>';
            $long_id = random_string('alnum', 12);

            $db      = \Config\Database::connect();

            $data = [
                'long_id' => $long_id,
                'customer_name' => $post->customer_name,
                'customer_company' => $post->company_name,
                'address' => $post->address,
                'customer_email' => $post->customer_email,
                'customer_phone' => $post->customer_phone,
                'city' => 'Surat',
                'state' => 'Gujarat',
                'country' => 'India',
                'pincode' => 395009,
                'order_total' => 0,
                'orderBy' => '1',
                'note' => $post->note
            ];
            
            $db->table('orders')->insert($data);
            $order_id = $db->insertID();           

            $cart = $post->cart;        
            $order_total = 0;    

            foreach($cart as $cart_item)
            {            
                $product_long_id = $cart_item->long_id;
                $product_quantity = $cart_item->quantity;
                $product_price = $cart_item->price;

                $option_name = $cart_item->option_name;
                $option_id = $cart_item->product_option_id;
                    
                if(isset($option_name))
                {
                    $query = $db->table('products')->where('long_id', $product_long_id)->get();
                    $result = $query->getRow();
                    if(!$result)
                    {
                        $message = 'Product not found.';
                        return $this->fail($message, 400);
                    }

                    $product_id = $result->id;
                    $product_name = $result->name;
                    $check_product_option_stock_query = $db->table('product_options')->select('option_name, option_stock, option_price')->where('id', $option_id)->get();
                    $check_product_option_stock = $check_product_option_stock_query->getRow();
                    if(is_null($check_product_option_stock))
                    {
                        $message = 'No Product Option Found.';
                        return $this->fail($message, 400);
                    }
                    else if($product_quantity <= $check_product_option_stock->option_stock)
                    {
                        $order_details = [
                            'order_id' => $order_id,
                            'product_id' => $product_id,
                            'product_name' => $product_name,
                            'option_name' => $check_product_option_stock->option_name,
                            'product_price' => $check_product_option_stock->option_price,
                            'quantity' => $product_quantity

                        ];
                        //insert order details
                        $order_details_insert = $db->table('order_details')->insert($order_details);

                        // subtract product stock
                        $remaining_stock = $check_product_option_stock->option_stock - $product_quantity;
                        
                        $update_product_data = array('option_stock' => $remaining_stock);
                        $subtract_query = $db->table('product_options');
                        $subtract_query->set($update_product_data);
                        $subtract_query->where('id', $option_id);
                        $subtract_query->update();   

                        $order_total = $order_total + ($product_price*$product_quantity);     
                    }
                    else
                    {
                        $message = 'Product Out of Stock.';
                        return $this->fail($message, 400);
                    }
                }
                else
                {
                    $check_product_stock_query = $db->table('products')->select('id, name, stock, price')->where('long_id', $product_long_id)->get();
                    $check_product_stock = $check_product_stock_query->getRow();
                    if(is_null($check_product_stock))
                    {
                        $message = 'No Product Found.';
                        return $this->fail($message, 400);
                    }
                    else if($product_quantity <= $check_product_stock->stock)
                    {
                        $order_details = [
                            'order_id' => $order_id,
                            'product_id' => $check_product_stock->id,
                            'product_name' => $check_product_stock->name,
                            // 'option_name' => '',
                            'product_price' => $check_product_stock->price,
                            'quantity' => $product_quantity

                        ];
                        //insert order details
                        $order_details_insert = $db->table('order_details')->insert($order_details);

                        // subtract product stock
                        $remaining_stock = $check_product_stock->stock - $product_quantity;
                        
                        $update_product_data = array('stock' => $remaining_stock);
                        $subtract_query = $db->table('products');
                        $subtract_query->set($update_product_data);
                        $subtract_query->where('id', $check_product_stock->id);
                        $subtract_query->update();   

                        $order_total = $order_total + ($product_price*$product_quantity);                        
                    }
                    else
                    {
                        $message = 'Product Out of Stock.';
                        return $this->fail($message, 400);
                    }
                }
            }

            $message = 'Order Placed';
            return $this->respond(['message' => $message], 200);
        // }
    }

    public function get_products()
    {
        $db      = \Config\Database::connect();
        $query = $db->table('products')->orderby('name', 'ASC')->get();
        $products = $query->getResult();
        if(is_null($products))
        {
            $message = 'No Product Found.';
            return $this->fail($message, 400);
        }
        else
        {
            return $this->respond($products, 200);
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