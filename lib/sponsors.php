<?php
			echo '<ul id="sponsors" style="">';
			echo '<li><strong>Sponsors</strong> &nbsp;</li>';
			
		    $doc = new DOMDocument();

 			# load the RSS -- replace 'lylo' with your user of choice

 			if($doc->load('http://search.twitter.com/search.rss?q=%23twitter2posts%23sponsors&rpp=50')) {

 			
 			# number of <li> elements to display.  20 is the maximum

 			$max_tweets = 10;

 			$i = 1;

 			foreach ($doc->getElementsByTagName('item') as $node) {

 			# fetch the title from the RSS feed.

 			# Note: 'pubDate' and 'link' are also useful (I use them in the sidebar of this blog)

 			$tweet = $node->getElementsByTagName('title')->item(0)->nodeValue;
 			
 			$link = $node->getElementsByTagName('link')->item(0)->nodeValue;
 			
 			
			$unix_time = $node->getElementsByTagName('pubDate')->item(0)->nodeValue;
 	        $unix_time = strtotime($unix_time);
 			
			# OPTIONAL: turn URLs into links

			$tweet_find = strpos($link,"twitter.com/ondrejdadok");
			$tweet = str_replace("#sponsors","", $tweet);
			$tweet = str_replace("#twitter2posts","", $tweet);
			
 			# OPTIONAL: turn @replies into links

 			$tweet = preg_replace("/@([0-9a-zA-Z]+)/",

 			"<a href=\"http://twitter.com/$1\">@$1</a>", $tweet);
			
			if($tweet_find === false){
			}else{
			$i++;
 			?>
            <li><?php if($i!=2){?>, <?php } ?><?php echo  $tweet; ?> </li>
            <?php         
			}
 			

 			if($i++ >= $max_tweets) break;

 			}

}
echo '<li>&nbsp;&nbsp;<strong>Thank You!</strong></li></ul>';
?>