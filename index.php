<?php
	
	require_once('config.php');
	
	if ( TRUE == isset($_POST['rating']) )
	{
		$rating		=	(int)$_POST['rating'];
		$min_stars	=	(int)$config['min_stars'];
		if ( $rating < $min_stars )
		{
			print "Bad Review: {$rating} out of 5 (minimum of {$min_stars}).";
		}
		else
		{
			print "Good Review: {$rating} out of 5 (minimum of {$min_stars}).";
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
				if ( preg_match("/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD", $data) )
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