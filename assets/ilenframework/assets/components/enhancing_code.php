<?php 
/**
 * Components: Enhancing CODE
 * @package ilentheme
 * 
 */


if ( !class_exists('ilenframework_component_enhancing_code') ) {
//global $IF_CONFIG;
//var_dump( $IF_CONFIG );
class ilenframework_component_enhancing_code{

	var $IF_CONFIG =null;

	function __construct(){

		global $IF_CONFIG;
		$this->IF_CONFIG =   $IF_CONFIG ;

		// add scripts & styles
		//add_action('admin_enqueue_scripts', array( &$this,'load_script_and_styles_enhancing_code') );

	}


	function display( $id_name , $value){	?>
		<div style="padding: 29px 1px;background:url('<?php echo $this->IF_CONFIG->parameter['url_framework']; ?>/assets/images/chrome-frame.png') no-repeat;">
			<textarea id="code_<?php echo $id_name; ?>" name="<?php echo $id_name; ?>"><?php echo $value; ?></textarea>
		</div>
		<script>
		  jQuery(document).ready(function(){
		  	var editor = CodeMirror.fromTextArea(document.getElementById("code_<?php echo $id_name; ?>"), {
		  		lineNumbers: true,
    			styleActiveLine: true,
    			matchBrackets: true
    		});

    		editor.setOption("theme", "ambiance");
		  });
	      
	    </script>
	    <style>
	    	/*.CodeMirror {
				background: #f8f8f8;
			}*/
	    </style>


	<?php }

	// =SCRIPT & STYLES---------------------------------------------
	/*function load_script_and_styles_enhancing_code(){
		//code 

		if( is_admin()  && $_GET["page"] == $this->IF_CONFIG->parameter['id_menu'] ){

			// Register styles
			wp_register_style( 'ilenframework-script-enhancing-code-style', $this->IF_CONFIG->parameter['url_framework'] ."/assets/css/enhancing-code/codemirror.css" );
			wp_register_style( 'ilenframework-script-enhancing-code-style-2', $this->IF_CONFIG->parameter['url_framework'] ."/assets/css/enhancing-code/ambiance.css" );

			// Enqueue styles
			wp_enqueue_style(  'ilenframework-script-enhancing-code-style' );
			wp_enqueue_style(  'ilenframework-script-enhancing-code-style-2' );

			wp_enqueue_script('ilenframework-script-enhancing-code', $this->IF_CONFIG->parameter['url_framework'] . '/assets/js/enhancing-code/codemirror.js', array( 'jquery' ), '4.0', true );
			wp_enqueue_script('ilenframework-script-enhancing-code-2', $this->IF_CONFIG->parameter['url_framework'] . '/assets/js/enhancing-code/css.js', array( 'jquery' ), '4.0', true );

		}
	}*/

	
}

}


global $IF_COMPONENT;

$IF_COMPONENT['component_enhancing_code'] = new ilenframework_component_enhancing_code;

?>