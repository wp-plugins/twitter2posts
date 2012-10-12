<?php 

add_filter( 'plugin_action_links_twitter2posts/twitter2posts.php', 't2p_plugin_manage_link', 10, 4 );

function t2p_plugin_manage_link( $actions, $plugin_file, $plugin_data, $context ) {
	
	// add a 'Configure' link to the front of the actions list for this plugin
	return array_merge( array( 'configure' => '<a href="' . admin_url( 'options-general.php?page=t2p_settings_page' ) . '">' . __( 'Settings' ) . '</a>' ), 
                            $actions );		
}
       
function t2p_getTextBetweenTags($content) {
	$foo = array();
	$foo[0] = array();
	
	preg_match_all("#<a.*?>([^<]+)</a>#", $content, $foo);
	if(isset($foo['0']['0'])){
		return $foo[0][0];
	}
}

function t2p_unshorten_url($url) {
  $ch = curl_init($url);
  curl_setopt_array($ch, array(
	CURLOPT_FOLLOWLOCATION => TRUE,  // the magic sauce
	CURLOPT_RETURNTRANSFER => TRUE,
	CURLOPT_SSL_VERIFYHOST => FALSE, // suppress certain SSL errors
	CURLOPT_SSL_VERIFYPEER => FALSE, 
  ));
  curl_exec($ch); 
  return curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
}

function t2p_get_category_id($catname){
	global $wpdb;
	$catid = $wpdb->get_row(" SELECT term_id, name FROM ".$wpdb->terms." WHERE name = '".$catname."' ");	
	return $catid->term_id;
}

function t2p_create_category_if_not_exist($category_name, $echo = false) {
    return $id = wp_insert_term( $category_name, 'category');
}

function t2p_remove_accent($str)
{
$a = array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','ÿ','Ā','ā','Ă','ă','Ą','ą','Ć','ć','Ĉ','ĉ','Ċ','ċ','Č','č','Ď','ď','Đ','đ','Ē','ē','Ĕ','ĕ','Ė','ė','Ę','ę','Ě','ě','Ĝ','ĝ','Ğ','ğ','Ġ','ġ','Ģ','ģ','Ĥ','ĥ','Ħ','ħ','Ĩ','ĩ','Ī','ī','Ĭ','ĭ','Į','į','İ','ı','Ĳ','ĳ','Ĵ','ĵ','Ķ','ķ','Ĺ','ĺ','Ļ','ļ','Ľ','ľ','Ŀ','ŀ','Ł','ł','Ń','ń','Ņ','ņ','Ň','ň','ŉ','Ō','ō','Ŏ','ŏ','Ő','ő','Œ','œ','Ŕ','ŕ','Ŗ','ŗ','Ř','ř','Ś','ś','Ŝ','ŝ','Ş','ş','Š','š','Ţ','ţ','Ť','ť','Ŧ','ŧ','Ũ','ũ','Ū','ū','Ŭ','ŭ','Ů','ů','Ű','ű','Ų','ų','Ŵ','ŵ','Ŷ','ŷ','Ÿ','Ź','ź','Ż','ż','Ž','ž','ſ','ƒ','Ơ','ơ','Ư','ư','Ǎ','ǎ','Ǐ','ǐ','Ǒ','ǒ','Ǔ','ǔ','Ǖ','ǖ','Ǘ','ǘ','Ǚ','ǚ','Ǜ','ǜ','Ǻ','ǻ','Ǽ','ǽ','Ǿ','ǿ');
$b = array('A','A','A','A','A','A','AE','C','E','E','E','E','I','I','I','I','D','N','O','O','O','O','O','O','U','U','U','U','Y','s','a','a','a','a','a','a','ae','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','o','u','u','u','u','y','y','A','a','A','a','A','a','C','c','C','c','C','c','C','c','D','d','D','d','E','e','E','e','E','e','E','e','E','e','G','g','G','g','G','g','G','g','H','h','H','h','I','i','I','i','I','i','I','i','I','i','IJ','ij','J','j','K','k','L','l','L','l','L','l','L','l','l','l','N','n','N','n','N','n','n','O','o','O','o','O','o','OE','oe','R','r','R','r','R','r','S','s','S','s','S','s','S','s','T','t','T','t','T','t','U','u','U','u','U','u','U','u','U','u','U','u','W','w','Y','y','Y','Z','z','Z','z','Z','z','s','f','O','o','U','u','A','a','I','i','O','o','U','u','U','u','U','u','U','u','U','u','A','a','AE','ae','O','o');
return str_replace($a, $b, $str);
}

function t2p_post_slug($str)
{
return strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array('', '-', ''), t2p_remove_accent($str)));
}
