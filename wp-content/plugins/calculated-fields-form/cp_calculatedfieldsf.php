<?php
/*
Plugin Name: Calculated Fields Form
Plugin URI: http://cff.dwbooster.com
Description: Create forms with field values calculated based in other form field values.
Version: 5.0.194
Text Domain: calculated-fields-form
Author: CodePeople
Author URI: http://cff.dwbooster.com
License: GPL
*/

require_once 'inc/cpcff_session.inc.php';
// Start Session
CP_SESSION::session_start();

// Defining main constants
define('CP_CALCULATEDFIELDSF_VERSION', '5.0.194' );
define('CP_CALCULATEDFIELDSF_MAIN_FILE_PATH', __FILE__ );
define('CP_CALCULATEDFIELDSF_BASE_PATH', dirname( CP_CALCULATEDFIELDSF_MAIN_FILE_PATH ) );
define('CP_CALCULATEDFIELDSF_BASE_NAME', plugin_basename( CP_CALCULATEDFIELDSF_MAIN_FILE_PATH ) );

/* initialization / install / uninstall functions */
require_once 'inc/cpcff_auxiliary.inc.php';
require_once 'config/cpcff_config.cfg.php';

require_once 'inc/cpcff_banner.inc.php';
require_once 'inc/cpcff_main.inc.php';

// Global variables
$cpcff_main = new CPCFF_MAIN; // Main plugin's object

require_once 'inc/cpcff_auto_update.inc.php';
require_once 'inc/cpcff_discounts.inc.php';
require_once 'inc/cpcff_form_cache.inc.php';

add_action( 'init', 'cp_calculated_fields_form_check_coupon', 1 );
add_action( 'init', 'cp_calculated_fields_form_check_posted_data', 11 );
add_action( 'widgets_init', create_function('', 'return register_widget("CP_calculatedfieldsf_Widget");') );

function cp_calculated_fields_form_check_coupon()
{
	if(
		!empty($_REQUEST['action']) &&
		$_REQUEST['action'] == 'checkcoupon'
	)
	{
		if(
			!empty($_REQUEST['couponcode']) &&
			!empty($_REQUEST['formid']) &&
			!empty($_REQUEST['formsequence']) &&
			!empty($_REQUEST['_wpnonce']) &&
			wp_verify_nonce($_REQUEST['_wpnonce'], 'cpcff_form_'.$_REQUEST['formid'].$_REQUEST['formsequence']) &&
			($coupon = CPCFF_COUPON::get_coupon($_REQUEST['formid'],$_REQUEST['couponcode'])) !== false
		){
			print json_encode(
					array(
						"code" 			=> $coupon->code,
						"discount" 		=> $coupon->discount,
						"availability" 	=> $coupon->availability
					)
				);
		}
		else
			print "{}";
		exit;
	}
}

// functions
//------------------------------------------

function cp_calculatedfieldsf_form_result( $atts, $content = "", $id = 0 )
	{

		if( CPCFF_AUXILIARY::is_crawler() ) return '';

		global $wpdb;
		if( $id == 0 ) $id = CP_SESSION::get_var('cp_cff_form_data');
        if( !empty( $id ) )
		{
			$content = html_entity_decode( $content );
			$result = $wpdb->get_row( $wpdb->prepare( "SELECT form_settings.form_structure AS form_structure, form_data.data AS data, form_data.paypal_post AS paypal_post, form_data.ipaddr as ipaddr, form_data.formid as formid FROM ".$wpdb->prefix.CP_CALCULATEDFIELDSF_FORMS_TABLE." AS form_settings,".CP_CALCULATEDFIELDSF_POSTS_TABLE_NAME." AS form_data WHERE form_data.id=%d AND form_data.formid=form_settings.id", $id ) );

			if( !is_null( $result ) )
			{
				$atts = shortcode_atts( array( 'fields' => '', 'formid' => 0 ), $atts );
				if(
					empty($atts['formid']) ||
					$atts['formid'] == $result->formid
				)
				{
					if( !empty( $atts[ 'fields' ] ) || !empty( $content ) )
					{
						$raw_form_str = CPCFF_AUXILIARY::clean_json( $result->form_structure );
						$form_data = json_decode( $raw_form_str );
					}

					if( empty( $form_data ) )
					{
						return apply_filters( 'cpcff_summary', "<p>" . str_replace( array('&lt;', '&gt;', '\"', "\'"), array('<', '>', '"', "'" ), preg_replace( "/\n+/", "<br />", $result->data ) ). "</p>" );
					}
					else
					{
						$fields = array();
						foreach($form_data[0] as $item)
						{
							$fields[$item->name] = $item;
						}
						$fields[ 'ipaddr' ] = $result->ipaddr;
						$result->paypal_post = unserialize( $result->paypal_post );
						$str = '';
						$atts[ 'fields' ] = explode( ",", str_replace( " ", "", $atts[ 'fields' ] ) );
						foreach( $atts[ 'fields' ] as $field )
						{
							if( isset( $fields[ $field ] ) )
							{
								if( isset( $result->paypal_post[ $field ] ) )
								{
									if( is_array( $result->paypal_post[ $field ] ) ) $result->paypal_post[ $field ] = implode( ',', $result->paypal_post[ $field ] );
									$str .= "<p>{$fields[ $field ]->title} {$result->paypal_post[ $field ]}</p>";
								}
								elseif( in_array( $fields[ $field ]->ftype, array( 'fSectionBreak' ) ) )
								{
									$str .= "<p><strong>".$fields[ $field ]->title."</strong>".(( !empty($fields[ $field ]->userhelp) ) ? "<br /><pan class='uh'>".$fields[ $field ]->userhelp."</span>" : '' )."</p>";
								}
							}

						}

						if( $content != '' )
						{
							$replaced_values = _cp_calculatedfieldsf_replace_vars( $fields, $result->paypal_post, $content, $result->data, 'html', $id );
							$str .= $replaced_values[ 'message' ];
						}

						return apply_filters( 'cpcff_summary', $str );
					}
				}
			}
		}

		return apply_filters( 'cpcff_summary', '' );
	}

function cp_calculatedfieldsf_available_templates(){
	global $CP_CFF_global_templates;

	if( empty( $CP_CFF_global_templates ) )
	{
		// Get available designs
		$tpls_dir = dir( plugin_dir_path( __FILE__ ).'templates' );
		$CP_CFF_global_templates = array();
		while( false !== ( $entry = $tpls_dir->read() ) )
		{
			if ( $entry != '.' && $entry != '..' && is_dir( $tpls_dir->path.'/'.$entry ) && file_exists( $tpls_dir->path.'/'.$entry.'/config.ini' ) )
			{
				if( ( $ini_array = parse_ini_file( $tpls_dir->path.'/'.$entry.'/config.ini' ) ) != false ||
					( $ini_array = parse_ini_string( file_get_contents( $tpls_dir->path.'/'.$entry.'/config.ini' ) ) ) != false )
				{
					if( !empty( $ini_array[ 'file' ] ) ) $ini_array[ 'file' ] = plugins_url( 'templates/'.$entry.'/'.$ini_array[ 'file' ], __FILE__ );
					if( !empty( $ini_array[ 'js' ] ) ) $ini_array[ 'js' ] = plugins_url( 'templates/'.$entry.'/'.$ini_array[ 'js' ], __FILE__ );
					if( !empty( $ini_array[ 'thumbnail' ] ) ) $ini_array[ 'thumbnail' ] = plugins_url( 'templates/'.$entry.'/'.$ini_array[ 'thumbnail' ], __FILE__ );
					$CP_CFF_global_templates[ $ini_array[ 'prefix' ] ] = $ini_array;
				}
			}
		}
	}

	return $CP_CFF_global_templates;
}

/**
 * Create a javascript variable, from: Post, Get, Session or Cookie
 */
function cp_calculatedfieldsf_create_var( $atts ) {

	if( CPCFF_AUXILIARY::is_crawler() ) return '';

	if( isset( $atts[ 'name' ] ) )
	{
		$var = trim( $atts[ 'name' ] );
		if( !empty( $var ) )
		{
			if( isset( $atts[ 'value' ] ) )
			{
				$value = json_encode( $atts[ 'value' ] );
			}
			else
			{
				$from = '_';
				if( isset( $atts[ 'from' ] ) ) $from .= strtoupper( trim( $atts[ 'from' ] ) );
				if( in_array( $from, array( '_POST', '_GET', '_SESSION', '_COOKIE' ) ) )
				{
					if( isset( $GLOBALS[ $from ][ $var ] ) ){
						if( $from == '_COOKIE' )
						{
							$cookie_var = 1;
							$value = 'fbuilder_get_cookie("'.$var.'")';
						}else $value = json_encode($GLOBALS[ $from ][ $var ]);
					}
					elseif( isset( $atts[ 'default_value' ] ) ) $value = json_encode($atts[ 'default_value' ]);
				}
				else
				{
					if( isset( $_POST[ $var ] ) ) 				$value = json_encode($_POST[ $var ]);
					elseif( isset( $_GET[ $var ] ) ) 			$value = json_encode($_GET[ $var ]);
					elseif( isset( $_SESSION[ $var ] ) )		$value = json_encode($_SESSION[ $var ]);
					elseif( isset( $_COOKIE[ $var ] ) ){
																$cookie_var = 1;
																$value = 'fbuilder_get_cookie("'.$var.'")';
					}
					elseif( isset( $atts[ 'default_value' ] ) ) $value = json_encode($atts[ 'default_value' ]);
				}
			}
			if( isset( $value ) )
			{
				$js_script  = '';
				global $calculated_fields_form_cookie_var;
				if( isset($cookie_var) && empty($calculated_fields_form_cookie_var) )
				{
					$calculated_fields_form_cookie_var  = 1;
					$js_script .= 'function fbuilder_get_cookie( name ){
										var re = new RegExp(name + "=([^;]+)");
										var value = re.exec(document.cookie);
										return (value != null) ? decodeURIComponent(value[1].replace(/\+/g, "%20")) : null;
									};';
				}
				return '
				<script>
					try{
					'.$js_script.'
					window["'.$var.'"]='.$value.';
					}catch( err ){}
				</script>
				';
			}
		}
	}
} // End cp_calculatedfieldsf_create_var

