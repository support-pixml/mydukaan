<?php

require_once getcwd().'/firebase/vendor/autoload.php';

use Kreait\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\AndroidConfig;

class Lib_common {

    function retrieve($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_USERAGENT, 'facebook-php-2.0');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    function rand_str($length = 8, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890') {
        // Length of character list
        $chars_length = (strlen($chars) - 1);

        // Start our string
        $string = $chars{rand(0, $chars_length)};

        // Generate random string
        for ($i = 1; $i < $length; $i = strlen($string)) {
            // Grab a random character from our list
            $r = $chars{rand(0, $chars_length)};

            // Make sure the same two characters don't appear next to each other
            if ($r != $string{$i - 1})
                $string .= $r;
        }

        // Return the string
        return $string;
    }

    function send_notification($user_id, $title = "", $desc = "")
    {

        $message = "AGRAWAL AGENCY";

        $this->CI = &get_instance();
        $this->CI->load->model('Mdl_cpanel');

        $token_id = $this->CI->Mdl_cpanel->get_token_id($user_id);
        //$token_id = 'fbxz_ieW3-8:APA91bGPyncTtSYzk1XDHOU1tU1InYnDclsxb1HdduD_Vg4LSPUYtgmc6rBuLf9lKNdQgcwQPacBY97TfpI4gOGjcd2lZoOVGkQA2ljLDAPlsxm8S7s51USN8eDJw-iFFQa9kJKLaa79'; 

        echo $token_id.'<br>';

        if(trim($token_id) == "")
            return;

        $firebase = (new Factory)
        ->withServiceAccount($this->CI->config->item('firebase_app_key'))
        ->create();
        $messaging = $firebase->getMessaging();

        $data = array('message' => $message,
            'contentTitle' => $title,
            'contentText' => $desc,
            'vibrate' => 1,
            'sound'  => 1
         );

        $config = AndroidConfig::fromArray([
            'ttl' => '3600s',
            'priority' => 'high',
            'notification' => [
                'title' => $title,
                'body' => $desc,
                'icon' => 'stock_ticker_update',
                'color' => '#f45342',
            ],
        ]);

        $message = CloudMessage::withTarget('token', $token_id)
        ->withNotification(Notification::create($title, $desc))
        ->withData(['sound' => 1])
        ->withAndroidConfig($config)
        ->withData(['body' => $desc]);// optional

        $result = $messaging->send($message);

    }

    function send_notifications_to_all($title = "", $desc = "")
    {

        $message = "AGRAWAL AGENCY";

        $this->CI = &get_instance();
        $this->CI->load->model('Mdl_cpanel');

        $tokens = $this->CI->Mdl_cpanel->get_all_tokens();
        //$token_id = 'fbxz_ieW3-8:APA91bGPyncTtSYzk1XDHOU1tU1InYnDclsxb1HdduD_Vg4LSPUYtgmc6rBuLf9lKNdQgcwQPacBY97TfpI4gOGjcd2lZoOVGkQA2ljLDAPlsxm8S7s51USN8eDJw-iFFQa9kJKLaa79'; 

        if(!$tokens)
            return FALSE;

        $deviceTokens = array();
        foreach($tokens AS $token)
        {
            $deviceTokens[] = $token['token_id'];
        }

        $firebase = (new Factory)
        ->withServiceAccount($this->CI->config->item('firebase_app_key'))
        ->create();
        $messaging = $firebase->getMessaging();

        $data = array('message' => $message,
            'contentTitle' => $title,
            'contentText' => $desc,
            'vibrate' => 1,
            'sound'  => 1
         );

        $config = AndroidConfig::fromArray([
            'ttl' => '3600s',
            'priority' => 'high',
            'notification' => [
                'title' => $title,
                'body' => $desc,
                'icon' => 'stock_ticker_update',
                'color' => '#f45342',
            ],
        ]);

        $message = CloudMessage::new()
        ->withNotification(Notification::create($title, $desc))
        ->withData(['sound' => 1])
        ->withAndroidConfig($config)
        ->withData(['body' => $desc]);

        $report = $messaging->sendMulticast($message, $deviceTokens);
        // ->withNotification(Notification::create('Title', 'Body'))
        // ->withData(['sound' => 1])
        // ->withAndroidConfig($config);

        echo 'Successful sends: '.$report->successes()->count().PHP_EOL;
        echo 'Failed sends: '.$report->failures()->count().PHP_EOL;

        if ($report->hasFailures()) {
            foreach ($report->failures()->getItems() as $failure) {
                echo $failure->error()->getMessage().PHP_EOL;
            }
        }
    }



    function send_sms($user_id, $message = "")
    {

        $this->CI = &get_instance();
        $this->CI->load->model('Mdl_cpanel');
        $user_info = $this->CI->Mdl_cpanel->get_user_info($user_id);

        $mobile_number = $user_info['mobile_number'];

        $url = 'http://msg.targetad.in/api/sendhttp.php?authkey=155400Aj452Ce2Hu59380a3a&mobiles='.$mobile_number.'&message='.urlencode($message).'&sender=AGRAGR&route=4';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $output = curl_exec($ch);
        curl_close($ch);
        
        return $output;


    }

