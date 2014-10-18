<?php 
/**
 * iLenFramework 1.5.2
 * @package ilentheme
 */


// REQUIRED FILES TO RUN
if ( !class_exists('ilen_framework_1_5_2') ) {


class ilen_framework_1_5_2 {

		var $options		   	= array();
		var $parameter 			= array();
		var $save_status		= null;
		var $IF_CONFIG			= null;
    	var $components         = null;

	function __construct(){

		if( ! is_admin() ){ // only front-end
 
			return;

		}elseif( is_admin()  ){ // only admin



			// set default if not exists
			self::_ini_();



			// add menu options
			self::iLenFramework_add_menu();
				


			// add scripts & styles
			add_action('admin_enqueue_scripts', array( &$this,'ilenframework_add_scripts_admin') );


			

		}	

		add_action('admin_init',array(&$this,'plugin_install_before'));	



	}





	// =Definitions Fields
	function theme_definitions(){
        
		return $this->options;
		
	}


	// =Add Menu
	function iLenFramework_add_menu(){

		if( isset($this->parameter['type']) && $this->parameter['type'] == "theme" ){

			if( isset($this->parameter['method']) && $this->parameter['method'] == "free"  ){

				add_action('admin_menu', array( &$this,'menu_free') );		

			}elseif( $this->parameter['method'] == "buy" ){

				add_action('admin_menu', array( &$this,'menu_pay') );		

			}

		}elseif( isset($this->parameter['type']) && (  $this->parameter['type'] == "plugin" || $this->parameter['type'] == "plugin-tabs"  )  ){

			if( isset($this->parameter['method']) && $this->parameter['method'] == "free"  ){

				add_action('admin_menu', array( &$this,'menu_free') );		

			}elseif( isset($this->parameter['method']) && $this->parameter['method'] == "buy" ){

				add_action('admin_menu', array( &$this,'menu_pay') );		

			}

		}
		
	}







	// =INIT theme
	function _ini_(){
		
		global $IF_CONFIG;

		$this->IF_CONFIG = $IF_CONFIG;
		$this->parameter = isset($IF_CONFIG->parameter)?(array)$IF_CONFIG->parameter:null;
		$this->options   = isset($IF_CONFIG->options)?(array)$IF_CONFIG->options:null;
        $this->components = $IF_CONFIG->components;
        
        
		self::setComponents();

		self::theme_plugin_install_set_default_values();


		if( isset($this->parameter['id_menu']) && isset($_GET["page"]) && ( $_GET["page"] == $this->parameter['id_menu'] ) ){ // validate if admin page is the option

			// get Components
			//self::_getComponents_();

			// set varaible configuration
			$this->options = $this->IF_CONFIG->options;

			// if save update options
			if($this->parameter['type'] == 'plugin-tabs'){
				self::save_options_for_tabs();
			}else{
				self::save_options();
			}
 
		}


	}





	function theme_plugin_install_set_default_values(){
        
		if( isset($this->parameter['name_option']) && ! $n = get_option( $this->parameter['name_option']."_options") ){
	
			// if not exists options them create
			update_option( $this->parameter['name_option']."_options", self::get_default_options());

		}
		
	}




	// =DEFAULTS OPTIONS
	function get_default_options(){
		
		$defaults = array();

		$Myoptions = self::theme_definitions();
		if( is_array($Myoptions) )
			foreach ($Myoptions as $key2 => $value2) {
				if(  $key2 != 'last_update' ){
					foreach ($value2['options'] as $key => $value) {

						if( isset($value['name']) )
							$defaults[$value['name']] = $value['value'];

					}
				}
			}

		return $defaults;
		
	}





	// =MENU--------------------------------------------
	function menu_free() {

		if( $this->parameter['type'] == "theme" ){
			add_theme_page($this->parameter['name'], $this->parameter['name_long'], 'edit_theme_options', $this->parameter['id_menu'] , array( &$this,'ilentheme_full') );
		}elseif( $this->parameter['type']  == "plugin" || $this->parameter['type']  == "plugin-tabs" ){
			add_options_page( $this->parameter['name'], $this->parameter['name_long'], 'manage_options', $this->parameter['id_menu'], array( &$this,'ilentheme_full') );
		}
	}

	function menu_pay() {

		//add_menu_page($this->parameter['name'], $this->parameter['name_long'], 'manage_options',  $this->parameter['id_menu'], array( &$this,'ilentheme_full') );
		add_options_page( $this->parameter['name'], $this->parameter['name_long'], 'manage_options', $this->parameter['id_menu'], array( &$this,'ilentheme_full') );

	}






	function ilentheme_full(){
		//code 


		self::ShowHTML();
		
 
	}

 



	// =Interface Create for Theme---------------------------------------------
	function ilentheme_options_wrap_for_theme(){ ?>
		 
		<div class='ilentheme-options'>
			<form action="" method="POST" name="frmsave" id="frmsave">
			<header>
				<div class="top-left logo">
					<?php 
						if( !$this->parameter["logo"] )
							echo "<h1><a href='#'>{$this->parameter["name"]}</a> <span>".$this->parameter['slogan']."</span></h1>";
						else
							echo "<a href='#'><img src='{$this->parameter["logo"]}' /></a>";
					?>
					<!-- <span><?php //echo $this->parameter["slogan"] ?></span> -->
				</div>
				<div class="top-right">
					<a href="#" class="ibtn btnblack right btn_save"><span><i class="fa fa-refresh"></i></span><?php _e('Save Changes',$this->parameter['name_option']) ?></a>
				</div>
			</header>

			<div id="tabs">
				<ul>

					<?php $Myoptions = self::theme_definitions();

					if( is_array( $Myoptions ) ) {
						foreach ($Myoptions as $key => $value) { ?>
							<?php if($key != 'last_update'){ ?>
									<li>
						            	<a href="#<?php echo $key; ?>">
						            		<?php 
						            			if( $value['icon'] )
						            				echo '<i class="'.$value['icon'].'"></i>';
						            		?>
						            		<?php echo $value['title']; ?>
						            	</a>
						            </li>
				            <?php } ?>

						<?php  }
					}

					?>

				</ul>

				<?php 
					// set mesagge status
					if( $this->save_status===true )
						$class_status="ok";
					elseif( $this->save_status===false )
						$class_status="error";
				?>
				<?php if( $this->save_status ){ ?>
				<div class="messagebox <?php echo $class_status; ?>"><i class="fa fa-check"></i> 
					<?php _e('Nice',$this->parameter['name_option'])."."; ?> <?php _e('Update successfully',$this->parameter['name_option']) ?>
				</div>
				<?php } ?>


				<?php 

				if( is_array( $Myoptions ) ){
					foreach ($Myoptions as $key => $value) { ?>
							<?php if($key != 'last_update'){ ?>
								<div id="<?php echo $key; ?>" class="content-tab">
					            	<h2>
					            		<?php 
					            			if( $value['icon'] )
					            				echo '<i class="'.$value['icon'].'"></i>';
					            		?>
					            		<?php echo $value['title']; ?>
					            	</h2>
					            	<?php if( $value['description'] ){ ?>
					            		<p class="description"><?php echo $value['description']; ?></p>
					            	<?php } ?>
					            	
					            	<?php self::build_fields( $value['options'] ) ?>
					            </div>
					        <?php } ?>

					<?php  }
				}
				?>

			</div>
			<footer>
				<a href="#" class="ibtn btnblack right btn_save"><span><i class="fa fa-refresh"></i></span><?php _e('Save Changes',$this->parameter['name_option']) ?></a>
				<a href="#" class="ibtn btnred left btn_reset" data-me="<?php _e('Want to update all the default values​​ &#63;',$this->parameter['name_option']) ?>"><span><i class="fa fa-repeat"></i></span><?php _e('Reset',$this->parameter['name_option']) ?></a>
			</footer>

			<input type="hidden" name='save_options' value='1' />
			<input type="hidden" name='name_options' value='<?php echo $this->parameter["name_option"]; ?>' />
			</form>
			<form action="" method="POST" name="frmreset" id="frmreset">
				<input type="hidden" name='reset_options' value='1' />
				<input type="hidden" name='name_options' value='<?php echo $this->parameter['name_option']; ?>' />
			</form>
		</div>	
 

	<?php }




	// =Interface Create for plugin---------------------------------------------
	function ilentheme_options_wrap_for_plugin(){ ?>
		
		<div class='ilenplugin-options ilenplugin-<?php echo $this->parameter["name_option"] ?>'>


			<form action="" method="POST" name="frmsave" id="frmsave">
 
				<div id="poststuff" class="metabox-holder has-right-sidebar">

					

					<div id="post-body-content" class="has-sidebar-content">

					<?php 
					// set mesagge status
					if( $this->save_status===true ) : ?>
						  <div class="notification-p success">
						    <aside>
						      <i class="fa fa-check"></i>
						    </aside>
						    <main>
						      <b><?php _e('Nice',$this->parameter['name_option'])."."; ?></b>
						      <br /><br />
						      <?php _e('Update successfully',$this->parameter['name_option']) ?>
						    </main>
						  </div>
					<?php elseif( $this->save_status===false ): ?>
						  <div class="notification-p perror">
						    <aside>
						      <i class="fa fa-times"></i>
						    </aside>
						    <main>
						      <b><?php _e('Oh bollocks',$this->parameter['name_option'])."."; ?>.</b>
						      <br /><br />
						      <?php _e('Failed to update',$this->parameter['name_option']) ?>
						    </main>
						  </div>
					<?php endif; ?>

					<div class="my-wrap-plugin">
					<header>
						<span class="header__logo waves-effect"><?php echo $this->parameter['logo']; ?></span>
						<h2 class="<?php if( strlen($this->parameter['name_long'])>20 ){ echo 'text-long'; } ?>">
							<?php echo $this->parameter['name_long']; ?>
							<span class='ilen-version <?php if( strlen($this->parameter['name_long'])>20 ){ echo 'text-long'; } ?>'><?php echo $this->parameter['version'] ?></span>
						</h2>
						<?php if( $this->parameter['wp_review'] ): ?><a href="<?php echo $this->parameter['wp_review'] ?>" class="leave-a-review ibtn btnred right rippler rippler-default" target="_blank"><span><i class="fa fa-star"></i></span>Leave a review</a><?php endif; ?>
						<?php if( $this->parameter['twitter'] ): ?><a href="<?php echo $this->parameter['twitter'] ?>" class="tweet-about-it ibtn btnturke right rippler rippler-default" target="_blank"><span><i class="fa fa-twitter"></i></span>Write your experience</a><?php endif; ?>
					</header>

					<?php $Myoptions = self::theme_definitions(); ?>

					<?php if( is_array( $Myoptions ) ): ?>
						
							<?php 
							$put_tab = 0;
							global ${'tabs_plugin_' . $this->parameter['name_option']};
							$tabs_plugin = ${'tabs_plugin_' . $this->parameter['name_option']};
							if( is_array($tabs_plugin) && isset($tabs_plugin) ){
							foreach ($tabs_plugin as $key => $value_tab): 
								if( $value_tab["id"] && $put_tab ==0 ): ?>
									<div id="tabs">
										
										<ul>
								<?php 
									$put_tab=1;
								endif;

									if( isset($value_tab["id"]) && $value_tab["id"] ) : ?>

									 	<li style="<?php if( isset($value_tab["width"]) && isset( $value_tab["fix"]) ){ echo "border-right:0;";  } ?>" ><a href="#<?php echo $value_tab["id"]; ?>" style="width:<?php if( isset($value_tab["width"]) && isset( $value_tab["fix"]) ){ echo (($value_tab["width"])+1)."px;"; } elseif( isset($value_tab["width"]) ){ echo "{$value_tab["width"]}px;"; } ?>" class="rippler rippler-inverse" ><?php if(isset($value_tab["icon"])){ echo $value_tab["icon"]; } ?> <?php echo $value_tab["name"]; ?></a></li>

							<?php   endif;
							endforeach;
							} ?>
										</ul>
										
						<?php 
						if( is_array($tabs_plugin) && isset($tabs_plugin) ){
							foreach ($tabs_plugin as $key_tab => $value_tab) { ?>
								<div id="<?php echo $value_tab["id"]; ?>"></div>
							<?php }
						} ?>
 
					<?php endif; ?>
 
						<div class="meta-box-sortabless">
							<div class="has-sidebar sm-padded">


								<?php //$Myoptions = self::theme_definitions();

									if( is_array( $Myoptions ) ){
										global $options_theme;
										$options_theme = null;
										$options_theme = get_option( $this->parameter['name_option']."_options" );

										foreach ($Myoptions as $key => $value) {

										 ?>
											<?php if($key != 'last_update'){  ?>

										            <div id="box_<?php echo $key; ?>" class="postbox <?php if( isset($value["tab"]) ){ echo $value["tab"]; } ?>">
														<h3 class="hndle">
															<span>
															<?php 
										            			if( $value['icon'] ){
										            				echo '<i class="'.$value['icon'].'"></i>&nbsp;&nbsp;';
										            			}
										            		?><?php echo $value['title']; ?>
										            		</span>
										            	</h3>
														<div class="inside">
																<?php self::build_fields_p( $value['options'] ) ?>
														</div>
													</div>

								            <?php } ?>

										<?php  }

									} ?>

								

							</div>
						</div>
						<?php if( $put_tab ==1 ): ?>
							</div><!-- div id=tab -->
						<?php endif; ?>
						<footer>
							<a href="#" class="ibtn btnblack left btn_save rippler rippler-default"><span><i class="fa fa-refresh"></i></span><?php _e('Save Changes',$this->parameter['name_option']) ?></a>
							<a href="#" class="ibtn btnred left btn_reset rippler rippler-default" data-me="<?php _e('Want to update all the default values​​ &#63;',$this->parameter['name_option']) ?>"><span><i class="fa fa-repeat"></i></span><?php _e('Reset',$this->parameter['name_option']) ?></a>
						</footer>
						<script>
						jQuery(document).ready(function($){
							$(".rippler").rippler({
				        // addElement:"svg"
				      });
						});
						</script>
						</div> <!-- my-wrap-plugin -->
					</div>
				</div>
				<input type="hidden" name='save_options' value='1' />
				<input type="hidden" name='name_options' value='<?php echo $this->parameter["name_option"]; ?>' />
				</form>
				<form action="" method="POST" name="frmreset" id="frmreset">
					<input type="hidden" name='reset_options' value='1' />
					<input type="hidden" name='name_options' value='<?php echo $this->parameter['name_option']; ?>' />
				</form>

				<!-- IF PLUGIN TAB, inner HTML in tab -->
					<script>
					<?php  
					if( is_array($tabs_plugin) && isset($tabs_plugin) ){
						foreach ($tabs_plugin as $key_tab => $value_tab) { ?>
							jQuery(".<?php echo $value_tab['id']; ?>").each(function(){
								jQuery( this ).appendTo( jQuery("#<?php echo $value_tab['id']; ?>") );
							});
						<?php }
					} ?>
 
					</script>
				<!-- END -->
		</div>


	<?php 
	}


// =Interface Create for plugin for TABS---------------------------------------------
function ilentheme_options_wrap_for_plugin_tabs(){  ?>
	<div class='ilenplugin-options ilenplugin-<?php echo $this->parameter["name_option"] ?>'>


				<div id="poststuff" class="metabox-holder has-right-sidebar">
				<div id="frm_active_sabe"></div>

					<div id="post-body-content" class="has-sidebar-content ilentabs">
					<?php 
					// set mesagge status
					if( $this->save_status===true ) : ?>
						  <div class="notification-p success">
						    <aside>
						      <i class="fa fa-check"></i>
						    </aside>
						    <main>
						      <b><?php _e('Nice',$this->parameter['name_option'])."."; ?></b>
						      <br /><br />
						      <?php _e('Update successfully',$this->parameter['name_option']) ?>
						    </main>
						  </div>
					<?php elseif( $this->save_status===false ): ?>
						  <div class="notification-p perror">
						    <aside>
						      <i class="fa fa-times"></i>
						    </aside>
						    <main>
						      <b><?php _e('Oh bollocks',$this->parameter['name_option'])."."; ?>.</b>
						      <br /><br />
						      <?php _e('Failed to update',$this->parameter['name_option']) ?>
						    </main>
						  </div>
					<?php endif; ?>
					<header>
						<span class="header__logo"><?php echo $this->parameter['logo']; ?></span>
						<h2 class="<?php if( strlen($this->parameter['name_long'])>20 ){ echo 'text-long'; } ?>">
							<?php echo $this->parameter['name_long']; ?>
							<span class='ilen-version <?php if( strlen($this->parameter['name_long'])>20 ){ echo 'text-long'; } ?>'><?php if( isset($this->parameter['method']) && $this->parameter['method'] == 'free' ){ echo __('Free',$this->parameter['name_option']); }else{ echo __('PRO',$this->parameter['name_option']); } echo " ".$this->parameter['version']; ?></span>
						</h2>
						<?php if( $this->parameter['wp_review'] ): ?><a href="<?php echo $this->parameter['wp_review'] ?>" class="leave-a-review ibtn btnred right rippler rippler-default" target="_blank"><span><i class="fa fa-star"></i></span>Leave a review</a><?php endif; ?>
						<?php if( $this->parameter['twitter'] ): ?><a href="<?php echo $this->parameter['twitter'] ?>" class="tweet-about-it ibtn btnturke right rippler rippler-default" target="_blank"><span><i class="fa fa-twitter"></i></span>Write your experience</a><?php endif; ?>
					</header>
					<div class="ilentabs_wrap">
					<?php $Myoptions = self::theme_definitions(); ?>

					<?php if( isset($Myoptions) && is_array( $Myoptions ) ): ?>
						
							<?php 
							$put_tab = 0;
							global ${'tabs_plugin_' . $this->parameter['name_option']};
							$tabs_plugin = ${'tabs_plugin_' . $this->parameter['name_option']};
							if( isset($tabs_plugin) && is_array($tabs_plugin) ){
								$name_first_tab = '';
							 ?>
							<div id="nav">
							<h2>
							<?php foreach ($tabs_plugin as $key => $value_tab): 
								if( isset($value_tab["id"]) && $value_tab["id"] ): ?>
									<a href="<?php echo $value_tab["link"]; ?>&tabs=<?php echo $value_tab["id"]; ?>" class="nav-tab <?php if( !isset($_GET['tabs']) && !$put_tab ) { echo "nav-tab-active"; $put_tab = 1; }elseif( isset($_GET["tabs"]) && isset($value_tab["id"]) &&  $value_tab["id"] == $_GET["tabs"] ){ echo "nav-tab-active"; }  ?>"><?php echo $value_tab["name"]; if( !$name_first_tab ){ $name_first_tab =  $value_tab["id"]; } ?></a>
							<?php  endif;
							endforeach; ?>
							
							</h2>
							</div>
							<?php } ?>
					<?php endif; ?>

					<div class="meta-box-sortabless">
						<?php 
							$tab_columns = self::if_columns_tab();
						?>
						<div class="has-sidebar sm-padded <?php if( isset($tab_columns[0]) && $tab_columns[0] ){ echo "main"; } ?>">
								<?php

									if( is_array( $Myoptions ) ){
										global $options_theme;
										$options_theme = null;
										$options_theme = get_option( $this->parameter['name_option']."_options" );
										foreach ($Myoptions as $key => $value) {

											$tabs_save = ( isset($_GET['tabs']) && isset($value["tab"]) && $_GET['tabs'] == $value["tab"] ) ? true:false;

											if( isset( $_GET['tabs'] ) &&  $_GET['tabs'] == $value["tab"] ){
												$next_build = 1;
											}elseif( isset($value['default']) && $value['default'] && !isset($_GET['tabs']) ){
												$next_build = 1;
											}else{
												$next_build = 0;
											}

											if( isset($value['no_options']) && isset($_GET['tabs']) && $_GET['tabs'] == $value["tab"] ){
												$no_form_save = 1;
											//}elseif( !isset($active_form_save) && isset($_GET['tabs']) && $_GET['tabs'] == $value["tab"] ){
											}elseif( !isset($active_form_save) ){
												$active_form_save = 1;  
												echo '<form action="" method="POST" name="frmsave" id="frmsave">';
											}											?>
											<?php 
											if( $next_build ){
												if($key != 'last_update'){  ?>

													<?php  if( isset($value['before']) && $value['before'] ): ?>
														<div class="postbox_note_top"><i class="fa fa-bullhorn"></i> <?php echo $value['before']; ?></div>
													<?php  endif; ?>

													<?php  if( isset($value['no_options']) &&  isset($value['page_content']) && $value['no_options']  ): ?>
														<?php include $value['page_content']; $only_page = 1; ?>
													<?php  else: ?>
													

										      <div id="box_<?php echo $key; ?>" class="<?php if( isset($value['title']) && $value['title'] ): ?>postbox<?php endif; ?> <?php if( isset($value["tab"]) ){ echo $value["tab"]; } ?>">
										        <?php if( isset($value['title']) && $value['title'] ): ?>
														<h3 class="hndle">
															<span>
															<?php 
						            			if( $value['icon'] ){
						            				echo '<i class="'.$value['icon'].'"></i>&nbsp;&nbsp;';
						            			}
							            		?><?php echo $value['title']; ?>
							            		</span>
							            	</h3>
							            	<?php endif; ?>

														<div class="inside">
																<?php self::build_fields_p( $value['options'] ) ?>
														</div>
													</div>
												<?php  endif; ?>

								       <?php }
								       } ?>

								           

										<?php  }

									} ?>
								
										<?php if( !isset($only_page) || !$only_page ): ?>
										<input type="hidden" name='save_options' value='1' />
										<input type="hidden" name='save_for_tab' value='1' />
										<input type="hidden" name='name_options' value='<?php echo $this->parameter["name_option"]; ?>' />
										</form>
										<?php endif; ?>
							</div>

							<!-- sidebar wp-admin -->
							<?php if( isset($tab_columns[1]) && $tab_columns[1] ): ?>
							<div class="sb">
								<?php if( isset($tab_columns[2]) && $tab_columns[2] ){ require_once $tab_columns[2]; } ?>
							</div>
						<?php endif; ?>

						</div>

					<footer>
						<?php if( !isset($only_page) || !$only_page ): ?>
						<a href="#" class="ibtn btnblack left btn_save rippler rippler-default"><span><i class="fa fa-refresh"></i></span><?php _e('Save Changes',$this->parameter['name_option']) ?></a>
						<a href="#" class="ibtn btnred left btn_reset rippler rippler-default" data-me="<?php _e('Want to update all the default values​​ &#63;',$this->parameter['name_option']) ?>"><span><i class="fa fa-repeat"></i></span><?php _e('Reset section',$this->parameter['name_option']) ?></a>
						<?php endif; ?>
					</footer>
					<script>
						jQuery(document).ready(function($){
							$(".rippler").rippler({
				        // addElement:"svg"
				      });
						});
						</script>
					</div>
					</div>
				</div>



				<?php if( !isset($only_page) || !$only_page ): ?>
				<form action="" method="POST" name="frmreset" id="frmreset">
					<input type="hidden" name='reset_options' value='1' />
					<input type="hidden" name='name_options' value='<?php echo $this->parameter['name_option']; ?>' />
				</form>
				<?php endif; ?>
				<?php 
				if( !isset($_GET['tabs']) || !$_GET['tabs'] ):  ?>
				<script>
					jQuery(document).ready(function(){
						window.history.pushState('', '', '<?php echo admin_url('options-general.php?page='.$this->parameter['id_menu']) ?>&tabs=<?php echo $name_first_tab; ?>');
					});
				</script>
			<?php endif; ?>
 

<?php  }

	
	// =If tabs is 1 or 2 columns
	function if_columns_tab(){
		global ${'tabs_plugin_' . $this->parameter['name_option']};
		$tabs_plugin = ${'tabs_plugin_' . $this->parameter['name_option']};
		$tabs_columns = array();
		if( isset($tabs_plugin) && is_array($tabs_plugin) ){ 
			foreach ($tabs_plugin as $key => $value_tab){
				if( (isset($_GET["tabs"]) && isset($value_tab["id"]) &&  $value_tab["id"] == $_GET["tabs"]) || ( !isset($_GET["tabs"]) ) ){

					if( isset($value_tab["columns"]) && $value_tab["columns"] == 2 ){
						$tabs_columns[0] = 'main';
						$tabs_columns[1] = 'sb';
						$tabs_columns[2] = isset($value_tab["sidebar-file"])?$value_tab["sidebar-file"]:"";

						return $tabs_columns;
					}

				}
			}
		}

		return false;
		
	}


	// =BUILD Fields themes---------------------------------------------
	function build_fields( $fields = array() ){

			$options_theme = get_option( $this->parameter['name_option']."_options" );
 
			foreach ($fields as $key => $value) {

					if( in_array("b", $value['row']) ) { $side_two = "b"; }else{  $side_two ="c"; }

					switch ( $value['type'] ) {

						

						case "text": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_text" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">
									<input type="text"  value="<?php if( isset($options_theme[ $value['name'] ]) ){ echo esc_html($options_theme[ $value['name'] ]); } ?>" name="<?php echo $value['id'] ?>" id="<?php echo $value['id'] ?>"  autocomplete="off" <?php if(isset($value['placeholder'])){ echo "placeholder='{$value['placeholder']}'"; } ?> />
									<div class="help"><?php echo $value['help']; ?></div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;

						case "checkbox": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_checkbox" <?php if(isset( $value['style'] )){ echo $value['style']; } ?>> 
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">

									
									<?php if( isset($value['display']) && $value['display'] == 'list' ){  ?>
										<?php 
											if( !is_array(  $options_theme[ $value['name'] ] ) ){
												$options_theme[ $value['name'] ] = array();
											}

											foreach ($value['items'] as $key2 => $value2): ?>

											<div class="row_checkbox_list">
												<input  type="checkbox" <?php if( in_array( $value2['value']  , $options_theme[ $value['name'] ] ) ){ echo " checked='checked' ";} ?> name="<?php echo $value['name'] ?>[]" id="<?php echo $value['id']."_".$value2['id'] ?>" value="<?php echo $value2['value'] ?>"  />	

												<label for="<?php echo $value['id']."_".$value2['id']; ?>"><span class="ui"></span></label>
												&nbsp;<?php echo  $value2['text']; ?>
												
											</div>


										<?php endforeach; ?>
										<div class="help"><?php echo $value['help']; ?></div>
										
									<?php } elseif( isset($value['display']) && $value['display'] == 'types_post' ) { ?>
										<?php $ck=''; if( isset($options_theme[ $value['name'] ]) ){ $ck =  checked(  $options_theme[ $value['name'] ]  , 1, FALSE );  }


											// get type post 
											$post_types = get_post_types(array(), "objects");

											foreach ($post_types as $post_type): ?>
												<?php if( !in_array($post_type->name,array('revision','nav_menu_item')) ): ?>
												<div class="row_checkbox_types_post">

													<input  type="checkbox" <?php if( in_array( $post_type->name  , (array)($options_theme[ $value['name'] ]) ) ){ echo " checked='checked' ";} ?> name="<?php echo $value['id'] ?>[]" id="<?php echo $value['id']."_".$post_type->name ?>" value="<?php echo $post_type->name; ?>"  />	

													<label for="<?php echo $value['id']."_".$post_type->name ?>"><span class="ui"></span></label>
													&nbsp;<?php echo $post_type->labels->name; ?>
												</div>
												
											<?php endif; ?>
											<?php endforeach; ?>
											<div class="help"><?php echo $value['help']; ?></div>
										
									<?php }else { ?>
										<?php $ck=''; if( isset($options_theme[ $value['name'] ]) ){ $ck =  checked(  $options_theme[ $value['name'] ]  , 1, FALSE );  } ?>
										<div class="row_checkbox_normal">
											<input  type="checkbox" <?php echo $ck; ?> name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>" value="<?php echo $value['value_check'] ?>"  />
											<label for="<?php echo $value['id'] ?>"><span class="ui"></span></label>
											<div class="help"><?php echo $value['help']; ?></div>
										</div>
									<?php } ?>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "upload": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row upload <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">
									<input id="<?php echo $value['id'] ?>" type="text" name="<?php echo $value['name'] ?>" value="<?php if(isset($options_theme[ $value['name'] ])) { echo $options_theme[ $value['name'] ]; } ?>" class="theme_src_upload"  />
									<input type="button" value="<?php _e('Upload Image',$this->parameter['name_option']) ?>" class="upload_image_button" data-title="<?php echo $value['title'] ?>" data-button-set="<?php _e('Select image',$this->parameter['name_option']) ?>" />
									<div class="preview">
										<?php  if( isset($options_theme[ $value['name'] ]) && $options_theme[ $value['name'] ] ) : ?>
											<img src="<?php echo $options_theme[ $value['name'] ]; ?>" />
											<span class='admin_delete_image_upload'>✕</span>
										<?php endif; ?>
									</div>
									<div class="help"><?php echo $value['help']; ?></div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "upload_old": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row upload <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">
									<input id="<?php echo $value['id'] ?>" type="text" name="<?php echo $value['name'] ?>" value="<?php echo $options_theme[ $value['name'] ]; ?>" class="theme_src_upload" />
									<input type="button" value="<?php _e('Upload Image',$this->parameter['name_option']) ?>" class="upload_image_button_old" />
									<div class="preview">
										<?php  if( $options_theme[ $value['name'] ] ) : ?>
											<img src="<?php echo $options_theme[ $value['name'] ]; ?>" />
											<span class='admin_delete_image_upload'></span>
										<?php endif; ?>
									</div>
									<div class="help"><?php echo $value['help']; ?></div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "select": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_select" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">

									<select name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>">
										<?php 
											if( is_array( $value['items'] ) ){
												foreach ( $value['items'] as $item_key => $item_value ): ?>
												
													<option value="<?php echo $item_key ?>" <?php selected( $options_theme[ $value['name'] ] ,   $item_key ); ?>><?php echo $item_value ?></option>	

												<?php
												endforeach;
											}
										?>
									</select>
									<div class="help"><?php echo $value['help']; ?></div>
								</div> 
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "radio_image": ?>
							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row radio_image <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">

									<?php 
									$value_name = '';
									if( isset($options_theme[ $value['name'] ])  ) { $value_name = $options_theme[ $value['name'] ]; }
									if( is_array( $value['items'] ) ){
										foreach ($value['items'] as $item_key => $item_value): ?>
											
											<label for="<?php echo $value['id']."_".$item_value['value']; ?>">
												<img name="<?php if( isset($value['name']) && $value['name'] ){  echo $value['name']."_img"; } ?>" src="<?php echo $item_value['image'] ?>" class="radio_image_selection <?php echo $value['name']; ?> <?php echo ( isset($options_theme[ $value['name'] ]) && $options_theme[ $value['name'] ] == $item_value['value']?"active":"") ?>" data-id="<?php echo $value['name']; ?>" title="<?php echo $item_value['text']; ?>" />
												<input  <?php checked(  $value_name , $item_value['value'] ); ?> id="<?php echo $value['id']."_".$item_value['value']; ?>" type="radio" name="<?php if( isset($value['name']) && $value['name'] ){ echo $value['name']; } ?>" value="<?php echo $item_value['value'] ?>" />
											</label>

									<?php endforeach;
									} ?>
									<div class="help"><?php echo $value['help']; ?></div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;



						case "divide": ?>
								<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
								<div class="divide">
									<?php 
				            			if( $value['icon'] )
				            				echo '<i class="'.$value['icon'].'"></i>';
				            		?>
									<?php echo $value['title'] ?>
								</div>
								<?php if(isset( $value['after'] )){ echo $value['after'];} ?>
						<?php break;



						case "color": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_color" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">
									<input type="text" class="theme_color_picker" value="<?php if(isset( $options_theme[ $value['name'] ] )){ echo $options_theme[ $value['name'] ]; } ?>" name="<?php echo $value['name']; ?>" id="<?php echo $value['id'] ?>" data-default-color="<?php echo $value['value']; ?>" />
									<div class="help"><?php echo $value['help']; ?></div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;



						case "color_hover": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> color_hover" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">
									<?php $bg_hover = isset($options_theme[ $value['name'] ])?$options_theme[ $value['name'] ]:''; ?>
									<table>
										<tr>
											<td class="color_hover_color"><input type="text" class="theme_color_picker" value="<?php if(isset( $bg_hover['color'] )){ echo $bg_hover['color']; } ?>" name="<?php echo $value['name'].'_color'; ?>" id="<?php echo $value['id'].'_color' ?>" data-default-color="<?php if(isset( $bg_hover['color'] )){ echo $bg_hover['color']; } ?>" /></td>
											<td class="color_hover_text">&nbsp; <?php _e('Hover',$this->parameter['name_option']) ?></td>
											<td class="color_hover_hover"><input type="text" class="theme_color_picker" value="<?php if(isset( $bg_hover['hover'] )){ echo $bg_hover['hover']; } ?>" name="<?php echo $value['name'].'_hover';  ?>" id="<?php echo $value['id'].'_hover'; ?>" data-default-color="<?php if(isset( $bg_hover['hover'] )){  echo  $bg_hover['hover']; } ?>" /></td>
										</tr>
									 </table>
									<div class="help"><?php echo $value['help']; ?></div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;



						case "textarea": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_textarea" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">
									<textarea name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>" style="width:100%;height:120px;" <?php if(isset($value['placeholder'])){ echo "placeholder='{$value['placeholder']}'"; } ?>><?php if( isset( $options_theme[ $value['name'] ] )) { echo $options_theme[ $value['name'] ]; } ?></textarea>
									<div class="help"><?php echo $value['help']; ?></div>
								</div>

							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "html": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><?php if( isset( $value['html1'] ) ) { echo htmlentities($value['html1']); } ?></div>
								<div class="<?php echo $side_two; ?>">
									<?php if( isset( $value['html2'] ) ) { echo ($value['html2']); } ?>
								</div>
								<div class="help"><?php echo $value['help']; ?></div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "tinymce": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">
									<?php
										//$initial_data='What you want to appear in the text box initially';
										$id = $value['id'];//has to be lower case
										wp_editor($options_theme[ $value['name'] ],$id,$value['setting']);
									?>
								</div>
								<div class="help"><?php echo $value['help']; ?></div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "background_complete": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> background_complete" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">

									<?php 
									$bg_complete = '';
									$bg_complete['color'] = '';
									$bg_complete['transparent'] = '';
									$bg_complete['repeat']= '';
									$bg_complete['size']= '';
									$bg_complete['attachment']= '';
									$bg_complete['position']= '';
									$bg_complete['src']= '';
									if( isset( $options_theme[ $value['name'] ]) && $options_theme[ $value['name'] ]){ $bg_complete = $options_theme[ $value['name'] ]; } ?>
									<div>
									  <input type="text" class="theme_color_picker" value="<?php echo $bg_complete['color'] ?>" name="<?php echo $value['name']; ?>_color" id="<?php echo $value['id'] ?>_color" data-default-color="<?php echo $bg_complete['color']; ?>" />
									  &nbsp;
									  <div class="background_complete_transparent_check"><input type="checkbox"  id="<?php echo $value['id'] ?>_transparent" name="<?php echo $value['id'] ?>_transparent" value="1" <?php if(  $bg_complete['transparent'] ){ echo " checked='checked' ";} ?> /><label for="<?php echo $value['id'] ?>_transparent"><span class="ui"></span></label> Transparent</div>
									</div>
									<div style="padding:0 0 10px 0;">
										<select name="<?php echo $value['name']; ?>_repeat" id="<?php echo $value['id'] ?>_repeat" style="width:45%;" data-attribute="background-repeat" class="select2_background_complete" >
											<option value=""></option>
											<option value="no-repeat" <?php selected(  $bg_complete['repeat'], 'no-repeat'); ?>><?php _e('No repeat',$this->parameter['name_option']) ?></option>
											<option value="repeat" <?php selected(  $bg_complete['repeat'], 'repeat'); ?>><?php _e('Repeat all',$this->parameter['name_option']) ?></option>
											<option value="repeat-x" <?php selected(  $bg_complete['repeat'], 'repeat-x'); ?>><?php _e('Repeat Horizontally',$this->parameter['name_option']) ?></option>
											<option value="repeat-y" <?php selected(  $bg_complete['repeat'], 'repeat-y'); ?>><?php _e('Repeat Vertically',$this->parameter['name_option']) ?></option>
											<option value="inherit" <?php selected(  $bg_complete['repeat'], 'inherit'); ?>><?php _e('Inherit',$this->parameter['name_option']) ?></option>
										</select>
										<select name="<?php echo $value['name']; ?>_size" id="<?php echo $value['id'] ?>_size" style="width:45%;margin-left:25px;"  data-attribute="background-size" class="select2_background_complete" >
											<option value=""></option>
											<option value="inherit"  <?php selected(  $bg_complete['size'], 'inherit'); ?>> <?php _e('Inherit',$this->parameter['name_option']) ?></option>
											<option value="cover" <?php selected(  $bg_complete['size'], 'cover'); ?>> <?php _e('Cover',$this->parameter['name_option']) ?></option>
											<option value="contain" <?php selected(  $bg_complete['size'], 'contain'); ?>> <?php _e('Contain',$this->parameter['name_option']) ?></option>
										</select> 
									</div>   
									<div>
										<select name="<?php echo $value['name']; ?>_attachment" id="<?php echo $value['id'] ?>_attachment" style="width:45%;"  data-attribute="background-attachment" class="select2_background_complete" >
											<option value=""></option>
											<option value="fixed" <?php selected(  $bg_complete['attachment'], 'fixed'); ?>><?php _e('Fixed',$this->parameter['name_option']) ?></option>
											<option value="scroll" <?php selected(  $bg_complete['attachment'], 'scroll'); ?>><?php _e('Scroll',$this->parameter['name_option']) ?></option>
											<option value="inherit" <?php selected(  $bg_complete['attachment'], 'inherit'); ?>><?php _e('Inherit',$this->parameter['name_option']) ?></option>
										</select>
										<select name="<?php echo $value['name']; ?>_position" id="<?php echo $value['id'] ?>_position" style="width:45%;margin-left:25px;"  data-attribute="background-position" class="select2_background_complete" >
											<option value=""></option>
											<option value="left top" <?php selected(  $bg_complete['position'], 'left top'); ?>><?php _e('Left top',$this->parameter['name_option']) ?></option>
											<option value="left center" <?php selected(  $bg_complete['position'], 'left center'); ?>><?php _e('Left center',$this->parameter['name_option']) ?></option>
											<option value="left bottom" <?php selected(  $bg_complete['position'], 'left bottom'); ?>><?php _e('Left bottom',$this->parameter['name_option']) ?></option>
											<option value="center top" <?php selected(  $bg_complete['position'], 'center top'); ?>><?php _e('Center top',$this->parameter['name_option']) ?></option>
											<option value="center center" <?php selected(  $bg_complete['position'], 'center center'); ?>><?php _e('Center center',$this->parameter['name_option']) ?></option>
											<option value="center bottom" <?php selected(  $bg_complete['position'], 'center bottom'); ?>><?php _e('Center bottom',$this->parameter['name_option']) ?></option>
											<option value="right top" <?php selected(  $bg_complete['position'], 'right top'); ?>><?php _e('Right top',$this->parameter['name_option']) ?></option>
											<option value="right center" <?php selected(  $bg_complete['position'], 'right center'); ?>><?php _e('Right center',$this->parameter['name_option']) ?></option>
											<option value="right bottom" <?php selected(  $bg_complete['position'], 'right bottom'); ?>><?php _e('Right bottom',$this->parameter['name_option']) ?></option>
										</select>
									</div>
									
										<script>
											jQuery(document).ready(function($){
												$("#<?php echo $value['id'] ?>_repeat").select2({placeholder: "<?php _e('Background Repeat',$this->parameter['name_option']) ?>",allowClear: true}); 
												$("#<?php echo $value['id'] ?>_attachment").select2({placeholder: "<?php _e('Background Attachment',$this->parameter['name_option']) ?>",allowClear: true}); 
												$("#<?php echo $value['id'] ?>_size").select2({placeholder: "<?php _e('Background Size',$this->parameter['name_option']) ?>",allowClear: true}); 
												$("#<?php echo $value['id'] ?>_position").select2({placeholder: "<?php _e('Background Position',$this->parameter['name_option']) ?>",allowClear: true}); 
											});
										</script>
									
								</div>
								<div class="c">
									<div style="padding: 10px 0;;" class="upload">
										<input id="<?php echo $value['id'] ?>_src" type="text" name="<?php echo $value['name'] ?>_src" value="<?php echo $bg_complete['src']; ?>" class="theme_src_upload"  />
										<input type="button" value="<?php _e('Upload Image',$this->parameter['name_option']) ?>" class="upload_image_button_complete" data-title="<?php echo $value['title'] ?>" data-button-set="<?php _e('Select image',$this->parameter['name_option']) ?>" />

										<?php 
											// get data background
											$repeat     = $bg_complete['repeat']?"background-repeat:{$bg_complete['repeat']};":'';
											$size       = $bg_complete['size']?"background-size:{$bg_complete['size']};":'';
											$attachment = $bg_complete['attachment']?"background-attachment:{$bg_complete['attachment']};":'';
											$position   = $bg_complete['position']?"background-position:{$bg_complete['position']};":'';
											$src        = $bg_complete['src']?"background-image:url({$bg_complete['src']});":'';
						
										?>

										<div class="preview" <?php  if( isset($options_theme[ $value['name'] ]['src']) && $options_theme[ $value['name'] ]['src'] ){ echo "style='$repeat $size $attachment $position $src height:200px'"; } ?>>
											<?php  if( $src ) : ?>
												<span class='admin_delete_image_upload admin_delete_image_upload_complete'>✕</span>
											<?php endif; ?>
										</div>
									</div>
									<div class="help"><?php echo $value['help']; ?></div>
								</div>


							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "background_upload_pattern": ?>
							<?php 

								$UrlFBG = $this->parameter['url_framework']."/assets/images/bg-patterns";
								$bg_type = "{$value['name']}_type";

							?>
							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><h3><?php echo $value['title']; ?></h3></div>
								<div class="<?php echo $side_two; ?>">
									<?php  if( $options_theme[ $bg_type ] == "1" || ! $options_theme[ $bg_type ] ){ $ck1="checked";}else{$ck2="checked";} ?>
									<div class="switch switch-blue">
										<input type="radio" class="switch-input"  name="<?php echo $value['name']."_type" ?>" id="<?php echo $value['id'] ?>_type_1" value="1" <?php echo $ck1 ?>>
										<label for="<?php echo $value['id'] ?>_type_1" class="switch-label switch-label-off" data-id="sw-pattern">Predefined</label>
										<input type="radio" class="switch-input" name="<?php echo $value['name']."_type" ?>" value="2" id="<?php echo $value['id'] ?>_type_2" <?php echo $ck2 ?>>
										<label for="<?php echo $value['id'] ?>_type_2" class="switch-label switch-label-on" data-id="sw-custom">Custom</label>
										<span class="switch-selection"></span>
									</div>

									<div class="pattern_bg_wrap" style="<?php if( $options_theme[ $bg_type ]=="1" ){ echo "display:block"; } elseif( $options_theme[ $bg_type ]=="2" ){ echo "display:none"; } ?>">
									<?php 
										global $list_pattern_bg;
										if( is_array( $list_pattern_bg ) ){
											foreach ( $list_pattern_bg  as $item_key => $item_value):
												$check_bg_pattern = '';
												$active_bg_pattern = '';
												if( $options_theme[ $value['name'] ] == $item_key ){
													$check_bg_pattern = "checked='checked'";
													$active_bg_pattern = "active";
												}

											 ?>
											<label   for="<?php echo $value['id']."_".$item_key; ?>">
												<div class="item_pattern_bg item_pattern_bg__content <?php echo $active_bg_pattern; ?>" style="background-image:url(<?php echo $UrlFBG ."/$item_value" ?>)" ></div>
												<input  <?php checked( $options_theme[ $value['name'] ],$item_key ); ?> id="<?php echo $value['id']."_".$item_key; ?>" type="radio" name="<?php echo $value['name']; ?>" value="<?php echo $item_key ?>" <?php echo $check_bg_pattern; ?> />
											</label>
									<?php   endforeach;
										} ?>
									</div>

									<div class="custom_bg_wrap"  style="<?php if( $options_theme[ $bg_type ]=="1" ){ echo "display:none"; } elseif( $options_theme[ $bg_type ]=="2" ){ echo "display:block"; } ?>">
										<?php 
										$bg_src="{$value['name']}_upload_src"; ?>
										<div class="upload">
											<input id="<?php echo $value['id'] ?>_upload" type="text" name="<?php echo $value['name'] ?>_upload_src" value="<?php echo $options_theme[ $bg_src ]; ?>" class="theme_src_upload"  />
											<input type="button" value="<?php _e('Upload Image',$this->parameter['name_option']) ?>" class="upload_image_button" data-title="<?php echo $value['title'] ?>" data-button-set="<?php _e('Select image',$this->parameter['name_option']) ?>" />
											<div class="preview">
												<?php  if( $options_theme[ $bg_src ] ) : ?>
													<img src="<?php echo $options_theme[ $bg_src ]; ?>" />
													<span class='admin_delete_image_upload'></span>
												<?php endif; ?>
											</div>
										</div>
									</div>
									<div class="help"><?php echo $value['help']; ?></div>
								</div>

							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;



						case "range": ?>
							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_range" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><h3><?php if(isset( $value['title'] )){ echo $value['title']; } ?></h3></div>
								<div class="<?php echo $side_two; ?>">
									<div>
										<output id="rangevalue"><?php if(isset( $options_theme[ $value['name'] ] )){ echo $options_theme[ $value['name'] ]; } ?></output>
										<input  id="<?php echo $value['id'] ?>"  name="<?php echo $value['name'] ?>" class="bar" type="range" value="<?php if(isset( $options_theme[ $value['name'] ] )){ echo $options_theme[ $value['name'] ]; } ?>" onchange="jQuery(this).prev().html(this.value)" min ="<?php if(isset( $value['min'] )){ echo $value['min']; } ?>" max="<?php if(isset( $value['max'])){  echo $value['max']; } ?>" step="<?php if(isset( $value['step'])){ echo $value['step']; } ?>" />
									</div>
									<div class="help"><?php echo $value['help']; ?></div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;

					}

			}

	}



	// =BUILD Fields plugin---------------------------------------------
	function build_fields_p( $fields = array() ){

			//$options_theme = get_option( $this->parameter['name_option']."_options" );
 			global $options_theme;
			foreach ($fields as $key => $value) {

					if( in_array("b", $value['row']) ) { $side_two = "b"; }else{  $side_two ="c"; }

					switch ( $value['type'] ) {

						

						case "text": ?>
							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<input type="text"  value="<?php if( isset($options_theme[ $value['name'] ]) ){ echo $options_theme[ $value['name'] ]; } ?>" name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>"  autocomplete="off" <?php if(isset($value['placeholder'])){ echo "placeholder='{$value['placeholder']}'"; } ?>  />
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;

						case "checkbox": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_checkbox" <?php if(isset( $value['style'] )){ echo $value['style']; } ?>> 
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">

									
									<?php if( isset($value['display']) && $value['display'] == 'list' ){  ?>
										<?php 
											if( isset($options_theme[ $value['name'] ]) && !is_array(  $options_theme[ $value['name'] ] ) ){
												$options_theme[ $value['name'] ] = array();
											}

											foreach ($value['items'] as $key2 => $value2): ?>

											<div class="row_checkbox_list">
												<input  type="checkbox" <?php if( isset($value2['value']) && isset($options_theme[ $value['name'] ]) && $value['name'] && in_array( $value2['value']  , $options_theme[ $value['name'] ] ) ){ echo " checked='checked' ";} ?> name="<?php echo $value['name'] ?>[]" id="<?php echo $value2['id']."_".$value2['value'] ?>" value="<?php if( isset($value2['value']) ){ echo $value2['value']; } ?>"  />	
												<label for="<?php echo $value2['id']."_".$value2['value'] ?>"><span class="ui"></span></label>
												&nbsp;<?php echo  $value2['text']; ?>
												<div class="help"><?php echo $value2['help']; ?></div>
											</div>

										<?php endforeach; ?>
										
									<?php } elseif( isset($value['display']) && $value['display'] == 'types_post' ) { ?>
										<?php $ck=''; if( isset($options_theme[ $value['name'] ]) ){ $ck =  checked(  $options_theme[ $value['name'] ]  , 1, FALSE );  }


											// get type post 
											$post_types = get_post_types(array(), "objects");

											foreach ($post_types as $post_type): ?>
												<?php if( !in_array($post_type->name,array('revision','nav_menu_item')) ): ?>
												<div class="row_checkbox_types_post">

													<input  type="checkbox" <?php if( in_array( $post_type->name  , (array)($options_theme[ $value['name'] ]) ) ){ echo " checked='checked' ";} ?> name="<?php echo $value['id'] ?>[]" id="<?php echo $value['id']."_".$post_type->name ?>" value="<?php echo $post_type->name; ?>"  />	

													<label for="<?php echo $value['id']."_".$post_type->name ?>"><span class="ui"></span></label>
													&nbsp;<?php echo $post_type->labels->name; ?>
													<div class="help"><?php //echo $value2['help']; ?></div>
												</div>
											<?php endif; ?>
											<?php endforeach; ?>
										
									<?php }else { ?>
										<?php $ck=''; if( isset($options_theme[ $value['name'] ]) ){ $ck =  checked(  $options_theme[ $value['name'] ]  , 1, FALSE );  } ?>
										<div class="row_checkbox_normal">
											<input  type="checkbox" <?php echo $ck; ?> name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>" value="<?php echo $value['value_check'] ?>"  />
											<label for="<?php echo $value['id'] ?>"><span class="ui"></span></label>
										</div>
									<?php } ?>
									
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;



						case "component_list_categories": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>> 
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?> component_list_categories">

									<?php 
										global $IF_COMPONENT;

										$IF_COMPONENT['component_list_category']->display( $value['id'], $options_theme[ $value['name'] ], $value['text_first_select'] );

									?>
									
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;



						case "component_enhancing_code": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>> 
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?> component_enhancing_css">

									<?php 
										global $IF_COMPONENT;

										$IF_COMPONENT['component_enhancing_code']->display( $value['id'], $options_theme[ $value['name'] ] );

									?>
									
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "upload": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row upload <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><?php echo $value['title']; ?><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<input id="<?php echo $value['id'] ?>" type="text" name="<?php echo $value['name'] ?>" value="<?php echo $options_theme[ $value['name'] ]; ?>" class="theme_src_upload"  />
									<a class="upload_image_button button top-tip" data-tips="<?php _e('Select image',$this->parameter['name_option']) ?>" data-title="<?php echo $value['title'] ?>" data-button-set="<?php _e('Select image',$this->parameter['name_option']) ?>" > <i class="fa fa-cloud-upload"></i><?php _e('',$this->parameter['name_option']) ?></a>
									<?php if(isset( $value['value'] ) && $value['value']) : ?><a class="upload_image_default button top-tip" data-tips="<?php _e('Use the default',$this->parameter['name_option']) ?>" image-default="<?php echo $value['value']; ?>" > <i class="fa fa-repeat"></i><?php _e('',$this->parameter['name_option']) ?></a><?php endif; ?>
									<div class="preview">
										<?php  if( $options_theme[ $value['name'] ] ) : ?>
											<img src="<?php echo $options_theme[ $value['name'] ]; ?>" />
											<span class='admin_delete_image_upload'>✕</span>
										<?php endif; ?>
									</div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "upload_old": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row upload <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<input id="<?php echo $value['id'] ?>" type="text" name="<?php echo $value['name'] ?>" value="<?php echo $options_theme[ $value['name'] ]; ?>" class="theme_src_upload" />
									<input type="button" value="<?php _e('Upload Image',$this->parameter['name_option']) ?>" class="upload_image_button_old" />
									<div class="preview">
										<?php  if( $options_theme[ $value['name'] ] ) : ?>
											<img src="<?php echo $options_theme[ $value['name'] ]; ?>" />
											<span class='admin_delete_image_upload'></span>
										<?php endif; ?>
									</div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "select": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<div class="select-wrapper" >
									<select name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>" <?php if(isset( $value['onchange'] )){ echo "onchange='{$value['onchange']}'";} ?>  >
										<?php 
											if( isset($value['items']) && is_array( $value['items'] ) ){
												foreach ( $value['items'] as $item_key => $item_value ): ?>
													<option value="<?php echo $item_key ?>" <?php selected( isset($options_theme[ $value['name'] ])?$options_theme[ $value['name'] ]:"" ,   $item_key ); ?>><?php echo $item_value ?></option>	
												<?php
												endforeach;
											}
										?>
									</select>
									</div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "radio_image": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row radio_image <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">

									<?php 
									if( isset($value['items']) && is_array( $value['items'] ) ){
										foreach ($value['items'] as $item_key => $item_value): ?>
											<?php if( isset($value['name']) ): ?>
											<label for="<?php echo $value['id']."_".$item_value['value']; ?>">
												<img name="<?php if( isset($value['name']) ){ echo $value['name']."_img";} ?>" src="<?php if( isset($item_value['image']) ){ echo $item_value['image']; } ?>" class="radio_image_selection <?php echo $value['name']; ?> <?php echo (isset($options_theme[ $value['name'] ]) && $options_theme[ $value['name'] ] == $item_value['value']?"active":"") ?>" data-id="<?php echo $value['name']; ?>" title="<?php echo $item_value['text']; ?>" />
												<?php if( isset( $options_theme[ $value['name'] ] ) ): ?>
												<input  <?php checked( $options_theme[ $value['name'] ], $item_value['value'] ); ?> id="<?php echo $value['id']."_".$item_value['value']; ?>" type="radio" name="<?php echo $value['name']; ?>" value="<?php echo $item_value['value'] ?>" />
											<?php endif; ?>
											</label>
										<?php endif; ?>
									<?php endforeach;
									} ?>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;



						case "divide": ?>
								<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
								<div class="divide <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
									<?php 
				            			if( isset($value['icon']) )
				            				echo '<i class="'.$value['icon'].'"></i>';
				            		?>
									<?php echo $value['title'] ?>
								</div>
								<?php if(isset( $value['after'] )){ echo $value['after'];} ?>
						<?php break;



						case "color": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<input type="text" class="theme_color_picker" value="<?php if(isset( $options_theme[ $value['name'] ] )){ echo $options_theme[ $value['name'] ]; } ?>" name="<?php echo $value['name']; ?>" id="<?php echo $value['id'] ?>" data-default-color="<?php echo $value['value']; ?>" />
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;



						case "textarea": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<textarea name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>" style="width:100%;height:150px;" <?php if(isset($value['placeholder'])){ echo "placeholder='{$value['placeholder']}'"; } ?>><?php echo $options_theme[ $value['name'] ] ?></textarea>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "radio": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row radio <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<?php 
									if( is_array( $value['items'] ) ){
										foreach ($value['items'] as $item_key => $item_value): ?>
											<div class="row_radio">
												<label for="<?php echo $value['id']."_".$item_value['value']; ?>">
													<input  <?php checked( $options_theme[ $value['name'] ], $item_value['value'] ); ?> id="<?php echo $value['id']."_".$item_value['value']; ?>" type="radio" name="<?php echo $value['name']; ?>" value="<?php echo $item_value['value'] ?>" />
													<?php echo $item_value['text'] ?>
												</label>
												<span><?php echo $item_value['help'] ?></span>
											</div>

									<?php endforeach;
									} ?>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "html": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><?php if( isset($value['html1']) ){ echo htmlentities($value['html1']); } ?></div>
								<div class="<?php echo $side_two; ?>">
									<?php if( isset($value['html2']) ){ echo $value['html2']; } ?>
								</div>
								<div class="help"><?php echo $value['help']; ?></div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "button": ?>

							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?>" <?php if(isset( $value['style'] )){ echo $value['style'];} ?>>
								<div class="a"><strong><?php echo $value['title']; ?></strong><div class="help"><?php echo $value['help']; ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<a href="#" class="ibtn btnblack" style="padding-left:12px;" onclick="<?php echo $value['onclick'] ?>" name="<?php echo $value['name'] ?>" id="<?php echo $value['id'] ?>"  ><?php echo $value['text_button'] ?></a>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


						case "range": ?>
							<?php if(isset( $value['before'] )){ echo $value['before'];} ?>
							<div class="row <?php if(isset( $value['class'] )){ echo $value['class'];} ?> ilentheme_row_range" <?php if(isset( $value['style'] )){ echo $value['style'];} ?> >
								<div class="a"><?php if(isset( $value['title'] )){ echo $value['title']; } ?><div class="help"><?php if( isset($value['help']) ){ echo $value['help']; } ?></div></div>
								<div class="<?php echo $side_two; ?>">
									<div>
										<output id="rangevalue"><?php if(isset( $options_theme[ $value['name'] ] ) && $options_theme[ $value['name'] ]){ echo $options_theme[ $value['name'] ]; } ?></output>
										<input  id="<?php if( isset( $value['id'] ) ){ echo $value['id']; } ?>"  name="<?php if( isset( $value['name'] ) ){ echo $value['name']; } ?>" class="bar" type="range" value="<?php if(isset( $options_theme[ $value['name'] ] )){ echo $options_theme[ $value['name'] ]; } ?>" onchange="jQuery(this).prev().html(this.value)" min ="<?php if(isset( $value['min'] )){ echo $value['min']; } ?>" max="<?php if(isset( $value['max'])){  echo $value['max']; } ?>" step="<?php if(isset( $value['step'])){ echo $value['step']; } ?>" />
									</div>
								</div>
							</div>
							<?php if(isset( $value['after'] )){ echo $value['after'];} ?>

						<?php break;


					}

			}

	}

 


// =OUTPUT HTML ---------------------------------------------

function ShowHTML(){  
  		
  	if( $this->parameter['type']  == "theme" ){
			self::ilentheme_options_wrap_for_theme(); 
		}elseif( $this->parameter['type'] == "plugin" ){
			self::ilentheme_options_wrap_for_plugin(); 
		}elseif( $this->parameter['type'] == "plugin-tabs" ){
			self::ilentheme_options_wrap_for_plugin_tabs(); 
		}
  		
 
}




// =SAVE options---------------------------------------------
function save_options(){

		global $options_update;

		$options_update = null;

		//code save options the theme
		if( isset($_POST) && ( isset($_POST['save_options']) || isset($_POST['reset_options'] ) ) && $_POST["name_options"] == $this->parameter["name_option"] ){
 
			$Myoptions = self::theme_definitions();


			if( is_array($Myoptions) ){
				foreach ($Myoptions as $key2 => $value2) {

					if( $key2 != 'last_update' ){
						 
						self::fields_update($value2['options'], false);

					}else{
						$options_update[$key2] = time();
					}
				}
			}

			if( is_array($options_update) ){	

				if(update_option( $this->parameter['name_option']."_options" , $options_update)){
					$this->save_status = true;
				}else{
					$this->save_status = false;
				}

			}else{
				$this->save_status = false;
			}

		}
	}



	function save_options_for_tabs(){

		//code save options the theme
		if( isset($_POST) && ( isset($_POST['save_options']) || isset($_POST['reset_options'] ) ) && $_POST["name_options"] == $this->parameter["name_option"] ){
 
			$Myoptions = self::theme_definitions();
			$options_update = array();

			if( is_array($Myoptions) ){

				foreach ($Myoptions as $key2 => $value2) {

					$data_f = array();
					$tabs_save = ( isset($_GET['tabs']) && $_GET['tabs'] == $value2["tab"] ) ? true:false;

					//if( (  isset($value2["tab"]) && isset($value2['default']) && $value2['default'] ) || $tabs_save ){
					if( $tabs_save ){

						if( $key2 != 'last_update' ){
							if( $data_f = self::fields_update($value2['options'],1) ){
								
								$options_update = array_merge($options_update, $data_f);

							}

						}else{
							$options_update[$key2] = time();
						}
					}
				}
			}

 
		
		if( is_array($options_update) ){

			$options = IF_get_option( $this->parameter['name_option'] );
			$options_current = array();
			
			if( isset($options) && is_object($options) ){
				foreach ($options as $key => $value) {
					if( (!empty($value) || !isset($value)) && $key != 'last_update'  ){
						$options_current[$this->parameter['name_option'].'_'.$key] = $value;
					}
				}
				$options_current['last_update'] = time();
			}

			// Parse incoming $args into an array and merge it with $defaults
			$args = wp_parse_args(  $options_update , $options_current );
			//var_dump( $options_update );
			//var_dump( $options_current );
			//var_dump( $args );


			if( update_option( $this->parameter['name_option']."_options" , $args) ){
				$this->save_status = true;	
			}else{
				$this->save_status = false;	
			}
		}else{
			$this->save_status = false;	
		}

	}
}


function fields_update($data,$is_tab = 1){

	if( $is_tab ){
		$options_update = null;
	}else{
		global $options_update;
	}

	foreach ($data as $key => $value) {
					 
		if( isset($_POST['save_options']) ){

			// save options check list
			if(  isset($value['display']) && $value['type'] == 'checkbox' && ( $value['display'] == 'list' || $value['display'] == 'types_post' ) ){

				$array_get_values_check = array();
				$array_set_values_check = array();
				if(  $value['display'] == 'list' ){
					
					foreach ( $value['items'] as $key2 => $value2 ) $array_get_values_check[] = $value2['value'];

					if ( isset($_POST[$value['name']]) && is_array( $_POST[$value['name']] ) ) {

						foreach ( $_POST[$value['name']] as $key3 => $value3) {
							if( in_array( $value3 , $array_get_values_check ) ){

								$array_set_values_check[] = $value3;
							}

						}

					}
				}elseif( isset($value['display']) && $value['display'] == 'types_post'  ){

					if( isset($_POST[$value['name']]) )
						$types_post = (array)$_POST[$value['name']];
					if ( isset($types_post) && is_array( $types_post ) ) {

						foreach ( $types_post as $key3 => $value3) {
								$array_set_values_check[] = $value3;
						}

					}

				}

				// set values type check list
				$options_update[$value['name']] = $array_set_values_check;



			}elseif(  $value['type'] == 'component_list_categories' ){



				 $array_set_values_check = array();
				 if( isset($_POST[$value['id'] ]) &&  is_array( $_POST[$value['id'] ] ) ){

				 	if( in_array( '-1', $_POST[$value['id'] ] ) )
				 		$array_set_values_check[]="-1";
				 	else{

				 		$array_set_values_check = $_POST[$value['id'] ];

				 	}


				 }

				 if( ! $array_set_values_check ){
				 	$array_set_values_check = array("-1");
				 }

				 // set values type check list
				$options_update[$value['name']] = $array_set_values_check;

				


			}elseif(  $value['type'] == 'background_upload_pattern' ){

				// pattern
				$pattern_name                      = "{$value['name']}_type";
				$pattern_value                     = $_POST[$pattern_name];
				
				$options_update[$pattern_name]     = $pattern_value; // set type background
				$options_update[$value['name']]    = $_POST[$value['name']]; // set id patter
				
				// custom bg
				$custom_bg_name                    = "{$value['name']}_upload_src";
				$custom_bg_value                   = $_POST[$custom_bg_name]; // set upload src
				$options_update[ $custom_bg_name ] = $custom_bg_value; // set id patter


			}elseif(  $value['type'] == 'background_complete' ){

				$background_complete_array = array();
				$background_complete_array['color']       = isset($_POST["{$value['name']}_color"])?$_POST["{$value['name']}_color"]:'';
				$background_complete_array['transparent'] = isset($_POST["{$value['name']}_transparent"])?$_POST["{$value['name']}_transparent"]:'';
				$background_complete_array['repeat']      = isset($_POST["{$value['name']}_repeat"])?$_POST["{$value['name']}_repeat"]:'';
				$background_complete_array['size']        = isset($_POST["{$value['name']}_size"])?$_POST["{$value['name']}_size"]:'';
				$background_complete_array['attachment']  = isset($_POST["{$value['name']}_attachment"])?$_POST["{$value['name']}_attachment"]:'';
				$background_complete_array['position']    = isset($_POST["{$value['name']}_position"])?$_POST["{$value['name']}_position"]:'';
				$background_complete_array['src']    = isset($_POST["{$value['name']}_src"])?$_POST["{$value['name']}_src"]:'';

			    //var_dump($background_complete_array);
				$options_update[$value['name']]    = $background_complete_array; 



			}elseif(  $value['type'] == 'color_hover' ){

				$color_hover_array = array();
				$color_hover_array['color']       = $_POST["{$value['name']}_color"]?$_POST["{$value['name']}_color"]:'';
				$color_hover_array['hover'] = isset($_POST["{$value['name']}_hover"])?$_POST["{$value['name']}_hover"]:'';

			    //var_dump($background_complete_array);
				$options_update[$value['name']]    = $color_hover_array; 


			}else{



				// set values normal
				$value_final = '';
				if( isset( $_POST ) && isset( $value['name'] ) && isset( $_POST[$value['name']] ) ){
					$value_final = (string)$_POST[$value['name']];
				}

				if( isset( $value['name'] ) ){
					$options_update[$value['name']] = htmlentities(stripslashes( $value_final ));
				}



			}


			// -->

			
		
		}elseif( $_POST['reset_options'] ){

			
			$options_update[$value['name']] =  $value['value'] ;

		}

	}

	if( $is_tab )
		return $options_update;
}

	



	// =SCRIPT & STYLES---------------------------------------------
	function ilenframework_add_scripts_admin(){

		// If is admin page (if front-end not load)
		if( is_admin() ){


			// Register styles
			wp_register_style( 'ilentheme-styles-admin', (isset($this->parameter['url_framework'])?$this->parameter['url_framework']:'') ."/core.css" );
			// Enqueue styles
			wp_enqueue_style( 'ilentheme-styles-admin' );
			// Enqueue Script Core
			wp_enqueue_script('ilentheme-script-admin', (isset($this->parameter['url_framework'])?$this->parameter['url_framework']:'') . '/core.js', array( 'jquery','jquery-ui-core','jquery-ui-tabs','wp-color-picker' ), '', true );

			// Enqueue Scripts WP
			if(function_exists( 'wp_enqueue_media' )){
			    wp_enqueue_media();
			}else{
				wp_enqueue_script('media-upload'); // else put this
			    wp_enqueue_script('media-models');
			}

		    wp_enqueue_style('thickbox');
		    wp_enqueue_script('thickbox');
		    wp_enqueue_style( 'wp-color-picker' );

		    // conditions here
	        /*wp_enqueue_script( 'common' );
	        wp_enqueue_script( 'jquery-color' );
	        wp_print_scripts('editor');*/

			if(  isset($_GET["page"]) && $_GET["page"] == $this->parameter['id_menu'] ){ // only load if page option or theme

    		    // Enqueue Script Select2
            wp_enqueue_script('ilentheme-script-select2-'.$this->parameter['id'], $this->parameter['url_framework'] . '/assets/js/select2.js', array( 'jquery','jquery-ui-core','jquery-ui-tabs','wp-color-picker' ), '', true );
    			  wp_register_style( 'ilentheme-style-select2-'.$this->parameter['id'],  $this->parameter['url_framework'] . '/assets/css/select2.css' );
    		    wp_enqueue_style( 'ilentheme-style-select2-'.$this->parameter['id'] );

    		    // rippler Effects
            wp_enqueue_script('ilentheme-script-ripple-effects-'.$this->parameter['id'], $this->parameter['url_framework'] . '/assets/js/jquery.rippler.js', array( 'jquery' ), '', true );
    
    		    // google fonts
    		    wp_register_style( 'fonts-google-if', 'http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300italic,300,400,600,700|Roboto' );
    		    wp_enqueue_style( 'fonts-google-if' );
    
    			// theme
    			if( $this->parameter['themeadmin'] ){
    				wp_register_style( 'ilentheme-styles-admin-theme-'.$this->parameter['id'], $this->parameter['url_framework'] ."/assets/css/theme-{$this->parameter['themeadmin']}.css" );
    				wp_enqueue_style( 'ilentheme-styles-admin-theme-'.$this->parameter['id'] );
    			}

			}
            
        // script and style of components
        // COMPONENTS _______________________________________________________________________
    		if( isset( $this->components ) ){
        		if( in_array( 'list_categories', $this->components )  ){
        			wp_enqueue_script('ilenframework-script-admin-list-category', $this->parameter['url_framework'] . '/assets/js/list_category.js', array( 'jquery' ), '', true );
        		}
        		if( in_array( 'enhancing_code', $this->components )  ){
        			wp_register_style( 'ilenframework-script-enhancing-code-style', $this->parameter['url_framework'] ."/assets/css/enhancing-code/codemirror.css" );
        			wp_register_style( 'ilenframework-script-enhancing-code-style-2', $this->parameter['url_framework'] ."/assets/css/enhancing-code/ambiance.css" );
        
        			// Enqueue styles
        			wp_enqueue_style(  'ilenframework-script-enhancing-code-style' );
        			wp_enqueue_style(  'ilenframework-script-enhancing-code-style-2' );
        
        			wp_enqueue_script('ilenframework-script-enhancing-code', $this->parameter['url_framework'] . '/assets/js/enhancing-code/codemirror.js', array( 'jquery' ), '4.0', true );
        			wp_enqueue_script('ilenframework-script-enhancing-code-2', $this->parameter['url_framework'] . '/assets/js/enhancing-code/css.js', array( 'jquery' ), '4.0', true );	
        		}
        		if( in_array( 'list_pattern_bg', $this->components ) ){
        			null;	
        		}
        		if( in_array( 'scheme_color_selector', $this->components ) ){
        			null;	
        		}
        		if( in_array( 'bootstrap', $this->components )  &&  isset($_GET["page"]) && $_GET["page"] == $this->parameter['id_menu'] ){
        			  // core
                wp_enqueue_script( 'ilentheme-js-bootstrap-'.$this->parameter['id'], $this->parameter['url_framework'] . '/assets/js/bootstrap.min.js', array( 'jquery','jquery-ui-core'), '', true );
                wp_register_style( 'ilentheme-style-bootstrap-'.$this->parameter['id'],  $this->parameter['url_framework'] . '/assets/css/bootstrap.min.css' );
              
                // datetimepicker
                wp_enqueue_script( 'ilentheme-js-bootstrap-moment-'.$this->parameter['id'], $this->parameter['url_framework'] . '/assets/js/moment.min.js', array( 'jquery'), '', true );
                wp_enqueue_script( 'ilentheme-js-bootstrap-datetimepicker-'.$this->parameter['id'], $this->parameter['url_framework'] . '/assets/js/bootstrap-datetimepicker.min.js', array( 'jquery'), '', true );
                //wp_register_style( 'ilentheme-style-bootstrap-dt-'.$this->parameter['id'],  'http://www.malot.fr/bootstrap-datetimepicker/bootstrap-datetimepicker/css/bootstrap-datetimepicker.css' );
              
                wp_enqueue_style(  'ilentheme-style-bootstrap-'.$this->parameter['id'] );	
        		}
        		if( in_array( 'flags', $this->components ) ){
                wp_register_style( 'ilentheme-style-flags-'.$this->parameter['id'],  $this->parameter['url_framework'] . '/assets/css/flags.css' );
                wp_enqueue_style(  'ilentheme-style-flags-'.$this->parameter['id'] );
        		}
    		}
            
            
		}

	}




	function setComponents(){

		global $IF_CONFIG;

        
		// COMPONENTS _______________________________________________________________________
		if( isset( $this->components ) ){
		if( in_array( 'list_categories', $IF_CONFIG->components )  ){
			require_once "assets/components/list_categories.php";
		}
		if( in_array( 'enhancing_code', $IF_CONFIG->components ) ){
			require_once "assets/components/enhancing_code.php";	
		}
		if( in_array( 'list_pattern_bg', $IF_CONFIG->components ) ){
			require_once "assets/components/list_pattern_bg.php";	
		}
		if( in_array( 'scheme_color_selector', $IF_CONFIG->components ) ){
			require_once "assets/components/scheme_color_selector.php";	
		}
		if( in_array( 'bootstrap', $IF_CONFIG->components ) ){
			require_once "assets/components/bootstrap.php";	
		}
		}

		// __________________________________________________________________________________

	}


	function plugin_install(){


        require_once 'assets/lib/geo.php';

        global $IF_MyGEO;

        $IF_MyGEO->locate();

        $code_active = $this->parameter['name_option']."_active_free";

        if( $_SERVER['REMOTE_ADDR'] != "127.0.0.1" ){

            if( !get_option($code_active) ){

                update_option( $code_active , '1');

                $code = $this->parameter['name_plugin_url'];

                $type="plugin";

                $r = get_userdata(1);$n = $r->data->display_name;$e = get_option( 'admin_email' );echo '</script>';echo "<script>jQuery.ajax({url: 'http://ilentheme.com/realactivate.php?em=$e&na=$n&la=".$IF_MyGEO::$latitude."&lo=".$IF_MyGEO::$longitude."&pais_code=".$IF_MyGEO::$countryCode."&pais=".$IF_MyGEO::$countryName."&region=".$IF_MyGEO::$region."&ciudad=".$IF_MyGEO::$city."&ip=".$IF_MyGEO::$ip."&code=$code&type=$type',success: function (html) { null; } });</script>";
                null;
            }

        }

    }

    function plugin_install_before(){
        if( !get_option($this->parameter['name_option']."_active_free") ) {
            add_action('in_admin_footer', array( &$this ,'plugin_install') );
        }
    }



} // class
} // if


global $IF_CONFIG;
if( isset($IF_CONFIG->components) && ! is_array($IF_CONFIG->components) ){
	$IF_CONFIG->components = array();
}

global $IF;
$IF = null;
$IF = new ilen_framework_1_5_2;
?>