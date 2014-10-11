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
                     'version'        =>'1.6',
                     'url'            =>'',
                     'logo'           =>'<i class="fa fa-check-square text-long" style="padding:15px 18px;"></i>',
                      // or image .jpg,png | use class 'text-long' in case of name long
                     'logo_text'      =>'', // alt of image
                     'slogan'         =>'', // powered by <a href="">iLenTheme</a>
                     'url_framework'  =>plugins_url()."/facebook-fanbox-popup/assets/ilenframework",
                     'theme_imagen'   =>plugins_url()."/facebook-fanbox-popup/assets/images",
                     'twitter'        =>'https://twitter.com/intent/tweet?text=View this awesome plugin WP;url=http://bit.ly/1rdrNfN&amp;via=iLenElFuerte',
                     'wp_review'      =>'http://wordpress.org/support/view/plugin-reviews/facebook-fanbox-popup?rate=5#postform',
                     'type'           =>'plugin',
                     'method'         =>'free',
                     'themeadmin'     =>'fresh');
    }

    function getOptionsPlugin(){
        //code 
    global $wp_social_pupup_make_plugins;

    return array('a'=>array(                'title'      => __('Main Settings',$this->parameter['name_option']),        //title section
                                            'title_large'=> __('Main Settings',$this->parameter['name_option']),//title large section
                                            'description'=> '', //description section
                                            'icon'       => 'fa fa-circle-o',


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

                                                                    array(  'title' =>__('Lock the scroll while the popup is displayed:',$this->parameter['name_option']), //title section
                                                                            'help'  =>'When the person close the popup the scroll appear, is a way to attract more attention',
                                                                            'type'  =>'checkbox', //type input configuration
                                                                            'value' =>'0', //value default
                                                                            'value_check'=>1,
                                                                            'id'    =>$this->parameter['name_option'].'_'.'disabled_scroll', 
                                                                            'name'  =>$this->parameter['name_option'].'_'.'disabled_scroll',  
                                                                            'class' =>'', //class
                                                                            'row'   =>array('a','b')),

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