    function send_trial_sms($user_id, $message = "")
    {

        $this->CI = &get_instance();
        $this->CI->load->model('Mdl_cpanel');
        $user_info = $this->CI->Mdl_cpanel->get_user_info($user_id);

        $mobile_number = 919909160070;

        $url = 'http://msg.targetad.in/api/sendhttp.php?authkey=155400Aj452Ce2Hu59380a3a&mobiles='.$mobile_number.'&message='.urlencode($message).'&sender=AGRAGR&route=4';
        echo $url.'<br>';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $output = curl_exec($ch);
        curl_close($ch);
        
        return $output;


    }

    function time_ago($date) {

        if (empty($date)) {

            return "No date provided";
        }

        $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");

        $lengths = array("60", "60", "24", "7", "4.35", "12", "10");

        $now = time();

        $unix_date = strtotime($date);

// check validity of date

        if (empty($unix_date)) {

            return "Bad date";
        }

// is it future date or past date

        if ($now > $unix_date) {

            $difference = $now - $unix_date;

            $tense = "ago";
        } else {

            $difference = $unix_date - $now;
            $tense = "from now";
        }

        for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {

            $difference /= $lengths[$j];
        }

        $difference = round($difference);

        if ($difference != 1) {

            $periods[$j].= "s";
        }

        return "$difference $periods[$j] {$tense}";
    }
	
	function readable_time($date)
	{
		$unix_date = strtotime($date);
		return date('D, j M g:iA', $unix_date);
	}

    function strleft($s1, $s2) {
        return substr($s1, 0, strpos($s1, $s2));
    }

    function get_location_name($id) {
        $this->CI = &get_instance();
        $this->CI->load->model('mdl_desktop');
        return $this->CI->mdl_desktop->get_location_name($id);
    }

    function get_category_name($id) {
        $this->CI = &get_instance();
        $this->CI->load->model('mdl_desktop');
        return $this->CI->mdl_desktop->get_category_name($id);
    }

    function get_user_name($id) {
        $this->CI = &get_instance();
        $this->CI->load->model('mdl_desktop');
        return $this->CI->mdl_desktop->get_user_name($id);
    }

    function get_location_url($id) {
        $this->CI = &get_instance();
        $this->CI->load->model('mdl_desktop');
        return $this->CI->mdl_desktop->get_location_url($id);
    }

    function get_city_name($id) {
        $this->CI = &get_instance();
        $this->CI->load->model('mdl_desktop');
        return $this->CI->mdl_desktop->get_city_name($id);
    }

    function get_city_id($city_url) {
        $this->CI = &get_instance();
        $this->CI->load->model('mdl_desktop');
        return $this->CI->mdl_desktop->get_city_id($city_url);
    }

    function get_cuisine_name($id) {
        $this->CI = &get_instance();
        $this->CI->load->model('mdl_desktop');
        return $this->CI->mdl_desktop->get_cuisine_name($id);
    }

    function get_restaurant_name($id) {
        $this->CI = &get_instance();
        $this->CI->load->model('mdl_desktop');
        return $this->CI->mdl_desktop->get_restaurant_name($id);
    }

    function total_city_restaurant($id) {
        $this->CI = &get_instance();
        $this->CI->load->model('mdl_desktop');
        return $this->CI->mdl_desktop->get_total_city_restaurants($id);
    }

    function total_nv_restaurant($id) {
        $this->CI = &get_instance();
        $this->CI->load->model('mdl_desktop');
        return $this->CI->mdl_desktop->get_nv_restaurants_count_by_city($id);
    }

    function total_home_delivery_restaurant($id) {
        $this->CI = &get_instance();
        $this->CI->load->model('mdl_desktop');
        return $this->CI->mdl_desktop->get_home_delivery_restaurants_count_by_city($id);
    }

    function total_banquet_restaurant($id) {
        $this->CI = &get_instance();
        $this->CI->load->model('mdl_desktop');
        return $this->CI->mdl_desktop->get_banquet_restaurants_count_by_city($id);
    }

    function total_favorites_restaurant($rest_id) {
        $this->CI = &get_instance();
        $this->CI->load->model('mdl_desktop');
        return $this->CI->mdl_desktop->get_restaurants_favorite_count($rest_id);
    }

    function total_deals($rest_id) {
        $this->CI = &get_instance();
        $this->CI->load->model('mdl_desktop');
        return $this->CI->mdl_desktop->total_deals($rest_id);
    }

    function check_if_users_favorite($rest_id, $user_id) {
        $this->CI = &get_instance();
        $this->CI->load->model('mdl_desktop');
        return $this->CI->mdl_desktop->check_if_users_favorite($rest_id, $user_id);
    }