function cp_calculatedfieldsf_html_post_page() {
    if (isset($_GET["cal"]) && $_GET["cal"] != '')
    {
        if (isset($_GET["list"]) && $_GET["list"] == '1')
            @include_once dirname( __FILE__ ) . '/inc/cpcff_admin_int_message_list.inc.php';
        else
            @include_once dirname( __FILE__ ) . '/inc/cpcff_admin_int.inc.php';
    }
    else
	{
		if (isset($_GET["page"]) &&$_GET["page"] == 'cp_calculated_fields_form_sub3')
        {
            echo("Redirecting to upgrade page...<script type='text/javascript'>document.location='http://cff.dwbooster.com/download';</script>");
            exit;
        }
        else if (isset($_GET["page"]) &&$_GET["page"] == 'cp_calculated_fields_form_sub2')
        {
            echo("Redirecting to demo page...<script type='text/javascript'>document.location='http://cff.dwbooster.com/documentation';</script>");
            exit;
        }
        else
			@include_once dirname( __FILE__ ) . '/inc/cpcff_admin_int_list.inc.php';
	}
}

function cp_calculatedfieldsf_load_discount_codes() {
    global $wpdb;

    if ( ! current_user_can('edit_pages') ) // prevent loading coupons from outside admin area
    {
        _e( 'No enough privilegies to load this content.', 'calculated-fields-form' );
        exit;
    }

    if (!defined('CP_CALCULATEDFIELDSF_ID'))
        define ('CP_CALCULATEDFIELDSF_ID',$_GET["dex_item"]);

    if (isset($_GET["cff_add_coupon"]) && $_GET["cff_add_coupon"] == "1")
		CPCFF_COUPON::add_coupon(CP_CALCULATEDFIELDSF_ID,$_GET["cff_coupon_code"],$_GET["cff_discount"],$_GET["cff_discounttype"],$_GET["cff_coupon_expires"]);

    if (isset($_GET["cff_delete_coupon"]) && $_GET["cff_delete_coupon"] == "1")
		CPCFF_COUPON::delete_coupon($_GET["cff_coupon_code"] );

	print CPCFF_COUPON::display_codes(CP_CALCULATEDFIELDSF_ID);
	exit;
}

