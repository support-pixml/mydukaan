<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Mdl_admin');
        $this->load->model('Mdl_cpanel');
        $this->load->library('lib_common');
    }

    function index()
    {

        $data = array(
            'TITLE' => 'Login - Agarwal Agency'
        );

        if(trim($this->session->userdata('user_id')) != "")
            redirect('admin/dashboard');

        $this->load->view('login', $data);
    }

    function chk_login()
    {
        $mobile_number  = $this->input->post('mobile_number');
        $password       = $this->input->post('password');

        $user = $this->Mdl_admin->check_login($mobile_number, $password);

            
        if(!$user)
            redirect('/');

        $this->session->set_userdata(array(
            'user_id' => $user['id'],
            'long_user_id' => $user['long_id'],
            'mobile_number' => $user['mobile_number'],
            'admin_name' => $user['name'],
            'user_role' => $user['user_type'])
        );

        redirect('admin/dashboard');
    }

    function logout()
    {
        $this->session->sess_destroy();
        redirect('/');
    }

    function dashboard()
    {
        if(trim($this->session->userdata('user_id')) == "")
            redirect("/");

        if($this->session->userdata('user_role') == 2)
            $this->Mdl_admin->manager_id = $this->session->userdata('user_id');

        $today_orders       = $this->Mdl_admin->get_orders_amount_by_date(date('Y-m-d'), date('Y-m-d'));
        $today_diff         = $this->Mdl_admin->get_orders_amount_by_date(date('Y-m-d', strtotime(date('Y-m-d').' -1 Days')), date('Y-m-d', strtotime(date('Y-m-d').' -1 Days')));

        $last_week_orders   = $this->Mdl_admin->get_orders_amount_by_date(date('Y-m-d', strtotime('Monday this week')), date('Y-m-d'));
        $last_week_diff     = $this->Mdl_admin->get_orders_amount_by_date(date('Y-m-d', strtotime('Monday last week')), date('Y-m-d', strtotime('Sunday last week')));

        $last_month_orders  = $this->Mdl_admin->get_orders_amount_by_date(date('Y-m-d', strtotime('first day of this month')), date('Y-m-d'));
        $last_month_diff    = $this->Mdl_admin->get_orders_amount_by_date(date('Y-m-d', strtotime('first day of last month')), date('Y-m-d', strtotime('last day of last month')));



        $today_count       		= $this->Mdl_admin->get_order_count_by_date(date('Y-m-d'), date('Y-m-d'));
        $today_count_diff       = $this->Mdl_admin->get_order_count_by_date(date('Y-m-d', strtotime(date('Y-m-d').' -1 Days')), date('Y-m-d', strtotime(date('Y-m-d').' -1 Days')));

        $last_week_count   		= $this->Mdl_admin->get_order_count_by_date(date('Y-m-d', strtotime('Monday this week')), date('Y-m-d'));
        $last_week_count_diff   = $this->Mdl_admin->get_order_count_by_date(date('Y-m-d', strtotime('Monday last week')), date('Y-m-d', strtotime('Sunday last week')));

        $last_month_count  		= $this->Mdl_admin->get_order_count_by_date(date('Y-m-d', strtotime('first day of this month')), date('Y-m-d'));
        $last_month_count_diff  = $this->Mdl_admin->get_order_count_by_date(date('Y-m-d', strtotime('first day of last month')), date('Y-m-d', strtotime('last day of last month')));

        $data = array(
            'TITLE' => 'Login - Agarwal Agency',
        );

        $managers = $this->Mdl_admin->get_all_managers();
        $delivery_boys = $this->Mdl_admin->get_all_delivery_boys();
        $price_types = $this->Mdl_admin->get_all_price_types();

        $data = array(
            'TITLE' => 'Dashboard - Agarwal Agency',
            'HEADING' => 'Dashboard',
            'today_orders' => $today_orders,
            'last_week_orders' => $last_week_orders,
            'today_orders' => $today_orders,
            'last_month_orders' => $last_month_orders,
            'last_week_diff' => $last_week_diff,
            'today_diff' => $today_diff,
            'last_month_diff' => $last_month_diff,
            'today_count' => $today_count,
            'today_count_diff' => $today_count_diff,
            'last_week_count' => $last_week_count,
            'last_week_count_diff' => $last_week_count_diff,
            'last_week_diff' => $last_week_diff,
            'last_month_count' => $last_month_count,
            'last_month_count_diff' => $last_month_count_diff,
            'managers' => $managers,
            'delivery_boys' => $delivery_boys,
            'price_types' => $price_types
        );
        $this->load->view('dashboard', $data);
    }

    function categories()
    {
        if(trim($this->session->userdata('user_id')) == "")
            redirect("/");

        if($this->session->userdata('user_role') == 2)
            redirect("admin/dashboard");

        $categories = $this->Mdl_admin->get_root_level_categories();

        $data = array(
            'TITLE' => 'Product Categories - Agarwal Agency',
            'HEADING' => 'Product Categories',
            'categories' => $categories,
        );
        $this->load->view('categories', $data);
    }

    function sliders()
    {
        if(trim($this->session->userdata('user_id')) == "")
            redirect("/");

        if($this->session->userdata('user_role') == 2)
            redirect("admin/dashboard");

        $data = array(
            'TITLE' => 'Sliders - Agarwal Agency',
            'HEADING' => 'Sliders'
        );

        $this->load->view('sliders', $data);
    }

    function ajax_category_list()
    {
        $categories = $this->Mdl_admin->get_categories();

        $result = array();
        $i = 1;
        foreach($categories AS $category)
        {
            $row = array();
            $row[] = $i;
            $row[] = $category['name'];
            $cat_id = $category['id'];
            $items = $this->Mdl_admin->get_products_by_category($cat_id);
            $row[] = count($items);
            $row[] = '<a href="#" data-cat_id="'.$cat_id.'" class="edit-action"><i class="fa fa-pencil text-primary"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" data-cat_id="'.$cat_id.'" class="delete-action"><i class="fa fa-trash text-danger"></i></a>';

            $result[] = $row;
            $i++;
        }

        $data = array('data' => $result);

        header('Content-type: application/json');
        echo json_encode($data);
        exit;
    }

    function ajax_slider_list()
    {
        $sliders = $this->Mdl_admin->get_sliders();

        $result = array();
        $i = 1;
        foreach($sliders AS $slider)
        {
            $row = array();
            $row[] = $i;
            $row[] = '<img src="'.base_url().'assets/uploads/'.$slider['banner_image'].'" class="img-fluid rounded" width="150" />';
            $row[] = $slider['web_link'];
            $row[] = '<a href="#" data-slider_id="'.$slider['id'].'" class="edit-action"><i class="fa fa-pencil text-primary"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" data-slider_id="'.$slider['id'].'" class="delete-action"><i class="fa fa-trash text-danger"></i></a>';

            $result[] = $row;
            $i++;
        }

        $data = array('data' => $result);

        header('Content-type: application/json');
        echo json_encode($data);
        exit;
    }

    function ajax_get_category_detail()
    {
        $cat_id = trim($this->input->post('cat_id'));
        $category = $this->Mdl_admin->get_data_by_id('categories', $cat_id);

        unset($category['sort_order']);

        header('Content-type: application/json');
        echo json_encode($category);
        exit;
    }

    function ajax_get_slider_detail()
    {
        $slider_id = trim($this->input->post('slider_id'));
        $slider = $this->Mdl_admin->get_data_by_id('sliders', $slider_id);

        header('Content-type: application/json');
        echo json_encode($slider);
        exit;
    }

    function ajax_save_category()
    {
        $category_name = trim($this->input->post('category_name'));
        $cat_id = trim($this->input->post('cat_id'));
        $parent_id = trim($this->input->post('parent_id'));

        if($category_name != "")
        {
            if($cat_id == "")
            {
                $insert_data = array(
                    'name' => $category_name,
                    'parent_id' => $parent_id
                );
                $this->Mdl_admin->insert_entry('categories', $insert_data);
            }
            else{
                $update_data = array(
                    'name' => $category_name,
                    'parent_id' => $parent_id
                );
                
                $this->Mdl_admin->update_entry('categories', $cat_id, $update_data);
            }
            echo TRUE;
        }
        else
        {
            echo FALSE;
        }
    }

    function ajax_save_slider()
    {
        $web_link = trim($this->input->post('web_link'));
        $slider_id = trim($this->input->post('slider_id'));

        $target_dir = "./assets/uploads/";
        $banner_image = "";

        if ($_FILES['banner_image']['name'] != NULL) {
            $path                     = $_FILES['banner_image']['name'];
            $extension                = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $file_name               = $this->lib_common->rand_str() . '.' . $extension;
            $config['upload_path']   = $target_dir;
            $config['allowed_types'] = 'jpeg|jpg|png';
            $config['file_name']     = $file_name;
            $config['max_size']      = 1024000;
            $config['overwrite']     = FALSE;

        }


        if ($_FILES['banner_image']['name'] != NULL) {

            $this->upload->initialize($config);
            if ($this->upload->do_upload('banner_image', $config)) {
                $banner_image = $file_name;
            }
            else
            {
                echo FALSE;
                exit;
            }
        }

        if($slider_id == "")
        {
            $insert_data = array(
                'web_link' => $web_link,
            );

            if(trim($banner_image) != "")
                $insert_data['banner_image'] = $banner_image;

            $this->Mdl_admin->insert_entry('sliders', $insert_data);
        }
        else{
            $update_data = array(
                'web_link' => $web_link,
            );

            if(trim($banner_image) != "")
                $update_data['banner_image'] = $banner_image;
            
            
            $this->Mdl_admin->update_entry('sliders', $slider_id, $update_data);
        }
        echo TRUE;
       
    }

    function ajax_save_product()
    {
        $product_name = trim($this->input->post('product_name'));
        $tax_percent = trim($this->input->post('tax_percent'));
        $long_product_id = trim($this->input->post('product_id'));
        $category_id = trim($this->input->post('category_id'));
        $product_id = $this->Mdl_admin->get_short_id('products', $long_product_id);


        if($product_name != "")
        {
            if($product_id == "")
            {
                $insert_data = array(
                    'long_id' => $this->lib_common->rand_str(),
                    'name' => $product_name,
                    'tax_percent' => $tax_percent,
                    'category_id' => $category_id
                );
                $this->Mdl_admin->insert_entry('products', $insert_data);
            }
            else{

                
                $update_data = array(
                    'name' => $product_name,
                    'tax_percent' => $tax_percent,
                    'category_id' => $category_id
                );
                
                $this->Mdl_admin->update_entry('products', $product_id, $update_data);
            }
            echo TRUE;
        }
        else
        {
            echo FALSE;
        }
    }

    function ajax_save_manager()
    {
        $manager_name       = trim($this->input->post('manager_name'));
        $mobile_number      = trim($this->input->post('mobile_number'));
        $password           = trim($this->input->post('password'));
        $office_address     = trim($this->input->post('office_address'));
        $bank_account_id    = trim($this->input->post('bank_account_id'));
        $is_active          = $this->input->post('is_active');

        $long_manager_id = trim($this->input->post('manager_id'));
        $manager_id = $this->Mdl_admin->get_short_id('users', $long_manager_id);


        if($manager_name != "" OR $mobile_number != "")
        {
            if($manager_id == "")
            {
                $insert_data = array(
                    'long_id' => $this->lib_common->rand_str(),
                    'name' => $manager_name,
                    'mobile_number' => $mobile_number,
                    'user_type' => 2,
                    'price_type' => 0,
                    'parent_id' => 1,
                    'is_active' => $is_active,
                    'created_by' => 1,
                    'created_at' => date("Y-m-d H:i:s")
                );

                if($password != "")
                    $insert_data['password'] = md5($password);

                $user_id = $this->Mdl_admin->insert_entry('users', $insert_data);

                $insert_data = array(
                    'user_id' => $user_id,
                    'address' => $office_address,
                    'account_id' => $bank_account_id,
                    'is_active' => $is_active
                );

                $this->Mdl_admin->insert_entry('manager_details', $insert_data);
            }
            else{

                
                $update_data = array(
                    'name' => $manager_name,
                    'mobile_number' => $mobile_number
                );

                if($password != "")
                    $update_data['password'] = md5($password);
                
                $this->Mdl_admin->update_entry('users', $manager_id, $update_data);

                $update_data = array(
                    'address' => $office_address,
                    'account_id' => $bank_account_id
                );

                $this->Mdl_admin->update_manager_detail($manager_id, $update_data);

            }
            echo TRUE;
        }
        else
        {
            echo FALSE;
        }
    }

    function ajax_save_delivery_boy()
    {
        $delivery_boy_name  = trim($this->input->post('delivery_boy_name'));
        $mobile_number      = trim($this->input->post('mobile_number'));
        $password           = trim($this->input->post('password'));
        $long_manager_id    = trim($this->input->post('manager_id'));
        $is_active          = $this->input->post('is_active');

        $long_delivery_boy_id = trim($this->input->post('delivery_boy_id'));
        $delivery_boy_id = $this->Mdl_admin->get_short_id('users', $long_delivery_boy_id);
        $manager_id = $this->Mdl_admin->get_short_id('users', $long_manager_id);


        if($delivery_boy_name != "" OR $mobile_number != "")
        {
            if($delivery_boy_id == "")
            {
                $insert_data = array(
                    'long_id' => $this->lib_common->rand_str(),
                    'name' => $delivery_boy_name,
                    'mobile_number' => $mobile_number,
                    'user_type' => 3,
                    'price_type' => 0,
                    'parent_id' => $manager_id,
                    'is_active' => $is_active,
                    'created_by' => 1,
                    'created_at' => date("Y-m-d H:i:s")
                );

                if($password != "")
                    $insert_data['password'] = md5($password);

                $user_id = $this->Mdl_admin->insert_entry('users', $insert_data);
            }
            else{

                
                $update_data = array(
                    'name' => $delivery_boy_name,
                    'mobile_number' => $mobile_number,
                    'parent_id' => $manager_id,
                    'is_active' => $is_active
                );

                if($password != "")
                    $update_data['password'] = md5($password);
                
                $this->Mdl_admin->update_entry('users', $delivery_boy_id, $update_data);
            }
            echo TRUE;
        }
        else
        {
            echo FALSE;
        }
    }

    function ajax_save_customer()
    {
        $customer_name       = trim($this->input->post('customer_name'));
        $mobile_number      = trim($this->input->post('mobile_number'));
        $password           = trim($this->input->post('password'));
        $long_manager_id    = trim($this->input->post('manager_id'));
        $price_type    = trim($this->input->post('price_type'));
        $order_interval    = trim($this->input->post('order_interval'));
        $long_customer_id = trim($this->input->post('customer_id'));
        $long_delivery_boy_id = trim($this->input->post('delivery_boy_id'));
        $is_active = $this->input->post('is_active');

        $customer_id = $this->Mdl_admin->get_short_id('users', $long_customer_id);
        $delivery_boy_id = $this->Mdl_admin->get_short_id('users', $long_delivery_boy_id);
        $manager_id = $this->Mdl_admin->get_short_id('users', $long_manager_id);


        if($customer_name != "" OR $mobile_number != "")
        {
            if($customer_id == "")
            {
                $insert_data = array(
                    'long_id' => $this->lib_common->rand_str(),
                    'name' => $customer_name,
                    'mobile_number' => $mobile_number,
                    'user_type' => 4,
                    'price_type' => $price_type,
                    'parent_id' => $manager_id,
                    'delivery_boy_id' => $delivery_boy_id,
                    'order_interval' => $order_interval,
                    'is_active' => $is_active,
                    'created_by' => 1,
                    'created_at' => date("Y-m-d H:i:s")
                );

                if($password != "")
                    $insert_data['password'] = md5($password);

                $user_id = $this->Mdl_admin->insert_entry('users', $insert_data);

                $message = "Thank You for choosing Agarwal Agency\n\nDownload Our App http://bit.ly/agagency \n\nEnter Your Mobile Number to login.";

                $this->lib_common->send_sms($user_id, $message);
            }
            else{

                
                $update_data = array(
                    'name' => $customer_name,
                    'mobile_number' => $mobile_number,
                    'parent_id' => $manager_id,
                    'price_type' => $price_type,
                    'delivery_boy_id' => $delivery_boy_id,
                    'order_interval' => $order_interval,
                    'is_active' => $is_active,
                );

                if($password != "")
                    $update_data['password'] = md5($password);
                
                $this->Mdl_admin->update_entry('users', $customer_id, $update_data);
            }
            echo TRUE;
        }
        else
        {
            echo FALSE;
        }
    }

    function ajax_get_product_detail()
    {
        $long_product_id = trim($this->input->post('product_id'));
        $product_id = $this->Mdl_admin->get_short_id('products', $long_product_id);
        $product = $this->Mdl_admin->get_data_by_id('products', $product_id);

        unset($product['sort_order']);

        header('Content-type: application/json');
        echo json_encode($product);
        exit;
    }

    function ajax_get_delivery_boy_detail()
    {
        $long_delivery_boy_id = trim($this->input->post('delivery_boy_id'));
        $delivery_boy_id = $this->Mdl_admin->get_short_id('users', $long_delivery_boy_id);
        $user_info = $this->Mdl_admin->get_data_by_id('users', $delivery_boy_id);

        $user_info['delivery_boy_name'] = $user_info['name'];
        $user_info['delivery_boy_id'] = $user_info['long_id'];

        $manager_id = $user_info['parent_id'];
        $parent_user_info = $this->Mdl_admin->get_data_by_id('users', $manager_id);

        $user_info['manager_id'] = $parent_user_info['long_id'];

        unset($user_info['id']);
        unset($user_info['long_id']);
        unset($user_info['name']);
        unset($user_info['password']);
        unset($user_info['user_type']);
        unset($user_info['price_type']);
        unset($user_info['verify_code']);
        unset($user_info['created_by']);
        unset($user_info['created_at']);

        header('Content-type: application/json');
        echo json_encode($user_info);
        exit;
    }

    function ajax_get_customer_detail()
    {
        $long_customer_id = trim($this->input->post('customer_id'));
        $customer_id = $this->Mdl_admin->get_short_id('users', $long_customer_id);

        $user_info = $this->Mdl_admin->get_data_by_id('users', $customer_id);

        $user_info['customer_name'] = $user_info['name'];
        $user_info['customer_id'] = $user_info['long_id'];

        $manager_id = $user_info['parent_id'];
        $delivery_boy_id = $user_info['delivery_boy_id'];
        $parent_user_info = $this->Mdl_admin->get_data_by_id('users', $manager_id);
        $delivery_boy_info = $this->Mdl_admin->get_data_by_id('users', $delivery_boy_id);

        $user_info['manager_id'] = $parent_user_info['long_id'];
        $user_info['delivery_boy_id'] = $delivery_boy_info['long_id'];

        $user_info['addresses'] = $this->Mdl_cpanel->get_addresses($customer_id);

        unset($user_info['id']);
        unset($user_info['long_id']);
        unset($user_info['name']);
        unset($user_info['password']);
        unset($user_info['user_type']);
        unset($user_info['verify_code']);
        unset($user_info['created_by']);
        unset($user_info['created_at']);

        header('Content-type: application/json');
        echo json_encode($user_info);
        exit;
    }

    function ajax_get_manager_detail()
    {
        $long_manager_id = trim($this->input->post('manager_id'));
        $manager_id = $this->Mdl_admin->get_short_id('users', $long_manager_id);
        $user_info = $this->Mdl_admin->get_data_by_id('users', $manager_id);
        $manager_info = $this->Mdl_admin->get_manager_detail($manager_id);
        $user_info['office_address'] = $manager_info['address'];
        $user_info['bank_account_id'] = $manager_info['account_id'];
        $user_info['manager_name'] = $user_info['name'];
        $user_info['manager_id'] = $user_info['long_id'];

        unset($user_info['id']);
        unset($user_info['long_id']);
        unset($user_info['name']);
        unset($user_info['password']);
        unset($user_info['user_type']);
        unset($user_info['price_type']);
        unset($user_info['verify_code']);
        unset($user_info['parent_id']);
        unset($user_info['created_by']);
        unset($user_info['created_at']);

        header('Content-type: application/json');
        echo json_encode($user_info);
        exit;
    }

    function ajax_delete_category()
    {
        $cat_id = trim($this->input->post('cat_id'));

        if($cat_id != "")
        {
            $this->Mdl_admin->delete_category($cat_id);
            echo TRUE;
        }
        else
        {
            echo FALSE;
        }
    }

    function ajax_delete_slider()
    {
        $slider_id = trim($this->input->post('slider_id'));

        if($slider_id != "")
        {
            $this->Mdl_admin->delete_slider($slider_id);
            echo TRUE;
        }
        else
        {
            echo FALSE;
        }
    }


    function ajax_delete_product()
    {
        $long_product_id = trim($this->input->post('product_id'));
        $product_id = $this->Mdl_admin->get_short_id('products', $long_product_id);

        if($product_id != "")
        {
            $this->Mdl_admin->delete_product($product_id);
            echo TRUE;
        }
        else
        {
            echo FALSE;
        }
    }
    
    function ajax_delete_manager()
    {
        $long_manager_id = trim($this->input->post('manager_id'));
        $manager_id = $this->Mdl_admin->get_short_id('users', $long_manager_id);

        if($manager_id != "")
        {
            $this->Mdl_admin->delete_manager($manager_id);
            echo TRUE;
        }
        else
        {
            echo FALSE;
        }
    }

    function ajax_delete_delivery_boy()
    {
        $long_delivery_boy_id = trim($this->input->post('delivery_boy_id'));
        $delivery_boy_id = $this->Mdl_admin->get_short_id('users', $long_delivery_boy_id);

        if($delivery_boy_id != "")
        {
            $this->Mdl_admin->delete_delivery_boy($delivery_boy_id);
            echo TRUE;
        }
        else
        {
            echo FALSE;
        }
    }

    function ajax_delete_customer()
    {
        $long_customer_id = trim($this->input->post('customer_id'));
        $customer_id = $this->Mdl_admin->get_short_id('users', $long_customer_id);

        if($customer_id != "")
        {
            $this->Mdl_admin->delete_customer($customer_id);
            echo TRUE;
        }
        else
        {
            echo FALSE;
        }
    }

    function products()
    {
        if(trim($this->session->userdata('user_id')) == "")
            redirect("/");

        if($this->session->userdata('user_role') == 2)
            redirect("admin/dashboard");

        $categories = $this->Mdl_admin->get_categories();

        $cat_array = array();
        foreach($categories AS $category)
        {
            if(!$this->Mdl_admin->has_child($category['id']))
                $cat_array[] = $category;
        }
        $data = array(
            'TITLE' => 'Products - Agarwal Agency',
            'categories' => $cat_array,
            'HEADING' => 'Products'
        );
        $this->load->view('products', $data);
    }

    function ajax_product_list()
    {
        $products = $this->Mdl_admin->get_all_products();

        $result = array();
        $i = 1;
        foreach($products AS $product)
        {
            $category = $this->Mdl_admin->get_data_by_id('categories', $product['category_id']);
            $product_id = $product['long_id'];
            $row = array();
            $row[] = $i;
            $row[] = $product['name'];
            $row[] = $category['name'];
            $row[] = $product['tax_percent'].' %';
            $row[] = '<a href="#" data-product_id="'.$product_id.'" class="edit-action"><i class="fa fa-pencil text-primary"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" data-product_id="'.$product_id.'" class="delete-action"><i class="fa fa-trash text-danger"></i></a>';
            $result[] = $row;
            $i++;
        }

        $data = array('data' => $result);

        header('Content-type: application/json');
        echo json_encode($data);
        exit;
    }

    function managers()
    {
        if(trim($this->session->userdata('user_id')) == "")
            redirect("/");

        if($this->session->userdata('user_role') == 2)
            redirect("admin/dashboard");

        $bank_accounts = $this->Mdl_admin->get_all_bank_accounts();

        $data = array(
            'TITLE' => 'Managers - Agarwal Agency',
            'bank_accounts' => $bank_accounts,
            'HEADING' => 'Managers'
        );
        $this->load->view('managers', $data);
    }

    function ajax_manager_list()
    {
        $managers = $this->Mdl_admin->get_all_managers();

        $result = array();
        $i = 1;
        foreach($managers AS $manager)
        {
            $long_manager_id = $manager['long_id'];
            $row = array();
            $row[] = $manager['id'];
            $row[] = $manager['name'];
            $row[] = $manager['mobile_number'];
            $row[] = $manager['is_active'] == 0 ? 'Inactive' : 'Active';
            $row[] = '<a href="#" data-manager_id="'.$long_manager_id.'" class="edit-action"><i class="fa fa-pencil text-primary"></i></a>';//&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" data-manager_id="'.$long_manager_id.'" class="delete-action"><i class="fa fa-trash text-danger"></i></a>';
            $result[] = $row;
            $i++;
        }

        $data = array('data' => $result);

        header('Content-type: application/json');
        echo json_encode($data);
        exit;
    }

    function delivery_boys()
    {
        if(trim($this->session->userdata('user_id')) == "")
            redirect("/");

        $managers = $this->Mdl_admin->get_all_managers();

        $data = array(
            'TITLE' => 'Delivery Boys - Agarwal Agency',
            'managers' => $managers,
            'HEADING' => 'Delivery Boys'
        );
        $this->load->view('delivery_boys', $data);
    }

    function ajax_delivery_boys_list()
    {
        if($this->session->userdata('user_role') == 2)
            $this->Mdl_admin->manager_id = $this->session->userdata('user_id');

        $delivery_boys = $this->Mdl_admin->get_all_delivery_boys();

        $result = array();
        $i = 1;
        foreach($delivery_boys AS $delivery_boy)
        {
            $delivery_boy_id = $delivery_boy['long_id'];
            $row = array();
            $row[] = $delivery_boy['id'];
            $row[] = $delivery_boy['name'];
            $row[] = $delivery_boy['mobile_number'];
            $row[] = $delivery_boy['is_active'] == 0 ? 'Inactive' : 'Active';
            $row[] = '<a href="#" data-delivery_boy_id="'.$delivery_boy_id.'" class="edit-action"><i class="fa fa-pencil text-primary"></i></a>';//&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" data-delivery_boy_id="'.$delivery_boy_id.'" class="delete-action"><i class="fa fa-trash text-danger"></i></a>';
            $result[] = $row;
            $i++;
        }

        $data = array('data' => $result);

        header('Content-type: application/json');
        echo json_encode($data);
        exit;
    }

    function customers()
    {
        if(trim($this->session->userdata('user_id')) == "")
            redirect("/");

        $managers = $this->Mdl_admin->get_all_managers();
        $delivery_boys = $this->Mdl_admin->get_all_delivery_boys();
        $price_types = $this->Mdl_admin->get_all_price_types();

        $data = array(
            'TITLE' => 'Customers - Agarwal Agency',
            'managers' => $managers,
            'delivery_boys' => $delivery_boys,
            'HEADING' => 'Customers',
            'price_types' => $price_types
        );
        $this->load->view('customers', $data);
    }

    function performance()
    {
        if($this->session->userdata('user_role') != 1)
            redirect('/');

        $data = array(
            'TITLE' => 'Performance - Agarwal Agency',
            'HEADING' => 'Performance',
        );
        $this->load->view('performance', $data);
    }


    function statistics()
    {
        if($this->session->userdata('user_role') != 1)
            redirect('/');

        $data = array(
            'TITLE' => 'Statistics - Agarwal Agency',
            'HEADING' => 'Statistics',
        );
        $this->load->view('statistics', $data);
    }



    function ajax_expected_orders_list()
    {
        if($this->session->userdata('user_role') == 2)
            $this->Mdl_admin->manager_id = $this->session->userdata('user_id');

        $customers = $this->Mdl_admin->get_all_customers(true);

        $start_date = $this->input->post('start_date');

        $result = array();
        $i = 1;
        foreach($customers AS $customer)
        {
            $long_customer_id = $customer['long_id'];
            $customer_id = $customer['id'];
            $order_interval = $customer['order_interval'];
            $date = date("Y-m-d", strtotime($start_date.' -'.$order_interval.' Days'));
            $order = $this->Mdl_admin->check_last_order($customer_id);

            //foreach($orders AS $order)
            //{

            if($order['order_date'] >= $date.' 00:00:00' AND $order['order_date'] <= $date.' 23:59:59')
            {

                $row = array();
                $row[] = $i;
                $row[] = $customer['name'];
                $row[] = $customer['mobile_number'];
                $row[] = '₹ '.$order['total_amount'];
                $row[] = date('j M y, h:i A', strtotime($order['order_date']));
                $result[] = $row;
                $i++;
            }
            //}
        }

        $data = array('data' => $result);

        header('Content-type: application/json');
        echo json_encode($data);
        exit;
    }

    /*
    function download_expected_orders()
    {
        if($this->session->userdata('user_role') == 2)
            $this->Mdl_admin->manager_id = $this->session->userdata('user_id');

        $customers = $this->Mdl_admin->get_all_customers();

        $start_date = $this->input->post('start_date');

        $result = array();
        $i = 1;
        foreach($customers AS $customer)
        {
            $long_customer_id = $customer['long_id'];
            $customer_id = $customer['id'];
            $order_interval = $customer['order_interval'];
            $date = date("Y-m-d", strtotime($start_date.' -'.$order_interval.' Days'));
            $order = $this->Mdl_admin->check_last_order($customer_id);

            //foreach($orders AS $order)
            //{

            if($order['order_date'] >= $date.' 00:00:00' AND $order['order_date'] <= $date.' 23:59:59')
            {

                $row = array();
                $row[] = $i;
                $row[] = $customer['name'];
                $row[] = $customer['mobile_number'];
                $row[] = '₹ '.$order['total_amount'];
                $row[] = date('j M y, h:i A', strtotime($order['order_date']));
                $result[] = $row;
                $i++;
            }
            //}
        }

        $data = array('data' => $result);

        header('Content-type: application/json');
        echo json_encode($data);
        exit;
    }
    /**/

    function ajax_delayed_orders_list()
    {
        if($this->session->userdata('user_role') == 2)
            $this->Mdl_admin->manager_id = $this->session->userdata('user_id');

        $customers = $this->Mdl_admin->get_all_customers(true);

        $start_date = date('Y-m-d');

        $result = array();
        $i = 1;
        foreach($customers AS $customer)
        {
            $long_customer_id = $customer['long_id'];
            $customer_id = $customer['id'];
            $order_interval = $customer['order_interval'];
            $date = date("Y-m-d", strtotime($start_date.' -'.$order_interval.' Days'));
            $order = $this->Mdl_admin->check_last_order($customer_id);

            //foreach($orders AS $order)
            //{

            if($order['order_date'] < $date.' 00:00:00')
            {

                if(trim($order['order_date']) == "")
                    continue;
                $row = array();
                $row[] = $i;
                $row[] = '<a href="#" data-customer_id="'.$long_customer_id.'" class="edit-action">'.$customer['name'].'</a>';
                $row[] = $customer['mobile_number'];
                $row[] = '₹ '.$order['total_amount'];
                $row[] = date('j M y, h:i A', strtotime($order['order_date']));
                $row[] = $this->dateDiffInDays($order['order_date'], $start_date).' Days';

                if($this->session->userdata('user_role') == 1)
                    $row[] = '<a href="#" class="btn btn-info pull-right btn-xs send_reminder_sms" data-customer_id="'.$customer_id.'"><span class="ti-alarm-clock"></span> SEND SMS</a>';
                $result[] = $row;
                $i++;
            }
            //}
        }

        $data = array('data' => $result);

        header('Content-type: application/json');
        echo json_encode($data);
        exit;
    }

    function download_report()
    {
        $long_customer_id = $this->input->get('customer_id');
        $customer_id = $this->Mdl_admin->get_short_id('users', $long_customer_id);

        $cust_info = $this->Mdl_admin->get_data_by_id('users', $customer_id);

        $start_date = '2019-10-01';
        $end_flag = false;

        $list = array();
        $row1 = array('Sr No', 'Month', 'Total Qty', 'Total Amount');

        array_push($list, $row1);

        for($i=1;;$i++)
        {
            $start_date = date('Y-m-d', strtotime($start_date.' +1 Month'));;
            $end_date = date('Y-m-t', strtotime($start_date));

            if($end_date > date('Y-m-d'))
            {
                $end_date = date('Y-m-d');
                $end_flag = TRUE;
            }

            $order_amount  = $this->Mdl_admin->get_orders_amount_by_date_by_customer($start_date, $end_date, $customer_id);
            $order_qty     = $this->Mdl_admin->get_order_count_by_date_by_customer($start_date, $end_date, $customer_id);

            $row = array();
            $row[] = $i;
            $row[] = date('M-y', strtotime($start_date));
            $row[] = $order_qty;
            $row[] = $order_amount;

            array_push($list, $row);

            if($end_flag)
                break;
        
        }

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$cust_info['name'].'.csv";');

        $fp = fopen('php://output', 'w');

        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }

        fpassthru($fp);
        fclose($fp);


    }

    function download_performers()
    {
        $list = array();
        $row1 = array('Sr No', 'Company Name', 'Mobile Number', 'Qty','Total Amount');

        array_push($list, $row1);

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $customers = $this->Mdl_admin->get_all_customers(true);

        $tmp_array = array();
        foreach($customers AS $customer)
        {
            $customer_id = $customer['id'];

            $order_amount  = $this->Mdl_admin->get_orders_amount_by_date_by_customer($start_date, $end_date, $customer_id);
            $order_qty     = $this->Mdl_admin->get_order_count_by_date_by_customer($start_date, $end_date, $customer_id);

            if($order_qty == 0 OR $order_qty == NULL OR trim($order_qty) == "")
                continue;

            $customer['order_amount'] = $order_amount;
            $customer['order_qty'] = $order_qty;

            $tmp_array[] = $customer;
            
        }

        $order_qty = array_column($tmp_array, 'order_qty');
        array_multisort($order_qty, SORT_DESC, $tmp_array);

        $result = array();
        $i = 1;

        foreach ($tmp_array as $obj) {
            # code...
            $row = array();
            $row[] = $i;
            $row[] = '<a href="'.base_url().'admin/customer_orders/'.$obj['long_id'].'">'.$obj['name'].'</a>';
            $row[] = $obj['mobile_number'];
            $row[] = $obj['order_qty'];
            $row[] = '&#8377; '.$obj['order_amount'];

            array_push($list, $row);
            $i++;
        }


        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="performers.csv";');

        $fp = fopen('php://output', 'w');

        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }

        fpassthru($fp);
        fclose($fp);
    }


    function ajax_top_performers_list()
    {

        $customers = $this->Mdl_admin->get_all_customers(true);

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $tmp_array = array();
        foreach($customers AS $customer)
        {
            $customer_id = $customer['id'];

            $order_amount  = $this->Mdl_admin->get_orders_amount_by_date_by_customer($start_date, $end_date, $customer_id);
            $order_qty     = $this->Mdl_admin->get_order_count_by_date_by_customer($start_date, $end_date, $customer_id);

            $customer['order_amount'] = $order_amount;
            $customer['order_qty'] = $order_qty;

            if($order_qty == 0 OR $order_qty == NULL OR trim($order_qty) == "")
                continue;

            $tmp_array[] = $customer;
            
        }

        $order_qty = array_column($tmp_array, 'order_qty');
        array_multisort($order_qty, SORT_DESC, $tmp_array);

        //$final_arr = array_slice($tmp_array, 0,25);

        $result = array();
        $i = 1;

        foreach ($tmp_array as $obj) {
            # code...
            $row = array();
            $row[] = $i;
            $row[] = '<a href="'.base_url().'admin/customer_orders/'.$obj['long_id'].'">'.$obj['name'].'</a>';
            $row[] = $obj['mobile_number'];
            $row[] = $obj['order_qty'];
            $row[] = '&#8377; '.$obj['order_amount'];
            $row[] = '<a href="#" class="btn btn-info btn-xs generate_report" data-customer_id="'.$obj['long_id'].'"><i class="ti-receipt"></i></a>';

            $result[] = $row;
            $i++;
        }

        $data = array('data' => $result);

        header('Content-type: application/json');
        echo json_encode($data);
        exit;
    }

    function ajax_statistics_list()
    {

        $customers = $this->Mdl_admin->get_all_customers(true);

        $start_date_1 = $this->input->post('start_date_1');
        $end_date_1 = $this->input->post('end_date_1');

        $start_date_2 = $this->input->post('start_date_2');
        $end_date_2 = $this->input->post('end_date_2');

        $tmp_array = array();
        foreach($customers AS $customer)
        {
            $customer_id = $customer['id'];

            $old_order_qty  = $this->Mdl_admin->get_order_count_by_date_by_customer($start_date_1, $end_date_1, $customer_id);
            $new_order_qty     = $this->Mdl_admin->get_order_count_by_date_by_customer($start_date_2, $end_date_2, $customer_id);

            $customer['old_order_qty'] = $old_order_qty;
            $customer['new_order_qty'] = $new_order_qty;

            if($old_order_qty == NULL or $new_order_qty == NULL)
            	continue;

            $customer['growth'] = ($new_order_qty - $old_order_qty) / $new_order_qty * 100;

            $tmp_array[] = $customer;
            
        }

        

        $growth = array_column($tmp_array, 'growth');
        array_multisort($growth, SORT_DESC, $tmp_array);

        //$final_arr = array_slice($tmp_array, 0,25);

        $result = array();
        $i = 1;

        foreach ($tmp_array as $obj) {
            # code...
            $row = array();
            $row[] = $i;
            $row[] = '<a href="'.base_url().'admin/customer_orders/'.$obj['long_id'].'">'.$obj['name'].'</a>';
            $row[] = $obj['mobile_number'];
            $row[] = $obj['old_order_qty'];
            $row[] = $obj['new_order_qty'];
            $row[] = number_format($obj['growth'],2).' %';

            $result[] = $row;
            $i++;
        }

        $data = array('data' => $result);

        header('Content-type: application/json');
        echo json_encode($data);
        exit;
    }

    function dateDiffInDays($date1, $date2)  
    { 
        // Calulating the difference in timestamps 
        $diff = strtotime($date2) - strtotime($date1); 
          
        // 1 day = 24 hours 
        // 24 * 60 * 60 = 86400 seconds 
        return abs(round($diff / 86400)); 
    } 

    function ajax_customer_list()
    {
        if($this->session->userdata('user_role') == 2)
            $this->Mdl_admin->manager_id = $this->session->userdata('user_id');

        $customers = $this->Mdl_admin->get_all_customers();

        $result = array();
        $i = 1;
        foreach($customers AS $customer)
        {
            $customer_id = $customer['long_id'];
            $row = array();
            $row[] = $i;
            $row[] = $customer['name'];
            $row[] = $customer['mobile_number'];
            $row[] = $customer['is_active'] == 0 ? 'Inactive' : 'Active';
            if($this->session->userdata('user_role') == 1)
            $row[] = '<a href="#" data-customer_id="'.$customer_id.'" class="edit-action"><i class="fa fa-pencil text-primary"></i></a>';//&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" data-customer_id="'.$customer_id.'" class="delete-action"><i class="fa fa-trash text-danger"></i></a>';
            else
                $row[] = "";
            $result[] = $row;
            $i++;
        }

        $data = array('data' => $result);

        header('Content-type: application/json');
        echo json_encode($data);
        exit;
    }

    function send_reminder_sms_to_all(){

    	if($this->session->userdata('user_role') != 1)
    		exit;

    	$customers = $this->Mdl_admin->get_all_customers(true);

    	$start_date = date('Y-m-d');

        $result = array();
        $i = 1;
        foreach($customers AS $customer)
        {
            $long_customer_id = $customer['long_id'];
            $customer_id = $customer['id'];
            $order_interval = $customer['order_interval'];
            $date = date("Y-m-d", strtotime($start_date.' -'.$order_interval.' Days'));
            $order = $this->Mdl_admin->check_last_order($customer_id);

            //foreach($orders AS $order)
            //{

            if($order['order_date'] < $date.' 00:00:00')
            {

                if(trim($order['order_date']) == "")
                    continue;

                $message = "Dear Customer,\n\nBook Your Order for LPG GAS CYLINDER now.\n--\nAgarwal Agency\n9974244581";
		        $output = $this->lib_common->send_sms($customer_id, $message);
		        $this->lib_common->send_notification($customer_id, 'ABOUT YOUR ORDER REMMINDER', $message);
            }
            //}
        }

        echo TRUE;
    }

    function send_reminder_sms(){

    	if($this->session->userdata('user_role') != 1)
    		exit;

    	$user_id = $_POST['customer_id'];

    	$message = "Dear Customer,\n\nBook Your Order for LPG GAS CYLINDER now.\n--\nAgarwal Agency\n9974244581";
        $output = $this->lib_common->send_sms($customer_id, $message);
        $this->lib_common->send_notification($customer_id, 'ABOUT YOUR ORDER REMMINDER', $message);

        echo TRUE;
    }

    function notifications()
    {
        if(trim($this->session->userdata('user_id')) == "")
            redirect("/");

        if($this->session->userdata('user_role') == 2)
            redirect("admin/dashboard");

        $data = array(
            'TITLE' => 'Notifications - Agarwal Agency',
            'HEADING' => 'Notifications'
        );
        $this->load->view('notifications', $data);
    }

    function ajax_notifications_list()
    {
        $notifications = $this->Mdl_admin->get_all_notifications();

        $result = array();
        $i = 1;
        foreach($notifications AS $notification)
        {
            $row = array();
            $row[] = $i;
            $row[] = $notification['title'];
            $row[] = $notification['sent_at'];

            $result[] = $row;
            $i++;
        }

        $data = array('data' => $result);

        header('Content-type: application/json');
        echo json_encode($data);
        exit;
    }

    function ajax_push_notifications()
    {
        $title = trim($this->input->post('title'));
        $message = trim($this->input->post('message'));
        
        if($message != "")
        {
            $insert_data = array(
                'title' => $message,
                'sent_at' => date('Y-m-d H:i:s')
            );

            $this->lib_common->send_notifications_to_all($title, $message);
            $this->Mdl_admin->insert_entry('notifications', $insert_data);
            
        }
        else
        {
            echo FALSE;
        }
    }

    function bank_accounts()
    {
        if(trim($this->session->userdata('user_id')) == "")
            redirect("/");

        if($this->session->userdata('user_role') == 2)
            redirect("admin/dashboard");

        $data = array(
            'TITLE' => 'Bank Accounts - Agarwal Agency',
            'HEADING' => 'Bank Accounts'
        );
        $this->load->view('bank_accounts', $data);
    }

    function ajax_bank_accounts_list()
    {
        $bank_accounts = $this->Mdl_admin->get_all_bank_accounts();

        $result = array();
        $i = 1;
        foreach($bank_accounts AS $bank_account)
        {
            $id = $bank_account['id'];
            $row = array();
            $row[] = $i;
            $row[] = $bank_account['account_name'];
            $row[] = $bank_account['account_no'];
            $row[] = $bank_account['bank_name'];
            $row[] = $bank_account['bank_address'];
            $row[] = '<a href="#" data-bank_account_id="'.$id.'" class="edit-action"><i class="fa fa-pencil text-primary"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" data-bank_account_id="'.$id.'" class="delete-action"><i class="fa fa-trash text-danger"></i></a>';
            $result[] = $row;
            $i++;
        }

        $data = array('data' => $result);

        header('Content-type: application/json');
        echo json_encode($data);
        exit;
    }

    function ajax_get_bank_account_detail()
    {
        $bank_account_id = trim($this->input->post('bank_account_id'));
        $bank_account_info = $this->Mdl_admin->get_data_by_id('bank_accounts', $bank_account_id);

        unset($bank_account_info['created_by']);
        unset($bank_account_info['created_at']);

        header('Content-type: application/json');
        echo json_encode($bank_account_info);
        exit;
    }

    function ajax_save_bank_account()
    {
        $account_name = trim($this->input->post('account_name'));
        $account_no = trim($this->input->post('account_no'));
        $bank_name = trim($this->input->post('bank_name'));
        $bank_address = trim($this->input->post('bank_address'));
        $ifsc_code = trim($this->input->post('ifsc_code'));
        $gst_no = trim($this->input->post('gst_no'));

        $bank_account_id = trim($this->input->post('bank_account_id'));

        if($account_name != "")
        {
            if($bank_account_id == "")
            {
                $insert_data = array(
                    'account_name' => $account_name,
                    'account_no' => $account_no,
                    'bank_name' => $bank_name,
                    'bank_address' => $bank_address,
                    'ifsc_code' => $ifsc_code,
                    'gst_no' => $gst_no
                );
                $this->Mdl_admin->insert_entry('bank_accounts', $insert_data);
            }
            else{
                $update_data = array(
                    'account_name' => $account_name,
                    'account_no' => $account_no,
                    'bank_name' => $bank_name,
                    'bank_address' => $bank_address,
                    'ifsc_code' => $ifsc_code,
                    'gst_no' => $gst_no
                );
                
                $this->Mdl_admin->update_entry('bank_accounts', $bank_account_id, $update_data);
            }
            echo TRUE;
        }
        else
        {
            echo FALSE;
        }
    }

    function ajax_delete_bank_account()
    {
        $bank_account_id = trim($this->input->post('bank_account_id'));

        if($bank_account_id != "")
        {
            $this->Mdl_admin->delete_bank_account($bank_account_id);
            echo TRUE;
        }
        else
        {
            echo FALSE;
        }
    }

    function orders()
    {
        if(trim($this->session->userdata('user_id')) == "")
            redirect("/");

        if($this->session->userdata('user_role') == 2)
            $this->Mdl_admin->manager_id = $this->session->userdata('user_id');

        $delivery_boys_array = $this->Mdl_admin->get_all_delivery_boys();

        $delivery_boys = array();
        foreach($delivery_boys_array AS $delivery_boy)
        {
            $delivery_boys[$delivery_boy['long_id']] = $delivery_boy['name'];
        }

        $order_status = array("0" => "Pending",
                            "1" => "In-Progress",
                            "2" => "Delivered",
                            "3" => "Cancelled"
                        );

        $data = array(
            'TITLE' => 'Orders - Agarwal Agency',
            'HEADING' => 'Orders',
            'delivery_boys' => json_encode($delivery_boys),
            'order_status' => json_encode($order_status)
        );
        $this->load->view('orders', $data);
    }

    function customer_orders($long_customer_id)
    {
        if(trim($this->session->userdata('user_id')) == "")
            redirect("/");

        $customer_id = $this->Mdl_admin->get_short_id('users', $long_customer_id);
        $customer_info = $this->Mdl_admin->get_customer_basic_info($customer_id);

        $data = array(
            'TITLE' => 'Customer Orders - Agarwal Agency',
            'HEADING' => $customer_info['name'].' - Orders',
            'long_customer_id' => $long_customer_id
        );
        $this->load->view('customer_orders', $data);
    }

    function ajax_orders_list()
    {
        if($this->session->userdata('user_role') == 2)
            $this->Mdl_admin->manager_id = $this->session->userdata('user_id');

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $orders = $this->Mdl_admin->get_orders($start_date, $end_date);


        $result = array();
        foreach($orders AS $order)
        {
            $id = $order['id'];
            $customer_info = $this->Mdl_admin->get_customer_basic_info($order['user_id']);
            $delivery_address = $this->Mdl_admin->get_data_by_id('delivery_addresses', $order['address_id']);
            $order_qty = $this->Mdl_admin->get_item_qty_by_order($order['id']);

            if($order['order_status'] == 0)
                $status = '<span class="badge badge-pill badge-warning">Pending</span>';
            if($order['order_status'] == 1)
                $status = '<span class="badge badge-pill badge-info">In-Process</span>';
            if($order['order_status'] == 2)
                $status = '<span class="badge badge-pill badge-success">Delivered</span>';
            if($order['order_status'] == 3)
                $status = '<span class="badge badge-pill badge-danger">Cancelled</span>';

            $status = $order['order_status'];
            $row = array();
            $row[] = $order['long_id'];
            $row[] = '<a href="'.base_url().'admin/order_detail/'.$order['long_id'].'">'.$id.'</a>';
            $row[] = '<a href="'.base_url().'admin/customer_orders/'.$customer_info['long_id'].'">'.$delivery_address['company_name'].'</a>';
            $row[] = $customer_info['mobile_number'];
            $row[] = $order_qty;
            $row[] = "&#8377; ".$order['total_amount'];
            $row[] = date('j M y, h:i A', strtotime($order['order_date']));
            $row[] = $this->Mdl_admin->get_long_id('users', $order['delivery_by']);
            $row[] = $status;
            $result[] = $row;
        }

        $data = array('data' => $result);

        header('Content-type: application/json');
        echo json_encode($data);
        exit;
    }

    function ajax_customer_orders_list()
    {
        $long_customer_id = $this->input->post('customer_id');
        $customer_id = $this->Mdl_admin->get_short_id('users', $long_customer_id);
        $this->Mdl_admin->customer_id = $customer_id;

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        
        $orders = $this->Mdl_admin->get_orders($start_date, $end_date);

        $result = array();
        foreach($orders AS $order)
        {
            $id = $order['id'];
            $customer_info = $this->Mdl_admin->get_customer_basic_info($order['user_id']);
            $delivery_address = $this->Mdl_admin->get_data_by_id('delivery_addresses', $order['address_id']);

            if($order['order_status'] == 0)
                $status = '<span class="badge badge-pill badge-warning">Pending</span>';
            if($order['order_status'] == 1)
                $status = '<span class="badge badge-pill badge-info">In-Process</span>';
            if($order['order_status'] == 2)
                $status = '<span class="badge badge-pill badge-success">Delivered</span>';
            if($order['order_status'] == 3)
                $status = '<span class="badge badge-pill badge-danger">Cancelled</span>';

            $row = array();
            $row[] = '<a href="'.base_url().'admin/order_detail/'.$order['long_id'].'">'.$id.'</a>';
            $row[] = $delivery_address['company_name'];
            $row[] = $customer_info['mobile_number'];
            $row[] = "&#8377; ".$order['total_amount'];
            $row[] = $status;
            $row[] = '<a href="'.base_url().'admin/order_detail/'.$order['long_id'].'"><i class="fa fa-eye text-primary"></i></a>';
            $result[] = $row;
        }

        $data = array('data' => $result);

        header('Content-type: application/json');
        echo json_encode($data);
        exit;
    }

    function ajax_update_order()
    {
        $long_order_id = $this->input->post('order_id');
        $order_id = $this->Mdl_admin->get_short_id('orders', $long_order_id);
        $order_info = $this->Mdl_admin->get_data_by_id('orders', $order_id);
        $cust_info = $this->Mdl_admin->get_data_by_id('users', $order_info['user_id']);

        $admin_name = $this->session->userdata('admin_name');

        if(trim($this->input->post('delivery_boy_id')) != "")
        {
            
            $long_delivery_boy_id = $this->input->post('delivery_boy_id');
            $delivery_boy_id = $this->Mdl_admin->get_short_id('users', $long_delivery_boy_id);
            $data['delivery_by'] = $delivery_boy_id;
            $delivery_boy_info = $this->Mdl_admin->get_data_by_id('users', $delivery_boy_id);

            if($order_info['order_status'] == 1)
            {
                $message = 'New Order #'.$order_id.' ('.strtoupper($cust_info['name']).') Assigned To You';
                $this->lib_common->send_sms($delivery_boy_id, $message);
                $this->lib_common->send_notification($delivery_boy_id, 'New Order Assgined', $message);
            }

            $txt = $admin_name.' - Order assigned To '.$delivery_boy_info['name'];
            $this->Mdl_admin->update_order_log($order_id, $txt);
        }

        if(trim($this->input->post('order_status')) != "")
        {
            $order_status = $this->input->post('order_status');
            $data['order_status'] = $order_status;

            if($order_status == 1)
            {
                $delivery_boy_id = $order_info['delivery_by'];

                $message = 'New Order #'.$order_id.' ('.strtoupper($cust_info['name']).') Assigned To You';
                $this->lib_common->send_sms($delivery_boy_id, $message);
                $this->lib_common->send_notification($delivery_boy_id, 'New Order Assgined', $message);
                $txt = $admin_name.' - Order Status changed to - In-Progress';
            }
            if($order_status == 2)
            {
                $message = "Your Order #".$order_id." has been delivered.\n--\nAgarwal Agency\n9974244581";
                $this->lib_common->send_sms($order_info['user_id'], $message);
                $this->lib_common->send_notification($order_info['user_id'], 'Order Status Updated', $message);
                $txt = $admin_name.' - Order Status changed to - Delivered';
            }

            if($order_status == 3)
            {
                $message = "Your Order #".$order_id." has been cancelled.\n--\nAgarwal Agency\n9974244581";
                $this->lib_common->send_sms($order_info['user_id'], $message);
                $this->lib_common->send_notification($order_info['user_id'], 'Order Status Updated', $message);
                $txt = $admin_name.' - Order Status changed to - Cancelled';
            }

            $this->Mdl_admin->update_order_log($order_id, $txt);            
        }

        $this->Mdl_admin->update_entry('orders', $order_id, $data);

        echo TRUE;
    }

    function order_detail($long_order_id)
    {
        if(trim($this->session->userdata('user_id')) == "")
            redirect("/");

        $order_id = $this->Mdl_admin->get_short_id('orders', $long_order_id);
        $order_info = $this->Mdl_admin->get_data_by_id('orders', $order_id);
        $order_info['products'] =  $this->Mdl_cpanel->get_ordered_items($order_id);
        $order_info['delivery_address'] = $this->Mdl_admin->get_data_by_id('delivery_addresses', $order_info['address_id']);
        $order_info['customer'] = $this->Mdl_admin->get_data_by_id('users', $order_info['user_id']);

        //if($this->session->userdata('user_role') == 2)
        //    $this->Mdl_admin->manager_id = $this->session->userdata('user_id');

        $this->Mdl_admin->manager_id = $order_info['customer']['parent_id'];

        $order_info['delivery_boys'] = $this->Mdl_admin->get_all_delivery_boys();
        $order_info['managers'] = $this->Mdl_admin->get_all_managers();

        $order_info['delivery_by'] = $this->Mdl_admin->get_long_id('users', $order_info['delivery_by']);


        $data = array(
            'TITLE' => 'Order Detail - Agarwal Agency',
            'order_info' => $order_info,
            'HEADING' => 'Order Detail'
        );
        $this->load->view('order_detail', $data);
    }

    function order_history()
    {
        $data = array(
            'TITLE' => 'order History - Agarwal Agency'
        );
        $this->load->view('order_history', $data);
    }

    function upload_csv()
    {
        if(trim($this->session->userdata('user_id')) == "")
            redirect("/");

        if (isset($_FILES['file']['name']))
        {
            $target_dir = "./assets/uploads/"; // Upload directory

            $path                    = $_FILES['file']['name'];
            $extension               = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $tmp_file_name           = $this->lib_common->rand_str();
            $file_name               = $tmp_file_name . '.' . $extension;
            $config['upload_path']   = $target_dir;
            $config['allowed_types'] = 'csv';
            $config['file_name']     = $file_name;

            $this->upload->initialize($config);

            if (!$this->upload->do_upload('file')) {
                $data['error'] = "Error while uploading";
                header('HTTP/1.1 500 Internal Server Error');
                header('Content-Type: application/json');
                echo json_encode($data);
                exit;
            }

            $file = fopen('./assets/uploads/'.$file_name,"r");
            $i = 1;
            while(! feof($file))
            {
                $row = fgetcsv($file);

                if($i == 1){
                    $i++;
                    continue;
                }

                $j = 1;
                $price_type = 1;
                foreach($row as $key=>$val)
                {

                    if($j < 3)
                    {
                            
                        if($j == 2)
                        {
                            $long_product_id = $val;
                            if(!$product_id = $this->Mdl_admin->get_short_id('products', $long_product_id))
                                continue;
                        }

                        $j++;
                        continue;
                    }

                    $val = (double)$val;

                    $this->Mdl_admin->replace_price($product_id, $price_type, $val);
                    $price_type++;
                }
            }
            fclose($file);
        }   
    }

    function download_products()
    {
        $list = array ();
        $row1 = array('Product Name', 'Product Id');

        $price_types = $this->Mdl_admin->get_all_price_types();

        foreach($price_types AS $price_type)
        {
            array_push($row1, $price_type['title']);
        }

        array_push($list, $row1);

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="product_prices.csv";');

        $fp = fopen('php://output', 'w');

        $products = $this->Mdl_admin->get_all_products();

        foreach ($products as $product) {
            $product_arr = array();
            $product_arr[] = $product['name'];
            $product_arr[] = $product['long_id'];
            $product_id = $product['id'];

            $pricing = $this->Mdl_admin->get_product_prices($product_id);

            foreach($pricing AS $price)
            {
                $product_arr[] = $price['price'];
            }

            array_push($list, $product_arr);
        }

        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }

        fpassthru($fp);

        fclose($fp);

    }

    function download_orders()
    {
        $list = array();
        $row1 = array('Order No', 'Company Name', 'Mobile Number', 'Qty','Total Amount', 'Delivery Date', 'Delivery By', 'Status', 'Ordered At');

        if($this->session->userdata('user_role') == 2)
        $this->Mdl_admin->manager_id = $this->session->userdata('user_id');

        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        array_push($list, $row1);

        $orders = $this->Mdl_admin->get_orders($start_date, $end_date);

        $result = array();
        foreach($orders AS $order)
        {
            $id = $order['id'];
            $order_qty = $this->Mdl_admin->get_item_qty_by_order($id);
            $customer_info = $this->Mdl_admin->get_customer_basic_info($order['user_id']);
            $delivery_boy_info = $this->Mdl_admin->get_customer_basic_info($order['delivery_by']);
            $delivery_address = $this->Mdl_admin->get_data_by_id('delivery_addresses', $order['address_id']);

            if($order['order_status'] == 0)
                $status = 'Pending';
            if($order['order_status'] == 1)
                $status = 'In-Process';
            if($order['order_status'] == 2)
                $status = 'Delivered';
            if($order['order_status'] == 3)
                $status = 'Cancelled';

            $row = array();
            $row[] = $order['id'];
            $row[] = $delivery_address['company_name'];
            $row[] = $customer_info['mobile_number'];
            $row[] = $order_qty;
            $row[] = $order['total_amount'];
            $row[] = $order['delivery_date'];
            $row[] = $delivery_boy_info['name'];
            $row[] = $status;
            $row[] = $order['order_date'];

            array_push($list, $row);
        }


        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="orders.csv";');

        $fp = fopen('php://output', 'w');

        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }

        fpassthru($fp);
        fclose($fp);

    }

    function upload_customers()
    {
        if(trim($this->session->userdata('user_id')) == "")
            redirect("/");

        if (isset($_FILES['file']['name']))
        {
            $target_dir = "./assets/uploads/"; // Upload directory

            $path                    = $_FILES['file']['name'];
            $extension               = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $tmp_file_name           = $this->lib_common->rand_str();
            $file_name               = $tmp_file_name . '.' . $extension;
            $config['upload_path']   = $target_dir;
            $config['allowed_types'] = 'csv';
            $config['file_name']     = $file_name;

            $this->upload->initialize($config);

            if (!$this->upload->do_upload('file')) {
                $data['error'] = "Error while uploading";
                header('HTTP/1.1 500 Internal Server Error');
                header('Content-Type: application/json');
                echo json_encode($data);
                exit;
            }

            $file = fopen('./assets/uploads/'.$file_name,"r");
            $i = 1;
            while(! feof($file))
            {
                $row = fgetcsv($file);

                if($i == 1){
                    $i++;
                    continue;
                }

                

                if($row[5] == 0 OR trim($row[5]) == "")
                    $manager_id = $this->session->userdata('user_id');
                else
                    $manager_id = $row[5];

                if($row[2] == 0 OR trim($row[2]) == "")
                    continue;

                $data = array(
                    'name' => $row[1],
                    'mobile_number' => $row[2],
                    'price_type' => $row[3],
                    'order_interval' => $row[4],
                    'parent_id' => $manager_id,
                    'delivery_boy_id' => $row[6]
                );


                $long_customer_id = $row[0];

                $customer_id = $this->Mdl_admin->get_short_id('users', $long_customer_id);

                if(!$customer_id)
                {
                    $data['long_id'] = $this->lib_common->rand_str();
                    $data['user_type'] = 4;
                    $data['created_by'] = $this->session->userdata('user_id');
                    $data['created_at'] = date('Y-m-d H:i:s');

                    $this->Mdl_admin->insert_entry('users', $data);
                }
                else{
                    $this->Mdl_admin->update_entry('users', $customer_id, $data);
                }

            }
            fclose($file);
        }
    }

    function download_customers()
    {
        $list = array();
        $row1 = array('Id', 'Company Name', 'Mobile Number', 'Price Type', 'Interval', 'Manager', 'Delivery Boy');

        if($this->session->userdata('user_role') == 2)
        $this->Mdl_admin->manager_id = $this->session->userdata('user_id');

        array_push($list, $row1);

        $customers = $this->Mdl_admin->get_all_customers();

        $result = array();
        $i = 1;
        foreach($customers AS $customer)
        {


                $row = array();
                $row[] = $customer['long_id'];
                $row[] = $customer['name'];
                $row[] = $customer['mobile_number'];
                $row[] = $customer['price_type'];
                $row[] = $customer['order_interval'];
                $row[] = $customer['parent_id'];
                $row[] = $customer['delivery_boy_id'];

                array_push($list, $row);
                $i++;
        }


        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="customers.csv";');

        $fp = fopen('php://output', 'w');

        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }

        fpassthru($fp);
        fclose($fp);
    }


    function delivery_sms()
    {
        $orders = $this->Mdl_admin->get_todays_delivery_orders();

        foreach($orders AS $order)
        {
            $message = "Dear Customer,\n\nYour Order #".$order['id']." is out for delivery & will be delivered today..\n--\nAgarwal Agency\n9974244581";
            $output = $this->lib_common->send_sms($order['user_id'], $message);
        }

        echo true;
    }

    function expected_order_sms()
    {
        $customers = $this->Mdl_admin->get_all_customers(true);

        $start_date = date('Y-m-d');

        $result = array();
        $i = 1;
        foreach($customers AS $customer)
        {
            $long_customer_id = $customer['long_id'];
            $customer_id = $customer['id'];
            $order_interval = $customer['order_interval'];
            $date = date("Y-m-d", strtotime($start_date.' -'.$order_interval.' Days'));
            $order = $this->Mdl_admin->check_last_order($customer_id);

            //foreach($orders AS $order)
            //{

            if($order['order_date'] >= $date.' 00:00:00' AND $order['order_date'] <= $date.' 23:59:59')
            {

            	$message = "Dear Customer,\n\nBook Your Order for LPG GAS CYLINDER now.\n--\nAgarwal Agency\n9974244581";
            	$output = $this->lib_common->send_sms($customer_id, $message);
            }
            //}
        }

        echo true;
    }

    function download_expected_orders()
    {
        $list = array();
        $row1 = array('Sr No', 'Company Name', 'Mobile Number', 'Last Order Amount', 'Last Order Date');

        if($this->session->userdata('user_role') == 2)
        $this->Mdl_admin->manager_id = $this->session->userdata('user_id');

        array_push($list, $row1);

        $customers = $this->Mdl_admin->get_all_customers();
        $start_date = $this->input->get('start_date');

        $result = array();
        $i = 1;
        foreach($customers AS $customer)
        {
            $long_customer_id = $customer['long_id'];
            $customer_id = $customer['id'];
            $order_interval = $customer['order_interval'];
            $date = date("Y-m-d", strtotime($start_date.' -'.$order_interval.' Days'));
            $order = $this->Mdl_admin->check_last_order($customer_id);

            //foreach($orders AS $order)
            //{

            if($order['order_date'] >= $date.' 00:00:00' AND $order['order_date'] <= $date.' 23:59:59')
            {

                $row = array();
                $row[] = $i;
                $row[] = $customer['name'];
                $row[] = $customer['mobile_number'];
                $row[] = $order['total_amount'];
                $row[] = $order['order_date'];

                array_push($list, $row);
                $i++;
            }
            //}
        }


        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="expected_orders_'.$start_date.'.csv";');

        $fp = fopen('php://output', 'w');

        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }

        fpassthru($fp);
        fclose($fp);
    }

    function download_delayed_orders()
    {
        $list = array();
        $row1 = array('Sr No', 'Company Name', 'Mobile Number', 'Last Order Amount', 'Last Order Date', 'Delay in Days');

        if($this->session->userdata('user_role') == 2)
        $this->Mdl_admin->manager_id = $this->session->userdata('user_id');

        array_push($list, $row1);

        $customers = $this->Mdl_admin->get_all_customers();

        $start_date = date('Y-m-d');

        $result = array();
        $i = 1;
        foreach($customers AS $customer)
        {
            $long_customer_id = $customer['long_id'];
            $customer_id = $customer['id'];
            $order_interval = $customer['order_interval'];
            $date = date("Y-m-d", strtotime($start_date.' -'.$order_interval.' Days'));
            $order = $this->Mdl_admin->check_last_order($customer_id);

            //foreach($orders AS $order)
            //{

            if($order['order_date'] < $date.' 00:00:00')
            {

                if(trim($order['order_date']) == "")
                    continue;
                $row = array();
                $row[] = $i;
                $row[] = $customer['name'];
                $row[] = $customer['mobile_number'];
                $row[] = $order['total_amount'];
                $row[] = $order['order_date'];
                $row[] = $this->dateDiffInDays($order['order_date'], $start_date).' Days';

                array_push($list, $row);
                $i++;
            }
            //}
        }

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="delayed_orders.csv";');

        $fp = fopen('php://output', 'w');

        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }

        fpassthru($fp);
        fclose($fp);
    }

    function test()
    {

        $start_date = '2019-02-01';
        $end_flag = false;
        for($i=1;$i<15;$i++)
        {
            $start_date = date('Y-m-d', strtotime($start_date.' +1 Month'));;
            $end_date = date('Y-m-t', strtotime($start_date));

            if($end_date > date('Y-m-d'))
            {
                $end_date = date('Y-m-d');
                $end_flag = TRUE;
            }

            echo $i.' | .'.$start_date.' - '.$end_date.'<br>';

            if($end_flag)
                break;
        
        }
        

        /*

        for($i=1; $i<=100; $i++)
        {
            $data = array(
                'title' => $i
            );

            $this->Mdl_admin->insert_entry("price_types", $data);
        }
        */
        /*
        $alphas     = range('A', 'Z');
        $flip_array = array_flip($alphas);
        $file = fopen('./assets/uploads/customers.csv',"r");
            $i = 1;
            while(! feof($file))
            {
                $row = fgetcsv($file);

                if($i == 1){
                    $i++;
                    continue;
                }

                if(trim($row[1]) == "")
                    continue;

                $price_type = $flip_array[$row[2]];
                $price_type++;
                $row[2] = $price_type;

                $data = array(
                    'long_id' => $this->lib_common->rand_str(),
                    'mobile_number' => $row[1],
                    'name' => $row[0],
                    'password' => md5('abc@123'),
                    'user_type' => 4,
                    'price_type' => $row[2],
                    'parent_id' => 2,
                    'delivery_boy_id' => rand(4, 6),
                    'order_interval' => 1,
                    'created_by' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                );

                $this->Mdl_admin->insert_entry("users", $data);
                    
            }
            fclose($file);
        */
    }


    function ajax_order_history_list()
    {
        $data = array(
            "data" => array(
            array("1", "Oils", "15"),
            array("2", "Lubricants", "20"),
            array("3", "Others", "21"),
            )
        );

        header('Content-type: application/json');
        echo json_encode($data);
        exit;
    }
    
}