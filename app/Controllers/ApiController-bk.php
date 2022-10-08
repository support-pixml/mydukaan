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
        $this->db      = \Config\Database::connect();
        $this->subdomain =  'technofab'; // explode(".", $_SERVER['SERVER_NAME']);
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
            $db      = \Config\Database::connect();
            $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
		    $customer = $query->getRow();
            
            $plan_query = $db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
            $customer_plan = $plan_query->getRow();

            if($customer_plan->expiry_date < date('Y-m-d'))
            {
                $message = 'Your account has been expired.';
                return $this->fail($message, 400);
            }

            $data = [
                'name' => $json->name,
                'phone' => $json->phone,
                'role' => $json->role,
                'customer_id' => $customer->id,
                'password' => md5($json->password),
                'long_id' => $long_id,
            ];
            
            $db->table('users')->insert($data);
            $message = 'User Created';
            return $this->respond(['message' => $message], 200);
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

            $db      = \Config\Database::connect();
            $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
		    $customer = $query->getRow();

            $plan_query = $db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
            $customer_plan = $plan_query->getRow();

            if($customer_plan == null)
            {
                $message = 'Your account has not been activated.';
                return $this->fail($message, 400);
            }

            if($customer_plan->expiry_date < date('Y-m-d'))
            {
                $message = 'Your account has been expired.';
                return $this->fail($message, 400);
            }
            
            $data = [
                'customer_id' => $customer->id,
                'phone' => $json->phone,
                'password' => md5($json->password)
            ];
            // echo '<pre>'; print_r($data); echo '</pre>';die;
            $query = $db->table('users')->select('id, long_id, phone, role, name')->where($data)->get();
            $user_data = $query->getRow();
            if(is_null($user_data))
            {
                $message = 'Phone or Password does\'nt match';
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
                        'access_token' => getSignedJWTForUser($user_data->phone)
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

    public function update_user()
    {
        $post = $this->request->getJSON();
        $db      = \Config\Database::connect();

        $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
        $customer = $query->getRow();

        $plan_query = $db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $query = $db->table('users')->where('long_id', $post->long_id)->get();
        $result = $query->getRow();
        if(!$result)
        {
            $message = 'User not found.';
            return $this->fail($message, 400);
        }

        $user_id = $result->id;

        $data = [
                'name' => $post->name,
                'phone' => $post->phone,
                'role' => $post->role,
            ];

        if($post->password !== null)
        {
            $data['password'] = md5($post->password);
        }

        $query = $db->table('users');
        $query->set($data);
        $query->where('id', $user_id);
        $query->update();

        $message = 'User Updated';
        return $this->respond(['message' => $message], 200);
    }

    public function get_users()
    {
        $db      = \Config\Database::connect();

        $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
        $customer = $query->getRow();

        $plan_query = $db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
        $customer = $query->getRow();

        $query = $db->table('users')->where('customer_id', $customer->id)->get();
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

    public function delete_user($user_long_id) 
    {
        $db      = \Config\Database::connect();

        $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
        $customer = $query->getRow();

        $plan_query = $db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $query = $db->table('users')->where('long_id', $user_long_id)->get();
        $result = $query->getRow();
        if(!$result)
        {
            $message = 'User not found.';
            return $this->fail($message, 400);
        }

        $user_id = $result->id;

        $query = $db->table('users');
        $query->where('id', $user_id);
        $query->delete();

        $message = 'User Removed';
        return $this->respond(['message' => $message], 200);
    }

    public function get_categories()
    {        
        $db      = \Config\Database::connect();

        $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
        $customer = $query->getRow();

        $plan_query = $db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $query = $db->table('categories')->where('customer_id', $customer->id)->orderby('name', 'ASC')->get();
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
            $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
            $customer = $query->getRow();

            $plan_query = $db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
            $customer_plan = $plan_query->getRow();

            if($customer_plan->expiry_date < date('Y-m-d'))
            {
                $message = 'Your account has been expired.';
                return $this->fail($message, 400);
            }

            $long_id = random_string('alnum', 12);
            $slug = $this->slugify($post['name']);

            $db      = \Config\Database::connect();
            $query = $db->table('categories')->where(['slug'=> $slug], ['customer_id' => $customer->id])->get();
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
                'customer_id' => $customer->id
            ];
            
            $db->table('categories')->insert($data);

            $message = 'Category Created';
            return $this->respond(['message' => $message], 200);
        }
    }

    public function update_category()
    {
        $validation = \Config\Services::validation();
        if(!$this->validate($validation->getRuleGroup('update_category')))
        {
            return $this->fail($validation->getErrors(), 400);
        }

        $post = $this->request->getPost();
        $db      = \Config\Database::connect();

        $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
        $customer = $query->getRow();

        $plan_query = $db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $query = $db->table('categories')->where('long_id', $post['category_id'])->get();
        $result = $query->getRow();
        if(!$result)
        {
            $message = 'Category not found.';
            return $this->fail($message, 400);
        }

        $category_id = $result->id;

        $slug = $this->slugify($post['name']);

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

        $query = $db->table('categories');
        $query->set($data);
        $query->where('id', $category_id);
        $query->update();

        $message = 'Category Updated';
        return $this->respond(['message' => $message], 200);
    }

    public function delete_category($category_long_id) 
    {
        $db      = \Config\Database::connect();

        $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
        $customer = $query->getRow();

        $plan_query = $db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $query = $db->table('categories')->where('long_id', $category_long_id)->get();
        $result = $query->getRow();
        if(!$result)
        {
            $message = 'Category not found.';
            return $this->fail($message, 400);
        }

        $category_id = $result->id;

        $query = $db->table('categories');
        $query->where('id', $category_id);
        $query->delete();

        $message = 'Category Removed';
        return $this->respond(['message' => $message], 200);
    }

    public function add_product()
    {
        // $validation = \Config\Services::validation();
        // if(!$this->validate($validation->getRuleGroup('product')))
        // {
        //     return $this->fail($validation->getErrors(), 400);
        // }
        // else
        // {
            $db      = \Config\Database::connect();
            $json = $this->request->getJSON();
            // echo '<pre>'; print_r($json); echo '</pre>';

            $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
            $customer = $query->getRow();         
            
            $plan_query = $db->table('customer_plans')->select('total_products, expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
            $customer_plan = $plan_query->getRow();

            if($customer_plan->expiry_date < date('Y-m-d'))
            {
                $message = 'Your account has been expired.';
                return $this->fail($message, 400);
            }
            
            $builder = $db->table("products")->select('products.id')->orderby('products.name', 'ASC');
            $builder->join('categories', 'products.category_id = categories.id', "left"); 
            $builder->where('categories.customer_id', $customer->id); 
            $products = $builder->get()->getResult();

            if($customer_plan->total_products <= count($products))
            {
                $message = 'You have exceeded maximum products upload limit.';
                return $this->fail($message, 400);
            }

            $long_id = random_string('alnum', 12);

            $data = [
                'long_id' => $long_id,
                'name' => $json->name,
                'category_id' => $json->category_id,
                'price' => $json->price,
                'description' => $json->description,
                'is_favorite' => $json->is_favorite
            ];

            if(isset($json->image) && $json->image !== '')
            {
                $img = $json->image;
                $img_name = md5(rand(0,9999));
                if(stripos($img, 'jpeg') !== false)
                {
                    $img = str_replace('data:image/jpeg;base64,', '', $img);
                    $file_name = $img_name.'_'.time().'.jpeg';
                    $file_thumb_name  = $img_name.'_'.time(). '_thumb.jpeg';
                }
                else if(stripos($img, 'png') !== false)
                {
                    $img = str_replace('data:image/png;base64,', '', $img);
                    $file_name = $img_name.'_'.time().'.png';
                    $file_thumb_name  = $img_name.'_'.time(). '_thumb.png';
                }
                else if(stripos($img, 'jpg') !== false)
                {
                    $img = str_replace('data:image/jpg;base64,', '', $img);
                    $file_name = $img_name.'_'.time().'.jpg';
                    $file_thumb_name  = $img_name.'_'.time(). '_thumb.jpg';
                }
                $img = str_replace(' ', '+', $img);
                $img_data = base64_decode($img);

                $target_dir = FCPATH.'/uploads/products/';
                $success = file_put_contents($target_dir.$file_name, $img_data);

                $data['image'] = $file_name;
                // echo '<pre>'; print_r($data); echo '</pre>';

                $image = service('image');

                $maintain_ratio = TRUE;
                $width          = 700;
                $height = 700;
                $quality        = 80;

                $msg = $image->withFile($target_dir . $file_name)
                        ->resize($width, $height, $maintain_ratio, 'width')
                        ->save($target_dir . $file_name);            
            }
            $db->table('products')->insert($data);
            $product_id = $db->insertID();       

            $message = 'Product Created';
            return $this->respond(['message' => $message], 200);
        }
    // }

    public function update_product($product_long_id)
    {
        $db      = \Config\Database::connect();

        $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
        $customer = $query->getRow();

        $plan_query = $db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $query = $db->table('products')->where('long_id', $product_long_id)->get();
        $result = $query->getRow();
        if(!$result)
        {
            $message = 'Product not found.';
            return $this->fail($message, 400);
        }

        $product_id = $result->id;

        $json = $this->request->getJSON();
        // echo '<pre>'; print_r($json); echo '</pre>';
        
        if($json->stock == '') $json->stock = 0;
        
        $data = [
            'name' => $json->name,
            'category_id' => $json->category_id,
            'price' => $json->price,
            'description' => $json->description,
            'is_favorite' => $json->is_favorite
        ];

        if(isset($json->image) && $json->image !== '')
        {
            $img = $json->image;
            $img_name = md5(rand(0,9999));
            if(stripos($img, 'jpeg') !== false)
            {
                $img = str_replace('data:image/jpeg;base64,', '', $img);
                $file_name = $img_name.'_'.time().'.jpeg';
                $file_thumb_name  = $img_name.'_'.time(). '_thumb.jpeg';
            }
            else if(stripos($img, 'png') !== false)
            {
                $img = str_replace('data:image/png;base64,', '', $img);
                $file_name = $img_name.'_'.time().'.png';
                $file_thumb_name  = $img_name.'_'.time(). '_thumb.png';
            }
            else if(stripos($img, 'jpg') !== false)
            {
                $img = str_replace('data:image/jpg;base64,', '', $img);
                $file_name = $img_name.'_'.time().'.jpg';
                $file_thumb_name  = $img_name.'_'.time(). '_thumb.jpg';
            }
            $img = str_replace(' ', '+', $img);
            $img_data = base64_decode($img);

            $target_dir = FCPATH.'/uploads/products/';
            $success = file_put_contents($target_dir.$file_name, $img_data);

            $data['image'] = $file_name;
        }

        $query = $db->table('products');
        $query->set($data);
        $query->where('id', $product_id);
        $query->update();
        
        $message = 'Product Updated';
        return $this->respond(['message' => $message], 200);
    }

    public function delete_product($product_long_id)
    {
        $db      = \Config\Database::connect();

        $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
        $customer = $query->getRow();

        $plan_query = $db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $query = $db->table('products')->where('long_id', $product_long_id)->get();
        $result = $query->getRow();
        if(!$result)
        {
            $message = 'Product not found.';
            return $this->fail($message, 400);
        }

        $product_id = $result->id;
        $product_image = $result->image;

        $query = $db->table('products');
        $query->where('id', $product_id);
        $query->delete();

        $target_dir = FCPATH.'/uploads/products/';
        
        if($product_image != null)
        {
            unlink($target_dir.$product_image);
        }

        $message = 'Product Removed';
        return $this->respond(['message' => $message], 200);
    }

    public function add_product_stock()
    {
        $post = $this->request->getPOST();
        $db      = \Config\Database::connect();
        // echo '<pre>'; print_r($json); echo '</pre>';
        $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
        $customer = $query->getRow();

        $plan_query = $db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $product_long_id = $post['long_id'];

        $db      = \Config\Database::connect();

        $query = $db->table('products')->where('long_id', $product_long_id)->get();
        $result = $query->getRow();
        if(!$result)
        {
            $message = 'Product not found.';
            return $this->fail($message, 400);
        }
        $product_id = $result->id;

        $data = [
            'product_id' => $product_id,
            'document_id' => $post['documentId'],
            'stock' => $post['stock'],
        ];
        
        $db->table('product_stocks')->insert($data);

        $message = 'Product stock added.';
        return $this->respond(['message' => $message], 200);
    }

    public function get_all_products()
    {
        $db      = \Config\Database::connect();

        $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
        $customer = $query->getRow();

        $plan_query = $db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }
        
        $favorite_products = [];
        
        $query = $db->table('categories')->select('id, long_id, name')->where('customer_id', $customer->id)->orderby('name', 'ASC')->get();
        $categories = $query->getResult();
        foreach($categories as $category)
        {
            $product_query = $db->table('products')->select('id, long_id, name, image, price, description, is_favorite')->where('category_id', $category->id)->orderby('name', 'ASC')->get();
            $products = $product_query->getResult();
            if(!$products)
                unset($category);
            else
            {
                foreach($products as $product)
                {
                    if($product->is_favorite == 1)
                    {
                        $favorite_products[] = $product; 
                    }
                    $product->base_price = $product->price;
                    $check_product_stock_query = $db->table('product_stocks')->select('stock')->where('product_id', $product->id)->get();
                    $check_product_stock = $check_product_stock_query->getResult();
                    $total_stock = 0;
                    foreach($check_product_stock as $product_stock)
                    {
                        $total_stock = $total_stock + $product_stock->stock;
                    }
                    $product->total_stock = $total_stock;
                }
                $category->products = $products;
                $category->product_count = count($products);
            }
        }
        if(!$categories)
        {
            $message = 'No Products Found.';
            return $this->fail($message, 400);
        }
        else
        {
            if(count($favorite_products) > 0)
            {
                $favorite = new \stdClass;
                $favorite->id = 0;
                $favorite->long_id = 'abcd1244';
                $favorite->name = 'Frequently Used';
                $favorite->products = $favorite_products;
                array_unshift($categories, $favorite);
            }
            return $this->respond($categories, 200);
        }
    }

    public function export_products()
    {
        $db      = \Config\Database::connect();

        $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
        $customer = $query->getRow();

        $plan_query = $db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $builder = $db->table("products");
        $builder->select('products.id, products.long_id, products.name, products.price, products.stock, categories.name as cat_name');
        $builder->join('categories', 'products.category_id = categories.id', "left"); 
        $builder->where('categories.customer_id', $customer->id); 
        $products = $builder->get()->getResult();

        $list = array();
        $row1 = array("Id", "Name", "Price", "Stock", "DOC ID", "Created At", "Category"); 

        array_push($list, $row1);

        foreach ($products as $product) {
            $product_arr = array();
            $product_arr[] = $product->long_id;
            $product_arr[] = $product->name;
            $product_arr[] = $product->price;
            $product_arr[] = '';
            $product_arr[] = '';
            $product_arr[] = '';
            $product_arr[] = $product->cat_name;
            
            array_push($list, $product_arr);

            $product_stock_query = $db->table('product_stocks')->where(['product_id' => $product->id])->get();
            $product_stocks = $product_stock_query->getResult();
            
            foreach($product_stocks as $stock)
            {
                if($stock->stock > 0)
                {
                    $stock_arr = array();
                    $stock_arr[] = '';
                    $stock_arr[] = '';
                    $stock_arr[] = '';
                    $stock_arr[] = $stock->stock;
                    $stock_arr[] = $stock->document_id;
                    $stock_arr[] = date('d-M-Y', strtotime($stock->created_at));
                    $stock_arr[] = '';
    
                    array_push($list, $stock_arr);
               }
            }
        }
        return $this->respond($list, 200);
    }

    public function import_products()
    {
        $db      = \Config\Database::connect();

        $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
        $customer = $query->getRow();

        $plan_query = $db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        if(isset($_FILES['csv']['name']))
        {
            $file = $this->request->getFile('csv');

            $target_dir = FCPATH.'/uploads/products/'; // Upload directory

            $path                    = $_FILES['csv']['name'];
            $extension               = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $tmp_file_name           = random_string('alnum', 12);
            $file_name               = $tmp_file_name . '.' . $extension;
            $config['upload_path']   = $target_dir;
            $config['allowed_types'] = 'csv';
            $config['file_name']     = $file_name;

            $file->move($target_dir, $file_name);

            $file = fopen($target_dir.$file_name, "r");
            
            $i = 1;
            while(!feof($file))
            {
                $row = fgetcsv($file);
                if($i == 1){
                    $i++;
                    continue;
                }
                // echo '<pre>'; print_r($row); echo '</pre>';die;
                $product_long_id = $row[0];  
                $product_name = $row[1];  
                $product_price = $row[2];  
                $product_stock = $row[3];  
                $category = $row[4];  

                $find_product_query = $db->table('products')->where('long_id', $product_long_id)->get();
                $product = $find_product_query->getRow();

                $find_category_query = $db->table('categories')->where('name', $category)->get();
                $category = $find_category_query->getRow();
                if(!$category)
                {
                    $message = $category.' not found in categories.';
                    return $this->fail($message, 400);
                }
                // echo '<pre>'; print_r($category); echo '</pre>';die;
                if($product_long_id !== '')
                {
                    //update
                    $product_data = array(
                        'name' => $product_name,
                        'price' => $product_price,
                        'stock' => $product_stock + $product->stock,
                        'category_id' => $category->id,
                    );
                    $query = $db->table('products');
                    $query->set($product_data);
                    $query->where('id', $product->id);
                    $query->update();
                }
                else
                {
                    // insert
                    $product_data = array(
                        'long_id' => random_string('alnum', 12),
                        'name' => $product_name,
                        'price' => $product_price,
                        'stock' => $product_stock,
                        'category_id' => $category->id,
                    );

                    $db->table('products')->insert($product_data);
                    $product_id = $db->insertID();
                }
            }
            fclose($file);
            $message = 'Products Uploaded';
            return $this->respond($message, 200);
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
            //  check user id
            $post = $this->request->getJSON();
            $long_id = random_string('alnum', 12);

            $db      = \Config\Database::connect();

            $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
            $customer = $query->getRow();

            $plan_query = $db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
            $customer_plan = $plan_query->getRow();

            if($customer_plan->expiry_date < date('Y-m-d'))
            {
                $message = 'Your account has been expired.';
                return $this->fail($message, 400);
            }

            $cart = $post->cart; 
            foreach($cart as $cart_item)
            {            
                $product_long_id = $cart_item->long_id;
                $product_quantity = $cart_item->quantity;
                $product_price = $cart_item->price;

                $check_product_data_query = $db->table('products')->select('id, name, price')->where('long_id', $product_long_id)->get();
                $check_product_data = $check_product_data_query->getRow();
                if(is_null($check_product_data))
                {
                    $message = 'No Product Found.';
                    return $this->fail($message, 400);
                }
                $check_product_stock_query = $db->table('product_stocks')->select('id, stock')->where(['product_id' => $check_product_data->id], ['stock !==' => 0])->orderBy('id', 'ASC')->get();
                $check_product_stock = $check_product_stock_query->getResult();
                if(is_null($check_product_stock))
                {
                    $message = 'Product Out of Stock.';
                    return $this->fail($message, 400);
                }

                $total_stock = 0;
                foreach($check_product_stock as $product_stock)
                {
                    $total_stock = $total_stock + $product_stock->stock;
                }

                if($product_quantity > $total_stock)
                {
                    $message = 'Product Out of Stock.';
                    return $this->fail($message, 400);
                }
            }

            $data = [
                'long_id' => $long_id,
                'customer_id' => $customer->id,
                'customer_name' => $post->customer_name,
                'customer_company' => $post->company_name,
                'customer_email' => $post->customer_email,
                'customer_phone' => $post->customer_phone,
                'order_total' => 0,
                'reference' => $post->reference,
                'orderBy' => $post->user_id,
                'note' => $post->note,
            ];            
            
            $db->table('orders')->insert($data);
            $order_id = $db->insertID();         

            $order_total = 0;    

            foreach($cart as $cart_item)
            {            
                $product_long_id = $cart_item->long_id;
                $product_quantity = $cart_item->quantity;
                $product_price = $cart_item->price;

                $check_product_data_query = $db->table('products')->select('id, name, price')->where('long_id', $product_long_id)->get();
                $check_product_data = $check_product_data_query->getRow();

                $check_product_stock_query = $db->table('product_stocks')->select('id, stock')->where(['product_id' => $check_product_data->id], ['stock !==' => 0])->orderBy('id', 'ASC')->get();
                $check_product_stock = $check_product_stock_query->getResult();

                // echo '<pre>'; print_r($check_product_stock); echo '</pre>';die;

                $order_details = [
                    'order_id' => $order_id,
                    'product_id' => $check_product_data->id,
                    'product_name' => $check_product_data->name,
                    'product_price' => $product_price,
                    'quantity' => $product_quantity
                ];
                //insert order details
                $order_details_insert = $db->table('order_details')->insert($order_details);

                foreach($check_product_stock as $product_stock)
                {
                    if($product_quantity < 0)
                        continue;

                    if($product_stock->stock >= $product_quantity)
                    {
                        // insert 
                        $remaining_stock = $product_stock->stock - $product_quantity;                          
                    }
                    else
                    {
                        $product_quantity = $product_quantity - $product_stock->stock;     
                        $remaining_stock = 0;
                    }

                    $subtract_stock_data = array('stock' => $remaining_stock);
                    $subtract_query = $db->table('product_stocks');
                    $subtract_query->set($subtract_stock_data);
                    $subtract_query->where('id', $product_stock->id);
                    $subtract_query->update();                    
                }

                $order_total = $order_total + ($product_price*$product_quantity);   
            }
            $update_order_data = array('order_total' => $order_total);
            $order_query = $db->table('orders');
            $order_query->set($update_order_data);
            $order_query->where('id', $order_id);
            $order_query->update();   

            $message = 'Your Order has been successfully placed.';
            return $this->respond(['message' => $message], 200);
        // }
    }

    public function get_products()
    {
        $db      = \Config\Database::connect();

        $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
        $customer = $query->getRow();

        $plan_query = $db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $builder = $db->table("products")->orderby('name', 'ASC');
        $builder->select('products.*, categories.name as cat_name');
        $builder->join('categories', 'products.category_id = categories.id', "left"); 
        $builder->where('categories.customer_id', $customer->id); 
        $products = $builder->get()->getResult();
        foreach($products as $product)
        {
            $check_product_stock_query = $db->table('product_stocks')->select('stock')->where('product_id', $product->id)->get();
            $check_product_stock = $check_product_stock_query->getResult();
            $total_stock = 0;
            foreach($check_product_stock as $product_stock)
            {
                $total_stock = $total_stock + $product_stock->stock;
            }
            $product->total_stock = $total_stock;
        }
        if(is_null($products))
        {
            $message = 'Sorry! No Product Found.';
            return $this->fail($message, 400);
        }
        else
        {
            return $this->respond($products, 200);
        }
    }

    public function get_orders()
    {
        $post = $this->request->getJSON();

        $db      = \Config\Database::connect();

        $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
        $customer = $query->getRow();

        $plan_query = $db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        if(isset($post->reference))
        {
            $reference = $post->reference;
        }
        if(isset($post->fromDate))
        {
            $fromDate = $post->fromDate;
        }
        if(isset($post->toDate))
        {
            $toDate = $post->toDate;
        }        
        if(isset($post->product_id))
        {
            $product_id = $post->product_id;
        }

        $builder = $db->table("orders")->orderby('id', 'DESC');
        $builder->select('orders.*, ref.name as ref_name, orderedBy.name as order_by');
        $builder->join('users as ref', 'orders.reference = ref.id', "left"); // added left here
        $builder->join('users as orderedBy', 'orders.orderBy = orderedBy.id', "left"); // added left here
        $builder->where('orders.customer_id', $customer->id); // added left here
        if(isset($reference) && $reference != "")
        {
            $builder->where('orders.reference', $reference);
        }
        if(isset($fromDate) && isset($toDate))
        {
            $builder->where('DATE(orders.created_at) >=', $fromDate);
            $builder->where('DATE(orders.created_at) <=', $toDate);
        }
        if(isset($product_id) && $product_id != "")
        {
            $builder->join('order_details', 'orders.id = order_details.order_id', "left"); // added left here
            $builder->where('order_details.product_id', $product_id)->distinct();
        }   
        $orders = $builder->get()->getResult();
        foreach($orders as $order)
        {
            $order_details_query_builder = $db->table("order_details")->select('*');
            $order_details_query_builder->where('order_id', $order->id);
            $order_details = $order_details_query_builder->get()->getResult();
            $order->order_details = $order_details;
        }       
        
        if(is_null($orders))
        {
            $message = 'Sorry! No Order Found.';
            return $this->fail($message, 400);
        }
        else
        {
            return $this->respond($orders, 200);
        }
    }

    public function delete_order($order_long_id)
    {
        $db      = \Config\Database::connect();

        $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
        $customer = $query->getRow();

        $plan_query = $db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $query = $db->table('orders')->where('long_id', $order_long_id)->get();
        $result = $query->getRow();
        if(!$result)
        {
            $message = 'Sorry! Order not found.';
            return $this->fail($message, 400);
        }

        $order_id = $result->id;

        $query = $db->table('orders');
        $query->where('id', $order_id);
        $query->delete();

        $query = $db->table('order_details');
        $query->where('order_id', $order_id);
        $query->delete();

        $message = 'Order has been Removed Successfully.';
        return $this->respond(['message' => $message], 200);
    }

    public function export_orders()
    {
        $post = $this->request->getJSON();

        $db      = \Config\Database::connect();

        $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
        $customer = $query->getRow();

        $plan_query = $db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        if(isset($post->reference))
        {
            $reference = $post->reference;
        }
        if(isset($post->fromDate))
        {
            $fromDate = $post->fromDate;
        }
        if(isset($post->toDate))
        {
            $toDate = $post->toDate;
        }        
        if(isset($post->product_id))
        {
            $product_id = $post->product_id;
        }

        $builder = $db->table("orders");
        $builder->select('orders.id, orders.long_id, orders.customer_name, orders.customer_company, orders.customer_email, orders.customer_phone, orders.order_total, orders.reference, orders.orderBy, ref.name as ref_name, orderedBy.name as order_by');
        $builder->join('users as ref', 'orders.reference = ref.id', "left"); // added left here
        $builder->join('users as orderedBy', 'orders.orderBy = orderedBy.id', "left"); // added left here
        $builder->where('orders.customer_id', $customer->id);
        if(isset($reference) && $reference != "")
        {
            $builder->where('orders.reference', $reference);
        }
        if(isset($fromDate) && isset($toDate))
        {
            $builder->where('DATE(orders.created_at) >=', $fromDate);
            $builder->where('DATE(orders.created_at) <=', $toDate);
        }
        if(isset($product_id) && $product_id != "")
        {
            $builder->join('order_details', 'orders.id = order_details.order_id', "left"); // added left here
            $builder->where('product_id', $product_id)->distinct();
        } 
        $orders = $builder->get()->getResult();

        $list = array();
        $row1 = array("Id", "Customer Name", "Customer Company", "Customer Email", "Customer Phone", "Order Total", "Reference", "Order By", "Product Name", "Price", "Quantity"); 

        array_push($list, $row1);

        foreach ($orders as $order) {
            $order_arr = array();
            $order_arr[] = $order->id;
            $order_arr[] = $order->customer_name;
            $order_arr[] = $order->customer_company;
            $order_arr[] = $order->customer_email;
            $order_arr[] = $order->customer_phone;
            $order_arr[] = $order->order_total;
            $order_arr[] = $order->ref_name;
            $order_arr[] = $order->order_by;
            $order_arr[] = '';
            $order_arr[] = '';
            $order_arr[] = '';

            array_push($list, $order_arr);

            $query = $db->table('order_details')->select('product_name, product_price, quantity')->where('order_id', $order->id)->get();
            $details = $query->getResult();
            
            foreach($details as $detail)
            {
                $order_details = array();
                $order_details[] = '';
                $order_details[] = '';
                $order_details[] = '';
                $order_details[] = '';
                $order_details[] = '';
                $order_details[] = '';
                $order_details[] = '';
                $order_details[] = '';
                $order_details[] = $detail->product_name;
                $order_details[] = $detail->product_price;
                $order_details[] = $detail->quantity;
                
                array_push($list, $order_details);
            }
            
        }
        return $this->respond($list, 200);
    }

    public function get_customer_data($customer_long_id)
    {
        $db      = \Config\Database::connect();

		$query = $db->table('customers')->where('long_id', $customer_long_id)->get();
		$customer = $query->getRow();
		$plan_query = $db->table('customer_plans')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
		$customer_plan = $plan_query->getRow();
		$customer->expiry_date = $customer_plan->expiry_date;
		$customer->total_products = $customer_plan->total_products;

        return $this->respond($customer, 200);
    }  

    public function get_searched_products()
    {
        $post = $this->request->getJSON();
        $search = $post->search;
        
        $db      = \Config\Database::connect();

        $query = $db->table('customers')->select('id')->where('url_title', 'technofab')->get();
        $customer = $query->getRow();

        $plan_query = $db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }
        
        $searched_products = [];

        $product_query = $db->table('products')->select('id, long_id, name, image, price, description');
        $product_query->like('name', $search);
        $product_query->orderby('name', 'ASC');
        $products = $product_query->get()->getResult();
        if(!$products)
        {
            $message = 'No Products Found.';
            return $this->fail($message, 400);
        }
        else
        {
            foreach($products as $product)
            {
                $product->base_price = $product->price;
                $check_product_stock_query = $db->table('product_stocks')->select('stock')->where('product_id', $product->id)->get();
                $check_product_stock = $check_product_stock_query->getResult();
                $total_stock = 0;
                foreach($check_product_stock as $product_stock)
                {
                    $total_stock = $total_stock + $product_stock->stock;
                }
                $product->total_stock = $total_stock;
            }
        }

        $search_result = new \stdClass;
        $search_result->id = 0;
        $search_result->long_id = 'search1234';
        $search_result->name = 'Searched Result: '.$search;
        $search_result->products = $products;
        array_unshift($searched_products, $search_result);
        
        return $this->respond($searched_products, 200);
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