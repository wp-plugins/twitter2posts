<?php
/*
Plugin Name: Twitter2Posts
Plugin URI: http://www.ondrejdadok.cz/twitter2posts/
Description: Simple Twitter timeline synchronization with your Posts Archive
Version: 1.1.1
Author: Ondřej Dadok
Author URI: http://www.ondrejdadok.cz
License: GPL2
*/

include "lib/functions.php";

$_T2P_PLUGIN_URL = plugins_url('', __FILE__);
$_T2P_DEFAULT_CATEGORY = "Twitter2Posts";
$_T2P_MAX_TWEETS_FEED = 20;

/* SET DEFAULT VALUES */
if(get_option('t2p_turnon') == ""){update_option('t2p_turnon', 0);}
if(get_option('t2p_last_import') == ""){update_option('t2p_last_import', '');}
if(get_option('t2p_delay') == ""){update_option('t2p_delay', 60);}
if(get_option('t2p_name') == ""){update_option('t2p_name', '');}

t2p_create_category_if_not_exist($_T2P_DEFAULT_CATEGORY);
update_option('t2p_category', $_T2P_DEFAULT_CATEGORY);

include "lib/cron.php"; 
//include "lib/menu.php"; 

function t2p_footer() {
	global $next_time_to_sync;
	global $time;
	if(get_option('t2p_turnon') == "1"){
		echo "<span id='footer-thankyou'><strong>Next Twitter2Posts sync:</strong> ".get_date_from_gmt(date("Y-m-d H:i:s", $next_time_to_sync), "d.m. Y H:i")."</span>";
	}
}

add_filter( 'admin_footer_text', 't2p_footer' );


// We need some CSS to position the paragraph
function t2p_footer_css() {
	
	echo "
	<style type='text/css'>
	#footer-thankyou {
		text-transform: uppercase;
		font-size: 90%;
	}
	#footer-thankyou strong{
		font-weight: normal;
		color: #a0a0a0;
	}
	</style>
	";
}

add_filter( 'update_footer', 't2p_footer_css' );



// create custom plugin settings menu
add_action('admin_menu', 't2p_create_menu');

function t2p_create_menu() {

	add_options_page( 'Twitter2Posts Settings', 'Twitter2Posts', 'manage_options', 't2p_settings_page', 't2p_settings_page' ); 
	//call register settings function
	add_action( 'admin_init', 't2p_settings' );
}


function t2p_settings() {
	global $time;
	//register our settings
	register_setting( 'baw-settings-group', 't2p_name' );
	register_setting( 'baw-settings-group', 't2p_category' );
	register_setting( 'baw-settings-group', 't2p_delay' );
	register_setting( 'baw-settings-group', 't2p_turnon' );
}
	
	
	function t2p_enqueue_styles() {
		wp_register_style( 't2p_style', plugins_url('in-admin-panel/style.css') );
		wp_enqueue_style( 't2p_style' );
	}
	add_action( 'admin_head', 't2p_enqueue_styles' );
	
	