function cp_calculated_fields_form_check_posted_data() {

    global $wpdb, $cpcff_main;

	if(!empty($_GET['cp_calculatedfieldsf_ipncheck']))
		cp_calculatedfieldsf_check_IPN_verification($_GET['cp_calculatedfieldsf_ipncheck']);

    if(isset($_GET) && array_key_exists('cp_calculated_fields_form_post',$_GET)) {
        if ($_GET["cp_calculated_fields_form_post"] == 'loadcoupons')
            cp_calculatedfieldsf_load_discount_codes();
    }

    if (isset( $_GET['cp_calculatedfieldsf'] ) && $_GET['cp_calculatedfieldsf'] == 'captcha' )
    {
        @include_once dirname( __FILE__ ) . '/captcha/captcha.php';
        exit;
    }

    if (isset( $_GET['cp_calculatedfieldsf_csv'] ) && is_admin() )
    {
        check_admin_referer( 'session_id_'.CP_SESSION::session_id(), '_cpcff_nonce' );
        cp_calculatedfieldsf_export_csv();
        return;
    }

    if (isset( $_GET['cp_calculatedfieldsf_export'] ) && is_admin() )
    {
        check_admin_referer( 'session_id_'.CP_SESSION::session_id(), '_cpcff_nonce' );
        cp_calculatedfieldsf_export_form();
        return;
    }

    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST['cp_calculatedfieldsf_post_options'] ) && is_admin() )
    {
        cp_calculatedfieldsf_save_options();
        if( isset( $_POST[ 'preview' ] ) )
		{
			print '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>';
            print( $cpcff_main->public_form( array( 'id' => $_POST[ 'cp_calculatedfieldsf_id' ] ) ));
			wp_footer();
			print '</body></html>';
			exit;
		}
		return;
    }

	if ( 'POST' != $_SERVER['REQUEST_METHOD'] || ! isset( $_POST['cp_calculatedfieldsf_pform_process'] ) )
	    if ( 'GET' != $_SERVER['REQUEST_METHOD'] || !isset( $_GET['hdcaptcha_cp_calculated_fields_form_post'] ) )
		    return;

    define("CP_CALCULATEDFIELDSF_ID",@$_POST["cp_calculatedfieldsf_id"]);

    if (isset($_GET["ps"])) $sequence = $_GET["ps"]; else if (isset($_POST["cp_calculatedfieldsf_pform_psequence"])) $sequence = $_POST["cp_calculatedfieldsf_pform_psequence"];
    if (!isset($_GET['hdcaptcha_cp_calculated_fields_form_post']) || $_GET['hdcaptcha_cp_calculated_fields_form_post'] == '') $_GET['hdcaptcha_cp_calculated_fields_form_post'] = @$_POST['hdcaptcha_cp_calculated_fields_form_post'];
    if (
			/**
			 * Filters applied for checking if the form's submission is valid or not
			 * returns a boolean
			 */
			!apply_filters( 'cpcff_valid_submission', true) ||
			(
				(cp_calculatedfieldsf_get_option('cv_enable_captcha', CP_CALCULATEDFIELDSF_DEFAULT_cv_enable_captcha) != 'false') &&
				( (strtolower($_GET['hdcaptcha_cp_calculated_fields_form_post']) != strtolower(CP_SESSION::get_var('rand_code'.$sequence))) ||
				  (CP_SESSION::get_var('rand_code'.$sequence) == '') )
			)
       )
    {
        echo 'captchafailed';
        exit;
    }

	// if this isn't the real post (it was the captcha verification) then echo ok and exit
    if ( 'POST' != $_SERVER['REQUEST_METHOD'] || ! isset( $_POST['cp_calculatedfieldsf_pform_process'] ) )
	{
	    echo 'ok';
        exit;
	}

	// Defines the $params array
	$params = array(
		'formid'   => CP_CALCULATEDFIELDSF_ID
	);

	// Check the honeypot
	if( ( $honeypot = get_option( 'CP_CALCULATEDFIELDSF_HONEY_POT', '' ) ) !== '' && !empty( $_REQUEST[ $honeypot ] ) )
	{
		exit;
	}

	// Check the nonce
	if (
		empty($_REQUEST['_wpnonce']) ||
		!wp_verify_nonce($_REQUEST['_wpnonce'], 'cpcff_form_'.CP_CALCULATEDFIELDSF_ID.$_POST["cp_calculatedfieldsf_pform_psequence"])
	)
	{
		_e( 'Failed security check', 'calculated-fields-form' );
		exit;
	}

	// get form info
    //---------------------------
    $paypal_zero_payment = cp_calculatedfieldsf_get_option('paypal_zero_payment',CP_CALCULATEDFIELDSF_DEFAULT_PAYPAL_ZERO_PAYMENT);
    require_once(ABSPATH . "wp-admin" . '/includes/file.php');

	$form_data = cp_calculatedfieldsf_get_option( 'form_structure', CP_CALCULATEDFIELDSF_DEFAULT_form_structure );
    $fields = array();
	$choicesTxt = array();	   // List of choices texts in fields where exits
    $choicesVal = array(); // List of choices vals  in fields where exits

    foreach ($form_data[0] as $item)
        //if (!isset($item->hidefield) ||$item->hidefield != '1')
        {
            $fields[$item->name] = $item;
            if( property_exists( $item, 'choicesVal' ) && property_exists( $item, 'choices' ) )
			{
				$choicesTxt[$item->name] = $item->choices;
				$choicesVal[$item->name] = $item->choicesVal;
			}

            if ($item->ftype == 'fPhone') // join fields for phone fields
            {
				$_POST[$item->name.$sequence] = '';
                for($i=0; $i<=substr_count($item->dformat," "); $i++)
                {
                    $_POST[$item->name.$sequence] .= ($_POST[$item->name.$sequence."_".$i]!=''?($i==0?'':'-').$_POST[$item->name.$sequence."_".$i]:'');
                    unset($_POST[$item->name.$sequence."_".$i]);
                }
            }
        }

	// get base price
	$request_cost = cp_calculatedfieldsf_get_option('request_cost', CP_CALCULATEDFIELDSF_DEFAULT_COST);
	$price_item = $fields[ $request_cost ];

	$find_arr = array( ',', '.');
	$replace_arr = array( '', '.');

	if( $price_item->ftype == 'fCalculated' )
	{
		$find_arr[ 0 ] = $price_item->groupingsymbol;
		$find_arr[ 1 ] = $price_item->decimalsymbol;
	}
    elseif( $price_item->ftype == 'fcurrency' )
	{
		$find_arr[ 0 ] = $price_item->thousandSeparator;
		$find_arr[ 1 ] = $price_item->centSeparator;
	}
	elseif( $price_item->ftype == 'fnumber' || $price_item->ftype == 'fnumberds' )
	{
		$find_arr[ 0 ] = $price_item->thousandSeparator;
		$find_arr[ 1 ] = $price_item->decimalSymbol;
	}

    $price = @$_POST[ $request_cost.$_POST["cp_calculatedfieldsf_pform_psequence"] ];
	$price = preg_replace( '/[^\d\.\,]/', '', $price );
	$price = str_replace( $find_arr, $replace_arr, $price );
	$paypal_base_amount = preg_replace( '/[^\d\.\,]/', '', cp_calculatedfieldsf_get_option( 'paypal_base_amount', 0 ) );
	$paypal_base_amount = str_replace( $find_arr, $replace_arr, $paypal_base_amount );
	$price = max( $price, $paypal_base_amount );

    // calculate discounts if any
    //---------------------------
	$price 			= CPCFF_COUPON::apply_discount(CP_CALCULATEDFIELDSF_ID, @$_POST["couponcode"], $price);
    $discount_note 	= CPCFF_COUPON::$discount_note;
    $coupon 		= CPCFF_COUPON::$coupon_applied;

    // grab posted data
    //---------------------------
    $buffer = "";

    foreach ($_POST as $item => $value)
	{
		$fieldname = str_replace($_POST["cp_calculatedfieldsf_pform_psequence"],'',$item);
        if ( array_key_exists( $fieldname, $fields ) )
        {
			// Check if the field is required and it is empty
			if(
				property_exists($fields[$fieldname],'required') &&
				!empty($fields[$fieldname]->required) &&
				$value === ''
			)
			{
				_e('At least a required field is empty', 'calculated-fields-form');
				exit;
			}

            $buffer .= stripcslashes($fields[$fieldname]->title . ": ". (is_array($value)?(implode(", ",$value)):($value))) . "\n\n";
            $params[$fieldname] = (is_array($value)) ? array_map('stripcslashes', $value) : stripcslashes($value);
        }
	}
    foreach ($_FILES as $item => $value)
	{
		$item = str_replace( $_POST["cp_calculatedfieldsf_pform_psequence"],'',$item );
		if ( isset( $fields[ $item ] ) )
        {
			$files_names_arr = array();
			$files_links_arr = array();
			$files_urls_arr  = array();
			for( $f = 0; $f < count( $value[ 'name' ] ); $f++ )
			{
				if( !empty( $value[ 'name' ][ $f ] ) )
				{
					$uploaded_file = array(
						'name' 		=> $value[ 'name' ][ $f ],
						'type' 		=> $value[ 'type' ][ $f ],
						'tmp_name' 	=> $value[ 'tmp_name' ][ $f ],
						'error' 	=> $value[ 'error' ][ $f ],
						'size' 		=> $value[ 'size' ][ $f ],
					);
					if( cp_calculatedfieldsf_check_upload( $uploaded_file ) )
					{
						$movefile = wp_handle_upload( $uploaded_file, array( 'test_form' => false ) );
						if ( empty( $movefile[ 'error' ] ) )
						{
							$files_links_arr[] = $params[ $item."_link" ][ $f ] = $movefile["file"];
							$files_urls_arr[]  = $params[ $item."_url" ][ $f ] = $movefile["url"];
							$files_names_arr[] = $uploaded_file[ 'name' ];

							/**
							 * Action called when the file is uploaded, the file's data is passed as parameter
							 */
							do_action(
								'cpcff_file_uploaded',
								$movefile,
								array(
									'names' => &$files_names_arr,
									'links' => &$files_links_arr,
									'urls'  => &$files_urls_arr
								)
							);
						}
					}
				}
			}

			$joinned_files_names = implode( ", ", $files_names_arr );
			$buffer .= $fields[ $item ]->title . ": ". $joinned_files_names . "\n\n";
			$params[ $item ] = $joinned_files_names;
			$params[ $item."_links"] = implode( ",",  $files_links_arr );
			$params[ $item."_urls"] = implode( ",",  $files_urls_arr );
		}
	}

	if(count($params) < 2)
	{
		_e( 'The form is empty', 'calculated-fields-form' );
		exit;
	}

    $buffer_A = $buffer;
    $params["final_price"] = $price;
    $params["coupon"] = ($coupon?$coupon->code.$discount_note:"");
    if (@$_POST["bccf_payment_option_paypal"] == '1')
        $params["payment_option"] = cp_calculatedfieldsf_get_option('enable_paypal_option_yes',CP_CALCULATEDFIELDSF_PAYPAL_OPTION_YES);
    else if (@$_POST["bccf_payment_option_paypal"] == '0')
        $params["payment_option"] = cp_calculatedfieldsf_get_option('enable_paypal_option_no',CP_CALCULATEDFIELDSF_PAYPAL_OPTION_NO);

    $to = cp_calculatedfieldsf_get_option('cu_user_email_field', CP_CALCULATEDFIELDSF_DEFAULT_cu_user_email_field);
	$to = explode( ',', $to );
	$to_arr = array();
	foreach( $to as $index => $value )
	{
		$value .= $_POST["cp_calculatedfieldsf_pform_psequence"];
		$_POST[ $value ] = trim( @$_POST[ $value ] );
		if( !empty( $_POST[ $value ] ) ) $to_arr[] = $_POST[ $value ];
	};

    $rows_affected = $wpdb->insert( CP_CALCULATEDFIELDSF_POSTS_TABLE_NAME, array( 'formid' => CP_CALCULATEDFIELDSF_ID,
                                                                        'time' => current_time('mysql'),
                                                                        'ipaddr' => $_SERVER['REMOTE_ADDR'],
                                                                        'notifyto' => implode( ',', $to_arr ),
                                                                        'paypal_post' => @serialize($params),
                                                                        'data' =>$buffer_A .($coupon?"\n\nCoupon code:".$coupon->code.$discount_note:"") ),
																		array( '%d', '%s', '%s', '%s', '%s', '%s' )
																		);
    if (!$rows_affected)
    {
        _e( 'Error saving data! Please try again.', 'calculated-fields-form' );
        _e( '<br /><br />Error debug information: ', 'calculated-fields-form' );
		$wpdb->print_error();
        exit;
    }

    $paypal_optional = (cp_calculatedfieldsf_get_option('enable_paypal',CP_CALCULATEDFIELDSF_DEFAULT_ENABLE_PAYPAL) == '2');

 	// save data here
    $item_number = $wpdb->insert_id;
	CP_SESSION::set_var('cp_cff_form_data', $item_number);

    if ( ( (floatval($price) >= 0 && !$paypal_zero_payment) || (floatval($price) > 0 && $paypal_zero_payment) )
          &&
          cp_calculatedfieldsf_get_option('enable_paypal',CP_CALCULATEDFIELDSF_DEFAULT_ENABLE_PAYPAL)
          &&
          ( !$paypal_optional || (@$_POST["bccf_payment_option_paypal"] == '1') )
          && (@$_POST["bccf_payment_option_paypal"] != '0')
        )
    {
        if (cp_calculatedfieldsf_get_option('paypal_mode',CP_CALCULATEDFIELDSF_DEFAULT_PAYPAL_MODE) == "sandbox")
            $ppurl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        else
            $ppurl = 'https://www.paypal.com/cgi-bin/webscr';
        if (cp_calculatedfieldsf_get_option('paypal_notiemails', '0') == '1')
            cp_calculatedfieldsf_process_ready_to_go_reservation( $item_number, "", $params, $fields );
?>
<html>
<head><title>Redirecting to Paypal...</title></head>
<body>
<form action="<?php echo $ppurl; ?>" name="ppform3" method="post">
<input type="hidden" name="business" value="<?php echo esc_attr(cp_calculatedfieldsf_get_option('paypal_email', CP_CALCULATEDFIELDSF_DEFAULT_PAYPAL_EMAIL)); ?>" />
<?php
$paypal_item_name = cp_calculatedfieldsf_get_option('paypal_product_name', CP_CALCULATEDFIELDSF_DEFAULT_PRODUCT_NAME).(@$_POST["services"]?": ".trim($services_formatted[1]):"").$discount_note;
foreach ($params as $item => $value)
    $paypal_item_name = str_replace('<%'.$item.'%>',(is_array($value)?(implode(", ",$value)):($value)),$paypal_item_name);
?>
<input type="hidden" name="item_name" value="<?php echo esc_attr($paypal_item_name); ?>" />
<input type="hidden" name="item_number" value="<?php echo esc_attr($item_number); ?>" />
<input type="hidden" name="email" value="<?php echo esc_attr(@$_POST[$to]); ?>" />

<?php
$paypal_recurrent = cp_calculatedfieldsf_get_option('paypal_recurrent',CP_CALCULATEDFIELDSF_DEFAULT_PAYPAL_RECURRENT);
$paypal_recurrent_setup = cp_calculatedfieldsf_get_option('paypal_recurrent_setup','');
$paypal_recurrent_setup_days = cp_calculatedfieldsf_get_option('paypal_recurrent_setup_days','15');

if( strpos( $paypal_recurrent, 'field' ) !== false )
{
	if(
		!empty( $params[ $paypal_recurrent ] ) &&
		!empty( $choicesTxt[ $paypal_recurrent ] ) &&
		!empty( $choicesVal[ $paypal_recurrent ] ) &&
		( $index = array_search( $params[ $paypal_recurrent ], $choicesTxt[ $paypal_recurrent ] ) ) !== false
	) $paypal_recurrent = $choicesVal[ $paypal_recurrent ][ $index ];
}

$paypal_recurrent = intval( $paypal_recurrent );

if ( $paypal_recurrent == 0 ) { ?>
<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="bn" value="NetFactorSL_SI_Custom" />
<input type="hidden" name="amount" value="<?php echo esc_attr($price); ?>" />
<?php } else { ?>
<?php if ($paypal_recurrent_setup != '') { ?>
<input type="hidden" name="a1" value="<?php echo esc_attr($paypal_recurrent_setup); ?>">
<input type="hidden" name="p1" value="<?php echo esc_attr($paypal_recurrent_setup_days); ?>">
<input type="hidden" name="t1" value="D">
<?php } ?>
<input type="hidden" name="cmd" value="_xclick-subscriptions">
<input type="hidden" name="bn" value="NetFactorSL_SI_Custom">
<input type="hidden" name="a3" value="<?php echo esc_attr($price); ?>">
<input type="hidden" name="p3" value="<?php echo esc_attr($paypal_recurrent); ?>">
<input type="hidden" name="t3" value="M">
<input type="hidden" name="src" value="1">
<input type="hidden" name="sra" value="1">
<?php } ?>

<input type="hidden" name="page_style" value="Primary" />
<input type="hidden" name="no_shipping" value="1" />
<input type="hidden" name="return" value="<?php echo esc_url(cp_calculatedfieldsf_get_option('fp_return_page', CP_CALCULATEDFIELDSF_DEFAULT_fp_return_page)); ?>">
<input type="hidden" name="cancel_return" value="<?php echo esc_url($_POST["cp_ref_page"]); ?>" />
<input type="hidden" name="no_note" value="1" />
<input type="hidden" name="currency_code" value="<?php echo esc_attr(strtoupper(cp_calculatedfieldsf_get_option('currency', CP_CALCULATEDFIELDSF_DEFAULT_CURRENCY))); ?>" />
<input type="hidden" name="lc" value="<?php echo esc_attr(cp_calculatedfieldsf_get_option('paypal_language', CP_CALCULATEDFIELDSF_DEFAULT_PAYPAL_LANGUAGE)); ?>" />
<input type="hidden" name="notify_url" value="<?php echo esc_url(CPCFF_AUXILIARY::site_url().'/?cp_calculatedfieldsf_ipncheck='.$item_number); ?>" />
<input type="hidden" name="ipn_test" value="1" />
<input class="pbutton" type="hidden" value="Buy Now" /></div>
</form>
<script type="text/javascript">
document.ppform3.submit();
</script>
</body>
</html>
<?php
        exit();
    }
    else
    {
        cp_calculatedfieldsf_process_ready_to_go_reservation( $item_number, "", $params, $fields );
		$location = cp_calculatedfieldsf_get_option('fp_return_page', CP_CALCULATEDFIELDSF_DEFAULT_fp_return_page);
        header("Location: ".$location);
        exit;
    }
}


