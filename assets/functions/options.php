<?php
/**
 * Options Plugin
 * Make configutarion
*/

if ( !class_exists('facebook_fanbox_popup_make') ) {

class facebook_fanbox_popup_make{

	public $parameter       = array();
	public $options         = array();
	public $components      = array();



	function __construct(){

		if( is_admin() )
			self::configuration_plugin();
		else
			self::parameters();

	}

	function getHeaderPlugin(){
		//code 

		global $wp_social_pupup_header_plugins;
		return array('id'             =>'facebook_fanbox_popup_id',
					 'id_menu'        =>'facebook_fanbox_popup',
					 'name'           =>'Facebook FanBox Popup',
					 'name_long'      =>'Facebook FanBox Popup',
					 'name_option'    =>'facebook_fanbox_popup',
					 'name_plugin_url'=>'facebook-fanbox-popup',
					 'descripcion'    =>'Promote your Fanpage in a cool natural way',
					 'version'        =>'3.83',
					 'url'            =>'',
					 'logo'           =>'<i class="fa fa-facebook text-long" style="padding:12px 18px;"></i>',
					  // or image .jpg,png | use class 'text-long' in case of name long
					 'logo_text'      =>'', // alt of image
					 'slogan'         =>'', // powered by <a href="">iLenTheme</a>
					 'url_framework'  =>plugins_url()."/facebook-fanbox-popup/assets/ilenframework",
					 'theme_imagen'   =>plugins_url()."/facebook-fanbox-popup/assets/images",
					 'wp_support'     =>'http://support.ilentheme.com/forums/forum/plugins/facebook-fanbox-popup/',
					 'wp_review'      =>'http://wordpress.org/support/view/plugin-reviews/facebook-fanbox-popup?rate=5#postform',
					 'link_donate'    =>'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DF2HDEBGSE5KY',
					 'type'           =>'plugin',
					 'method'         =>'free',
					 'themeadmin'     =>'fresh',
					 'scripts_admin'  =>array( 'page' => array('facebook_fanbox_popup' => array('jquery_ui_reset')), ));
	}

	function getOptionsPlugin(){
		//code 
	global $wp_social_pupup_make_plugins;

	global ${'tabs_plugin_' . $this->parameter['name_option']};
	${'tabs_plugin_' . $this->parameter['name_option']} = array();
	${'tabs_plugin_' . $this->parameter['name_option']}['tab01']=array('id'=>'tab01','name'=>'Main Settings','icon'=>'<i class="fa fa-circle-o"></i>','width'=>'550px'); 
	${'tabs_plugin_' . $this->parameter['name_option']}['tab02']=array('id'=>'tab02','name'=>'Styling','icon'=>'<i class="fa fa-pencil"></i>','width'=>'300px'); // ,'fix'=>1


	return array('a'=>array(                'title'      => __('Main Settings',$this->parameter['name_option']),        //title section
											'title_large'=> __('Main Settings',$this->parameter['name_option']),//title large section
											'description'=> '', //description section
											'icon'       => 'fa fa-circle-o',
											'tab'        => 'tab01',


											'options'    => array(  
																	 

																	array(  'title' =>__('Enable / Disable:',$this->parameter['name_option']), //title section
																			'help'  =>'Enable / Disable the plugin.',
																			'type'  =>'checkbox', //type input configuration
																			'value' =>'1', //value default
																			'value_check'=>1,
																			'id'    =>$this->parameter['name_option'].'_'.'enabled', 
																			'name'  =>$this->parameter['name_option'].'_'.'enabled',  
																			'class' =>'', //class
																			'row'   =>array('a','b')),


																	array(  'title' =>__('Enter the URL of your facebook fanpage:',$this->parameter['name_option']), //title section
																			'help'  =>'Example: https://facebook.com/FarandulaEcuatoriana',
																			'type'  =>'text',  
																			'value' =>'', 
																			'id'    =>$this->parameter['name_option'].'_'.'fb_id', 
																			'name'  =>$this->parameter['name_option'].'_'.'fb_id',  
																			'class' =>'', //class
																			'row'   =>array('a','b')),

																	array(  'title' =>__('Show Post?:',$this->parameter['name_option']), //title section
																			'help'  =>'Displays the last post your facebook page (clean the cache to see the result)',
																			'type'  =>'checkbox', //type input configuration
																			'value' =>'1', //value default
																			'value_check'=>1,
																			'id'    =>$this->parameter['name_option'].'_'.'show_post', 
																			'name'  =>$this->parameter['name_option'].'_'.'show_post',  
																			'class' =>'', //class
																			'row'   =>array('a','b')),

																	array(  'title' =>__('Show face:',$this->parameter['name_option']), //title section
																			'help'  =>'Show profile photos when friends like this.',
																			'type'  =>'checkbox', //type input configuration
																			'value' =>'1', //value default
																			'value_check'=>1,
																			'id'    =>$this->parameter['name_option'].'_'.'show_face', 
																			'name'  =>$this->parameter['name_option'].'_'.'show_face',  
																			'class' =>'', //class
																			'row'   =>array('a','b')),

																	array(  'title' =>__('Use Small Header:',$this->parameter['name_option']), //title section
																			'help'  =>'Uses a smaller version of the page header.',
																			'type'  =>'checkbox', //type input configuration
																			'value' =>'1', //value default
																			'value_check'=>1,
																			'id'    =>$this->parameter['name_option'].'_'.'header', 
																			'name'  =>$this->parameter['name_option'].'_'.'header',  
																			'class' =>'', //class
																			'row'   =>array('a','b')),

																	array(  'title' =>__('Show in:',$this->parameter['name_option']), //title section
																			'help'  =>__('Where to show popup.',$this->parameter['name_option']), //descripcion section
																			'type'  =>'checkbox', 
																			'value' =>array('post','home'),
																			'value_check'=>array('post','home'),
																			'display'   =>'list', // list or horizontal
																			'items' => array(
																								array('id'=>$this->parameter['name_option'].'_'.'show_in',
																									  'value'=>'home',
																									  'text' =>__('Home',$this->parameter['name_option']),
																									  'help' => '' ),
																								array('id'=>$this->parameter['name_option'].'_'.'show_in',
																									  'value'=>'post',
																									  'text' =>__('Post',$this->parameter['name_option']),
																									  'help' => '' ),
																								array('id'=>$this->parameter['name_option'].'_'.'show_in',
																									  'value'=>'page',
																									  'text' =>__('Page',$this->parameter['name_option']),
																									  'help' => '' ),
																								array('id'=>$this->parameter['name_option'].'_'.'show_in',
																									  'value'=>'everywhere',
																									  'text' =>__('Everywhere',$this->parameter['name_option']),
																									  'help' => '' ),
																							),
																			 
																			'id'    =>$this->parameter['name_option'].'_'.'show_in', //id
																			'name'  =>$this->parameter['name_option'].'_'.'show_in', //name
																			'class' =>'', //class
																			'row'   =>array('a','b')),
																	
																	array(  'title' =>__('Days until popup shows again?:',$this->parameter['name_option']), //title section
																			'help'  =>"When a user closes the popup he won't see it again until all these days pass",
																			'type'  =>'text',
																			'value' =>'7', //value default
																			'id'    =>$this->parameter['name_option'].'_'.'until_popup', 
																			'name'  =>$this->parameter['name_option'].'_'.'until_popup',  
																			'class' =>'', //class
																			'row'   =>array('a','b')),

																	array(  'title' =>__('Seconds for popup to appear ?:',$this->parameter['name_option']), //title section
																			'help'  =>"After the page is loaded, popup will be shown after X seconds",
																			'type'  =>'text',
																			'value' =>'1', //value default
																			'id'    =>$this->parameter['name_option'].'_'.'seconds_appear', 
																			'name'  =>$this->parameter['name_option'].'_'.'seconds_appear',  
																			'class' =>'', //class
																			'row'   =>array('a','b')),

																	array(  'title' =>__('Closing in gray area',$this->parameter['name_option']), //title section
																			'help'  =>'If you enable this option the popup will close which give a click anywhere on the gray area',
																			'type'  =>'checkbox', //type input configuration
																			'value' =>'1', //value default
																			'value_check'=>1,
																			'id'    =>$this->parameter['name_option'].'_'.'closing_grey_area', 
																			'name'  =>$this->parameter['name_option'].'_'.'closing_grey_area',  
																			'class' =>'', //class
																			'row'   =>array('a','b')),

																	array(  'title' =>__('Width',$this->parameter['name_option']), //title section
																			'help'  =>"Choose the width you want to display the popup. Example: 250, 400, 500 (max 500)",
																			'type'  =>'text',
																			'value' =>'500', //value default
																			'id'    =>$this->parameter['name_option'].'_'.'width', 
																			'name'  =>$this->parameter['name_option'].'_'.'width',  
																			'class' =>'', //class
																			'row'   =>array('a','b')),

																	/*array(  'title' =>__('Lock the scroll while the popup is displayed:',$this->parameter['name_option']), //title section
																			'help'  =>'When the person close the popup the scroll appear, is a way to attract more attention',
																			'type'  =>'checkbox', //type input configuration
																			'value' =>'0', //value default
																			'value_check'=>1,
																			'id'    =>$this->parameter['name_option'].'_'.'disabled_scroll', 
																			'name'  =>$this->parameter['name_option'].'_'.'disabled_scroll',  
																			'class' =>'', //class
																			'row'   =>array('a','b')),*/

																	array(  'title' =>__('Clear Cookie:',$this->parameter['name_option']),  
																			'help'  =>"If you already closed the popup and don't want to wait for 7 days, click this button to see the popup again.",
																			'type'  =>'button',
																			'value' =>'#', // URL
																			'onclick'=>"return clearCookie('fbfanboxp');",
																			'text_button'=>'Delete Cookie',
																			'id'    =>$this->parameter['name_option'].'_'.'reset_cookie', 
																			'name'  =>$this->parameter['name_option'].'_'.'reset_cookie',  
																			'class' =>'',
																			'row'   =>array('a','b')),



															)
										),
				'b'=>array(                 'title'      => __('Style',$this->parameter['name_option']),        //title section
											'title_large'=> __('Style',$this->parameter['name_option']),//title large section
											'description'=> '', //description section
											'icon'       => 'fa fa-paint-brush',
											'tab'        => 'tab02',


											'options'    => array(  
																	 

																	array(  	'title'	=>__('Background Color',$this->parameter['name_option']),
																				'help'  =>'Select the background color', 
																				'type'  =>'color',
																				'value' =>'#000',
																				'id'    =>$this->parameter['name_option'].'_'.'bg_color',
																				'name'  =>$this->parameter['name_option'].'_'.'bg_color', 
																				'class' =>'', 
																				'row'   =>array('a','b')),

																	array(  	'title' =>__('Close background color',$this->parameter['name_option']),
																				'help'  =>'Select the background color of button clase', 
																				'type'  =>'color',
																				'value' =>'#6D6D6D',
																				'id'    =>$this->parameter['name_option'].'_'.'close_color',
																				'name'  =>$this->parameter['name_option'].'_'.'close_color', 
																				'class' =>'', 
																				'row'   =>array('a','b')),

																	array(  	'title' =>__('Close color character "✕"',$this->parameter['name_option']),
																				'help'  =>'Select the background color of button close', 
																				'type'  =>'color',
																				'value' =>'#fff',
																				'id'    =>$this->parameter['name_option'].'_'.'close_color_text',
																				'name'  =>$this->parameter['name_option'].'_'.'close_color_text', 
																				'class' =>'', 
																				'row'   =>array('a','b')),
 

															)
										),
						   
				'last_update'=>time(),


			);
		
	}



	function parameters(){
		
		//require_once 'assets/functions/options.php';
		//global $wp_social_pupup_header_plugins;

		//$this->parameter = $wp_social_pupup_header_plugins;
		$this->parameter = self::getHeaderPlugin();
	}

	function myoptions_build(){
		
		//require_once 'assets/functions/options.php';
		//global $wp_social_pupup_make_plugins;

		//$this->options = $wp_social_pupup_make_plugins;
		$this->options = self::getOptionsPlugin();

		return $this->options;
		
	}

	function use_components(){
		//code 
		$this->components = array();

	}

	function configuration_plugin(){
		// set parameter 
		self::parameters();

		// my configuration 
		self::myoptions_build();

		// my component to use
		self::use_components();
	}

}
}


?>