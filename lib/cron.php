<?php
		//header('Content-type: text/plain');
	 			
		global $wpdb;
		
		$time = time();
		
		$next_time_to_sync = get_option('t2p_last_import')+(get_option('t2p_delay')*60);
		
		if(($next_time_to_sync<=$time && get_option("t2p_turnon") == 1) or (!empty($_POST["t2p_turnon"]))){
		
		update_option( 't2p_last_import', $time );
		
		
	    $doc = new DOMDocument();

 			# load the RSS -- replace 'lylo' with your user of choice

 			if($doc->load('http://api.twitter.com/1/statuses/user_timeline.rss?screen_name='.get_option("t2p_name").'')) {
 		
 			# number of <li> elements to display.  20 is the maximum

 			$max_tweets = $_T2P_MAX_TWEETS_FEED;

	 		$i = 1;

			foreach ($doc->getElementsByTagName('item') as $node) {

 			# fetch the title from the RSS feed.

 			# Note: 'pubDate' and 'link' are also useful (I use them in the sidebar of this blog)

 			$tweet = $node->getElementsByTagName('title')->item(0)->nodeValue;
 			$link = $node->getElementsByTagName('link')->item(0)->nodeValue;
 			
 			$unix_time = $node->getElementsByTagName('pubDate')->item(0)->nodeValue;
 	        $unix_time = strtotime($unix_time);
 			
 			# <link> username </link> | tweet message
		
			
 			$tweet_without_tags = strip_tags($tweet);
 		
 			
			
			
			//coz WP autoreplacin interprets like a comma ' to ’ on title, we dont need duplicities 
			$tweet_without_tags_special = str_replace("'", "’", $tweet_without_tags); 
			
				$finder = $wpdb->get_results(" SELECT * FROM ".$wpdb->posts." WHERE post_name = '".t2p_post_slug($tweet_without_tags_special)."' && (post_status = 'publish' || post_status = 'trash') ");
			
				if($finder){
					
					// This Tweet is allready in database, so dont add that shit agan!
					
					}else{
					
					$t_co = preg_replace('@(https?://(t.co)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', '<a href="$1">$1</a>', $tweet);
					$ext_hyperlink = t2p_unshorten_url(strip_tags(t2p_getTextBetweenTags($t_co)));
					$tweet = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', '<a href="$1">$1</a>', $tweet);
					$tweet = preg_replace("/@([0-9a-zA-Z_]+)/", "<a href=\"http://twitter.com/$1\">@$1</a>", $tweet);
					$tweet_without_username = substr($tweet, stripos($tweet, ':') + 1);
					$tweet_username = str_replace($tweet_without_username, "", $tweet);
					$tweet = '<a href="'.$link.'" target="_blank">'.$tweet_username.'</a> '.$tweet_without_username;
					
					//echo $tweet_without_tags_special;
					$date = get_date_from_gmt(date('Y-m-d H:i:s', $unix_time));
					$gmt_date = date('Y-m-d H:i:s', $unix_time);
					
					
					$wpdb->insert( 
						$wpdb->posts, 
						array( 
							'post_date' => $date, 
							'post_date_gmt' => $gmt_date, 
							'post_content' => $tweet,
							'post_title' => $tweet_without_tags_special,
							'post_name' => t2p_post_slug($tweet_without_tags_special),
							'post_status' => 'publish',
							'post_author' => 1 
						) 
					);		
					$last_id = $wpdb->insert_id;
					$set_format = set_post_format($last_id, 'status');
					wp_set_post_terms($last_id,array(t2p_get_category_id(get_option('t2p_category'))),'category');
						
					add_post_meta($last_id, 'ext_link', $ext_hyperlink);
					
				if($i++ >= $max_tweets) break;
	
				}
			}


 			} 
		}	
			
			