function cp_calculatedfieldsf_check_upload($uploadfiles) {
    $filetmp = $uploadfiles['tmp_name'];
    //clean filename and extract extension
    $filename = $uploadfiles['name'];
    // get file info
    $filetype = wp_check_filetype( basename( $filename ), null );

    if ( in_array ($filetype["ext"],array("php","asp","aspx","cgi","pl","perl","exe")) )
        return false;
    else
        return true;
}

function cp_calculatedfieldsf_check_IPN_verification( $itemnumber ) {
    global $wpdb;

	$itemnumber = intval(@$itemnumber);

    $item_name = $_POST['item_name'];
    $item_number = $_POST['item_number'];
    $payment_status = $_POST['payment_status'];
    $payment_amount = $_POST['mc_gross'];
    $payment_currency = $_POST['mc_currency'];
    $txn_id = $_POST['txn_id'];
    $receiver_email = $_POST['receiver_email'];
    $payer_email = $_POST['payer_email'];
    $payment_type = $_POST['payment_type'];
/**
	if ($payment_status != 'Completed' && $payment_type != 'echeck')
	    return;

	if ($payment_type == 'echeck' && $payment_status != 'Pending')
	    return;
*/
	$str = '';
    if (isset($_POST["first_name"])) $str .= 'Buyer: '.$_POST["first_name"]." ".$_POST["last_name"]."\n";
    if (isset($_POST["payer_email"])) $str .= 'Payer email: '.$_POST["payer_email"]."\n";
	if (isset($_POST["residence_country"])) $str .= 'Country code: '.$_POST["residence_country"]."\n";
	if (isset($_POST["payer_status"])) $str .= 'Payer status: '.$_POST["payer_status"]."\n";
	if (isset($_POST["protection_eligibility"])) $str .= 'Protection eligibility: '.$_POST["protection_eligibility"]."\n";

	if (isset($_POST["item_name"])) $str .= 'Item: '.$_POST["item_name"]."\n";
	if (isset($_POST["payment_gross"]))
	     $str .= 'Payment: '.$_POST["payment_gross"]." ".$_POST["mc_currency"]." (Fee: ".$_POST["payment_fee"].")"."\n";
	else if (isset($_POST["mc_gross"]) && isset($_POST["mc_currency"]) && isset($_POST["mc_fee"]))
	     $str .= 'Payment: '.$_POST["mc_gross"]." ".$_POST["mc_currency"]." (Fee: ".$_POST["mc_fee"].")"."\n";
	if (isset($_POST["payment_date"])) $str .= 'Payment date: '.$_POST["payment_date"];
	if (isset($_POST["payment_type"]) && isset($_POST["payment_status"])) $str .= 'Payment type/status: '.$_POST["payment_type"]."/".$_POST["payment_status"]."\n";
	if (isset($_POST["business"])) $str .= 'Business: '.$_POST["business"]."\n";
	if (isset($_POST["receiver_email"])) $str .= 'Receiver email: '.$_POST["receiver_email"]."\n";

    $myrows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".CP_CALCULATEDFIELDSF_POSTS_TABLE_NAME." WHERE id=%d", $itemnumber ) );
    $params = unserialize($myrows[0]->paypal_post);

    if ($myrows[0]->paid == 0)
    {
		$params[ 'paypal_data' ] = $str;
        $wpdb->query( $wpdb->prepare( "UPDATE ".CP_CALCULATEDFIELDSF_POSTS_TABLE_NAME." SET paid=1, paypal_post=%s WHERE id=%d", serialize( $params ), $itemnumber ) );
		if (!defined('CP_CALCULATEDFIELDSF_ID'))
            define ('CP_CALCULATEDFIELDSF_ID',$myrows[0]->formid);
        if (cp_calculatedfieldsf_get_option('paypal_notiemails', '0') != '1')
		{
			$form_data = cp_calculatedfieldsf_get_option( 'form_structure', CP_CALCULATEDFIELDSF_DEFAULT_form_structure );
			$fields = array();
			foreach ($form_data[0] as $item) $fields[$item->name] = $item;
			cp_calculatedfieldsf_process_ready_to_go_reservation( $itemnumber, $payer_email, $params, $fields );
		}
        echo 'OK - processed';
    }
    else
        echo 'OK - already processed';

    exit();
}

