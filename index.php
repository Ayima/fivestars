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
				$email = send_feedback();
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
	
	function send_feedback()
	{
		global $_POST;
		$to			=	$config['email'];
		$from		=	( isset($_POST['customer_email']) ) ? cleaned($_POST['customer_email'], 'email') : $config['email'];
		$subject	=	"Customer Feedback - {$_POST['rating']}/5 Star Rating";
		$vars		=	"\n== Customer Details ==\n";
		foreach ($_POST as $key => $value)
		{
			if ( $key == 'feedback' ) { continue; }
			$vars	.=	ucwords(str_replace('_', '', $key)).":\t{$value}\n";
		}
		$message	=	"\n== Feedback ==\n{$_POST['feedback']}\n{$vars}\n";
		$headers	=	"From: {$from}\r\nReply-To: {$from}\r\nX-Mailer: Ayima/fivestars";
		mail($to, $subject, $message, $headers);
	}