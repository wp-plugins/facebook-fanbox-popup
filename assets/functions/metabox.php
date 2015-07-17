<?php 

class metabox_facebook_fanbox_popup{

    //the vars
    var $metabox_header = null;
    var $metabox_body   = null;
    var $parameter      = null;
    var $ME             = null;

    public function __construct(){
 
        global $post_type,$post,$FFB_CORE,$IF;   

        $this->parameter  = isset($IF->parameter)?(array)$IF->parameter:null;
        $this->ME = $IF;
        
        self::_builder();

        add_action( 'admin_head',  array( &$this , '_add' ) , 5 );
        add_action( 'save_post' ,  array( &$this->ME , 'IF_save_metabox' ) , 10 , 1 );
        // ---------------

    }

 

    function _builder(){

        $this->metabox_header['main_metabox'] = array(
                                                                'id'         => 'facebook_fanbox_metabox_id',
                                                                'title'      => 'Facebook fanbox popup',
                                                                'post_type'  => 'post',
                                                                'context'    => 'normal',  // (normal, advanced, or side)
                                                                'priority'   => 'low', // (high, core, default, or low)
                                                                'position'   => 'vertical', // vertical or horizontal
                                                                'tabs'       => array(
                                                                                    array('id'=>'ffb_tab_01','name'=>'Setting','icon'=>'<i class="fa fa-wrench"></i>','width'=>'200'),
                                                                                )
                                                          );

 

        $this->metabox_body['main_metabox']   = array(
                                                    'a'=>array( 'title'      => __(''), 
                                                                'title_large'=> __(''), 
                                                                'description'=> __(''), 
                                                                'tab'        => 'ffb_tab_01',

                                                                'options'    => array(
                                                                                        array(  'title' =>__('Disable facebok popup',$this->parameter['name_option']), //title section
                                                                                                'help'  =>'If you enable this option the popup will not be shown in this post.',
                                                                                                'type'  =>'checkbox', //type input configuration
                                                                                                'value' =>'0', // default
                                                                                                'value_check'=>1, // value data
                                                                                                'id'    =>'disabled_popup_facebook',  
                                                                                                'name'  =>'disabled_popup_facebook',  
                                                                                                'class' =>'', //class
                                                                                                'sanitizes'=>'s',
                                                                                                'row'   =>array('a','b')),

                                                                                        /*array(  'title' =>__('fixed'),   
                                                                                                'help'  =>__('Title top of related'),
                                                                                                'type'  =>'text',
                                                                                                'value' =>'ok',
                                                                                                'id'    =>'test_test',
                                                                                                'name'  =>'test_test',
                                                                                                'class' =>'',
                                                                                                'sanitizes'=>'s',
                                                                                                'row'   =>array('a','b')),*/
                                                                                    ),
                                                              ),

                                                    );


        $this->ME->parameter['metabox_name']   = $this->parameter['name_option']."__metabox";
        $this->ME->parameter['header_metabox'] = $this->metabox_header;
        $this->ME->parameter['body_metabox']   = $this->metabox_body;
        

    }




    function _add(){

        global $post_type,$post;
 
        if( $post_type == 'page' || $post_type == 'post' ){

            $this->ME->create_metabox( $this->metabox_header , $this->metabox_body , $this->ME->parameter['metabox_name'] , $post_type  );

        }


    }


}



if( is_admin() ) new metabox_facebook_fanbox_popup;
?>