function cp_calculatedfieldsf_process_ready_to_go_reservation( $itemnumber, $payer_email = "", $params = array(), $fields = array() )
{

   global $wpdb;

   $itemnumber = intval( $itemnumber );

   $myrows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".CP_CALCULATEDFIELDSF_POSTS_TABLE_NAME." WHERE id=%d", $itemnumber ) );

   $mycalendarrows = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM '. $wpdb->prefix.CP_CALCULATEDFIELDSF_FORMS_TABLE .' WHERE `id`=%d', $myrows[0]->formid ) );

   if (!defined('CP_CALCULATEDFIELDSF_ID'))
        define ('CP_CALCULATEDFIELDSF_ID',$myrows[0]->formid);


    $buffer_A = $myrows[0]->data;
    $buffer = $buffer_A;

	$fields[ 'ipaddr' ] = $myrows[0]->ipaddr;

    global $cp_calculatedfieldsf_envelope_from;
    add_filter('phpmailer_init','cp_calculatedfieldsf_envelope_from');

    // 1- Send email
    //---------------------------
    $email_data = _cp_calculatedfieldsf_replace_vars(
        $fields,
        $params,
        cp_calculatedfieldsf_get_option('fp_message', CP_CALCULATEDFIELDSF_DEFAULT_fp_message),
        $buffer,
        cp_calculatedfieldsf_get_option('fp_emailformat', CP_CALCULATEDFIELDSF_DEFAULT_email_format),
        $itemnumber
    );

    if ('true' == cp_calculatedfieldsf_get_option('fp_inc_additional_info', CP_CALCULATEDFIELDSF_DEFAULT_fp_inc_additional_info))
    {
        $email_data[ 'message' ] .="ADDITIONAL INFORMATION\n"
								 ."*********************************\n";

		$basic_data = "IP: ".$myrows[0]->ipaddr."\n"
              ."Server Time:  ".date("Y-m-d H:i:s")."\n";

		/**
		 *	Includes additional information to the email's message,
		 *  are passed two parameters: the basic information, and the IP address
		 */
		$basic_data = apply_filters( 'cpcff_additional_information',  $basic_data, $myrows[0]->ipaddr );
		$email_data[ 'message' ] .= $basic_data;
	}

    $subject = cp_calculatedfieldsf_get_option('fp_subject', CP_CALCULATEDFIELDSF_DEFAULT_fp_subject);
    $subject = _cp_calculatedfieldsf_replace_vars( $fields, $params, $subject, '', 'plain text', $itemnumber );

	$form_data = cp_calculatedfieldsf_get_option( 'form_structure', CP_CALCULATEDFIELDSF_DEFAULT_form_structure );
	$from = cp_calculatedfieldsf_translate_tags(cp_calculatedfieldsf_get_option('fp_from_email', CP_CALCULATEDFIELDSF_DEFAULT_fp_from_email), $params, $form_data);
    $to = explode(",",
                   cp_calculatedfieldsf_translate_tags(cp_calculatedfieldsf_get_option('fp_destination_emails', CP_CALCULATEDFIELDSF_DEFAULT_fp_destination_emails), $params, $form_data)
                 );

    if ('html' == cp_calculatedfieldsf_get_option('fp_emailformat', CP_CALCULATEDFIELDSF_DEFAULT_email_format))
	{
		$content_type = "Content-Type: text/html; charset=utf-8\n";
	}
	else $content_type = "Content-Type: text/plain; charset=utf-8\n";

    $replyto = explode( ',', $myrows[0]->notifyto );
    if (cp_calculatedfieldsf_get_option('fp_emailfrommethod', "fixed") == "customer" && !empty( $replyto ) )
	    $from_1 = $replyto[ 0 ];
	else
        $from_1 = $from;

    $cp_calculatedfieldsf_envelope_from = $from_1;
    if ( strpos($from_1,">") === false ) $from_1 = '"'.$from_1.'" <'.$from_1.'>';

    foreach ($to as $item)
        if (trim($item) != '')
        {
			try
			{
				wp_mail(trim($item), $subject[ 'message' ], $email_data[ 'message' ],
					"From: ".$from_1."\r\n".
					( ( !empty( $replyto ) ) ? "Reply-To: ".str_replace(' ', '', implode( ',', $replyto ))."\r\n" : "" ).
					$content_type.
					"X-Mailer: PHP/" . phpversion(), $email_data[ 'attachments' ] );
			}
			catch( Exception $mail_err ){}
        }

    // 2- Send copy to user
    //---------------------------
    $notifyto = explode( ',', $myrows[0]->notifyto ); // Allows send multiple notification emails.

    if ( ( !empty( $notifyto ) || $payer_email != '') && 'true' == cp_calculatedfieldsf_get_option('cu_enable_copy_to_user', CP_CALCULATEDFIELDSF_DEFAULT_cu_enable_copy_to_user))
    {
        $email_data = _cp_calculatedfieldsf_replace_vars(
                        $fields,
                        $params,
                        cp_calculatedfieldsf_get_option('cu_message', CP_CALCULATEDFIELDSF_DEFAULT_cu_message),
                        $buffer_A,
                        cp_calculatedfieldsf_get_option('cu_emailformat', CP_CALCULATEDFIELDSF_DEFAULT_email_format),
                        $itemnumber
                    );

        $subject = cp_calculatedfieldsf_get_option('cu_subject', CP_CALCULATEDFIELDSF_DEFAULT_cu_subject);
        $subject = _cp_calculatedfieldsf_replace_vars( $fields, $params, $subject, '', 'plain text', $itemnumber );

		if ('html' == cp_calculatedfieldsf_get_option('cu_emailformat', CP_CALCULATEDFIELDSF_DEFAULT_email_format))
		{
			$content_type = "Content-Type: text/html; charset=utf-8\n";
		}
		else $content_type = "Content-Type: text/plain; charset=utf-8\n";

		$cp_calculatedfieldsf_envelope_from = $from;
		if ( strpos($from,">") === false ) $from = '"'.$from.'" <'.$from.'>';

		if ( !empty( $notifyto ) )
		{
			foreach( $notifyto as $email_address )
			{
				try
				{
					wp_mail( $email_address, $subject[ 'message' ], $email_data[ 'message' ],
							"From: ".$from."\r\n".
							$content_type.
							"X-Mailer: PHP/" . phpversion());
				}
				catch( Exception $mail_err ){}
			}
		}

        if ( !in_array( $payer_email, $notifyto ) && $payer_email != '')
		{
			try
			{
				wp_mail(trim($payer_email), $subject[ 'message' ], $email_data[ 'message' ],
						"From: ".$from."\r\n".
						$content_type.
						"X-Mailer: PHP/" . phpversion());
			}
			catch( Exception $mail_err ){}
		}
    }
    remove_filter('phpmailer_init','cp_calculatedfieldsf_envelope_from');
}

function cp_calculatedfieldsf_envelope_from( $phpmailer )
{
	// Checks if the email's headers should be corrected or not
	if( !get_option( 'CP_CALCULATEDFIELDSF_EMAIL_HEADERS', false ) ) return $phpmailer;

	global $cp_calculatedfieldsf_envelope_from;

	$cp_calculatedfieldsf_envelope_from = strtolower( $cp_calculatedfieldsf_envelope_from );
	$parts = explode( '@',  $cp_calculatedfieldsf_envelope_from );
	$home_url = CPCFF_AUXILIARY::site_url();

	if(
		strtolower( $phpmailer->Mailer ) == 'smtp' ||
		count( $parts ) != 2 ||
		strpos( $home_url, $parts[ 1 ] ) === false
	) return $phpmailer;

    $phpmailer->Sender = $cp_calculatedfieldsf_envelope_from;
    $phpmailer->From = $cp_calculatedfieldsf_envelope_from;
	return $phpmailer;
}

function cp_calculatedfieldsf_export_form ()
{
    if (!is_admin())
        return;

    global $wpdb;

	$_GET['name'] = intval(@$_GET['name']);

    if (!defined('CP_CALCULATEDFIELDSF_ID'))
        define ('CP_CALCULATEDFIELDSF_ID',intval($_GET["name"]));

	$form = '';
    $form_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.CP_CALCULATEDFIELDSF_FORMS_TABLE." WHERE id=%d", $_GET['name'] ), ARRAY_A);

	if(!empty($form_data))
	{
		$form_data["form_name"] = 'Exported: '.$form_data["form_name"];
		$addons_array = array();

		/**
		 *	Passes the array with the addons data and the form's id.
		 */
		$addons_array = apply_filters('cpcff_export_addons', $addons_array, $form_data['id']);
		$form_data[ 'addons' ] = $addons_array;
		unset($form_data["id"]);
		$form = serialize($form_data);
	}

    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=export.cpfm");

    echo $form;
    exit;
}

function cp_calculatedfieldsf_sorting_fields_in_containers( $fields_list )
{
	$new_fields_list = array();
	while( count( $fields_list ) )
	{
		$field = array_shift( $fields_list );
		$fieldType = strtolower( $field->ftype );

		if( $fieldType == 'ffieldset' || $fieldType == 'fdiv' )
		{
			$fields = $field->fields;
			if( !empty( $fields ) )
			{
				$tmp_list = array();
				$tmp_counter = 0;
				foreach( $fields as $index => $fieldName )
				{
					for( $i = 0; $i < count( $fields_list ); $i++ )
					{
						if( $fieldName == $fields_list[ $i ]->name )
						{
							$tmp_list[ $tmp_counter ] = array_splice( $fields_list, $i, 1 );
							$tmp_list[ $tmp_counter ] = $tmp_list[ $tmp_counter ][ 0 ];
							$tmp_counter++;
							break;
						}
					}
				}
				$fields_list = array_merge( $tmp_list, $fields_list );
			}
		}
		else
		{
			$new_fields_list[] = $field;
		}
	}
	return $new_fields_list;
}

