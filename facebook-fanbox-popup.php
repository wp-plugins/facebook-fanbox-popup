<?php
/*
Plugin Name: Facebook FanBox Popup
Plugin URI: https://wordpress.org/plugins/facebook-fanbox-popup/
Description: Promote your Fanpage in a cool natural way
Version: 2.1
Author: iLen
Author URI:
*/
if ( !class_exists('facebook_fanbox_popup') ) {
require_once 'assets/functions/options.php';
class facebook_fanbox_popup extends facebook_fanbox_popup_make{
 
	function __construct(){

		parent::__construct(); // configuration general



 		if( is_admin() ){
            
            add_action( 'admin_enqueue_scripts', array( &$this,'fanbox_popup_admin' ) );

        }elseif( ! is_admin() ) {

        	// get utils: IF_get_option
      		require_once plugin_dir_path( __FILE__ )."assets/ilenframework/assets/lib/utils.php";
          global $opt_fanbox_popup;
          $opt_fanbox_popup = IF_get_option( $this->parameter['name_option'] );


          if( isset($opt_fanbox_popup->enabled) && $opt_fanbox_popup->enabled ){

		        self::add_actions_FacebookFanBox();

          }

        }



	}



 
	//--PLUGIN------------------------ ---------------
	function print_scripts(){
		//code 
		
		global $opt_fanbox_popup,$print_script;

		$array_show_in = $opt_fanbox_popup->show_in;
		
		$print_script = false;

		if( in_array( 'everywhere', $array_show_in ) ){
 
				$print_script = true;

		}
		if( in_array( 'post' , $array_show_in ) && $print_script == false ){

			if( is_single() ){

				$print_script = true;

			}

		}
		if( in_array( 'page' , $array_show_in ) && $print_script == false ){

			if( is_page() ){

				$print_script = true;

			}

		}
		if( in_array( 'home' , $array_show_in ) && $print_script == false ){

			if( is_home() || is_front_page() ){

				$print_script = true;

			}

		}


		if( $print_script == true ) {

            	add_action('wp_enqueue_scripts', array( &$this,'fanbox_popup_front_script') );

        }
 
	}


	

 



	/**
	* Load scripts and styles
	*/
	function fanbox_popup_front_script(){
			wp_enqueue_script('wsp-fb', 'http://connect.facebook.net/en_US/all.js#xfbml=1', array('jquery'),$this->parameter['version'],FALSE);
			wp_enqueue_script('wsp-fanbox', plugins_url( 'assets/js/spu.js' , __FILE__ ),array('jquery'),$this->parameter['version']);
			wp_enqueue_style('wsp-css-fanbox', plugins_url( 'assets/css/spu.css' , __FILE__ ),'all',$this->parameter['version']);
	}

	/**
	* Load scripts and styles in footer
	*/
	function print_scripts_footer(){

		global $opt_fanbox_popup,$print_script;
		//$credit = $opt_fanbox_popup->credits;

		if( $print_script ){
		?>							
		<script type="text/javascript">
			jQuery(document).ready(function($){
					
			setTimeout( 
			function(){				
				facebook_fanbox_popup({
					// Configure display of popup
					s_to_close: "<?php echo isset($opt_fanbox_popup->seconds_close)?$opt_fanbox_popup->seconds_close:0; ?>",
					days_no_click: "<?php echo isset($opt_fanbox_popup->until_popup)?$opt_fanbox_popup->until_popup:0; ?>",
					disabled_scroll: <?php echo (isset($opt_fanbox_popup->disabled_scroll) && $opt_fanbox_popup->disabled_scroll )?$opt_fanbox_popup->disabled_scroll:0; ?>,
					stream: <?php echo isset($opt_fanbox_popup->show_post) && $opt_fanbox_popup->show_post?1:0; ?>
				})
			}
				,<?php echo (int)$opt_fanbox_popup->seconds_appear * 1000 ;?>
					);
			});	
		</script>
		<?php
		}
	}

    function fanbox_popup_admin(){
        wp_enqueue_script( 'facebook_fanbox_popup_js', plugins_url('/assets/js/plugin.js',__FILE__), array( 'jquery' ), '1.0', true );
        wp_enqueue_style( 'facebook_fanbox_popup_css_admin', plugins_url('/assets/css/admin.css',__FILE__),'all',$this->parameter['version']);
    }

    /**
	* Print popup html markup in footer
	*/
	function print_pop()
	{
		global $opt_fanbox_popup,$print_script;
		//$credit = $opt_fanbox_popup->credits;
 
 		if( $print_script ){
		


 		$fb_id = isset($opt_fanbox_popup->fb_id) && $opt_fanbox_popup->fb_id?$opt_fanbox_popup->fb_id:"https://www.facebook.com/FarandulaEcuatoriana";
 		$show_post = isset($opt_fanbox_popup->show_post) && $opt_fanbox_popup->show_post?"true":"false";
 		$height = $show_post == 'true'?"data-height='500'":"";
		echo ' <!-- Plugin: Facebook FanBox Popup (https://wordpress.org/plugins/facebook-fanbox-popup/) --><br />
				<div id="ffbp-bg" data-version="'.$this->parameter["version"].'"></div>
				<div id="ffbp">
				<a href="#" onClick="fbfanboxp('. $opt_fanbox_popup->until_popup .');" id="ffbp-close">✕</a>';
				echo '<div id="ffbp-body">';
				echo '<div id="ffbp-msg-cont">
						    <div class="fb-like-box" data-href="'.$fb_id.'" data-colorscheme="light" data-show-faces="true" data-header="false" data-stream="'.$show_post.'" data-show-border="false" '.$height.'></div>
					  </div>';
				echo "</div>";
		echo '</div>';
		//echo "<input type='hidden' name='hd_msg_thanks' id='hd_msg_thanks' value='".$opt_fanbox_popup->thanks_message."' />";

		}
	}



	function is_MobileOrTable(){
		

		require_once "assets/ilenframework/assets/lib/Mobile_Detect.php";

		$detect = new Mobile_Detect;

		if( $detect->isMobile() || $detect->isTablet() )
		 	return true;
		else
			return false;

	}

	function add_actions_FacebookFanBox(){
		global $print_script;

			add_action('template_redirect', array(&$this,'print_scripts') );
			add_action( 'wp_footer',array(&$this,'print_pop' ) );	
			add_action( 'wp_footer', array( &$this,'print_scripts_footer'));
			

	}




} // end class
} // end if

global $IF_CONFIG;
unset($IF_CONFIG);
$IF_CONFIG = null;
$IF_CONFIG = new facebook_fanbox_popup;
require_once "assets/ilenframework/core.php";
?>