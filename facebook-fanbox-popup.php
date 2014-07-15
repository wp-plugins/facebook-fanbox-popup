<?php
/*
Plugin Name: Facebook FanBox Popup
Plugin URI: https://wordpress.org/plugins/facebook-fanbox-popup/
Description: Promote your Fanpage in a cool natural way
Version: 1.0
Author: iLen
Author URI: 
*/
if ( !class_exists('facebook_fanbox_popup') ) {
require_once 'assets/functions/options.php';
class facebook_fanbox_popup extends facebook_fanbox_popup_make{

	public $parameter 		= array();
	public $options 		= array();
	public $components		= array();

	function __construct(){

		parent::__construct(); // configuration general



 		if( is_admin() ){
            
            add_action( 'admin_enqueue_scripts', array( &$this,'fanbox_popup_admin' ) );

        }elseif( ! is_admin() ) {
            global $opt_fanbox_popup;
            $opt_fanbox_popup = get_option( $this->parameter['name_option']."_options" ) ;


            if( $opt_fanbox_popup[$this->parameter['name_option'].'_enabled'] ){

			        self::add_actions_FacebookFanBox();

            }

        }



	}



 
	//--PLUGIN------------------------ ---------------
	function print_scripts(){
		//code 
		
		global $opt_fanbox_popup,$print_script;

		$array_show_in = $opt_fanbox_popup[$this->parameter['name_option'].'_show_in'];
		
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
		$credit = $opt_fanbox_popup[$this->parameter['name_option'].'_credits'];

		if( $print_script ){
		?>							
		<script type="text/javascript">
			jQuery(document).ready(function($){
					
			setTimeout( 
			function(){				
				facebook_fanbox_popup({
					// Configure display of popup
					s_to_close: "<?php echo $opt_fanbox_popup[$this->parameter['name_option'].'_seconds_close']; ?>",
					days_no_click: "<?php echo $opt_fanbox_popup[$this->parameter['name_option'].'_until_popup']; ?>",
				})
			}
				,<?php echo (int)$opt_fanbox_popup[$this->parameter['name_option'].'_seconds_appear'] * 1000 ;?>
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
		$credit = $opt_fanbox_popup[$this->parameter['name_option'].'_credits'];
 
 		if( $print_script ){
		


 		$fb_id = $opt_fanbox_popup[$this->parameter['name_option'].'_fb_id']?$opt_fanbox_popup[$this->parameter['name_option'].'_fb_id']:"https://www.facebook.com/FarandulaEcuatoriana";
		echo '<div id="ffbp-bg"></div>
				<div id="ffbp">
				<a href="#" onClick="fbfanboxp('. $opt_fanbox_popup[$this->parameter['name_option'].'_until_popup'] .');" id="ffbp-close">X</a>';
				echo '<div id="ffbp-body">';
				echo '<div id="ffbp-msg-cont">
						    <div class="fb-like-box" data-href="'.$fb_id.'" data-colorscheme="light" data-show-faces="true" data-header="false" data-stream="false" data-show-border="false"></div>
					  </div>';
		echo ( $credit ) ? '<div id="spu-bottom"><span style="font-size:10px;float: right;margin-top: -6px;">Social PopUP by <a href="http://www.timersys.com">Timersys</a></span></div>':'';
				echo "</div>";
		echo '</div>';
		echo "<input type='hidden' name='hd_msg_thanks' id='hd_msg_thanks' value='".$opt_fanbox_popup[$this->parameter['name_option'].'_thanks_message']."' />";

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