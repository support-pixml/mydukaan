<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\Files\UploadedFile;
use Exception;
use ReflectionException;

class AdminController extends Controller
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
        // $subdomain = explode(".", $_SERVER['SERVER_NAME']);        
    }

    public function index()
    {
        $session = \Config\Services::session();
        if($session->has('admin_user'))
        {
            return redirect()->route('dashboard');
        }
        else
            return view('admin/login');
    }

    public function signin()
    {
        $validation =  \Config\Services::validation();
        if(!$this->validate($validation->getRuleGroup('admin_signin')))
        {
            $session = \Config\Services::session();
            
            $session->setFlashdata('error_message', $validation->getErrors());
            return redirect()->route('cpanel-login');
        }
        else
        {
            $post = $this->request->getPOST();
            
            $session = \Config\Services::session();

            $data = [
                'username' => $post['username'],
                'password' => md5($post['password'])
            ];
            $db      = \Config\Database::connect();
            $query = $db->table('admin_users')->select('id, username')->where($data)->get();
            $user_data = $query->getRow();
            if(is_null($user_data))
            {
                $message = 'username or Password does not match';
                $session->setFlashdata('error_message', $message);
                return redirect()->route('cpanel-login');
            }
            else
            {
                $session->set('admin_user', $user_data);
                return redirect()->route('customers');
            }
        }
    }

    public function dashboard()
    {
        $session = \Config\Services::session();
        if(!$session->has('admin_user'))
        {
            return redirect()->route('cpanel-login');
        }
        else
        {
            return view('admin/dashboard');
        }
    }

    public function customers()
    {
        $session = \Config\Services::session();
        if(!$session->has('admin_user'))
        {
            return redirect()->route('cpanel-login');
        }
        else
        {
            $db      = \Config\Database::connect();
            $query = $db->table('customers')->select('id, long_id, company_name')->get();
            $customers = $query->getResult();

            return view('admin/customers', ["customers" => $customers]);
        }
    }

    public function save_customer()
    {
        // ini_set('memory_limit', '-1');
        $session = \Config\Services::session();
		if(!$session->has('admin_user')){return redirect()->route('cpanel-login');} 
        $db      = \Config\Database::connect();
		// $this->form_validation->set_rules('galleryName', 'Gallery Name', 'trim|required|min_length[5]');
   
		// if($this->input->post('gallery_long_id') == NULL)
		// {			
		// 	if (empty($_FILES['feature_image']['name']))
		// 	{
        //         $this->form_validation->set_rules('feature_image', 'Banner Image', 'required');
		// 	}
        // }
    
		// if($this->form_validation->run() == FALSE)
		// {
		// 	$response['status'] = 0;  
        //     $response['message'] = validation_errors(); 
        //     echo json_encode($response); 
        //     return;
		// }
        if(!$session->has('admin_user'))
        {

        }
		else
		{
            $post = $this->request->getPOST();
			if($post['customer_long_id'] == NULL) // new customer
			{
                $long_id = random_string('alnum', 12);
                $url_title = $post['url_title'];

                $query = $db->table('customers')->where('url_title', $url_title)->get();
                $result = $query->getRow();
                if($result != NULL)
                {                    
                    $message = 'URL Link is taken. Please change URl Link.';
                    return $this->fail($message, 400);
                }

                $customer_data = array(
                    'customer_name'       => $post['customer_name'],
                    'company_name'       => $post['company_name'],
                    'long_id'      => $long_id,
                    'url_title'     => $url_title,
                    'bank_name'       => $post['bank_name'],
                    'bank_ac_no'       => $post['bank_ac_no'],
                    'bank_ifsc_code'       => $post['bank_ifsc_code'],
                    'gst_no'       => $post['gst_no'],
                    'address'       => $post['address']
                );

                if(!empty($_FILES['logo']['name']))
                {
                    $logo_image = $this->request->getFile('logo');

                    $target_dir = FCPATH.'/uploads/customers/'; // Upload directory

                    $path                    = $_FILES['logo']['name'];
                    $extension               = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    $tmp_file_name           = random_string('alnum', 12);
                    $file_name               = $tmp_file_name . '.' . $extension;
                    $config['upload_path']   = $target_dir;
                    $config['allowed_types'] = 'jpg|jpeg|png';
                    $config['file_name']     = $file_name;     
                    $customer_data['logo']   = $file_name;     
                    
                    $logo_image->move($target_dir, $file_name); 
                }      

                $db->table('customers')->insert($customer_data);
                $customer_id = $db->insertID();  

                $user_data = array(
                    'long_id'       => random_string('alnum', 12),
                    'customer_id'       => $customer_id,
                    'name'       => $post['customer_name'],
                    'phone'      => $post['customer_phone'],
                    'password'     => md5($post['customer_phone']),
                    'role' => 1);

                $db->table('users')->insert($user_data);

                $message = 'Successfully Saved';
                return $this->respond(['message' => $message], 200);
			}
			else  // edit 
			{
                $customer_long_id = $post['customer_long_id'];
                
                $query = $db->table('customers')->where('long_id', $customer_long_id)->get();
                $result = $query->getRow();
                if($result == NULL)
                {                    
                    $message = 'Customer not found.';
                    return $this->fail($message, 400);
                }

                $customer_data = array(
                                    'customer_name' => $post['customer_name'],
                                    'company_name' => $post['company_name'],
                                    'bank_name'       => $post['bank_name'],
                                    'bank_ac_no'       => $post['bank_ac_no'],
                                    'bank_ifsc_code'       => $post['bank_ifsc_code'],
                                    'gst_no'       => $post['gst_no'],
                                    'address'       => $post['address']);

				if(!empty($_FILES['logo']['name']))
				{	
                    $logo_image = $this->request->getFile('logo');

                    $target_dir = FCPATH.'/uploads/customers/'; // Upload directory

                    $path                    = $_FILES['logo']['name'];
                    $extension               = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    $tmp_file_name           = random_string('alnum', 12);
                    $file_name               = $tmp_file_name . '.' . $extension;
                    $config['upload_path']   = $target_dir;
                    $config['allowed_types'] = 'jpg|jpeg|png';
                    $config['file_name']     = $file_name;  
                                    
                    $logo_image->move($target_dir, $file_name);     
                    $customer_data['logo'] = $file_name;
				}

                $query = $db->table('customers');
                $query->set($customer_data);
                $query->where('id', $result->id);
                $query->update();

                $customer_id = $result->id;

                $user_query = $db->table('users')->where('long_id', $post['customer_user_long_id'])->get();
                $user = $user_query->getRow();
                if($user == NULL)
                {                    
                    $message = 'User not found.';
                    return $this->fail($message, 400);
                }

                if(trim($post['password']) && strlen($post['password']) >= 8)
                {
                    $user_data = array('password' => md5($post['password']));

                    $user_query = $db->table('users');
                    $user_query->set($user_data);
                    $user_query->where('id', $user->id);
                    $user_query->update();
                }

                if($post['company']) // duplicate data
                {
                    $company_long_id = $post['company'];
                    $query = $db->table('customers')->select('id')->where('long_id', $company_long_id)->get();
                    $company_result = $query->getRow();

                    $company_id = $company_result->id;
                    
                    $category_query =  $db->table('categories')->where('customer_id', $company_id)->get();
                    $category_result = $category_query->getResult();

                    
                    foreach($category_result as $category)
                    {
                        $category_data = array(
                            'long_id'       => random_string('alnum', 12),
                            'customer_id'   => $customer_id,
                            'name'       => $category->name,
                            'slug'      => $category->slug);
                            
                            // $db->table('categories')->insert($category_data);
                            
                        $product_query =  $db->table('products')->where('category_id', $category->id)->get();
                        $product_result = $product_query->getResult();
    
                        $src_dir = FCPATH.'/uploads/products/';

                        foreach($product_result as $product)
                        {
    
                            $get_category_id_query = $db->table('categories')->select('id')->where('name', $category->name)->where('customer_id', $customer_id)->get();
                            $get_category_id_result = $get_category_id_query->getRow();

                            if($product->image != NULL)
                            {
                                $src_file = $src_dir.$product->image;
                                $extension = pathinfo($src_file, PATHINFO_EXTENSION);

                                $new_file = md5(random_string('alnum', 12)).'.'.$extension;

                                copy($src_file, $src_dir.$new_file);
                            }
                            else
                            {
                                $new_file = NULL;
                            }
    
                            $product_data = array(
                                'long_id'       => random_string('alnum', 12),
                                'name'          => $product->name,
                                'price'         => $product->price,
                                'stock'         => $product->stock,
                                'image'         => $new_file,
                                'category_id'   => $get_category_id_result->id,
                                'description'   => $product->description,
                                'is_favorite'   => $product->is_favorite);      
                                
                            echo '<pre>'; print_r($product_data); echo '</pre>';die;
            
                            $db->table('products')->insert($product_data);
                        }
                    }
                }

                $message = 'Successfully Updated.';
                return $this->respond(['message' => $message], 200);
			}
		}		
    } 
    
    function check_url_title()
	{
        $session = \Config\Services::session();
		if(!$session->has('admin_user')){return redirect()->route('cpanel-login');} 
        $db      = \Config\Database::connect();
        
		$post = $this->request->getPOST();

		$url_title = trim($post['url_title']);

        $query = $db->table('customers')->where('url_title', $url_title)->get();
        $result = $query->getRow();
        if($result != NULL)
        {                    
            $message = $url_title.' is already taken.';
            return $this->fail($message, 400);
        }
        else
        {
            return $this->respond(true, 200);
        }
	}

    public function get_customers()
    {
        $session = \Config\Services::session();
		if(!$session->has('admin_user')){return redirect()->route('cpanel-login');} 
        $db      = \Config\Database::connect();

        $query = $db->table('customers')->get();
        $customers = $query->getResult();

        $data = array();
		foreach($customers as $customer)
		{
			$row = array();		
			$row[] = $customer->company_name;
			$row[] = '<a href="http://'.$customer->url_title.'.d-store.co.in" target="_blank" class="text-primary">'.$customer->url_title.'.d-store.co.in</a>';
			$row[] = $customer->customer_name;

            $plan_query = $db->table('customer_plans')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
            $customer_plan = $plan_query->getRow();
            if($customer_plan != null)
            {
                $row[] = date('d-M-Y', strtotime($customer_plan->expiry_date));
            }
            else
            {
                $row[] = 'No Plan found.';
            }

			$row[] = date('d-M-Y H:i A', strtotime($customer->created_at));
			$row[] = '<button class="btn btn-link" data-toggle="modal" data-target="#AddCustomer" id="'.$customer->long_id.'" onClick="fetch_customer_data(this.id);"><i class="fa fa-edit"></i></button><a class="btn btn-link" href="/customer_plans/'.$customer->long_id.'"><i class="fa fa-credit-card"></i></a><button type="button" class="btn btn-link btn-xs" id="'.$customer->long_id.'" onClick="remove_customer(this.id);"><i class="fa fa-trash text-danger"></i></button>';
			$data[] = $row;
		}
		$output = array("data" => $data);
        echo json_encode($output);
    }

    public function get_customer_data()
    {
        $session = \Config\Services::session();
		if(!$session->has('admin_user')){return redirect()->route('cpanel-login');} 
        $db      = \Config\Database::connect();

        $post = $this->request->getPOST();

        $customer_long_id = $post['customer_long_id'];

        $query = $db->table('customers')->where('long_id', $customer_long_id)->get();
        $customer = $query->getRow();

        if($customer == NULL)
        {                    
            $message = 'Customer not found.';
            return $this->fail($message, 400);
        }

        $user_query = $db->table('users')->select('long_id, phone')->where('customer_id', $customer->id)->orderBy('created_at', 'asc')->get();
        $customer->user_data = $user_query->getRow();
        
        return $this->respond($customer, 200);
    }

    public function delete_customer()
    {
        $session = \Config\Services::session();
		if(!$session->has('admin_user')){return redirect()->route('cpanel-login');} 
        $db      = \Config\Database::connect();

        $post = $this->request->getPOST();

        $customer_long_id = $post['customer_long_id'];

        $customer_query = $db->table('customers')->where('long_id', $customer_long_id)->get();
        $customer = $customer_query->getRow();
        if($customer == NULL)
        {                    
            $message = 'Customer not found.';
            return $this->fail($message, 400);
        }

        // delete users
        $query = $db->table('users');
        $query->where('customer_id', $customer->id);
        $query->delete();

        // find categories
        $category_query = $db->table('categories')->where('customer_id', $customer->id)->get();
        $categories = $category_query->getResult();
        foreach($categories as $category)
        {
            // find products
            $product_query = $db->table('products')->where('category_id', $category->id)->get();
            $products = $product_query->getResult();
            foreach($products as $product)
            {
                // delete product_stocks
                $query = $db->table('product_stocks');
                $query->where('product_id', $product->id);
                $query->delete();
            }
             // delete products
            $query = $db->table('products');
            $query->where('category_id', $category->id);
            $query->delete();
        }

        $query = $db->table('categories');
        $query->where('customer_id', $customer->id);
        $query->delete();

        // find orders
        $order_query = $db->table('orders')->where('customer_id', $customer->id)->get();
        $orders = $order_query->getResult();
        foreach($orders as $order)
        {
             // delete order details
            $query = $db->table('order_details');
            $query->where('order_id', $order->id);
            $query->delete();
        }

        $query = $db->table('customers');
        $query->where('id', $customer->id);
        $query->delete();

        $target_dir = FCPATH.'/uploads/customers/';
        
        if($customer->logo != null)
        {
            unlink($target_dir.$customer->logo);
        }
        
        $message = 'Customer Removed.';
        return $this->respond($message, 200);
    }

    public function remove_customer_logo()
    {
        $session = \Config\Services::session();
		if(!$session->has('admin_user')){return redirect()->route('cpanel-login');} 
        $db      = \Config\Database::connect();

        $post = $this->request->getPOST();

        $customer_long_id = $post['customer_long_id'];

        $customer_query = $db->table('customers')->where('long_id', $customer_long_id)->get();
        $customer = $customer_query->getRow();
        if($customer == NULL)
        {                    
            $message = 'Customer not found.';
            return $this->fail($message, 400);
        }

        $customer_data = array('logo' => null);

        $query = $db->table('customers');
                $query->set($customer_data);
                $query->where('id', $customer->id);
                $query->update();
  
        $target_dir = FCPATH.'/uploads/customers/';
        
        if($customer->logo != null)
        {
            unlink($target_dir.$customer->logo);
        }
        
        $message = 'Logo Removed.';
        return $this->respond($message, 200);
    }

    public function customer_plans($customer_long_id)
    {
        $session = \Config\Services::session();
        if(!$session->has('admin_user'))
        {
            return redirect()->route('cpanel-login');
        }
        $db      = \Config\Database::connect();
        $query = $db->table('customers')->where('long_id', $customer_long_id)->get();
        $result = $query->getRow();
        if($result == NULL)
        {                    
            $message = 'Customer not found.';
            return $this->fail($message, 400);
        }        
        else
        {
            return view('admin/customer_plans');
        }
    }

    public function get_customer_plan($customer_long_id)
    {
        $session = \Config\Services::session();
		if(!$session->has('admin_user')){return redirect()->route('cpanel-login');} 
        $db  = \Config\Database::connect();
        
        $query = $db->table('customers')->where('long_id', $customer_long_id)->get();
        $customer = $query->getRow();
        if($customer == NULL)
        {                    
            $message = 'Customer not found.';
            return $this->fail($message, 400);
        }

        $query = $db->table('customer_plans')->where('customer_id', $customer->id)->orderBy('created_at', 'desc')->get();
        $plans = $query->getResult();

        $data = array();
		foreach($plans as $plan)
		{
			$row = array();		
			$row[] = $plan->total_products;
			$row[] = date('D, d-M-Y', strtotime($plan->expiry_date));
            if($plan->amount != null)
			    $row[] = '&#8377;'.$plan->amount;
            else
			    $row[] = 'No Amount';
			$row[] = date('d-M-Y H:i A', strtotime($plan->created_at));
            $row[] = '<button class="btn btn-link" data-toggle="modal" data-target="#AddCustomerPlan" id="'.$plan->id.'" onClick="fetch_plan_data(this.id);"><i class="fa fa-edit"></i></button><button type="button" class="btn btn-link btn-xs" id="'.$plan->id.'" onClick="delete_plan(this.id);"><i class="fa fa-trash text-danger"></i></button>';
	
			$data[] = $row;
		}
		$output = array("data" => $data);
        echo json_encode($output);
    }

    public function save_customer_plan($customer_long_id)
    {
        // ini_set('memory_limit', '-1');
        $session = \Config\Services::session();
		if(!$session->has('admin_user')){return redirect()->route('cpanel-login');} 
        $db      = \Config\Database::connect();
		// $this->form_validation->set_rules('galleryName', 'Gallery Name', 'trim|required|min_length[5]');
   
		// if($this->input->post('gallery_long_id') == NULL)
		// {			
		// 	if (empty($_FILES['feature_image']['name']))
		// 	{
        //         $this->form_validation->set_rules('feature_image', 'Banner Image', 'required');
		// 	}
        // }
    
		// if($this->form_validation->run() == FALSE)
		// {
		// 	$response['status'] = 0;  
        //     $response['message'] = validation_errors(); 
        //     echo json_encode($response); 
        //     return;
		// }
        if(!$session->has('admin_user'))
        {

        }
		else
		{
            $post = $this->request->getPOST();

            $query = $db->table('customers')->where('long_id', $customer_long_id)->get();
            $customer = $query->getRow();
            if($customer == NULL)
            {                    
                $message = 'Customer not found.';
                return $this->fail($message, 400);
            }                       
            
            if($post['plan_id'] == null)
            {
                $customer_plan_data = array(
                                    'customer_id'       => $customer->id,
                                    'total_products'       => $post['total_products'],
                                    'amount'       => $post['amount'],
                                    'expiry_date'       => $post['expiry_date']);

                $db->table('customer_plans')->insert($customer_plan_data);

                $message = 'Plan Successfully Saved';
                return $this->respond(['message' => $message], 200);
            }
            else
            {
                $customer_plan_data = array(
                    'customer_id'       => $customer->id,
                    'total_products'       => $post['total_products'],
                    'amount'       => $post['amount'],
                    'expiry_date'       => $post['expiry_date']);

                $query = $db->table('customer_plans');
                $query->set($customer_plan_data);
                $query->where('id', $post['plan_id']);
                $query->update();

                $message = 'Plan Successfully Updated';
                return $this->respond(['message' => $message], 200);
            }
		}		
    } 

    public function get_plan_data()
    {
        $session = \Config\Services::session();
		if(!$session->has('admin_user')){return redirect()->route('cpanel-login');} 
        $db      = \Config\Database::connect();

        $post = $this->request->getPOST();

        $plan_id = $post['plan_id'];

        $query = $db->table('customer_plans')->where('id', $plan_id)->get();
        $plan = $query->getRow();
        if($plan == NULL)
        {                    
            $message = 'Plan not found.';
            return $this->fail($message, 400);
        }
        
        return $this->respond($plan, 200);
    }

    public function delete_customer_plan()
    {
        $session = \Config\Services::session();
		if(!$session->has('admin_user')){return redirect()->route('cpanel-login');} 
        $db      = \Config\Database::connect();

        $post = $this->request->getPOST();

        $query = $db->table('customer_plans');
        $query->where('id', $post['plan_id']);
        $query->delete();

        $message = 'Plan Removed.';
        return $this->respond($message, 200);
    }

    public function logout()
    {
        $session = \Config\Services::session();
        $session->destroy();
        return redirect()->route('cpanel-login');
    }

    public function duplicate_image()
    {
        $from = 18;
        $to = 36;

        $db      = \Config\Database::connect();

        $cat_query = $db->table('categories')->where('customer_id', $from)->get();
        $categories = $cat_query->getResult();
        echo '<pre>'; print_r($categories); echo '</pre>';die;
        
        $src_dir = FCPATH.'/uploads/products/';

        foreach($categories as $category)
        {
            $prod_query = $db->table('products')->where('category_id', $category->id)->get();
            $products = $prod_query->getResult();

            foreach($products as $product)
            {
                $get_category_id_query = $db->table('categories')->select('id')->where('name', $category->name)->where('customer_id', $to)->get();
                $get_category_id_result = $get_category_id_query->getRow();

                $src_file = $src_dir.$product->image;
                $extension = pathinfo($src_file, PATHINFO_EXTENSION);

                $new_file = md5(random_string('alnum', 12)).'.'.$extension;

                copy($src_file, $src_dir.$new_file);

                $product_data = array('image' => $new_file);    
                    
                $product_query = $this->db->table('product');
                $temp_order_query->set($product_data);
                $temp_order_query->where('name', $product->name);
                $temp_order_query->where('category_id', $get_category_id_result->id);
                $temp_order_query->update(); 
            }
        }

    }

}
