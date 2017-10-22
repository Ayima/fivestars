<?php
	
	$config	=	[
		
		"company"			=>		"Acme Inc", // Your company/website name
		"email"				=>		"fivestars@ayima.tech", // Your email address (where complaints and ratings are sent)
		
		"min_stars"			=>		4, // Minimum number of stars needed to redirect to a review site
		
		"promo_title"		=>		"Insert A Headline Here", // Promo Title - Entice people to rate you
		"promo_content"		=>		"This content can be changed to whatever you wish in the config.php file", // Promo Content
		
		"secret_key"		=>		"Insert a random password/string here", // Used to make your data file URLs unguessable

	];
	
	$review_sites = [
		
		[
			"name"			=>		"Google",
			"write_url"		=>		"https://search.google.com/local/writereview?placeid=ChIJ8RDv9VMbdkgRKkRJmykvEEM", // URL to write review. e.g. for Google: https://support.google.com/business/answer/7035772?hl=en-GB
			"scrape_url"	=>		"https://www.google.co.uk/search?q=The+Old+Red+Cow,+71+Long+Ln,+London+EC1A+9EJ,+UK", // URL to scrape star rating and count
			"max_score"		=>		5, // The maximum score/star rating on this review site (normally 5)
			"stars_regex"	=>		"/\<span.*?class\=\"rtng\".*?\>.*?([0-9.]{1,4}).*?\</imx", // RegEx pattern to extract the stars score
			"count_regex"	=>		"/\<span\>([0-9,]+).*?reviews.*?\</imx", // RegEx pattern to extract the reviews counts
		],
		
		[
			"name"			=>		"Facebook",
			"write_url"		=>		"https://www.facebook.com/pg/oldredcow/reviews/", // URL to write review. e.g. for Google: https://support.google.com/business/answer/7035772?hl=en-GB
			"scrape_url"	=>		"https://www.facebook.com/pg/oldredcow/reviews/", // URL to scrape star rating and count
			"max_score"		=>		5, // The maximum score/star rating on this review site (normally 5)
			"stars_regex"	=>		"/\<script.*?ld\+json.*?\>.*?aggregateRating.*?ratingValue[\"': ]+([0-9.]{1,4}).*?\</imx", // RegEx pattern to extract the stars score
			"count_regex"	=>		"/\<script.*?ld\+json.*?\>.*?aggregateRating.*?ratingCount[\"': ]+([0-9,]{1,8}).*?\</imx", // RegEx pattern to extract the reviews counts
		],
		
		[
			"name"			=>		"DesignMyNight",
			"write_url"		=>		"https://www.designmynight.com/london/pubs/city-of-london/old-red-cow#!#reviews", // URL to write review. e.g. for Google: https://support.google.com/business/answer/7035772?hl=en-GB
			"scrape_url"	=>		"https://www.designmynight.com/london/pubs/city-of-london/old-red-cow", // URL to scrape star rating and count
			"max_score"		=>		5, // The maximum score/star rating on this review site (normally 5)
			"stars_regex"	=>		"/itemprop.*?=[\"' ]*?ratingValue[\"' ]*?content[\"' ]*?\=[\"' ]*?([0-9.]{1,4})[\"' ]*?/imx", // RegEx pattern to extract the stars score
			"count_regex"	=>		"/itemprop.*?=[\"' ]*?ratingCount[\"' ]*?content[\"' ]*?\=[\"' ]*?([0-9,]{1,8})[\"' ]*?/imx", // RegEx pattern to extract the reviews counts
		],
		
		[
			"name"			=>		"Foursquare",
			"write_url"		=>		"https://foursquare.com/v/the-old-red-cow/4ada5cf0f964a520e32121e3", // URL to write review. e.g. for Google: https://support.google.com/business/answer/7035772?hl=en-GB
			"scrape_url"	=>		"https://foursquare.com/v/the-old-red-cow/4ada5cf0f964a520e32121e3", // URL to scrape star rating and count
			"max_score"		=>		10, // The maximum score/star rating on this review site (normally 5)
			"stars_regex"	=>		"/ratingValue[\"' ]*?\>.*?([0-9.]{1,4}).*?\</imx", // RegEx pattern to extract the stars score
			"count_regex"	=>		"/ratingCount[\"' ]*?\>.*?([0-9,]{1,8}).*?\</imx", // RegEx pattern to extract the reviews counts
		],
		
	];