function cp_calculatedfieldsf_export_csv ()
{
	$toExclude = array( 'fcommentarea', 'fsectionbreak', 'fpagebreak', 'fsummary', 'fmedia', 'ffieldset', 'fdiv', 'fbutton' );

    if (!is_admin())
        return;
    global $wpdb;

    if (!defined('CP_CALCULATEDFIELDSF_ID'))
        define ('CP_CALCULATEDFIELDSF_ID',intval($_GET["cal"]));

    $headers = array( "Form ID",  "Submission ID",  "Time",  "IP Address",  "email",  "Paid",  "Final Price",  "Coupon" );
	$fields = array( 0, 1, 2, 3, 4, 5, 6, 7 );
    $values = array();
	$form_data = cp_calculatedfieldsf_get_option( 'form_structure', CP_CALCULATEDFIELDSF_DEFAULT_form_structure );
	$fields_list = cp_calculatedfieldsf_sorting_fields_in_containers( $form_data[ 0 ] );

	// Get headers and fields
	for( $i = 0; $i < count( $fields_list ); $i++ )
	{
		$field = $fields_list[ $i ];
		$fieldType = strtolower( $field->ftype );
		if( !in_array( $fieldType, $toExclude ) )
		{
			$fields[] 	 = $field->name;
			$headers[] = ( !empty( $field->shortlabel ) ) ? $field->shortlabel : ( ( !empty( $field->title ) ) ? $field->title : $field->name );
		}
	}

	// Get rows
    $cond = '';
    if ($_GET["search"] != '') $cond .= " AND (data like '%".esc_sql($_GET["search"])."%' OR paypal_post LIKE '%".esc_sql($_GET["search"])."%')";
    if ($_GET["dfrom"] != '') $cond .= " AND (`time` >= '".esc_sql($_GET["dfrom"])."')";
    if ($_GET["dto"] != '') $cond .= " AND (`time` <= '".esc_sql($_GET["dto"])." 23:59:59')";
    if (CP_CALCULATEDFIELDSF_ID != 0) $cond .= " AND formid=".CP_CALCULATEDFIELDSF_ID;

	$events_query = "SELECT * FROM ".CP_CALCULATEDFIELDSF_POSTS_TABLE_NAME." WHERE 1=1 ".$cond." ORDER BY `time` DESC";
	/**
	 * Allows modify the query of messages, passing the query as parameter
	 * returns the new query
	 */
	$events_query = apply_filters( 'cpcff_csv_query', $events_query );
	$events = $wpdb->get_results( $events_query );

    foreach ($events as $item)
    {

        $data = array();
        $data = @unserialize( $item->paypal_post );
		if( $data === false ) continue;

        $value = array( $item->formid, $item->id, $item->time, $item->ipaddr, $item->notifyto, ( $item->paid ? "Yes" : "No" ), @$data[ "final_price" ], @$data[ "coupon" ] );

        unset($data["final_price"]);
        unset($data["coupon"]);

		$value = array_merge( $value, $data );
		$values[] = $value;
    }

    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=export.csv");

	// Print headers
    foreach ( $headers as $header )
        echo '"'.str_replace( '"', '""', $header ).'",';
    echo "\n";

	// Print rows
    foreach ( $values as $item )
    {
        foreach( $fields as $field )
		{
			if ( !isset( $item[ $field ] ) )
                $item[ $field ] = '';

            if( isset( $item[ $field.'_url' ] ) )
				$field.='_url';

            if ( is_array( $item[ $field ] ) )
                $item[ $field ] = implode( $item[ $field ], ',' );

            echo '"' . str_replace( '"', '""', $item[ $field ] ) . '",';
		}
		echo "\n";
    }

    exit;
}

function cp_calculatedfieldsf_save_options()
{
    check_admin_referer( 'session_id_'.CP_SESSION::session_id(), '_cpcff_nonce' );
    global $wpdb;
    if (!defined('CP_CALCULATEDFIELDSF_ID'))
        define ('CP_CALCULATEDFIELDSF_ID',$_POST["cp_calculatedfieldsf_id"]);

	$error_occur = false;
    if( isset( $_POST[ 'form_structure' ] ) )
    {
		// Remove bom characters
		$bom = pack('H*','EFBBBF');
		$_POST[ 'form_structure' ] = preg_replace("/$bom/", '', $_POST[ 'form_structure' ]);

		$form_structure_obj = CPCFF_AUXILIARY::json_decode( $_POST[ 'form_structure' ] );
		if( !empty( $form_structure_obj ) )
		{
			global $cpcff_default_texts_array;
			$cpcff_text_array = '';

			if( isset( $_POST[ 'cpcff_text_array' ] ) )
			{
				foreach( $_POST[ 'cpcff_text_array' ] as $cpcff_text_index => $cpcff_text_attr )
				{
					if( $cpcff_text_index  == 'errors')
					{
						foreach( $_POST[ 'cpcff_text_array' ][ $cpcff_text_index ] as $cpcff_text_sub_index => $cpcff_text_sub_attr )
						{
							$_POST[ 'cpcff_text_array' ][ $cpcff_text_index ][$cpcff_text_sub_index][ 'text' ] = stripcslashes( $cpcff_text_sub_attr[ 'text' ] );
						}
					}
					else
					{
						$_POST[ 'cpcff_text_array' ][ $cpcff_text_index ][ 'text' ] = stripcslashes( $cpcff_text_attr[ 'text' ] );
					}
				}
				$cpcff_text_array = $_POST[ 'cpcff_text_array' ];
				unset( $_POST[ 'cpcff_text_array' ] );
			}

			foreach ($_POST as $item => $value)
			{
				if( is_array( $value ) )
				{
					foreach( $value as $subitem => $subvalue )
					{
						$value[ $subitem ] = stripcslashes( $subvalue );
					}
				}
				else
				{
					$value = stripcslashes( $value );
				}
				$_POST[$item] = $value;
			}

			$data = array(
						  'form_structure' => $_POST['form_structure'],
						  'fp_from_email' => $_POST['fp_from_email'],
						  'fp_destination_emails' => $_POST['fp_destination_emails'],
						  'fp_subject' => $_POST['fp_subject'],
						  'fp_inc_additional_info' => $_POST['fp_inc_additional_info'],
						  'fp_return_page' => $_POST['fp_return_page'],
						  'fp_message' => $_POST['fp_message'],
						  'fp_emailformat' => $_POST['fp_emailformat'],

						  'cu_enable_copy_to_user' => $_POST['cu_enable_copy_to_user'],
						  'cu_user_email_field' => ( !empty( $_POST[ 'cu_user_email_field' ] ) ? implode( ',', $_POST[ 'cu_user_email_field' ] ) : '' ),
						  'cu_subject' => $_POST['cu_subject'],
						  'cu_message' => $_POST['cu_message'],
						  'cu_emailformat' => $_POST['cu_emailformat'],
						  'fp_emailfrommethod' => $_POST['fp_emailfrommethod'],

						  'enable_paypal' => @$_POST["enable_paypal"],
						  'enable_submit' => @$_POST["enable_submit"],
						  'paypal_notiemails' => @$_POST["paypal_notiemails"],
						  'paypal_email' => $_POST["paypal_email"],
						  'request_cost' => @$_POST["request_cost"],
						  'paypal_product_name' => $_POST["paypal_product_name"],
						  'currency' => $_POST["currency"],
						  'paypal_language' => $_POST["paypal_language"],
						  'paypal_mode' => $_POST["paypal_mode"],
						  'paypal_recurrent' => ( ( $_POST["paypal_recurrent"] == 'field' ) ? ( ( !empty( $_POST["paypal_recurrent_field"] ) ) ? $_POST["paypal_recurrent_field"] : 0 ) : $_POST["paypal_recurrent"] ),
						  'paypal_recurrent_setup' => @$_POST["paypal_recurrent_setup"],
						  'paypal_recurrent_setup_days' => @$_POST["paypal_recurrent_setup_days"],
						  'paypal_identify_prices' => (isset($_POST['paypal_identify_prices'])?$_POST['paypal_identify_prices']:'0'),
						  'paypal_zero_payment' => $_POST["paypal_zero_payment"],
						  'paypal_base_amount' => trim( $_POST["paypal_base_amount"] ),

						  'enable_paypal_option_yes' => (@$_POST['enable_paypal_option_yes']?$_POST['enable_paypal_option_yes']:CP_CALCULATEDFIELDSF_PAYPAL_OPTION_YES),
						  'enable_paypal_option_no' => (@$_POST['enable_paypal_option_no']?$_POST['enable_paypal_option_no']:CP_CALCULATEDFIELDSF_PAYPAL_OPTION_NO),

						  'vs_use_validation' => $_POST['vs_use_validation'],
						  'vs_text_is_required' => $_POST['vs_text_is_required'],
						  'vs_text_is_email' => $_POST['vs_text_is_email'],
						  'vs_text_datemmddyyyy' => $_POST['vs_text_datemmddyyyy'],
						  'vs_text_dateddmmyyyy' => $_POST['vs_text_dateddmmyyyy'],
						  'vs_text_number' => $_POST['vs_text_number'],
						  'vs_text_digits' => $_POST['vs_text_digits'],
						  'vs_text_max' => $_POST['vs_text_max'],
						  'vs_text_min' => $_POST['vs_text_min'],
						  'vs_text_submitbtn' => $_POST['vs_text_submitbtn'],
						  'vs_text_previousbtn' => $_POST['vs_text_previousbtn'],
						  'vs_text_nextbtn' => $_POST['vs_text_nextbtn'],
						  'vs_all_texts' => serialize( $cpcff_text_array ),

						  'cv_enable_captcha' => $_POST['cv_enable_captcha'],
						  'cv_width' => $_POST['cv_width'],
						  'cv_height' => $_POST['cv_height'],
						  'cv_chars' => $_POST['cv_chars'],
						  'cv_font' => $_POST['cv_font'],
						  'cv_min_font_size' => $_POST['cv_min_font_size'],
						  'cv_max_font_size' => $_POST['cv_max_font_size'],
						  'cv_noise' => $_POST['cv_noise'],
						  'cv_noise_length' => $_POST['cv_noise_length'],
						  'cv_background' => $_POST['cv_background'],
						  'cv_border' => $_POST['cv_border'],
						  'cv_text_enter_valid_captcha' => $_POST['cv_text_enter_valid_captcha'],
						  'cache' => ''
			);
			$_update_result = $wpdb->update (
				$wpdb->prefix.CP_CALCULATEDFIELDSF_FORMS_TABLE,
				$data,
				array( 'id' => CP_CALCULATEDFIELDSF_ID ),
				array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ),
				array( '%d' )
				);
			if( $_update_result === false )
			{
				global $cff_structure_error;
				$cff_structure_error = __('<div class="error-text">The data cannot be stored in database because has occurred an error with the database structure. Please, go to the plugins section and Deactivate/Activate the plugin to be sure the structure of database has been checked, and corrected if needed. If the issue persist, please <a href="http://wordpress.dwbooster.com/support">contact us</a></div>', 'calculated-fields-form' );
			}
		}
		else
		{
			$error_occur = true;
		}
	}
	else
    {
		$error_occur = true;
    }

	if( $error_occur )
	{
		global $cff_structure_error;
        $cff_structure_error = __('<div class="error-text">The data cannot be stored in database because has occurred an error with the form structure. Please, try to save the data again. If have been copied and pasted data from external text editors, the data can contain invalid characters. If the issue persist, please <a href="http://wordpress.dwbooster.com/support">contact us</a></div>', 'calculated-fields-form' );
	}
}

