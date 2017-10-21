<?php

	set_time_limit(7200);
	
	require_once('config.php');

	$user_agents		=	[
		"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36",
		"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36",
		"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Safari/604.1.38",
		"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:55.0) Gecko/20100101 Firefox/55.0",
		"Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0; Trident/5.0)",
	];
	
	$reviews=array();
	foreach ($review_sites as $site)
	{
		if ( !filter_var($site['scrape_url'], FILTER_VALIDATE_URL) ) { continue; }
		
		$ua_rand		=	array_rand($user_agents);
		$user_agent		=	$user_agents[$ua_rand];
		$options		=	array('http' => array('user_agent' => $user_agent));
		$context		=	stream_context_create($options);
		$html			=	file_get_contents($site['scrape_url'], false, $context);
		
		if ( preg_match($site['stars_regex'], $html, $stars_data) )
		{
			$reviews[$site['name']]['rating'] = $stars_data[1];
			
			$relative_decimal = $stars_data[1] / $site['max_score'];
			$relative_percent = $relative_decimal * 100;
			
			$reviews[$site['name']]['relative_rating'] = $relative_percent;
		}
		else { continue; }
		
		if ( preg_match($site['count_regex'], $html, $count_data) )
		{
			$reviews[$site['name']]['count'] = $count_data[1];
		}

	}


	if ( isset($_GET['img']) )
	{
		header('Content-type: image/png');
		exit;
	}
	else
	{
		return var_dump($reviews);
		exit;
	}