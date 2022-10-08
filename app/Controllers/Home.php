<?php

namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
		$db      = \Config\Database::connect();

		// $subdomain = explode(".", $_SERVER['SERVER_NAME']);

		$query = $db->table('customers')->where('url_title', 'technofab')->get();
		$customer = $query->getRow();
		if($customer == null)
		{
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
		$plan_query = $db->table('customer_plans')->where('customer_id', $customer->id)->orderBy('expiry_date', 'DESC')->get();
		$customer_plan = $plan_query->getRow();
		if($customer_plan != null)
		{
			$customer->expiry_date = $customer_plan->expiry_date;
			$customer->total_products = $customer_plan->total_products;
		}
			
		$data['customer'] = $customer;

		return view('App\Views\welcome_message', $data);
	}
}
