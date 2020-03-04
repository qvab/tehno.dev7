<?php
/**
 * Main class with main actions and filters: CPCFF_MAIN class
 *
 * @package CFF.
 * @since 1.0.170
 */

if(!class_exists('CPCFF_MAIN'))
{
	/**
	 * Class that defines the main actions and filters, and plugin's functionalities.
	 *
	 * @since  1.0.170
	 */
	class CPCFF_MAIN
	{
		/**
		 * Counter of forms in a same page
		 * Metaclass property.
		 *
		 * @since 1.0.170
		 * @var int $form_counter
		 */
		static public $form_counter = 0;

		/**
		 * Identifies if the class was instanciated from the public website or WordPress
		 * Instance property.
		 *
		 * @sinze 1.0.170
		 * @var bool $_is_admin
		 */
		private $_is_admin = false;

		/**
		 * Plugin URL
		 * Instance property.
		 *
		 * @sinze 1.0.170
		 * @var string $_plugin_url
		 */
		private $_plugin_url;

		/**
		 * Flag to know if the public resources were included
		 * Instance property.
		 *
		 * @sinze 1.0.170
		 * @var bool $_are_resources_loaded default false
		 */
		private $_are_resources_loaded = false;

		/**
		 * Constructs a CPCFF_MAIN object, and define the hooks to the filters and actions.
		 */
		public function __construct()
		{
			// Initializes the $_is_admin property
			$this->_is_admin = is_admin();

			// Initializes the $_plugin_url property
			$this->_plugin_url = plugin_dir_url(CP_CALCULATEDFIELDSF_MAIN_FILE_PATH);

			// Plugin activation/deactivation
			$this->_activate_deactivate();

			// Load the language file
			add_action( 'plugins_loaded', array($this, 'plugins_loaded') );

			// Run the initialization code
			add_action( 'init', array($this, 'init'), 1 );

		} // End __construct

		/**
		 * Loads the primary resources, previous to the plugin's initialization
		 *
		 * Loads resources like the laguages files, etc.
		 *
		 * @return void.
		 */
		public function plugins_loaded()
		{
			// Load the language file
			$this->_textdomain();

			// Load controls scripts
			$this->_load_controls_scrips();
		} // End plugins_loaded

		/**
		 * Initializes the plugin, runs as soon as possible.
		 *
		 * Initilize the plugin's sections, intercepts the submissions, generates the resources etc.
		 *
		 * @return void.
		 */
		public function init()
		{
			if ( $this->_is_admin ) // Initializes the WordPress modules.
			{
				// Adds the plugin links in the plugins sections
				add_filter( 'plugin_action_links_'.CP_CALCULATEDFIELDSF_BASE_NAME, array($this, 'links' ) );

				// Creates the menu entries in the WordPress menu.
				add_action( 'admin_menu', array( $this, 'admin_menu' ) );

				// Displays the shortcode insertion buttons.
				add_action( 'media_buttons', array( $this, 'media_buttons' ) );

				// Loads the admin resources
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_resources' ), 1 );
			}
			else // Initializes the public modules.
			{
				$this->_define_shortcodes();
			}
		} // End init

		/**
		 * Adds the plugin's links in the plugins section.
		 *
		 * Links for accessing to the help, settings, developers website, etc.
		 *
		 * @param array $links.
		 *
		 * @return array.
		 */
		public function links( $links )
		{
			array_unshift(
				$links,
				'<a href="http://cff.dwbooster.com/customization" target="_blank">'.__('Request custom changes').'</a>',
				'<a href="admin.php?page=cp_calculated_fields_form">'.__('Settings').'</a>',
				'<a href="http://cff.dwbooster.com/download" target="_blank">'.__('Upgrade').'</a>',
				'<a href="http://cff.dwbooster.com/documentation" target="_blank">'.__('Help').'</a>'
			);
			return $links;
		} // End links

		/**
		 * Prints the buttons for inserting the different shortcodes into the pages/posts contents.
		 *
		 * Prints the HTML code that appears beside the media button with the icons and code to insert the shortcodes:
		 *
		 * - CP_CALCULATED_FIELDS
		 * - CP_CALCULATED_FIELDS_RESULT
		 * - CP_CALCULATED_FIELDS_VAR
		 *
		 * @return void.
		 */
		public function media_buttons()
		{
			print '<a href="javascript:cp_calculatedfieldsf_insertForm();" title="'.esc_attr__('Insert Calculated Fields Form', 'calculated-fields-form' ).'"><img src="'.$this->_plugin_url.'images/cp_form.gif" alt="'.esc_attr__('Insert Calculated Fields Form', 'calculated-fields-form' ).'" /></a><a href="javascript:cp_calculatedfieldsf_insertForm(true);" title="'.esc_attr__('Insert Calculated Fields Form Results', 'calculated-fields-form' ).'"><img src="'.$this->_plugin_url.'images/cp_form_result.gif" alt="'.esc_attr__('Insert Calculated Fields Form Results', 'calculated-fields-form' ).'" /></a><a href="javascript:cp_calculatedfieldsf_insertVar();" title="'.esc_attr__('Create a JavaScript var from POST, GET, SESSION, or COOKIE var', 'calculated-fields-form' ).'"><img src="'.$this->_plugin_url.'images/cp_var.gif" alt="'.esc_attr__('Create a JavaScript var from POST, GET, SESSION, or COOKIE var', 'calculated-fields-form' ).'" /></a>';
		} // End media_buttons

		/**
		 * Generates the entries in the WordPress menu.
		 *
		 * @return void.
		 */
		public function admin_menu()
		{
			// Settings page
			add_options_page('Calculated Fields Form Options', 'Calculated Fields Form', 'manage_options', 'cp_calculated_fields_form', 'cp_calculatedfieldsf_html_post_page' );

			// Menu option
			add_menu_page( 'Calculated Fields Form Options', 'Calculated Fields Form', 'manage_options', 'cp_calculated_fields_form', 'cp_calculatedfieldsf_html_post_page' );

			// Submenu options
			add_submenu_page( 'cp_calculated_fields_form', 'Documentation', 'Documentation', 'read', "cp_calculated_fields_form_sub2", 'cp_calculatedfieldsf_html_post_page' );

			add_submenu_page( 'cp_calculated_fields_form', 'Upgrade', 'Upgrade', 'read', "cp_calculated_fields_form_sub3", 'cp_calculatedfieldsf_html_post_page' );
		} // End admin_menu

		/**
		 * Loads the javascript and style files.
		 *
		 * Checks if there is the settings page of the plugin for loading the corresponding JS and CSS files,
		 * or if it is a post or page the script for inserting the shortcodes in the content's editor.
		 *
		 * @since 1.0.171
		 *
		 * @param string $hook.
		 * @return void.
		 */
		public function admin_resources( $hook )
		{
			// Checks if it is the plugin's page
			if ( isset($_GET['page']) && 'cp_calculated_fields_form' == $_GET['page'] )
			{
				wp_enqueue_script( "jquery" );
				wp_enqueue_script( "jquery-ui-core" );
				wp_enqueue_script( "jquery-ui-sortable" );
				wp_enqueue_script( "jquery-ui-tabs" );
				wp_enqueue_script( "jquery-ui-droppable" );
				wp_enqueue_script( "jquery-ui-button" );
				wp_enqueue_script( "jquery-ui-datepicker" );
				wp_deregister_script('query-stringify');
				wp_register_script('query-stringify', plugins_url('/js/jQuery.stringify.js', CP_CALCULATEDFIELDSF_MAIN_FILE_PATH), array( 'jquery' ), 'pro');
				wp_enqueue_script( "query-stringify" );

				//URL to the admin resources
				$admin_resources = admin_url( "admin.php?page=cp_calculated_fields_form&cp_cff_resources=admin" );
				wp_enqueue_script( 'cp_calculatedfieldsf_builder_script', $admin_resources, array("jquery","jquery-ui-core","jquery-ui-sortable","jquery-ui-tabs","jquery-ui-droppable","jquery-ui-button", "jquery-ui-accordion","jquery-ui-datepicker","query-stringify") );
				wp_enqueue_script( 'cp_calculatedfieldsf_builder_script_caret', plugins_url('/js/jquery.caret.js', CP_CALCULATEDFIELDSF_MAIN_FILE_PATH),array("jquery"));
				wp_enqueue_style('jquery-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
			}

			// Checks if it is a page or post
			if( 'post.php' == $hook  || 'post-new.php' == $hook )
			{
				wp_enqueue_script( 'cp_calculatedfieldsf_script', plugins_url('/js/cp_calculatedfieldsf_scripts.js', CP_CALCULATEDFIELDSF_MAIN_FILE_PATH) );
			}
		} // End admin_resources

		/**
		 * Returns the public version of the form wih its resources.
		 *
		 * The method class the filters: cpcff_pre_form, and cpcff_the_form
		 * @since 1.0.171
		 * @param array $atts includes the attributes required to identify the form, and create the variables.
		 * @return string $buffered_contents a text with the public version of the form and resources.
		 */
		public function public_form( $atts )
		{
			// If the website is being visited by crawler, display empty text.
			if( CPCFF_AUXILIARY::is_crawler() ) return '';
			global $wpdb, $cpcff_default_texts_array;

			if( empty( $atts[ 'id' ] ) ) // if was not passed the form's id get all.
			{
				$myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.CP_CALCULATEDFIELDSF_FORMS_TABLE );
			}
			else
			{
				$myrows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.CP_CALCULATEDFIELDSF_FORMS_TABLE." WHERE id=%d",$atts[ 'id' ] ) );
			}

			if( empty( $myrows ) ) return ''; // The form does not exists, or there are no forms.
			$atts[ 'id' ] = $myrows[0]->id; // If was not passed the form's id, uses the if of first form.
			$id = $atts[ 'id' ]; // Alias for the $atts[ 'id' ] variable.

			CPCFF_MAIN::$form_counter++; // Current form

			/**
			 * Filters applied before generate the form,
			 * is passed as parameter an array with the forms attributes, and return the list of attributes
			 */
			$atts = apply_filters( 'cpcff_pre_form',  $atts );

			ob_start();

			// Constant defined to protect the "inc/cpcff_public_int.inc.php" file against direct accesses.
			if ( !defined('CP_AUTH_INCLUDE') ) define('CP_AUTH_INCLUDE', true);

			$this->_public_resources($id); // Load form scripts and other resources

			/* TO-DO: This method should be analyzed after moving other functions to the main class . */
			$button_label = cp_calculatedfieldsf_get_option('vs_text_submitbtn', 'Submit', $id);
			$button_label = ($button_label==''?'Submit':$button_label);
			@include CP_CALCULATEDFIELDSF_BASE_PATH . '/inc/cpcff_public_int.inc.php';

			$buffered_contents = ob_get_contents();

			// The attributes excepting "id" are converted in javascript variables with a global scope
			if( count( $atts ) > 1 )
			{
				$buffered_contents .= '<script>';
				foreach( $atts as $i => $v )
				{
					if( $i != 'id' && !is_numeric( $i ) )
					{
						$nV = ( is_numeric( $v ) ) ? $v : json_encode( $v ); // Sanitizing the attribute's value
						$buffered_contents .= $i.'='.$nV.';';
						$buffered_contents .= 'if(typeof '.$i.'_arr == "undefined") '.$i.'_arr={}; '.$i.'_arr["_'.self::$form_counter.'"]='.$nV.';';
					}
				}
				$buffered_contents .= '</script>';
			}
			ob_end_clean();

			/**
			 * Filters applied after generate the form,
			 * is passed as parameter the HTML code of the form with the corresponding <LINK> and <SCRIPT> tags,
			 * and returns the HTML code to includes in the webpage
			 */
			$buffered_contents = apply_filters( 'cpcff_the_form', $buffered_contents,  $atts[ 'id' ] );

			return $buffered_contents;
		} // End  public_form

		/*********************************** PRIVATE METHODS  ********************************************/

		/**
		 * Defines the activativation/deactivation hooks, and new blog hook.
		 *
		 * Requires the cpcff_install_uninstall.inc.php file with the activate/deactivate code, and the code to run with new blogs.
		 *
		 * @sinze 1.0.171
		 * @return void.
		 */
		private function _activate_deactivate()
		{
			require_once CP_CALCULATEDFIELDSF_BASE_PATH.'/inc/cpcff_install_uninstall.inc.php';
			register_activation_hook(CP_CALCULATEDFIELDSF_MAIN_FILE_PATH,array('CPCFF_INSTALLER','install'));
			register_deactivation_hook(CP_CALCULATEDFIELDSF_MAIN_FILE_PATH,array('CPCFF_INSTALLER','uninstall'));
			add_action('wpmu_new_blog', array('CPCFF_INSTALLER', 'new_blog'), 10, 6);
		} // End _activate_deactivate

		/**
		 * Loads the language file.
		 *
		 * Loads the language file associated to the plugin, and creates the textdomain.
		 *
		 * @return void.
		 */
		private function _textdomain()
		{
			load_plugin_textdomain( 'calculated-fields-form', FALSE, dirname( CP_CALCULATEDFIELDSF_BASE_NAME ) . '/languages/' );
		} // End _textdomain

		/**
		 * Loads the controls scripts.
		 *
		 * Checks if there is defined the "cp_cff_resources" parameter, and loads the public or admin scripsts for the controls.
		 * If the scripsts are loaded the plugin exits the PHP execution.
		 *
		 * @return void.
		 */
		private function _load_controls_scrips()
		{
			if( isset( $_REQUEST[ 'cp_cff_resources' ] ) )
			{
				// Set the corresponding header
				if(!headers_sent())
				{
					header("Content-type: application/javascript");
				}

				if($this->_is_admin)
				{
					require_once CP_CALCULATEDFIELDSF_BASE_PATH.'/js/fbuilder-loader-admin.php';
				}
				else
				{
					require_once CP_CALCULATEDFIELDSF_BASE_PATH.'/js/fbuilder-loader-public.php';
				}
				exit;
			}
		} // End _load_controls_scrips

		/**
		 * Defines the shortcodes used by the plugin's code:
		 *
		 * - CP_CALCULATED_FIELDS
		 * - CP_CALCULATED_FIELDS_RESULT
		 * - CP_CALCULATED_FIELDS_VAR
		 *
		 * @return void.
		 */
		private function _define_shortcodes()
		{
			add_shortcode( 'CP_CALCULATED_FIELDS', array($this,'public_form') );
			add_shortcode( 'CP_CALCULATED_FIELDS_RESULT', 'cp_calculatedfieldsf_form_result' );
			add_shortcode( 'CP_CALCULATED_FIELDS_VAR', 'cp_calculatedfieldsf_create_var' );
		} // End _define_shortcodes

		/**
		 * Returns a JSON object with the configuration object.
		 *
		 * Uses the global variable $cpcff_default_texts_array, defined in the "config/cpcff_config.cfg.php"
		 *
		 * @sinze 1.0.171
		 * @param int $formid the form's id.
		 * @return string $json
		 */
		private function _get_form_configuration( $formid )
		{
			global $cpcff_default_texts_array;

			$previous_label = cp_calculatedfieldsf_get_option('vs_text_previousbtn', 'Previous',$formid);
			$previous_label = ( $previous_label=='' ? 'Previous' : $previous_label );
			$next_label = cp_calculatedfieldsf_get_option('vs_text_nextbtn', 'Next',$formid);
			$next_label = ( $next_label == '' ? 'Next' : $next_label );

			$cpcff_texts_array = cp_calculatedfieldsf_get_option( 'vs_all_texts', $cpcff_default_texts_array, $formid );
			$cpcff_texts_array = CPCFF_AUXILIARY::array_replace_recursive(
				$cpcff_default_texts_array,
				( is_string( $cpcff_texts_array ) && is_array( unserialize( $cpcff_texts_array ) ) )
					? unserialize( $cpcff_texts_array )
					: ( ( is_array( $cpcff_texts_array ) ) ? $cpcff_texts_array : array() )
			);

			$obj = array(
					"pub"=>true,
					"identifier"=>'_'.self::$form_counter,
					"messages"=> array(
						"required" => cp_calculatedfieldsf_get_option('vs_text_is_required', CP_CALCULATEDFIELDSF_DEFAULT_vs_text_is_required,$formid),
						"email" => cp_calculatedfieldsf_get_option('vs_text_is_email', CP_CALCULATEDFIELDSF_DEFAULT_vs_text_is_email,$formid),
						"datemmddyyyy" => cp_calculatedfieldsf_get_option('vs_text_datemmddyyyy', CP_CALCULATEDFIELDSF_DEFAULT_vs_text_datemmddyyyy,$formid),
						"dateddmmyyyy" => cp_calculatedfieldsf_get_option('vs_text_dateddmmyyyy', CP_CALCULATEDFIELDSF_DEFAULT_vs_text_dateddmmyyyy,$formid),
						"number" => cp_calculatedfieldsf_get_option('vs_text_number', CP_CALCULATEDFIELDSF_DEFAULT_vs_text_number,$formid),
						"digits" => cp_calculatedfieldsf_get_option('vs_text_digits', CP_CALCULATEDFIELDSF_DEFAULT_vs_text_digits,$formid),
						"max" => cp_calculatedfieldsf_get_option('vs_text_max', CP_CALCULATEDFIELDSF_DEFAULT_vs_text_max,$formid),
						"min" => cp_calculatedfieldsf_get_option('vs_text_min', CP_CALCULATEDFIELDSF_DEFAULT_vs_text_min,$formid),
						"previous" => $previous_label,
						"next" => $next_label,
						"pageof" => $cpcff_texts_array[ 'page_of_text' ][ 'text' ],
						"discount" => $cpcff_texts_array[ 'discount_text' ][ 'text' ],
						"minlength" => $cpcff_texts_array[ 'errors' ][ 'minlength' ][ 'text' ],
						"maxlength" => $cpcff_texts_array[ 'errors' ][ 'maxlength' ][ 'text' ],
						"equalTo" => $cpcff_texts_array[ 'errors' ][ 'equalTo' ][ 'text' ],
						"accept" => $cpcff_texts_array[ 'errors' ][ 'accept' ][ 'text' ],
						"upload_size" => $cpcff_texts_array[ 'errors' ][ 'upload_size' ][ 'text' ]
					)
				);
				return json_encode( $obj );
		} // End _get_form_configuration

		/**
		 * Loads the javascript and style files used by the public forms.
		 *
		 * Checks if the plugin was configured for loading HTML tags directly, or to use the WordPress functions.
		 *
		 * @since 1.0.171
		 * @param int $formid the form's id.
		 * @return void.
		 */
		private function _public_resources( $formid )
		{
			/* TO-DO: This method should be analyzed after moving other functions to the main class . */

			$public_js_path = (
					get_option( 'CP_CALCULATEDFIELDSF_USE_CACHE', CP_CALCULATEDFIELDSF_USE_CACHE ) &&
					file_exists( CP_CALCULATEDFIELDSF_BASE_PATH.'/js/cache/all.js' )
				) ? plugins_url('/js/cache/all.js', CP_CALCULATEDFIELDSF_MAIN_FILE_PATH)
				  : CPCFF_AUXILIARY::site_url().( ( strpos( CPCFF_AUXILIARY::site_url(),'?' ) === false ) ? '/?' : '&' ).'cp_cff_resources=public&min='.get_option( 'CP_CALCULATEDFIELDSF_USE_CACHE', CP_CALCULATEDFIELDSF_USE_CACHE );

			$config_json = $this->_get_form_configuration($formid);

			if (CP_CALCULATEDFIELDSF_DEFAULT_DEFER_SCRIPTS_LOADING)
			{
				wp_deregister_script('query-stringify');
				wp_register_script('query-stringify', plugins_url('/js/jQuery.stringify.js', CP_CALCULATEDFIELDSF_MAIN_FILE_PATH), array(), 'pro');

				wp_deregister_script('cp_calculatedfieldsf_validate_script');
				wp_register_script('cp_calculatedfieldsf_validate_script', plugins_url('/js/jquery.validate.js', CP_CALCULATEDFIELDSF_MAIN_FILE_PATH));
				wp_enqueue_script( 'cp_calculatedfieldsf_builder_script', $public_js_path, array("jquery","jquery-ui-core","jquery-ui-button","jquery-ui-widget","jquery-ui-position","jquery-ui-tooltip","query-stringify","cp_calculatedfieldsf_validate_script", "jquery-ui-datepicker", "jquery-ui-slider"), CP_CALCULATEDFIELDSF_VERSION, true );

				wp_localize_script('cp_calculatedfieldsf_builder_script', 'cp_calculatedfieldsf_fbuilder_config_'.self::$form_counter, array('obj' => $config_json));
			}
			else
			{
				// This code won't be used in most cases. This code is for preventing problems in wrong WP themes and conflicts with third party plugins.
				if( !$this->_are_resources_loaded ) // Load the resources only one time
				{
					$this->_are_resources_loaded = true; // Resources loaded

					$plugin_url = plugins_url('', CP_CALCULATEDFIELDSF_MAIN_FILE_PATH);

					$prefix_ui = ''; // Used for compatibility with old versions of WordPress
					if ( @file_exists( CP_CALCULATEDFIELDSF_BASE_PATH.'/../../../wp-includes/js/jquery/ui/jquery.ui.core.min.js' ) )
					{
						$prefix_ui = 'jquery.ui.';
					}

					if(!wp_script_is('jquery', 'done'))
						print '<script type="text/javascript" src="'.$plugin_url.'/../../../wp-includes/js/jquery/jquery.js"></script>';
					if(!wp_script_is('jquery-ui-core', 'done'))
						print '<script type="text/javascript" src="'.$plugin_url.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'core.min.js"></script>';
					if(!wp_script_is('jquery-ui-datepicker', 'done'))
						print '<script type="text/javascript" src="'.$plugin_url.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'datepicker.min.js"></script>';
					if(!wp_script_is('jquery-ui-widget', 'done'))
						print '<script type="text/javascript" src="'.$plugin_url.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'widget.min.js"></script>';
					if(!wp_script_is('jquery-ui-position', 'done'))
						print '<script type="text/javascript" src="'.$plugin_url.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'position.min.js"></script>';
					if(!wp_script_is('jquery-ui-tooltip', 'done'))
						print '<script type="text/javascript" src="'.$plugin_url.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'tooltip.min.js"></script>';
					if(!wp_script_is('jquery-ui-mouse', 'done'))
						print '<script type="text/javascript" src="'.$plugin_url.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'mouse.min.js"></script>';
					if(!wp_script_is('jquery-ui-slider', 'done'))
						print '<script type="text/javascript" src="'.$plugin_url.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'slider.min.js"></script>';
				?>
					<script>if( typeof fbuilderjQuery == 'undefined') var fbuilderjQuery = jQuery.noConflict( );</script>
					<script type='text/javascript' src='<?php echo plugins_url('js/jquery.validate.js', CP_CALCULATEDFIELDSF_MAIN_FILE_PATH); ?>'></script>
					<script type='text/javascript' src='<?php echo plugins_url('js/jQuery.stringify.js', CP_CALCULATEDFIELDSF_MAIN_FILE_PATH); ?>'></script>
					<script type='text/javascript' src='<?php echo $public_js_path.(( strpos( $public_js_path, '?' ) == false ) ? '?' : '&' ).'ver='.CP_CALCULATEDFIELDSF_VERSION; ?>'></script>
				<?php
				}
				?>
				<script type='text/javascript'>
					/* <![CDATA[ */
					<?php
						print 'var cp_calculatedfieldsf_fbuilder_config_'.self::$form_counter.'={"obj":'.$config_json.'};';
					?>
					/* ]]> */
				</script>
				<?php
			}
		} // End _public_resources
	} // End CPCFF_MAIN
}