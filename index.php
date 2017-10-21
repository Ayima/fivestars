<?php
	
	require_once('config.php');
	
	if ( TRUE == isset($_POST['rating']) )
	{
		$rating		=	(int)$_POST['rating'];
		$min_stars	=	(int)$config['min_stars'];
		if ( $rating < $min_stars )
		{
			if ( isset($_POST['feedback']) )
			{
				$to			=	$config['email'];
				$from		=	( $_POST['customer_email'] !== '' ) ? cleaned($_POST['customer_email'], 'email') : $config['email'];
				$subject	=	"Customer Feedback - {$_POST['rating']}/5 Star Rating";
				$vars		=	"\n== Customer Details ==\n";
				foreach ($_POST as $key => $value)
				{
					if ( $key == 'feedback' ) { continue; }
					$vars	.=	ucwords(str_replace('_', ' ', $key)).":\t{$value}\n";
				}
				$message	=	"\n== Feedback ==\n{$_POST['feedback']}\n{$vars}\n";
				$headers	=	"From: {$from}\r\nReply-To: {$from}\r\nX-Mailer: Ayima/fivestars";
				mail($to, $subject, $message, $headers);
				save_rating();
				print templated('unhappy-sent');
			}
			else
			{
				$config['rating']			=	( isset($_POST['rating']) ) ? cleaned($_POST['rating'], 'rating') : 0;
				$config['customer_id']		=	( isset($_POST['customer_id']) ) ? cleaned($_POST['customer_id']) : '';
				$config['order_id']			=	( isset($_POST['order_id']) ) ? cleaned($_POST['order_id']) : '';
				$config['customer_name']	=	( isset($_POST['customer_name']) ) ? cleaned($_POST['customer_name'], 'name') : '';
				$config['customer_email']	=	( isset($_POST['customer_email']) ) ? cleaned($_POST['customer_email'], 'email') : '';
				print templated('unhappy');
			}
		}
		else
		{
			print templated('happy');
		}
	}
	else
	{
		$config['rating']			=	( isset($_GET['rating']) ) ? cleaned($_GET['rating'], 'rating') : 0;
		$config['customer_id']		=	( isset($_GET['customer_id']) ) ? cleaned($_GET['customer_id']) : '';
		$config['order_id']			=	( isset($_GET['order_id']) ) ? cleaned($_GET['order_id']) : '';
		$config['customer_name']	=	( isset($_GET['customer_name']) ) ? cleaned($_GET['customer_name'], 'name') : '';
		$config['customer_email']	=	( isset($_GET['customer_email']) ) ? cleaned($_GET['customer_email'], 'email') : '';
		
		print templated();
	}
	
	function templated( $file='index' ) {
		global $config;
		$file = "views/{$file}.html";
		if (!file_exists($file)) {
			return "Error loading template file ({$file}.html)";
		}
		$output = file_get_contents( $file );

		foreach ($config as $key => $value) {
			$tagToReplace = "[@$key]";
			$output = str_replace($tagToReplace, $value, $output);
		}
		return $output;
	}
	
	function cleaned($data, $type='other')
	{
		switch ($type)
		{
			case "email" :
				if ( filter_var($data, FILTER_VALIDATE_EMAIL) )
				{
					return $data;
				}
				else
				{
					return preg_replace("/[^A-Za-z0-9\@\.\-\_]/", '', $data);
				}
			break;
			
			case "rating" :
				if ( preg_match("/^[0-5]$/i", $data) )
				{
					return $data;
				}
				else
				{
					return preg_replace("/[^0-5]/", '', $data);
				}
			break;
			
			case "name" :
				return preg_replace("/[^A-Za-z -']/", '', $data);
			break;
			
			default:
				return preg_replace("/[^A-Za-z0-9\/\-']/", '', $data);
			break;
		}
	}
	
	function save_rating()
	{
		global $_POST;
		global $config;
		
		$secret_key		=	substr( sha1($config['secret_key'].$config['email']), 0, 10 );
		$file_name		=	"ratings-".date('Y-m')."-{$secret_key}.csv";
		$file_path		=	"data/{$file_name}";
		$handle			=	fopen($file_path, "a");
		
		if ( filesize($file_path) < 32 )
		{
			$field_names = ['Date', 'Time', 'Star Rating', 'Customer ID', 'Order ID', 'Customer Name', 'Customer Email', 'Customer IP', 'Redirected To', 'Feedback'];
			fputcsv($handle, $field_names);
		}
		if ( !isset($config['redirect_to']) ) { $config['redirect_to'] = 'Feedback Form'; }
		if ( !isset($_POST['feedback']) ) { $_POST['feedback'] = ''; }
		$data_row = [date('Y-m-d'), date('H:i:s'), $_POST['rating'], $_POST['customer_id'], $_POST['order_id'], $_POST['customer_name'], $_POST['customer_email'], $_SERVER['REMOTE_ADDR'], $config['redirect_to'], $_POST['feedback']];
		fputcsv($handle, $data_row);
		
		fclose($handle);
	}
	