    function header_ad_img($city_id) {
        $this->CI = &get_instance();
        $this->CI->load->model('mdl_desktop');
        return $this->CI->mdl_desktop->retrieve_website_header_ad_image($city_id);
    }

    function sidebar_ad_img($city_id) {
        $this->CI = &get_instance();
        $this->CI->load->model('mdl_desktop');
        return $this->CI->mdl_desktop->retrieve_website_sidebar_ad_image($city_id);
    }

    function send_mail($to, $subject, $message, $from) {

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: ' . $from . "\r\n";
        $headers .= "Bcc: dipak@pixml.in\r\n";

        mail($to, $subject, $message, $headers);
    }
	
	function mysql_log($obj)
	{
		if(is_array($obj))
		{
			$obj = serialize($obj);
		}
		
		$this->CI = &get_instance();
		$this->CI->load->model('mdl_desktop');
		$this->CI->mdl_desktop->mysql_log($obj);
		
	}
	
	function generate_order_mail_body($order_id, $user_info_flag = TRUE)
	{
		$this->CI = &get_instance();
		$order_detail = $this->CI->mdl_home->get_data_by_id($order_id, 'rest_delivery_order');
		$rest_id = $order_detail[0]->rest_id;
		$user_id = $order_detail[0]->user_id;
		
		$restaurant['basic'] = $this->CI->mdl_desktop->get_restaurant_info($rest_id);
        $restaurant['contact'] = $this->CI->mdl_desktop->get_restaurant_contact_details($rest_id);
		$user_info = $this->CI->mdl_desktop->get_user_location_info($order_id, $user_id);
		
		$order_items = $this->CI->mdl_home->get_ordered_items($order_id);
		
		$order_taxes = $this->CI->mdl_home->get_data_by_field('order_id', $order_id, 'rest_order_taxes');
		
		/*
		echo '<pre>';
		print_r($order_items);
		echo '</pre>';
		/**/
		$data = array(
		'order_id' => $order_id,
		'restaurant' => $restaurant,
		'user' => $user_info,
		'order_items' => $order_items,
		'order_detail' => $order_detail[0],
		'order_taxes' => $order_taxes,
		'user_info_flag' => $user_info_flag
		);
		$mail_body = $this->CI->load ->view('orders/order_mail', $data, TRUE);
		
		return $mail_body;
	}
	
	function generate_order_sms_body($order_id, $user_info_flag = TRUE)
	{
		$this->CI = &get_instance();
		$order_detail = $this->CI->mdl_home->get_data_by_id($order_id, 'rest_delivery_order');
		$rest_id = $order_detail[0]->rest_id;
		$user_id = $order_detail[0]->user_id;
		
		$restaurant['basic'] = $this->CI->mdl_desktop->get_restaurant_info($rest_id);
        $restaurant['contact'] = $this->CI->mdl_desktop->get_restaurant_contact_details($rest_id);
		$user_info = $this->CI->mdl_desktop->get_user_location_info($order_id, $user_id);
		
		$order_items = $this->CI->mdl_home->get_ordered_items($order_id);
		
		$order_taxes = $this->CI->mdl_home->get_data_by_field('order_id', $order_id, 'rest_order_taxes');
		
		$data = array(
		'order_id' => $order_id,
		'restaurant' => $restaurant,
		'user' => $user_info,
		'order_items' => $order_items,
		'order_detail' => $order_detail[0],
		'order_taxes' => $order_taxes,
		'user_info_flag' => $user_info_flag
		);
		
		$sms_body = $this->CI -> load -> view('orders/order_sms', $data, TRUE);
		
		return $sms_body;
	}
	
	function genereate_order_received_sms_body($order_id)
	{
		$this->CI = &get_instance();
		$order_detail = $this->CI->mdl_home->get_data_by_id($order_id, 'rest_delivery_order');
		$rest_id = $order_detail[0]->rest_id;
		$user_id = $order_detail[0]->user_id;
		
		$restaurant['basic'] = $this->CI->mdl_desktop->get_restaurant_info($rest_id);
        $restaurant['contact'] = $this->CI->mdl_desktop->get_restaurant_contact_details($rest_id);
		$user_info = $this->CI->mdl_desktop->get_user_location_info($order_id, $user_id);
		
		$order_items = $this->CI->mdl_home->get_ordered_items($order_id);
		
		$order_taxes = $this->CI->mdl_home->get_data_by_field('order_id', $order_id, 'rest_order_taxes');
		
		$data = array(
		'order_id' => $order_id,
		'restaurant' => $restaurant,
		'user' => $user_info,
		'order_items' => $order_items,
		'order_detail' => $order_detail[0],
		'order_taxes' => $order_taxes
		);
		
		$sms_body = $this->CI -> load -> view('orders/order_received_sms', $data, TRUE);
		
		return $sms_body;
		
	}

}

?>