function t2p_settings_page() {
	wp_enqueue_style( 'custom_wp_admin_css', plugins_url('in-admin-panel/style.css', __FILE__));
	
	global $time;
	global $next_time_to_sync;
	
?>         


<div class="wrap twitter2posts">

<div id="main_container">

<div class="header">
    <div class="logo"><a href="#"><img src="<?php echo plugins_url('in-admin-panel/', __FILE__) ?>images/logo.png" alt="" title="" border="0" /></a></div>
    
    <div class="right_header"><a href="http://www.ondrejdadok.cz" target="_blank" class="nounderline">© Ondřej Dadok, </a> <a href="http://www.ondrejdadok.cz" target="_blank">Visit site</a> <a href="mailto:info@ondrejdadok.cz" class="messages">Email me</a> <a href="#" class="beer" rel="modal-profile">Buy me a beer</a></div>
    <div id="clock_a"></div>
</div>
    
<div id="donate">
	
    

</div>

<div class="main_content">
               <div class="menu">
                    <ul>
                    <li><a href="#" class="current">Settings</a></li>
                    </ul>
               </div> 
                    
                    
    <div class="center_content">  
    
    <div class="right_content">            
        
         <div class="form">
         
     
     
    <script type="text/javascript">
     jQuery(document).ready(function () {
     	
     	jQuery.noConflict();
     	
     	// Position modal box in the center of the page
     	jQuery.fn.center = function () {
     		this.css("position","absolute");
     		this.css("top", ( jQuery(window).height() - this.height() ) / 2+jQuery(window).scrollTop() + "px");
     		this.css("left", ( jQuery(window).width() - this.width() ) / 2+jQuery(window).scrollLeft() + "px");
     		return this;
     	  }
     	
     	jQuery(".modal-profile").center();
     	
     	// Set height of light out div	
     	jQuery('.modal-lightsout').css("height", jQuery(document).height());	
     
     	// Fade in modal box once link is clicked
     	jQuery('a[rel="modal-profile"]').click(function() {
     		jQuery('.modal-profile').fadeIn("slow");
     		jQuery('.modal-lightsout').fadeTo("slow", .5);
     	});
     	
     	// closes modal box once close link is clicked, or if the lights out divis clicked
     	jQuery('a.modal-close-profile, .modal-lightsout').click(function() {
     		jQuery('.modal-profile').fadeOut("slow");
     		jQuery('.modal-lightsout').fadeOut("slow");
     	});
     
     
     
	    jQuery(".donate-table td").click(function() {
	    	//jQuery("#amount_cont").hide();
	       	jQuery(".donate-table td").removeClass("current-donate");
	    	jQuery(this).addClass("current-donate");
	    	jQuery("#amount").val(jQuery(".current-donate span.price strong").html());
	    	jQuery('#amount_cont *').css("color", "#bcdeec");
	    	 });
	   
	    jQuery(".customprice").click(function() {
	    	jQuery(".donate-table td").removeClass("current-donate");
	       	jQuery("#amount").val('').select();
	       	jQuery("#amount_cont").fadeIn();
	       	jQuery('#amount_cont *').css("color", "#fff");	
	    	 });
	      
     });
     
     
     </script>

<form method="post" action="options.php" class="niceform">

    <?php settings_fields( 'baw-settings-group' ); ?>
    <?php //do_settings( 'baw-settings-group' ); ?>
    
        <table class="form-table">
       
        <tr valign="top">
        <th scope="row">Turn On</th>
        <td><input type="checkbox" name="t2p_turnon" value="1" <?php if( get_option('t2p_turnon') == 1){ ?> checked<?php } ?> /></td>
        </tr>
      
      <tr valign="top">
          <th scope="row">Twitter username<br /> <small>Just write your twitter.com/<strong>username</strong></small></th>
          <td><input type="text" name="t2p_name" value="<?php echo get_option('t2p_name'); ?>" /></td>
          </tr>
          
     <tr valign="top">
           <th scope="row">Tweets Category<br /> <small>All imported Tweets will be Posts to this category</strong></small></th>
           <td><input type="text" name="t2p_category" value="<?php echo get_option('t2p_category'); ?>" disabled/></td>
           </tr>
       
        <tr valign="top">
        <th scope="row">Refresh delay<br /> <small>Delay to check your Twitter feed and import to Posts</small></th>
	    <td><select name="t2p_delay">
	        <option value="5"<?php if(get_option('t2p_delay') == 5){echo ' selected';} ?>>5 minutes</option>
	        <option value="10"<?php if(get_option('t2p_delay') == 10){echo ' selected';} ?>>10 minutes</option>
	        <option value="15"<?php if(get_option('t2p_delay') == 15){echo ' selected';} ?>>15 minutes</option>
	        <option value="30"<?php if(get_option('t2p_delay') == 30){echo ' selected';} ?>>30 minutes</option>
	        <option value="45"<?php if(get_option('t2p_delay') == 45){echo ' selected';} ?>>45 minutes</option>
	 	  	<option value="60"<?php if(get_option('t2p_delay') == 60){echo ' selected';} ?>>1 hour</option>
			<option value="120"<?php if(get_option('t2p_delay') == 120){echo ' selected';} ?>>2 hours</option>
			<option value="180"<?php if(get_option('t2p_delay') == 180){echo ' selected';} ?>>3 hours</option>
	    </select></td>
        </tr>
     
     <tr valign="top">
           <th scope="row"></th>
           <td><input type="submit" class="button-primary" value="<?php _e('Save & Sync') ?>" />
           </td>
           </tr>
     
        
    </table>
    
   <p class="advice"><img src="<?php echo plugins_url('images/clock.png', __FILE__) ?>" />
   PLEASE <strong>DONT FORGET</strong> TO <a href="options-general.php">SET YOUR LOCAL TIMEZONE</a> OTHERWISE YOUR IMPORTED TWEETS WILL NOT HAVE CORRECT TIME.
   </p>
  
   <p class="advice"><img src="<?php echo plugins_url('images/archive.png', __FILE__) ?>" />
   <strong>ADVICE</strong>: IF YOU TRASHED SOME TWEETS FROM POSTS, TWEETS WILL NOT BE IMPORTED AGAIN, COZ SOMETIMES WE DONT WANT TO SHOW ALL TWEETS TO OUR VISITORS. TO RECOVER ORIGINAL TWEETS PLEASE EMPTY TRASH OR RESTORE TRASHED TWEETS.
   
   </p>
   
  
</form>
         </div>  
      
     
     </div><!-- end of right content-->
            
                    
  </div>   <!--end of center content -->               
                    
                    
    
    
    <div class="clear"></div>
    </div>
    


</div>
</div>



<div class="modal-lightsout"></div>
<div class="modal-profile">
    <h2><strong>I love to develop plugins</strong> to you <strong>for free!</strong></h2>
    <p><strong>But if you like this plugin and want to give me some bounty</strong> or you wish to sponsor some specific feature, you're welcome to <strong>send me a donation</strong>. All sponsors are published on the plugin page at the bottom. <strong>Thank you!</strong></p>
    
    <a href="#" title="Close donation" class="modal-close-profile"><img src="<?php echo plugins_url('images/close.png', __FILE__) ?>" alt="Close donation" /></a>
    
    <a href="#" class="customprice">Custom amount</a>
    
    <table class="donate-table">
    <tr>
    	
    	<td>
    	   	<img src="<?php echo plugins_url('images/gambrinus.png', __FILE__) ?>">
    	   	<h3>GAMB</h3> <span class="price"><strong>0.5</strong>€</span>
    	</td>
    	<td>
    	   	<img src="<?php echo plugins_url('images/budweiser.png', __FILE__) ?>">
    	   	<h3>BUDVAR</h3>  <span class="price"><strong>0.5</strong>€</span>
    	</td>
    	<td>
	    	<img src="<?php echo plugins_url('images/heineken.png', __FILE__) ?>">
	    	<h3>HEINEKEN</h3> <span class="price"><strong>1.0</strong>€</span>
    	</td>
    	<td class="current-donate">
	    	<img src="<?php echo plugins_url('images/pilsner12.png', __FILE__) ?>">
	    	<h3>PLZEŇ</h3> <span class="price"><strong>2.0</strong>€</span>
    	</td>
    	    	
    	<td>
    		<img src="<?php echo plugins_url('images/stellaartois.png', __FILE__) ?>">
    		<h3>STELLA</h3> <span class="price"><strong>2.5</strong>€</span>
    
    	    </tr>
    </table>
    
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" id="donate_form">
	    <input type="hidden" name="cmd" value="_donations">
	    <input type="hidden" name="business" value="info@ondrejdadok.cz">
	    <input type="hidden" name="lc" value="US">
	    <input type="hidden" name="item_name" value="Twitter2Posts Donation">
	    <div id="amount_cont">
	    <input type="text" name="amount" value="2.0" id="amount" size="2" maxlength="4"> <span>€</span>
	    </div>
	    <input type="text" name="item_number" value="First & Last Name, message.." id="name" onclick="this.value=''">
	    <input type="hidden" name="no_note" value="0">
	    <input type="hidden" name="currency_code" value="EUR">
	    <input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_LG.gif:NonHostedGuest">
	    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" id="submit_donate">
	    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
    </form>
    
</div>

<?php
include "lib/sponsors.php";
} 