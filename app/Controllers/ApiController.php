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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

//Load Composer's autoloader
require '../vendor/autoload.php';

class ApiController extends Controller
{
    use ResponseTrait;
    protected $helpers = ['form', 'text'];
    public $subdomain;
    public $db;
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
        $domain_arr = explode(".", $_SERVER['SERVER_NAME']);
        $this->subdomain = 'd40'; // $domain_arr[0];
        header('Access-Control-Allow-Origin: *');
		Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
		Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE'); //method allowed

		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
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
             
            $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
		    $customer = $query->getRow();
            
            $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
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
            
            $this->db->table('users')->insert($data);
            $message = 'User Created Successfully.';
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
             
            $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
		    $customer = $query->getRow();

            $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
            $customer_plan = $plan_query->getRow();

            if($customer_plan == null)
            {
                $message = 'Sorry! Your account is inactive.';
                return $this->fail($message, 400);
            }

            if($customer_plan->expiry_date < date('Y-m-d'))
            {
                $message = 'Sorry! Your account has been expired.';
                return $this->fail($message, 400);
            }
            
            $data = [
                'customer_id' => $customer->id,
                'phone' => $json->phone,
                'password' => md5($json->password)
            ];
            
            $query = $this->db->table('users')->select('id, long_id, phone, role, name')->where($data)->get();
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

        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $query = $this->db->table('users')->where('long_id', $post->long_id)->get();
        $result = $query->getRow();
        if(!$result)
        {
            $message = 'Sorry! User not found.';
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

        $query = $this->db->table('users');
        $query->set($data);
        $query->where('id', $user_id);
        $query->update();

        $message = 'User Updated Successfully.';
        return $this->respond(['message' => $message], 200);
    }

    public function get_users()
    {
        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $query = $this->db->table('users')->where('customer_id', $customer->id)->get();
        $users = $query->getResult();
        if(is_null($users))
        {
            $message = 'Sorry! No User Found.';
            return $this->fail($message, 400);
        }
        else
        {
            return $this->respond($users, 200);
        }
    }

    public function delete_user($user_long_id) 
    {
        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $query = $this->db->table('users')->where('long_id', $user_long_id)->get();
        $result = $query->getRow();
        if(!$result)
        {
            $message = 'Sorry! User not found.';
            return $this->fail($message, 400);
        }

        $user_id = $result->id;

        $query = $this->db->table('users');
        $query->where('id', $user_id);
        $query->delete();

        $message = 'User Removed Successfully.';
        return $this->respond(['message' => $message], 200);
    }