// cp_calculatedfieldsf_get_option:
$cp_calculatedfieldsf_option_buffered_item = false;
$cp_calculatedfieldsf_option_buffered_id = -1;

function cp_calculatedfieldsf_get_option ($field, $default_value, $id = '')
{
	$value = '';
    if (!defined("CP_CALCULATEDFIELDSF_ID"))
        define ("CP_CALCULATEDFIELDSF_ID", (!empty($id)) ? $id : 1);
    if (empty($id))
        $id = CP_CALCULATEDFIELDSF_ID;

    global $wpdb, $cp_calculatedfieldsf_option_buffered_item, $cp_calculatedfieldsf_option_buffered_id;
    if ($cp_calculatedfieldsf_option_buffered_id == $id)
	{
        if( property_exists( $cp_calculatedfieldsf_option_buffered_item, $field ) ) $value = @$cp_calculatedfieldsf_option_buffered_item->$field;
	}
    else
    {
       $myrows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.CP_CALCULATEDFIELDSF_FORMS_TABLE." WHERE id=%d", $id ) );
		if( !empty( $myrows ) )
		{
			if( property_exists( $myrows[0], $field ) )
			{
				$value = @$myrows[0]->$field;
			}
			else
			{
				$value = $default_value;
			}
			$cp_calculatedfieldsf_option_buffered_item = $myrows[0];
			$cp_calculatedfieldsf_option_buffered_id  = $id;
		}
		else
		{
			$value = $default_value;
		}
	}

	if( $field == 'form_structure'  && !is_array( $value ) )
	{
		$form_data = CPCFF_AUXILIARY::json_decode( $value, 'normal' );
		$value = $cp_calculatedfieldsf_option_buffered_item->form_structure = ( !is_null( $form_data ) ) ? $form_data : '';
	}

    if ( ( $field == 'vs_all_texts' && empty( $value ) ) || ( $value == '' && $cp_calculatedfieldsf_option_buffered_item->form_structure == '' ) )
        $value = $default_value;

	/**
	 * Filters applied before returning a form option,
	 * use three parameters: The value of option, the name of option and the form's id
	 * returns the new option's value
	 */
    $value = apply_filters( 'cpcff_get_option', $value, $field, $id );

    return $value;
}

// AUXILIARY FUNCTIONS
// ***********************************************************************

/**
 * Add the attribute: property="stylesheet" to the link tag if has not been defined.
 */
function cp_calculatedfieldsf_link_tag( $tag )
{
	if( preg_match( '/property\\s*=/i', $tag ) == 0 )
	{
		return str_replace( '/>', ' property="stylesheet" />', $tag );
	}
	return $tag;
} // End cp_calculatedfieldsf_link_tag

function cp_calculatedfieldsf_translate_tags ($tags, $params, $form_data)
{
    foreach($form_data[0] as $item)
        if ($item->ftype == 'fdropdown' || $item->ftype == 'fradio' || $item->ftype == 'fcheck')
            for ($i=0;$i<count($item->choices); $i++)
                if (@$params[$item->name] == @$item->choices[$i])
                    $params[$item->name] = @$item->choicesVal[$i];

    foreach ($params as $item => $value)
        $tags = str_replace('<%'.$item.'%>',(is_array($value)?(implode(", ",$value)):($value)),$tags);

    return $tags;
}

/**
 * Extract all tags with the format <%...%> from the message
 */
function _cp_calculatedfieldsf_extract_tags( $message )
{
	$tags_arr = array();
	if(
		preg_match_all(
			"/<%(info|fieldname\d+|fieldname\d+_label|fieldname\d+_shortlabel|fieldname\d+_value|fieldname\d+_url|fieldname\d+_urls|coupon|itemnumber|formid|final_price|payment_option|ipaddress|currentdate_mmddyyyy|currentdate_ddmmyyyy)\b(?:(?!%>).)*%>/i",
			$message,
			$matches
		)
	)
	{
		$tag = array();
		foreach( $matches[ 0 ] as $index => $value )
		{
			$tag[ 'node' ] = $value;
			$tag[ 'tag' ]  = strtolower( $matches[ 1 ][ $index ] );
			$tag[ 'if_not_empty' ] 	= preg_match( "/if_not_empty/i", $value );
			$tag[ 'before' ]    	= ( preg_match( "/before\s*=\s*\{\{((?:(?!\}\}).)*)\}\}/i",  $value, $match ) ) ? $match[ 1 ] : '';
			$tag[ 'after' ]   		= ( preg_match( "/after\s*=\s*\{\{((?:(?!\}\}).)*)\}\}/i", $value, $match ) ) ? $match[ 1 ] : '';
			$tag[ 'separator' ]    	= ( preg_match( "/separator\s*=\s*\{\{((?:(?!\}\}).)*)\}\}/i",  $value, $match ) ) ? $match[ 1 ] : '';

			$baseTag = ( preg_match( "/(fieldname\d+)_(label|value|shortlabel)/i", $tag[ 'tag' ], $match ) ) ? $match[ 1 ] : $tag[ 'tag' ];

			if( empty( $tags_arr[ $baseTag ] ) ) $tags_arr[ $baseTag ] = array();
			$tags_arr[ $baseTag ][] = $tag;
		}
	}
	return $tags_arr;
}

