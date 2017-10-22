<?php

	set_time_limit(7200);
	
	require_once('config.php');

	$secret_key		=	substr( sha1($config['secret_key'].$config['email']), 0, 10 );
	$file_name3		=	"expirecache-{$secret_key}.txt";
	$file_path3		=	"data/{$file_name3}";
	if ( file_exists($file_path3) == FALSE ) { file_put_contents($file_path3, '1'); }
	$last_cache		=	trim( file_get_contents($file_path3) );
	$min_cache		=	time()-43200;
	if ($last_cache <= $min_cache)
	{
		$user_agents		=	[
			"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36",
			"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36",
			"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Safari/604.1.38",
			"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:55.0) Gecko/20100101 Firefox/55.0",
		];

		$file_name		=	"reviewsites-".date('Y-m')."-{$secret_key}.csv";
		$file_path		=	"data/{$file_name}";
		$handle			=	fopen($file_path, "a");
		if ( filesize($file_path) < 32 )
		{
			$field_names = ['Date', 'Time', 'Review Site', 'Average Score', 'Relative Score', 'Review Count'];
			fputcsv($handle, $field_names);
		}

		$reviews=array();
		$worst_relative=array();
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
				$relative_percent = round($relative_decimal * 100);
				
				$reviews[$site['name']]['relative_rating'] = $relative_percent;
			}
			else { continue; }
			
			if ( preg_match($site['count_regex'], $html, $count_data) )
			{
				$reviews[$site['name']]['count'] = $count_data[1];
			}
			
			$data_row = [ date('Y-m-d'), date('H:i:s'), $site['name'], $reviews[$site['name']]['rating'], $reviews[$site['name']]['relative_rating'], $reviews[$site['name']]['count'] ];
			fputcsv($handle, $data_row);
			
			if ( array_key_exists('score', $worst_relative) )
			{
				if ( $worst_relative['score'] > $reviews[$site['name']]['relative_rating'] )
				{
					$worst_relative = [ 'site' => $site['name'], 'score' => $reviews[$site['name']]['relative_rating'] ];
				}
			}
			else
			{
				$worst_relative = [ 'site' => $site['name'], 'score' => $reviews[$site['name']]['relative_rating'] ];
			}
			
		}
		
		fclose($handle);
	
		foreach ($review_sites as $get_url)
		{
			if ( $get_url['name'] == $worst_relative['site'] )
			{
				$review_url = $get_url['write_url'];
			}
		}
	
		$file_name2		=	"nextreview-{$secret_key}.txt";
		$file_path2		=	"data/{$file_name2}";
		$handle2 = fopen($file_path2, 'w');
		fwrite($handle2, $worst_relative['site']."|".$review_url);
		fclose($handle2);
		
		$handle3 = fopen($file_path3, 'w');
		fwrite($handle3, time());
		fclose($handle3);
	}
	
	if ( isset($_GET['img']) )
	{
		header('Content-type: image/png');
		exit;
	}
	else
	{
		//echo json_encode($reviews)."\n".json_encode($worst_relative);
		exit;
	}