    public function get_categories()
    {    
        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $query = $this->db->table('categories')->where('customer_id', $customer->id)->orderBy('name', 'ASC')->get();
        $categories = $query->getResult();
        if(is_null($categories))
        {
            $message = 'Sorry! No Category Found.';
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
            $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
            $customer = $query->getRow();

            $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
            $customer_plan = $plan_query->getRow();

            if($customer_plan->expiry_date < date('Y-m-d'))
            {
                $message = 'Your account has been expired.';
                return $this->fail($message, 400);
            }

            $long_id = random_string('alnum', 12);
            $slug = $this->slugify($post['name']);

             
            $query = $this->db->table('categories')->where(['slug'=> $slug, 'customer_id' => $customer->id])->get();
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
            
            $this->db->table('categories')->insert($data);

            $message = 'Category Created Successfully.';
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
         

        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $query = $this->db->table('categories')->where('long_id', $post['category_id'])->get();
        $result = $query->getRow();
        if(!$result)
        {
            $message = 'Sorry! Category not found.';
            return $this->fail($message, 400);
        }

        $category_id = $result->id;

        $slug = $this->slugify($post['name']);

        $query = $this->db->table('categories')->where(['slug'=> $slug, 'customer_id' => $customer->id])->get();
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

        $query = $this->db->table('categories');
        $query->set($data);
        $query->where('id', $category_id);
        $query->update();

        $message = 'Category Updated Successfully.';
        return $this->respond(['message' => $message], 200);
    }

    public function delete_category($category_long_id) 
    {
        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $query = $this->db->table('categories')->where('long_id', $category_long_id)->get();
        $result = $query->getRow();
        if(!$result)
        {
            $message = 'Sorry! Category not found.';
            return $this->fail($message, 400);
        }

        $category_id = $result->id;

        $query = $this->db->table('categories');
        $query->where('id', $category_id);
        $query->delete();

        $message = 'Category Removed Successfully.';
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
             
            $json = $this->request->getJSON();
            // echo '<pre>'; print_r($json); echo '</pre>';

            $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
            $customer = $query->getRow();         
            
            $plan_query = $this->db->table('customer_plans')->select('total_products, expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
            $customer_plan = $plan_query->getRow();

            if($customer_plan->expiry_date < date('Y-m-d'))
            {
                $message = 'Your account has been expired.';
                return $this->fail($message, 400);
            }
            
            $builder = $this->db->table("products")->select('products.id')->orderBy('products.name', 'ASC');
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
                'min_stock_qty' => $json->min_stock_qty,
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

                $exif = @exif_read_data($target_dir.$file_name);

                if (!empty($exif['Orientation']))
                {                
                    $oris = array();
    
                    switch($exif['Orientation'])
                    {
                        case 1: // no need to perform any changes
                        break;
    
                        case 2: // horizontal flip
                        // $oris[] = 'hor';
                        break;
                                                        
                        case 3: // 180 rotate left
                        $oris[] = '180';
                        break;
                                            
                        case 4: // vertical flip
                        // $oris[] = 'ver';
                        break;
                                        
                        case 5: // vertical flip + 90 rotate right
                        // $oris[] = 'ver';
                        $oris[] = '270';
                        break;
                                        
                        case 6: // 90 rotate right
                        $oris[] = '270';
                        break;
                                        
                        case 7: // horizontal flip + 90 rotate right
                        // $oris[] = 'hor';
                        $oris[] = '270';
                        break;
                                        
                        case 8: // 90 rotate left
                        $oris[] = '90';
                        break;
                        
                        default: break;
                    }
                
                    foreach ($oris as $ori) {
                        $rotate_angle = $ori;
                    }
                }

                if (!empty($exif['Orientation']))
                { 
                    $msg = $image->withFile($target_dir . $file_name)
                            ->resize($width, $height, $maintain_ratio, 'width')
                            ->rotate($rotate_angle)
                            ->save($target_dir . $file_name);    
                }
                else
                {
                    $msg = $image->withFile($target_dir . $file_name)
                            ->resize($width, $height, $maintain_ratio, 'width')
                            ->save($target_dir . $file_name);   
                }            
            }
            $this->db->table('products')->insert($data);
            $product_id = $this->db->insertID();       

            $message = 'Product Created Successfully.';
            return $this->respond(['message' => $message], 200);
        }
    // }

    public function update_product($product_long_id)
    {
        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $query = $this->db->table('products')->where('long_id', $product_long_id)->get();
        $result = $query->getRow();
        if(!$result)
        {
            $message = 'Sorry! Product not found.';
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
            'min_stock_qty' => $json->min_stock_qty,
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

            $image = service('image');

            $maintain_ratio = TRUE;
            $width          = 700;
            $height = 700;
            $quality        = 80;

            $exif = @exif_read_data($target_dir.$file_name);

            if (!empty($exif['Orientation']))
            {                
                $oris = array();

                switch($exif['Orientation'])
                {
                    case 1: // no need to perform any changes
                    break;

                    case 2: // horizontal flip
                    // $oris[] = 'hor';
                    break;
                                                    
                    case 3: // 180 rotate left
                    $oris[] = '180';
                    break;
                                        
                    case 4: // vertical flip
                    // $oris[] = 'ver';
                    break;
                                    
                    case 5: // vertical flip + 90 rotate right
                    // $oris[] = 'ver';
                    $oris[] = '270';
                    break;
                                    
                    case 6: // 90 rotate right
                    $oris[] = '270';
                    break;
                                    
                    case 7: // horizontal flip + 90 rotate right
                    // $oris[] = 'hor';
                    $oris[] = '270';
                    break;
                                    
                    case 8: // 90 rotate left
                    $oris[] = '90';
                    break;
                    
                    default: break;
                }
            
                foreach ($oris as $ori) {
                    $rotate_angle = $ori;
                }
            }

            if (!empty($exif['Orientation']) && isset($rotate_angle))
            { 
                $msg = $image->withFile($target_dir . $file_name)
                        ->resize($width, $height, $maintain_ratio, 'width')
                        ->rotate($rotate_angle)
                        ->save($target_dir . $file_name);    
            }
            else
            {
                $msg = $image->withFile($target_dir . $file_name)
                        ->resize($width, $height, $maintain_ratio, 'width')
                        ->save($target_dir . $file_name);   
            }
        }

        $query = $this->db->table('products');
        $query->set($data);
        $query->where('id', $product_id);
        $query->update();
        
        $message = 'Product Updated Successfully.';
        return $this->respond(['message' => $message], 200);
    }

    public function delete_product($product_long_id)
    {     
        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $query = $this->db->table('products')->where('long_id', $product_long_id)->get();
        $result = $query->getRow();
        if(!$result)
        {
            $message = 'Sorry! Product not found.';
            return $this->fail($message, 400);
        }

        $product_id = $result->id;
        $product_image = $result->image;

        $query = $this->db->table('products');
        $query->where('id', $product_id);
        $query->delete();

        $target_dir = FCPATH.'/uploads/products/';
        if ($product_image != null)
        {
            unlink($target_dir.$product_image);
        }

        $message = 'Product Removed Successfully.';
        return $this->respond(['message' => $message], 200);
    }

    public function add_product_stock()
    {
        $post = $this->request->getPOST();

        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $product_long_id = $post['long_id'];         

        $query = $this->db->table('products')->where('long_id', $product_long_id)->get();
        $result = $query->getRow();
        if(!$result)
        {
            $message = 'Product not found.';
            return $this->fail($message, 400);
        }
        $product_id = $result->id;

        $check_product_stock_query = $this->db->table('product_stocks')->select('stock')->where('product_id', $product_id)->get();
        $check_product_stock = $check_product_stock_query->getResult();
        $total_stock = 0;
        foreach($check_product_stock as $product_stock)
        {
            $total_stock = $total_stock + $product_stock->stock;
        }
        $total_stock_last_time = $total_stock;

        $data = [
            'product_id' => $product_id,
            'document_id' => $post['documentId'],
            'stock' => $post['stock'],
            'original_stock' => $post['stock'],
            'total_stock_last_time' => $total_stock_last_time
        ];
        
        $this->db->table('product_stocks')->insert($data);

        $message = 'Product stock added successfully.';
        return $this->respond(['message' => $message], 200);
    }

    public function get_all_products()
    {
        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }
        
        $favorite_products = [];
        $min_stock_products = [];

        // $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        // $customer = $query->getRow();
        
        $query = $this->db->table('categories')->select('id, long_id, slug, name')->where('customer_id', $customer->id)->orderBy('name', 'ASC')->get();
        $categories = $query->getResult();

        foreach($categories as $category)
        {
            $product_query = $this->db->table('products')->select('id, long_id, name, image, price, min_stock_qty, description, is_favorite')->where('category_id', $category->id)->orderBy('name', 'ASC')->get();
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
                    $check_product_stock_query = $this->db->table('product_stocks')->select('stock')->where('product_id', $product->id)->get();
                    $check_product_stock = $check_product_stock_query->getResult();
                    $total_stock = 0;
                    foreach($check_product_stock as $product_stock)
                    {
                        $total_stock = $total_stock + $product_stock->stock;
                    }
                    $product->total_stock = $total_stock;
                    if($product->total_stock != 0 && $product->total_stock <= $product->min_stock_qty)
                    {
                        $min_stock_products[] = $product;
                    }
                }
                $category->products = $products;
                $category->product_count = count($products);
            }
        }
        if(!$categories)
        {
            $message = 'Sorry! No Products Found.';
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
                $favorite->slug = 'frequently-used';
                $favorite->products = $favorite_products;
                array_unshift($categories, $favorite);
            }
            if(count($min_stock_products) > 0)
            {
                $min_stocks_products = new \stdClass;
                $min_stocks_products->id = 'A1';
                $min_stocks_products->long_id = 'minstock123';
                $min_stocks_products->name = 'Min Stock Qty';
                $min_stocks_products->slug = 'min-stocks-qty';
                $min_stocks_products->products = $min_stock_products;
                array_unshift($categories, $min_stocks_products);
            }
            return $this->respond($categories, 200);
        }
    }

    public function export_products()
    {
        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $builder = $this->db->table("products");
        $builder->select('products.id, products.long_id, products.name, products.price, products.stock, products.description, categories.name as cat_name');
        $builder->join('categories', 'products.category_id = categories.id', "left"); 
        $builder->where('categories.customer_id', $customer->id); 
        $products = $builder->get()->getResult();

        $list = array();
        $row1 = array("Id", "Category", "Product Name", "Price", "Description", "Doc Id", "Stock", "Created At"); 

        array_push($list, $row1);

        foreach ($products as $product) {
            $product_arr = array();
            $product_arr[] = $product->id;
            $product_arr[] = $product->cat_name;
            $product_arr[] = $product->name;
            $product_arr[] = $product->price;
            $product_arr[] = $product->description;
            $product_arr[] = '';
            $product_arr[] = '';
            $product_arr[] = '';
            
            
            array_push($list, $product_arr);

            $product_stock_query = $this->db->table('product_stocks')->where(['product_id' => $product->id])->get();
            $product_stocks = $product_stock_query->getResult();
            
            foreach($product_stocks as $stock)
            {
                if($stock->stock > 0)
                {
                    $stock_arr = array();
                    $stock_arr[] = $product->id;
                    $stock_arr[] = $product->cat_name;
                    $stock_arr[] = $product->name;
                    $stock_arr[] = '';
                    $stock_arr[] = '';
                    $stock_arr[] = $stock->document_id;
                    $stock_arr[] = $stock->stock;
                    $stock_arr[] = date('d-M-Y', strtotime($stock->created_at));
    
                    array_push($list, $stock_arr);
               }
            }
        }
        return $this->respond($list, 200);
    }

    public function demo_export_products()
    {
        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $builder = $this->db->table("products");
        $builder->select('products.id, products.long_id, products.name, products.price, products.stock, products.description, categories.name as cat_name');
        $builder->join('categories', 'products.category_id = categories.id', "left"); 
        $builder->where('categories.customer_id', $customer->id); 
        $products = $builder->get()->getResult();

        $list = array();
        $row1 = array("Id", "Category", "Product Name", "Price", "Description"); 

        array_push($list, $row1);

        foreach ($products as $product) 
        {
            $product_arr = array();
            $product_arr[] = $product->long_id;
            $product_arr[] = $product->cat_name;
            $product_arr[] = $product->name;
            $product_arr[] = $product->price;
            $product_arr[] = $product->description;            
            
            array_push($list, $product_arr);
        }
        return $this->respond($list, 200);
    }

    public function import_products()
    {
        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
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
                if(!is_array($row))
                {
                    continue;
                }
                
                if(!isset($row[1]))
                {
                    $message = 'There is some issue with file. Some of products are not updated.';
                    return $this->fail($message, 400);
                    continue;
                }

                
                // continue;
                if($i == 1){
                    $i++;
                    continue;
                }
                
                $product_long_id = $row[0];  
                $category = $row[1];  
                $product_name = $row[2];  
                $product_price = $row[3];  
                $product_description = $row[4];  

                $find_category_query = $this->db->table('categories')->where('name', $category)->get();
                $category = $find_category_query->getRow();
                if(!$category)
                {
                    continue;
                }

                $find_product_query = $this->db->table('products')->where('long_id', $product_long_id)->get();
                $product = $find_product_query->getRow();

                // echo '<pre>'; print_r($category); echo '</pre>';die;
                if($product_long_id !== '')
                {
                    //update
                    $product_data = array(
                        'name' => $product_name,
                        'price' => $product_price,
                        'category_id' => $category->id,
                        'description' => $product_description,
                    );
                    $query = $this->db->table('products');
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
                        'category_id' => $category->id,
                        'description' => $product_description,
                    );

                    $this->db->table('products')->insert($product_data);
                    $product_id = $this->db->insertID();
                }
            }
            fclose($file);
            $message = 'Products Uploaded Successfully.';
            return $this->respond(['message' => $message], 200);
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

            $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
            $customer = $query->getRow();

            $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
            $customer_plan = $plan_query->getRow();

            if($customer_plan->expiry_date < date('Y-m-d'))
            {
                $message = 'Your account has been expired.';
                return $this->fail($message, 400);
            }

            $cart = $post->cart; 
            $note = $this->check_cart_qties($cart, $post->user_role);
            
            if($post->user_role == 3 || $post->user_role == 4)
            {
                if(!is_null($note)) 
                { $post->note .= ' | '.$note; }
                $this->temp_order($customer->id, $post);

                $message = 'Your Order has been proccessed.';
                return $this->respond(['message' => $message], 200);
            }

            $this->confirm_order($post);

            $message = 'Your Order has been successfully placed.';
            return $this->respond(['message' => $message], 200);
        // }
    }

    public function temp_order($customer_id, $order_data)
    {
        $temp_order_data = array(
            'customer_id' => $customer_id,
            'order_data' => json_encode($order_data));
        $this->db->table('temp_orders')->insert($temp_order_data);  
    }

    private function check_cart_qties($cart, $user_role = null)
    {
        $note = '';
        foreach($cart as $cart_item)
        {            
            $product_long_id = $cart_item->long_id;
            $product_quantity = $cart_item->quantity;
            $product_price = $cart_item->price;

            $check_product_data_query = $this->db->table('products')->select('id, name, price')->where('long_id', $product_long_id)->get();
            $check_product_data = $check_product_data_query->getRow();
            if(is_null($check_product_data))
            {
                $message = 'Sorry! No Product Found.';
                return $this->fail($message, 400);
            }

            $check_product_stock_query = $this->db->table('product_stocks')->select('id, stock')->where(['product_id' => $check_product_data->id], ['stock !==' => 0])->orderBy('id', 'ASC')->get();
            $check_product_stock = $check_product_stock_query->getResult();

            if($user_role !== '4')
            {
                if(is_null($check_product_stock))
                {
                    $message = 'Sorry! Product Out of Stock.';
                    return $this->fail($message, 400);
                }

                $total_stock = 0;
                foreach($check_product_stock as $product_stock)
                {
                    $total_stock = $total_stock + $product_stock->stock;
                }                

                if($product_quantity > $total_stock)
                {
                    $message = 'Sorry! Product Out of Stock.';
                    return $this->fail($message, 400);
                }
            }
            else
            {
                $total_stock = 0;
                foreach($check_product_stock as $product_stock)
                {
                    $total_stock = $total_stock + $product_stock->stock;
                }  

                if($product_quantity > $total_stock)
                {
                    $remaining_stock = $total_stock - $product_quantity;
                    $note .= ' | '.$check_product_data->name.' stock is '.$remaining_stock;
                }
            }
        }
        return $note;
    }

    public function confirm_order($post)
    {
        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $long_id = random_string('alnum', 12);
        $cart = $post->cart; 

        $this->check_cart_qties($cart, $post->user_role);

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
            'is_reserve' => $post->is_reserve,
            'note' => $post->note,
        ];       
        
        $this->db->table('orders')->insert($data);
        $order_id = $this->db->insertID();         

        $order_total = 0;    

        foreach($cart as $cart_item)
        {            
            $product_long_id = $cart_item->long_id;
            $product_quantity = $cart_item->quantity;
            $product_price = $cart_item->price;

            $check_product_data_query = $this->db->table('products')->select('id, name, price')->where('long_id', $product_long_id)->get();
            $check_product_data = $check_product_data_query->getRow();

            $check_product_stock_query = $this->db->table('product_stocks')->select('id, stock')->where(['product_id' => $check_product_data->id], ['stock !==' => 0])->orderBy('id', 'ASC')->get();
            $check_product_stock = $check_product_stock_query->getResult();

            $order_details = [
                'order_id' => $order_id,
                'product_id' => $check_product_data->id,
                'product_name' => $check_product_data->name,
                'product_price' => $product_price,
                'quantity' => $product_quantity
            ];
            //insert order details
            $order_details_insert = $this->db->table('order_details')->insert($order_details);

            $order_total = $order_total + ($product_price*$product_quantity);   

            foreach($check_product_stock as $product_stock)
            {
                if($product_quantity <= 0)
                    continue;

                if($product_stock->stock >= $product_quantity)
                {
                    // insert 
                    $remaining_stock = $product_stock->stock - $product_quantity; 
                    $product_quantity = 0;                         
                }
                else
                {
                    $product_quantity = $product_quantity - $product_stock->stock;     
                    $remaining_stock = 0;
                }

                $subtract_stock_data = array('stock' => $remaining_stock);
                $subtract_query = $this->db->table('product_stocks');
                $subtract_query->set($subtract_stock_data);
                $subtract_query->where('id', $product_stock->id);
                $subtract_query->update();            
            }                
        }
        $update_order_data = array('order_total' => $order_total);
        $order_query = $this->db->table('orders');
        $order_query->set($update_order_data);
        $order_query->where('id', $order_id);
        $order_query->update();       
    }

    public function edit_temp_order()
    {
        $post = $this->request->getJSON();                      

        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $temp_order_id = $post->id;

        $builder = $this->db->table("temp_orders");
        $builder->where('customer_id', $customer->id); // added left here
        $builder->where('id', $temp_order_id); // added left here
        $temp_order_data = $builder->get()->getRow();
        
        $order_data = json_decode($temp_order_data->order_data);
        $note = '';
        foreach($order_data->cart as $detail)
        {
            foreach($post->order_details as $k => $order_detail)
            {
                if($order_detail->id === $detail->id && $order_detail->quantity !== $detail->quantity) // check if product id and quantity
                {
                    if($order_detail->quantity == 0) // if product quantity is 0 then remove this product
                    {
                        unset($order_data->cart[$k]);
                    }
                    else
                    {
                        $detail->quantity = $order_detail->quantity; // update product quantity

                        $check_product_stock_query = $this->db->table('product_stocks')->select('id, stock')->where(['product_id' => $order_detail->id], ['stock !==' => 0])->orderBy('id', 'ASC')->get();
                        $check_product_stock = $check_product_stock_query->getResult();
                        
                        $total_stock = 0;
                        foreach($check_product_stock as $product_stock)
                        {
                            $total_stock = $total_stock + $product_stock->stock;
                        }  

                        if($detail->quantity > $total_stock)
                        {
                            $remaining_stock = $total_stock - $detail->quantity;
                            $note .= ' | update';
                            $note .= ' | '.$order_detail->name.' stock is '.$remaining_stock;
                        }
                    }
                }
            }
        }
        $order_data->note .= $note;
        array_multisort($order_data->cart, SORT_ASC);

        $update_temp_order_data = array('order_data' => json_encode($order_data));
        $temp_order_query = $this->db->table('temp_orders');
        $temp_order_query->set($update_temp_order_data);
        $temp_order_query->where('id', $temp_order_id);
        $temp_order_query->update();  
        
        $message = 'Order has been successfully updated.';
        return $this->respond(['message' => $message], 200);
    }

    public function edit_reserve_order()
    {
        $post = $this->request->getJSON();                      

        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        echo '<pre>'; print_r($post); echo '</pre>';die;

        $reserve_order_id = $post->id;

        $builder = $this->db->table("orders");// added left here
        $builder->where('order_id', $reserve_order_id); // added left here
        $reserve_order_data = $builder->get()->getRow();

        $builder = $this->db->table("order_details");// added left here
        $builder->where('order_id', $reserve_order_id); // added left here
        $reserve_order_details = $builder->get()->getResult();

        $note = $reserve_order_data->note == '' ? $reserve_order_data->note.' | ' : '';
        $order_total = $reserve_order_data->order_total;
        foreach($reserve_order_details as $detail)
        {
            foreach($post->order_details as $k => $order_detail)
            {
                if($order_detail->id === $detail->id && $order_detail->quantity !== $detail->quantity) // check if product id and quantity
                {
                    $old_qtys = $detail->quantity;

                    if($order_detail->quantity == 0) // if product quantity is 0 then remove this product
                    {
                        $order_total = $order_total - ($detail->product_price*$old_qtys);
                        
                        $query = $this->db->table('order_details');
                        $query->where('order_id', $reserve_order_id);
                        $query->where('product_id', $detail->product_id);
                        $query->delete();
                        // unset($reserve_order_details[$k]);
                    }
                    else
                    {
                        $detail->quantity = $order_detail->quantity; // update product quantity

                        $check_product_stock_query = $this->db->table('product_stocks')->select('id, stock')->where(['product_id' => $order_detail->id], ['stock !==' => 0])->orderBy('id', 'ASC')->get();
                        $check_product_stock = $check_product_stock_query->getResult();
                        
                        $total_stock = 0;
                        foreach($check_product_stock as $product_stock)
                        {
                            $total_stock = $total_stock + $product_stock->stock;
                        }  

                        if($detail->quantity > $old_qtys)
                        {
                            $remaining_stock = $total_stock - $detail->quantity;
                            $note .= ' | update';
                            $note .= ' | '.$order_detail->name.' stock is '.$remaining_stock;
                        }

                        if($detail->quantity > $total_stock)
                        {
                            $remaining_stock = $total_stock - $detail->quantity;
                            $note .= ' | update';
                            $note .= ' | '.$order_detail->name.' stock is '.$remaining_stock;
                        }
                    }
                }
            }
        }
        // $order_data->note .= $note;
        // array_multisort($order_data->cart, SORT_ASC);
    }

    public function get_products()
    {
        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $builder = $this->db->table("products")->orderBy('name', 'ASC');
        $builder->select('products.*, categories.name as cat_name');
        $builder->join('categories', 'products.category_id = categories.id', "left"); 
        $builder->where('categories.customer_id', $customer->id); 
        $products = $builder->get()->getResult();
        foreach($products as $product)
        {
            $check_product_stock_query = $this->db->table('product_stocks')->select('stock')->where('product_id', $product->id)->get();
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

        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
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
        if(isset($post->companyName))
        {
            $companyName = $post->companyName;
        }

        $builder = $this->db->table("orders")->orderBy('id', 'DESC');
        $builder->select('orders.*, ref.name as ref_name, orderedBy.name as order_by');
        $builder->join('users as ref', 'orders.reference = ref.id', "left"); // added left here
        $builder->join('users as orderedBy', 'orders.orderBy = orderedBy.id', "left"); // added left here
        $builder->where('orders.customer_id', $customer->id); // added left here
        if(isset($companyName) && $companyName != "")
        {
            $builder->like('orders.customer_company', $companyName, 'both');
        }
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
            $order_details_query_builder = $this->db->table("order_details")->select('*');
            $order_details_query_builder->where('order_id', $order->id);
            $order_details = $order_details_query_builder->get()->getResult();
            foreach($order_details as $detail)
            {
                $detail->image = $this->get_product_image_from_product_id($detail->product_id);
                $detail->category_name = $this->get_category_name_from_product_id($detail->product_id);
                $detail->product_description = $this->get_product_description_from_product_id($detail->product_id);
            }
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
    
    public function get_temp_orders()
    {
        $post = $this->request->getJSON();         

        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $builder = $this->db->table("temp_orders")->orderBy('id', 'DESC');
        $builder->where('temp_orders.customer_id', $customer->id); // added left here
        $orders = $builder->get()->getResult();
        $temp_orders = [];

        foreach($orders as $order)
        {
            $order_data = json_decode($order->order_data);

            $temp_order = new \stdClass;
            $temp_order->id = $order->id;
            $temp_order->is_confirm = $order->is_confirm;
            $temp_order->user_id = $order_data->user_id;
            $temp_order->customer_name = $order_data->customer_name;
            $temp_order->company_name = $order_data->company_name;
            $temp_order->customer_email = $order_data->customer_email;
            $temp_order->customer_phone = $order_data->customer_phone;            
            $temp_order->note = nl2br($order_data->note);

            $reference_builder = $this->db->table("users")->select('name');
            $reference_builder->where('id', $order_data->reference);
            $reference = $reference_builder->get()->getRow();
            if($reference)
                $temp_order->reference = $reference->name;
            else
                $temp_order->reference = '---';

            $orderby_builder = $this->db->table("users")->select('name');
            $orderby_builder->where('id', $order_data->user_id);
            $orderby = $orderby_builder->get()->getRow();

            $temp_order->orderby = $orderby->name;

            foreach($order_data->cart as $detail)
            {
                $detail->image = $this->get_product_image_from_product_id($detail->id);
                $detail->category_name = $this->get_category_name_from_product_id($detail->id);
                $detail->product_description = $this->get_product_description_from_product_id($detail->id);
            }
            $temp_order->order_details = $order_data->cart;
            $temp_order->order_at = date('d-M-y', strtotime($order->created_at));

            // array_unshift($temp_orders, $temp_order);
            $temp_orders[] = $temp_order;
        }     
        
        if(is_null($temp_orders))
        {
            $message = 'Sorry! No Order Found.';
            return $this->fail($message, 400);
        }
        else
        {
            return $this->respond($temp_orders, 200);
        }
    }

    public function confirm_temp_order($temp_order_id)
    {
        $builder = $this->db->table("temp_orders");
        $builder->where('id', $temp_order_id); // added left here
        $temp_order_data = $builder->get()->getRow();

        $order_data = json_decode($temp_order_data->order_data);

        $this->confirm_order($order_data);

        $data = array('is_confirm' => 1);

        $query = $this->db->table('temp_orders');
        $query->set($data);
        $query->where('id', $temp_order_id);
        $query->update();

        $message = 'Order has been Successfully Placed.';
        return $this->respond(['message' => $message], 200);
    }
    
    public function delete_temp_order($temp_order_id)
    {
        $query = $this->db->table('temp_orders');
        $query->where('id', $temp_order_id);
        $query->delete();

        $message = 'Order has been Successfully removed.';
        return $this->respond(['message' => $message], 200);
    }

    public function delete_order($order_long_id)
    {
        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $query = $this->db->table('orders')->where('long_id', $order_long_id)->get();
        $result = $query->getRow();
        if(!$result)
        {
            $message = 'Sorry! Order not found.';
            return $this->fail($message, 400);
        }

        $order_id = $result->id;

        $query = $this->db->table('orders');
        $query->where('id', $order_id);
        $query->delete();

        $query = $this->db->table('order_details');
        $query->where('order_id', $order_id);
        $query->delete();

        $message = 'Order Removed Successfully.';
        return $this->respond(['message' => $message], 200);
    }

    public function export_orders()
    {
        $post = $this->request->getJSON();

        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
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
        if(isset($post->companyName))
        {
            $companyName = $post->companyName;
        }

        $builder = $this->db->table("orders");
        $builder->select('orders.id, orders.long_id, orders.customer_name, orders.customer_company, orders.customer_email, orders.customer_phone, orders.order_total, orders.reference, orders.orderBy, orders.created_at, ref.name as ref_name, orderedBy.name as order_by');
        $builder->join('users as ref', 'orders.reference = ref.id', "left"); // added left here
        $builder->join('users as orderedBy', 'orders.orderBy = orderedBy.id', "left"); // added left here
        $builder->where('orders.customer_id', $customer->id);
        if(isset($companyName) && $companyName != "")
        {
            $builder->like('orders.customer_company', $companyName, 'both');
        }
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
        $row1 = array("Id", "Customer Name", "Customer Company", "Customer Email", "Customer Phone", "Order Total", "Reference", "Order By", "Ordered At", "Product Name", "Price", "Quantity"); 

        array_push($list, $row1);

        foreach ($orders as $order) {
            
            // array_push($list, $order_arr);
            
            $query = $this->db->table('order_details')->select('product_name, product_price, quantity')->where('order_id', $order->id)->get();
            $details = $query->getResult();
            
            foreach($details as $detail)
            {
                $order_arr = array();
                $order_arr[] = $order->id;
                $order_arr[] = $order->customer_name;
                $order_arr[] = $order->customer_company;
                $order_arr[] = $order->customer_email;
                $order_arr[] = $order->customer_phone;
                $order_arr[] = $order->order_total;
                $order_arr[] = $order->ref_name;
                $order_arr[] = $order->order_by;
                $order_arr[] = $order->created_at;
                // $order_arr[] = '';
                // $order_arr[] = '';
                // $order_arr[] = '';

                // $order_details = array();
                // $order_details[] = '';
                // $order_details[] = '';
                // $order_details[] = '';
                // $order_details[] = '';
                // $order_details[] = '';
                // $order_details[] = '';
                // $order_details[] = '';
                // $order_details[] = '';
                // $order_details[] = '';
                $order_arr[] = $detail->product_name;
                $order_arr[] = $detail->product_price;
                $order_arr[] = $detail->quantity;
                
                array_push($list, $order_arr);
            }
        }
        return $this->respond($list, 200);
    }

    public function get_customer_data($customer_long_id)
    {
		$query = $this->db->table('customers')->where('long_id', $customer_long_id)->get();
		$customer = $query->getRow();
		$plan_query = $this->db->table('customer_plans')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
		$customer_plan = $plan_query->getRow();
		$customer->expiry_date = $customer_plan->expiry_date;
		$customer->total_products = $customer_plan->total_products;

        return $this->respond($customer, 200);
    }  

    public function get_searched_products()
    {
        $post = $this->request->getJSON();
        $search = $post->search;

        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }
        
        $searched_products = [];

        $product_query = $this->db->table('categories')->select('products.id, products.long_id, products.name, products.image, products.price, products.description, products.min_stock_qty');
        $product_query->join('products', 'categories.id = products.category_id', "left"); // added left here
        $product_query->where('categories.customer_id', $customer->id);
        // $product_query = $this->db->table('products')->select('id, long_id, name, image, price, description');
        $product_query->like('products.name', $search);
        $product_query->orderBy('products.name', 'ASC');
        $products = $product_query->get()->getResult();
        // $products = $builder->get()->getResult();
        foreach($products as $product)
        {
            $check_product_stock_query = $this->db->table('product_stocks')->select('stock')->where('product_id', $product->id)->get();
            $check_product_stock = $check_product_stock_query->getResult();
            $total_stock = 0;
            foreach($check_product_stock as $product_stock)
            {
                $total_stock = $total_stock + $product_stock->stock;
            }
            $product->total_stock = $total_stock;
        }
        if(!$products)
        {
            $search_result = new \stdClass;
            $search_result->id = 0;
            $search_result->long_id = 'search1234';
            $search_result->name = 'No result found related to : '.$search;
            $search_result->products = $products;
            array_unshift($searched_products, $search_result);
            
            return $this->respond($searched_products, 200);
            // $message = 'Sorry! No Products Found.';
            // return $this->fail($message, 400);
        }
        else
        {
            foreach($products as $product)
            {
                $product->base_price = $product->price;
                $check_product_stock_query = $this->db->table('product_stocks')->select('stock')->where('product_id', $product->id)->get();
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

    public function print_order($order_long_id)
    {
        $query = $this->db->table('customers')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();
        if(!$customer)
        {
            $message = 'No Customer Data Found.';
            return $this->fail($message, 400);
        }

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }
        
        $builder = $this->db->table("orders");
        $builder->select('orders.*, ref.name as ref_name, orderedBy.name as order_by');
        $builder->join('users as ref', 'orders.reference = ref.id', "left"); // added left here
        $builder->join('users as orderedBy', 'orders.orderBy = orderedBy.id', "left"); // added left here
        $builder->where('orders.long_id', $order_long_id); // added left here
        $result = $builder->get()->getRow();
        if(!$result)
        {
            $message = 'Sorry! Order not found.';
            return $this->fail($message, 400);
        }

        $query = $this->db->table('order_details')->where('order_id', $result->id)->get();
        $order_details = $query->getResult();

        foreach($order_details as $detail)
        {
            $detail->image = $this->get_product_image_from_product_id($detail->product_id);
            $detail->category_name = $this->get_category_name_from_product_id($detail->product_id);
            $detail->product_description = $this->get_product_description_from_product_id($detail->product_id);
        }
        $result->order_details = $order_details;
        
        return view('pdf/template-students', ["order_data" => $result, 'customer_data' => $customer]);
    }

    public function print_temp_order($temp_order_id)
    {
        $query = $this->db->table('customers')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();
        if(!$customer)
        {
            $message = 'No Customer Data Found.';
            return $this->fail($message, 400);
        }

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $temp_order_query = $this->db->table('temp_orders')->where('id', $temp_order_id)->get();
        $temp_order_data = $temp_order_query->getRow();
        if(!$temp_order_data)
        {
            $message = 'No Order Found.';
            return $this->fail($message, 400);
        }

        $order_data = json_decode($temp_order_data->order_data);
        // echo '<pre>'; print_r($order_data); echo '</pre>';die;

        $temp_order = new \stdClass;
        $temp_order->id = $temp_order_data->id;
        $temp_order->user_id = $order_data->user_id;
        $temp_order->customer_name = $order_data->customer_name;
        $temp_order->company_name = $order_data->company_name;
        $temp_order->customer_email = $order_data->customer_email;
        $temp_order->customer_phone = $order_data->customer_phone;            
        $temp_order->note = $order_data->note;

        $reference_builder = $this->db->table("users")->select('name');
        $reference_builder->where('id', $order_data->reference);
        $reference = $reference_builder->get()->getRow();
        if($reference)
            $temp_order->reference = $reference->name;
        else
            $temp_order->reference = '---';

        $orderby_builder = $this->db->table("users")->select('name');
        $orderby_builder->where('id', $order_data->user_id);
        $orderby = $orderby_builder->get()->getRow();

        $temp_order->orderby = $orderby->name;

        foreach($order_data->cart as $detail)
        {
            $detail->image = $this->get_product_image_from_product_id($detail->id);
            $detail->category_name = $this->get_category_name_from_product_id($detail->id);
            $detail->product_description = $this->get_product_description_from_product_id($detail->id);
        }
        $temp_order->order_details = $order_data->cart;

        $temp_order->order_at = date('d-M-y', strtotime($temp_order_data->created_at));

        // echo '<pre>'; print_r($temp_order); echo '</pre>';die;
        
        return view('pdf/temp_order_details', ["order_data" => $temp_order, 'customer_data' => $customer]);
    }

    public function get_product_stocks_details($product_long_id)
    {
        $query = $this->db->table('customers')->select('id')->where('url_title', $this->subdomain)->get();
        $customer = $query->getRow();

        $plan_query = $this->db->table('customer_plans')->select('expiry_date')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
        $customer_plan = $plan_query->getRow();

        if($customer_plan->expiry_date < date('Y-m-d'))
        {
            $message = 'Your account has been expired.';
            return $this->fail($message, 400);
        }

        $query = $this->db->table('products')->where('long_id', $product_long_id)->get();
        $result = $query->getRow();
        if(!$result)
        {
            $message = 'Sorry! Product not found.';
            return $this->fail($message, 400);
        }

        $product_id = $result->id;

        $builder = $this->db->table("product_stocks");
        $builder->select('product_stocks.product_id, product_stocks.document_id, product_stocks.stock, product_stocks.original_stock, product_stocks.total_stock_last_time, product_stocks.created_at, products.min_stock_qty');
        $builder->join('products', 'product_stocks.product_id = products.id', "left");
        $builder->where('product_stocks.product_id', $product_id); 
        $builder->orderBy('product_stocks.created_at', 'DESC'); 
        $product_stocks = $builder->get()->getResult();

        return $this->respond($product_stocks, 200);
    }    

    private function get_category_name_from_product_id($product_id)
    {
        $category_builder = $this->db->table("products")->select('categories.name');
        $category_builder->join('categories', 'products.category_id = categories.id', "left"); // added left here
        $category_builder->where('products.id', $product_id);
        $category = $category_builder->get()->getRow();
        if($category)
            return $category->name;
        else
            return '';        
    }

    private function get_product_description_from_product_id($product_id)
    {
        $product_builder = $this->db->table("products")->select('description');
        $product_builder->where('products.id', $product_id);
        $product = $product_builder->get()->getRow();
        if($product)
            return $product->description;
        else
            return '';        
    }

    private function get_product_image_from_product_id($product_id)
    {
        $product_builder = $this->db->table("products")->select('image');
        $product_builder->where('products.id', $product_id);
        $product = $product_builder->get()->getRow();
        if($product)
            return $product->image;
        else
            return '';        
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