function _cp_calculatedfieldsf_replace_vars( $fields, $params, $message, $buffer = '', $contentType = 'html', $itemnumber = '' )
{
	// Lambda functions
	$arrayReplacementFunction = create_function('&$tags, $tagName, $replacement, &$message', 'if(isset($tags[ $tagName ])){foreach( $tags[ $tagName ] as $tagData ){ $message = str_replace( $tagData[ "node" ], $tagData[ "before" ].$replacement.$tagData[ "after" ], $message );}unset( $tags[ $tagName ] );}');

	$singleReplacementFunction = create_function('$tagData, $value, &$message', '$message = str_replace( $tagData[ "node" ], $tagData[ "before" ].$value.$tagData[ "after" ],$message );');

	$message = str_replace( '< %', '<%', $message );
    $attachments = array();

	// Remove empty blocks
	while( preg_match( "/<%\s*fieldname(\d+)_block\s*%>/", $message, $matches ) )
	{
		if( empty( $params[ 'fieldname'.$matches[ 1 ] ] ) )
		{
			$from = strpos( $message, $matches[ 0 ] );
			if( preg_match( "/<%\s*fieldname(".$matches[ 1 ].")_endblock\s*%>/", $message, $matches_end ) )
			{
				$lenght = strpos( $message, $matches_end[ 0 ] ) + strlen( $matches_end[ 0 ] ) - $from;
			}
			else
			{
				$lenght = strlen( $matches[ 0 ] );
			}
			$message = substr_replace( $message, '', $from, $lenght );
		}
		else
		{
			$message = preg_replace( array( "/<%\s*fieldname".$matches[ 1 ]."_block\s*%>/", "/<%\s*fieldname".$matches[ 1 ]."_endblock\s*%>/"), "", $message );
		}
	}

	$tags = _cp_calculatedfieldsf_extract_tags( $message );

	if ( 'html' == $contentType )
    {
        $message = str_replace( "\n", "", $message );
        $buffer = str_replace( array('&lt;', '&gt;', '\"', "\'"), array('<', '>', '"', "'" ), $buffer );
    }

	// Replace the INFO tags
    if( !empty( $tags[ 'info' ] ) )
	{
		$buffer1 = $buffer;
		do{
			$tmp = $buffer1;
			$buffer1 = preg_replace(
				array(
					"/^[^\n:]*:{1,2}\s*\n/",
					"/\n[^\n:]*:{1,2}\s*\n/",
					"/\n[^\n:]*:{1,2}\s*$/"
				),
				array(
					"",
					"\n",
					""
				),
				$buffer1
			);
		}while( $buffer1 <> $tmp );

		foreach( $tags[ 'info' ] as $tagData )
		{
			$singleReplacementFunction( $tagData, ( ( $tagData[ 'if_not_empty' ] ) ? $buffer1 : $buffer ), $message );
		}
		unset( $tags[ 'info' ] );
	}

	foreach ($params as $item => $value)
    {
		$value_bk = $value;
		if( isset( $tags[ $item ] ) )
		{
			$label 		= ( isset( $fields[ $item ] ) && property_exists( $fields[ $item ], 'title' ) ) ? $fields[ $item ]->title : '';
			$shortlabel = ( isset( $fields[ $item ] ) && property_exists( $fields[ $item ], 'shortlabel' ) ) ? $fields[ $item ]->shortlabel : '';
			$value = ( !empty( $value ) || is_numeric( $value ) && $value == 0 ) ? ( ( is_array( $value ) ) ? implode( ", ", $value ) : $value ) : '';

			if ( 'html' == $contentType )
			{
				$label = str_replace( array('&lt;', '&gt;', '\"', "\'"), array('<', '>', '"', "'" ), $label );
				$shortlabel = str_replace( array('&lt;', '&gt;', '\"', "\'"), array('<', '>', '"', "'" ), $shortlabel );
				$value = str_replace( array('&lt;', '&gt;', '\"', "\'"), array('<', '>', '"', "'" ), $value );
			}

			foreach( $tags[ $item ] as $tagData )
			{
				if( $tagData[ 'if_not_empty' ] == 0 || $value !== '' )
				{
					switch( $tagData[ 'tag' ] )
					{
						case $item:
							$singleReplacementFunction( $tagData, $label.$tagData[ 'separator' ].$value, $message );
						break;
						case $item.'_label':
							$singleReplacementFunction( $tagData, $label, $message );
						break;
						case $item.'_value':
							$singleReplacementFunction( $tagData, $value, $message );
						break;
						case $item.'_shortlabel':
							$singleReplacementFunction( $tagData, $shortlabel, $message );
						break;
					}
				}
				else
				{
					$message = str_replace( $tagData[ 'node' ], '', $message );
				}
			}
			unset( $tags[ $item ] );
		}

        if( preg_match( "/_link\b/i", $item ) )
        {
            $attachments = array_merge( $attachments, $value_bk );
        }
    }

	$arrayReplacementFunction( $tags, 'formid', $params['formid'], $message );
	$arrayReplacementFunction( $tags, 'itemnumber', $itemnumber, $message );
	$arrayReplacementFunction( $tags, 'currentdate_mmddyyyy', date("m/d/Y H:i:s"), $message );
	$arrayReplacementFunction( $tags, 'currentdate_ddmmyyyy', date("d/m/Y H:i:s"), $message );
	$arrayReplacementFunction( $tags, 'ipaddress', $fields[ 'ipaddr' ], $message );

    // Replace coupons code
	if( isset( $_REQUEST[ 'couponcode' ] ) && isset( $tags[ 'couponcode' ] ) )
    {
		$arrayReplacementFunction( $tags, 'couponcode', $_REQUEST[ 'couponcode' ], $message );
    }

	foreach( $tags as $tagArr )
    {
        foreach( $tagArr as $tagData )
		{
			$message = str_replace( $tagData[ 'node' ], '', $message );
		}
	}

    if ( 'html' == $contentType )
    {
        $message = str_replace( "\n", "<br>", $message );
    }

	return array( 'message' => $message, 'attachments' => $attachments );
}

// END AUXILIARY

// WIDGET CODE BELOW
// ***********************************************************************

class CP_calculatedfieldsf_Widget extends WP_Widget
{
  function __construct()
  {
    $widget_ops = array('classname' => 'CP_calculatedfieldsf_Widget', 'description' => 'Displays a form integrated with Paypal' );
    parent::__construct('CP_calculatedfieldsf_Widget', 'Calculated Fields Form', $widget_ops);
  }

  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'formid' => '' ) );
    $title = $instance['title'];
    $formid = $instance['formid'];
    ?><p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label>
    <label for="<?php echo $this->get_field_id('formid'); ?>">Form ID: <input class="widefat" id="<?php echo $this->get_field_id('formid'); ?>" name="<?php echo $this->get_field_name('formid'); ?>" type="text" value="<?php echo esc_attr($formid); ?>" /></label>
    </p><?php
  }

  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    $instance['formid'] = $new_instance['formid'];
    return $instance;
  }

  function widget($args, $instance)
  {
	global $cpcff_main;
    extract($args, EXTR_SKIP);

    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
    $formid = $instance['formid'];

    if (!empty($title))
      echo $before_title . $title . $after_title;

    // WIDGET CODE GOES HERE
    echo $cpcff_main->public_form( array( 'id' => $formid ) );

    echo $after_widget;
  }

}

// DASHBOARD WIDGET CODE BELOW
// ***********************************************************************

function cp_calculatedfieldsf_add_dashboard_widgets() {
	if( !current_user_can( 'manage_options' ) ) return;
	wp_add_dashboard_widget(
                 'cp_calculatedfieldsf_dashboard_widgets',
                 'Calculated Fields Form Activity',
                 'cp_calculatedfieldsf_dashboard_widgets'
        );
}
add_action( 'wp_dashboard_setup', 'cp_calculatedfieldsf_add_dashboard_widgets' );

/**
 * Output of the dashboard widget.
 */
function cp_calculatedfieldsf_dashboard_widgets() {
    global $wpdb;
    $styleA = 'style="border-right:1px solid rgb(238, 238, 238);border-bottom:1px solid rgb(238, 238, 238);"';
    $styleB = 'style="border-bottom:1px solid rgb(238, 238, 238);"';
    $styleC = 'style="color:#FF0000;"';
    $styleD = 'style="font-weight:bold;"';
    ?>
    <div style="max-height:400px; overflow-y:auto;">
    <table style="width:100%;">
        <tr>
            <th align="left" <?php echo $styleA; ?>><?php _e( 'ID', 'calculated-fields-form' ); ?></th>
            <th align="left" <?php echo $styleA; ?>><?php _e( 'Form', 'calculated-fields-form' ); ?></th>
            <th align="left" <?php echo $styleA; ?>><?php _e( 'Date', 'calculated-fields-form' ); ?></th>
            <th align="left" <?php echo $styleA; ?>><?php _e( 'Email', 'calculated-fields-form' ); ?></th>
            <th align="left" <?php echo $styleB; ?>><?php _e( 'Payment Info', 'calculated-fields-form' ); ?></th>
        </tr>
    <?php

        $submissions_result = $wpdb->get_results( "SELECT ftable.form_name, ptable.* FROM ".$wpdb->prefix.CP_CALCULATEDFIELDSF_FORMS_TABLE." as ftable, ".CP_CALCULATEDFIELDSF_POSTS_TABLE_NAME." as ptable WHERE ptable.formid=ftable.id AND ptable.time > CURDATE() - INTERVAL 7 DAY ORDER BY ptable.time DESC;" );
        foreach( $submissions_result as $row )
        {
			// Add links
			$paypal_post = @unserialize( $row->paypal_post );
			$_urls = '';

			if( $paypal_post !== false )
			{
				foreach( $paypal_post as $_key => $_value )
				{
					if( strpos( $_key, '_url' ) )
					{
						if( is_array( $_value ) )
						{
							foreach( $_value as $_url )
							{
								$_urls .= '<p><a href="'.esc_attr( $_url ).'" target="_blank">'.$_url.'</a></p>';
							}
						}
					}
				}
			}

            echo '
            <tr>
                <td align="left" '.$styleA.'><span '.$styleD.'>'.$row->id.'</span></td>
                <td align="left" '.$styleA.'>
                    <a href="admin.php?page=cp_calculated_fields_form&cal='.$row->formid.'&list=1&r='.rand().'">'.$row->form_name.'</a>
                </td>
                <td align="left" '.$styleA.'>'.$row->time.'</td>
                <td align="left" '.$styleA.'>'.$row->notifyto.'</td>
                <td align="left" '.$styleB.'>'.( ( $row->paid ) ? __('Paid', 'calculated-fields-form' ) : '<span '.$styleC.'>'.__('Not Paid', 'calculated-fields-form' ).'</span>' ).'</td>
            </tr>
            <tr>
                <td colspan="5"  '.$styleB.' >
                '.preg_replace(
                        '/\n+/',
                        '<br>',
                        str_replace(
                        array( "\'", '\"' ),
                        array( "'", '"' ),
                        $row->data)
                 ).$_urls.'
                </td>
            </tr>
            ';
        }

    ?>
    </table>
    </div>
    <?php
}
?>