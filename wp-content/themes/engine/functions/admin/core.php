<?php
/**
 *
 */
function it_options_init() {
	register_setting( IT_SETTINGS, IT_SETTINGS );
	
	# Add default options if they don't exist
	add_option( IT_SETTINGS, it_options( 'settings', 'default' ) );
	add_option( IT_INTERNAL_SETTINGS, it_options( 'internal', 'default' ) );
	# delete_option(IT_SETTINGS);
	# delete_option(IT_INTERNAL_SETTINGS);
	
	if( it_ajax_request() ) {
		# Ajax option save
		if( isset( $_POST['it_option_save'] ) ) {
			it_ajax_option_save();
			
		# Sidebar option save
		} elseif( isset( $_POST['it_sidebar_save'] ) ) {
			it_sidebar_option_save();
			
		} elseif( isset( $_POST['it_sidebar_delete'] ) ) {
			it_sidebar_option_delete();
						
		} elseif( isset( $_POST['action'] ) && $_POST['action'] == 'add-menu-item' ) {
			add_filter( 'nav_menu_description', create_function('','return "";') );
		}
	}
	
	# Option import
	if( ( !it_ajax_request() ) && ( isset( $_POST['it_import_options'] ) ) ) {
		it_import_options( $_POST[IT_SETTINGS]['import_options'] );

	# Reset options
	} elseif( ( !it_ajax_request() ) && ( isset( $_POST[IT_SETTINGS]['reset'] ) ) ) {
		it_load_defaults();
		wp_redirect( admin_url( 'admin.php?page=it-options&reset=true' ) );
		exit;
		
	# load demo settings
	} elseif( ( !it_ajax_request() ) && ( isset( $_POST[IT_SETTINGS]['load_demo'] ) ) ) {
		it_load_demo();
		wp_redirect( admin_url( 'admin.php?page=it-options&demo=true' ) );
		exit;
		
	# $_POST option save
	} elseif( ( !it_ajax_request() ) && ( isset( $_POST['it_admin_wpnonce'] ) ) ) {
		unset(  $_POST[IT_SETTINGS]['export_options'] );
	}
	
}

/**
 *
 */
function it_sidebar_option_delete() {
	check_ajax_referer( IT_SETTINGS . '_wpnonce', 'it_admin_wpnonce' );
	
	$data = $_POST;
	
	$saved_sidebars = get_option( IT_SIDEBARS );
	
	$msg = array( 'success' => false, 'sidebar_id' => $data['sidebar_id'], 'message' => sprintf( __( 'Error: Sidebar &quot;%1$s&quot; not deleted, please try again.', IT_TEXTDOMAIN ), $data['it_sidebar_delete'] ) );
	
	unset( $saved_sidebars[$data['sidebar_id']] );
	
	if( update_option( IT_SIDEBARS, $saved_sidebars ) ) {
		$msg = array( 'success' => 'deleted_sidebar', 'sidebar_id' => $data['sidebar_id'], 'message' => sprintf( __( 'Sidebar &quot;%1$s&quot; Deleted.', IT_TEXTDOMAIN ), $data['it_sidebar_delete'] ) );
	}
	
	$echo = json_encode( $msg );

	@header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
	echo $echo;
	exit;
}

/**
 *
 */
function it_sidebar_option_save() {
	check_ajax_referer( IT_SETTINGS . '_wpnonce', 'it_admin_wpnonce' );
	
	$data = $_POST;
	
	$saved_sidebars = get_option( IT_SIDEBARS );
	
	$msg = array( 'success' => false, 'sidebar' => $data['custom_sidebars'], 'message' => sprintf( __( 'Error: Sidebar &quot;%1$s&quot; not saved, please try again.', IT_TEXTDOMAIN ), $data['custom_sidebars'] ) );
	
	if( empty( $saved_sidebars ) ) {
		$update_sidebar[$data['it_sidebar_id']] = $data['custom_sidebars'];
		
		if( update_option( IT_SIDEBARS, $update_sidebar ) )
			$msg = array( 'success' => 'saved_sidebar', 'sidebar' => $data['custom_sidebars'], 'sidebar_id' => $data['it_sidebar_id'], 'message' => sprintf( __( 'Sidebar &quot;%1$s&quot; Added.', IT_TEXTDOMAIN ), $data['custom_sidebars'] ) );
		
	} elseif( is_array( $saved_sidebars ) ) {
		
		if( in_array( $data['custom_sidebars'], $saved_sidebars ) ) {
			$msg = array( 'success' => false, 'sidebar' => $data['custom_sidebars'], 'message' => sprintf( __( 'Sidebar &quot;%1$s&quot; Already Exists.', IT_TEXTDOMAIN ), $data['custom_sidebars'] ) );
			
		} elseif( !in_array( $data['custom_sidebars'], $saved_sidebars ) ) {
			$sidebar[$data['it_sidebar_id']] = $data['custom_sidebars'];
			$update_sidebar = $saved_sidebars + $sidebar;
			
			if( update_option( IT_SIDEBARS, $update_sidebar ) )
				$msg = array( 'success' => 'saved_sidebar', 'sidebar' => $data['custom_sidebars'], 'sidebar_id' => $data['it_sidebar_id'], 'message' => sprintf( __( 'Sidebar &quot;%1$s&quot; Added.', IT_TEXTDOMAIN ), $data['custom_sidebars'] ) );
			
		}
	}
		
	$echo = json_encode( $msg );

	@header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
	echo $echo;
	exit;
}

/**
 *
 */
function it_ajax_option_save() {
	check_ajax_referer( IT_SETTINGS . '_wpnonce', 'it_admin_wpnonce' );
	
	$data = it_prep_data($_POST);
	
	$count = count($_POST, COUNT_RECURSIVE);
	
	unset( $data['_wp_http_referer'], $data['_wpnonce'], $data['action'], $data['it_full_submit'], $data[IT_SETTINGS]['export_options'] );
	unset( $data['it_admin_wpnonce'], $data['it_option_save'], $data['option_page'] );
	
	$msg = array( 'success' => false, 'message' => __( 'Error: Options not saved, please try again.', IT_TEXTDOMAIN ) );
	
	if( get_option( IT_SETTINGS ) != $data[IT_SETTINGS] ) {
		
		if( update_option( IT_SETTINGS, $data[IT_SETTINGS] ) )
			$msg = array( 'success' => 'options_saved', 'message' => $count . __( ' Total Options Saved.', IT_TEXTDOMAIN ) );
			
	} else {
		$msg = array( 'success' => true, 'message' => $count . __( ' Total Options Saved.', IT_TEXTDOMAIN ) );
	}
	
	$echo = json_encode( $msg );

	@header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
	echo $echo;
	exit;
}

/**
 * 
 */
function it_shortcode_generator() {

	$shortcodes = it_shortcodes();
	
	$options = array();
	
	foreach( $shortcodes as $shortcode ) {
		$shortcode = str_replace( '.php', '',$shortcode );
		$shortcode = preg_replace( '/[0-9-]/', '', $shortcode );
		
		if( $shortcode[0] != '_' ) {
			$class = 'it' . ucwords( $shortcode );
			$options[] = call_user_func( array( &$class, '_options'), $class );
		}
	}
	
	return $options;
}

/**
 *
 */
function it_check_wp_version(){
	global $wp_version;
	
	$check_WP = '3.7';
	$is_ok = version_compare($wp_version, $check_WP, '>=');
	
	if ( ($is_ok == FALSE) ) {
		return false;
	}
	
	return true;
}

/**
 * 
 */
function it_sociable_option() {
	$sociables = array(
	    'vkontakte' => 'VK',
	    'facebook' => 'Facebook',
		'twitter' => 'Twitter',
		'odnoklassniki' => 'OK',
		'googleplus' => 'Google+',
		'pinterest' => 'Pinterest',
		'vimeo' => 'Vimeo',
		'tumblr' => 'Tumblr',
		'instagram' => 'Instagram',
		'flickr' => 'Flickr',
		'youtube' => 'Youtube',
		'linkedin' => 'LinkedIn',
		'stumbleupon' => 'StumbleUpon',
		'skype' => 'Skype'
		);
	
	return array( 'sociables' => $sociables );
}

/**
 * 
 */
function it_reactions_option() {
	$reactions = array(
		'' => __('Choose One...',IT_TEXTDOMAIN),
		'emo-happy' => __('Happy',IT_TEXTDOMAIN),
		'emo-squint' => __('Glad',IT_TEXTDOMAIN),
		'emo-grin' => __('Elated',IT_TEXTDOMAIN),
		'emo-wink' => __('Wink',IT_TEXTDOMAIN),
		'emo-wink2' => __('Wink 2',IT_TEXTDOMAIN),
		'emo-thumbsup' => __('Thumbs Up',IT_TEXTDOMAIN),
		'emo-laugh' => __('Laugh',IT_TEXTDOMAIN),
		'emo-sunglasses' => __('Cool',IT_TEXTDOMAIN),
		'emo-beer' => __('Cheers!',IT_TEXTDOMAIN),
		'emo-coffee' => __('Coffee',IT_TEXTDOMAIN),
		'emo-tongue' => __('Tongue Out',IT_TEXTDOMAIN),
		'emo-saint' => __('Halo',IT_TEXTDOMAIN),
		'emo-sleep' => __('Sleep',IT_TEXTDOMAIN),
		'emo-displeased' => __('Unsure',IT_TEXTDOMAIN),
		'emo-surprised' => __('Surprised',IT_TEXTDOMAIN),
		'emo-unhappy' => __('Sad',IT_TEXTDOMAIN),
		'emo-cry' => __('Cry',IT_TEXTDOMAIN),
		'emo-angry' => __('Angry',IT_TEXTDOMAIN),
		'emo-shoot' => __('Gun',IT_TEXTDOMAIN),
		'emo-devil' => __('Devil',IT_TEXTDOMAIN)
		);
	
	return array( 'reactions' => $reactions );
}

/**
 *
 */
function it_signoffs() {
	$signoff = it_get_setting('signoff');
	$options = array();
	if ( isset($signoff['keys']) && $signoff['keys'] != '#' ) {
		$signoff_keys = explode(',',$signoff['keys']);
		foreach ($signoff_keys as $skey) {
			if ( $skey != '#') {
				$signoff_name = ( !empty( $signoff[$skey]['name'] ) ) ? $signoff[$skey]['name'] : '#';	
				$options[$signoff_name] = $signoff_name;	
			}
		}
	}
	return $options;
}

/**
 *
 */
function it_awards_meta() {	
	$awards = it_get_setting('review_awards');	
	$options = array();			
	foreach($awards as $award) {	
		if(isset($award[0]) && is_object($award[0])) {
			$name = stripslashes($award[0]->name);
			$meta_name = $award[0]->meta_name;
			$icon = $award[0]->icon;
			$isBadge = $award[0]->isBadge;
			if(!empty($name) && empty($isBadge)) {
				$options[$meta_name] = array('name' => $name, 'icon' => $icon);
			}
		}
	}	
	return $options;
}

/**
 *
 */
function it_managed_cats() {	
	$categories = it_get_setting('categories');	
	$options = array(); 
	foreach($categories as $category) {
		if(is_array($category)) {
			if(array_key_exists('id',$category)) {
				$id = $category['id'];
				$name = get_cat_name($id);
				if(!empty($id)) {
					$options[$id] = $name;
				}
			}
		}
	}
	return $options;
}

/**
 *
 */
function it_badges_meta() {	
	$awards = it_get_setting('review_awards');	
	$options = array();			
	foreach($awards as $award) {	
		if(isset($award[0]) && is_object($award[0])) {
			$name = stripslashes($award[0]->name);
			$meta_name = $award[0]->meta_name;
			$icon = $award[0]->icon;
			$isBadge = $award[0]->isBadge;
			if(!empty($name) && !empty($isBadge)) {
				$options[$meta_name] = array('name' => $name, 'icon' => $icon);
			}
		}
	}	
	return $options;
}

/**
 *
 */
function it_reactions_meta() {
	$reactions = it_get_setting('reactions');
	$options = array();
	if ( isset($reactions['keys']) && $reactions['keys'] != '#' ) {
		$reactions_keys = explode(',',$reactions['keys']);
		foreach ($reactions_keys as $rkey) {
			if ( $rkey != '#') {				
				$reaction_name = ( !empty( $reactions[$rkey]['name'] ) ) ? $reactions[$rkey]['name'] : '#';	
				$reaction_slug = ( !empty( $reactions[$rkey]['slug'] ) ) ? $reactions[$rkey]['slug'] : '#';	
				$reaction_icon = ( !empty( $reactions[$rkey]['icon'] ) ) ? $reactions[$rkey]['icon'] : '#';	
				$reaction_preset = ( !empty( $reactions[$rkey]['preset'] ) ) ? $reactions[$rkey]['preset'] : '#';				
				if($reaction_icon=='#') {
					$icon = '<span class="theme-icon-' . $reaction_preset . ' meta-icon"></span>';
				} else {
					$icon = '<span class="meta-icon"><img src="' . $reaction_icon . '" /></span>';
				}
				$options[$reaction_slug] = array('name' => $reaction_name, 'icon' => $icon);
			}
		}
	}	
	return $options;
}

/**
 * 
 */
function it_tinymce_init_size() {
	if( isset( $_GET['page'] ) ) {
		if( $_GET['page'] == 'it-options' ) {
			$tinymce = 'TinyMCE_' . IT_SETTINGS . '_content_size';
			if( !isset( $_COOKIE[$tinymce] ) )
				setcookie($tinymce, 'cw=577&ch=251');
		}
	}
}

/**
 *
 */
function it_import_options( $import ) {
	
	$imported_options = it_decode( $import, $serialize = true );
	
	if( is_array( $imported_options ) ) {
		
		if( array_key_exists( 'it_options_export', $imported_options ) ) {
			if( get_option( IT_SETTINGS ) != $imported_options ) {

				if( update_option( IT_SETTINGS, $imported_options ) )
					wp_redirect( admin_url( 'admin.php?page=it-options&import=true' ) );
				else
					wp_redirect( admin_url( 'admin.php?page=it-options&import=false' ) );

			} else {
				wp_redirect( admin_url( 'admin.php?page=it-options&import=true' ) );
			}
			
		} else {
			wp_redirect( admin_url( 'admin.php?page=it-options&import=false' ) );
		}
		
	} else {
		wp_redirect( admin_url( 'admin.php?page=it-options&import=false' ) );
	}
	
	exit;
}

/**
 *
 */
function it_load_defaults() {
	update_option( IT_SETTINGS, it_options( 'settings', 'default' ) );
	update_option( IT_WIDGETS, it_options( 'widgets', 'default' ) );
	update_option( IT_MODS, it_options( 'mods', 'default' ) );
	delete_option( IT_SIDEBARS );	
}

/**
 *
 */
function it_load_demo() {
	#load the theme options
	update_option( IT_SETTINGS, it_options( 'settings', 'demo' ) );
	#load the sidebar_widgets array
	update_option( IT_WIDGETS, it_options( 'widgets', 'demo' ) );
	#load the theme_mods array
	update_option( IT_MODS, it_options( 'mods', 'demo' ) );
	
	#load each individual widget
	$options = it_decode( it_options( 'widget', 'demo' ), $serialize = true );
	foreach ($options as $option_name => $option_value) {
		update_option($option_name, $option_value);
	}
}

/**
 *
 */
function it_options( $type, $state ) {	

	$options = '';
	
	switch($type) {
		case "settings":
		
			if($state=='demo') {
				
				#demo settings code
				$options = '7V3rk9s2kv9-Vfc_cCtVG7tWoxGp97hSSZxs9lx18ebWyV1dne9YEAlJ8JAETYAjT1Lzvy9ACSRI8AHO6HEe40McjYRGoxu_bjReDXDjjMY3f5Ab27n588cU01c-InEA7t0Ab_D-m1fkZnL4kSYpzL9cHL7kJd00CfIf5qL4ltL45vp6t9sNUeSnhCYIBHQLQ0iGHg6vYbRBEbzexVcejiiM6HUaBxj45NoZ2ZNr277mdQ_jaJPXbY9krjvk023-2_jwk710ivK2XH4L0WZL8x-FzPNRQ3kmlbv1C8HmRxXsO-dTWbapzDvEKxTAkmKF7AXFvIairBSVZlFDU1GMHlFFOyqR6BCCKHQRU0UZJsujaZMzuOIMyvqc1fGXm7xwjt8EpVcnhepQ5CrWshgdFVRXGZtyCwSm1-BO6YVmTG0ZJAIOC-i7AVjBQDXCn1krrX9A4N-o4oIIBPcUecT1sA9b-IlvPFYZDl2PEA1IFYXdYKOBW6l86Ls4Cu77EZGwi6imZZ96ShL6_cqTsLn8slz8A9GQN4K7fU-7-A4mCZK6TbimX7EP7tXO5pQUhTCGCcK-4pFBUAMeThOl4QomxW-HnxYqGgn0KGLYDWGUuvQ-hsrgFMINKFz7qI4uTiC3mEMpwPj9gW5G9QPcQ10lyPeZ46tYgyD-q48oTt5_TaxfkHdb_L6s1JJwo6pWIkT_BcdpABLrLd7VODJmT7fZ2Iy6rHhZJkngBhEKkw6qRZkKeB5OI9pBJJyohwOccBrmm1qKz2qKu8zTaQizJzk0bgUSd7V5DNW6jWpaaR5Fd7Cdz6REEYOIGVAbgWPXEGyZF4UdAo1LdCucMArSs2HrRzZsreVlJTrWo3qK5r6GR5w4Bh6iOi52y0m6y4v-X7Ph0g0BijQGoX1Z5it0_GVWWNYS0bCSjGjv97SLd9c-VdrvEvQ71BjmM4pDRNGLBn7yYBJ30ohOWyecaJWiwM89PriZcw9sc1fs8GmIkBr5insPMI6V4YgiGqgzkl-30HoLd0Spg0c_zU0VjomBEadUGZRATaCelWTY2oDfWWzWXHU-irExlXmg5oKLckGnuWQxyQqDbsZ82ENSTCj4oGiNIha6qsCLMaGEjecx2EBFF2PVcmqG_8Y2Mz_RCOYHdOO04aHo-gCSDkA0NuBRQGi2us8XB82Op7b_mx3nEbt_zLt_3ND9i3IwRdThHkVekPps0uIBCjc4QTlMAIPJIeATQBaK5U6o4GeP8q-dGtTzJkqF7fzrify1UwSSBp1nQafop8kR4SnqnBb4nOjgU9G2Fj6dKj7HJXxWUfv_FlrqmGk8n4bnm7YNfPO8jihizs8Mfc8OAIXdfFUsUTTBQWFhcPB8cCCK3cJ7oq4R2QNnMB5MBtPBVxJ0RE-AxNtmaxaVqdYh9jnOVOu4sBKC-c8JV0ICjh7_ghFMw_yqKYARXURxTGGkVJ3gnRoc2boTcxxbPzFsGmd0CWdUtxrfZ0rmdIS8NIFRrQWYEekZgMAEJyY4aQlOhACjLDaR45JJvmLDI5P6sMQ2YYkJS8yyr1n2NTGGiTFMjGFijNYYw1ZiDEca1JQIY8YjjEkblEr04nidwdUzHGRsM8iY1Yyjr385Bilf4LqX2ZAzG3ImHv2C41FxomMysJtXvZhkAXzEqpeJSb-UmNRsx5mwxCyVGRCY0MQMH6dfKsvvZ6Z0y28R9b6QYQITE5iYdRIzFpmAxICgBIKJWSwz1xc6T_Hwy6mm981SqZmPmLsLeaINWzri51KMA4pi4vqIgFWOh850HfkNu6wWcT09gNGmNR_SSKa6Yx2GtRmO5GMHCYg2Opfni8KHREotNEIvMAQoqKQMaU54kXE4QM_ttAJbQky1tJTCKavtKinliSo4clptjnOZiudycX24BqmUAqQ24ZiU_yKbcxK3NJYImhWWujunAQlFXgDdDg-T53ZIINhfMHZZKxH_pPgXxojiUHUnBWkXM5WC0PsAtovkjGX1JdDDIdMDv1AaQjalrzmsSsGGqLdNWU1OU02V5DSim_8bp1bIEWCBgGArQLdFW51FU2VrFFCYEJ70Qkm9Zs_UlD1KBTJhTQaCZXlFA_ghilwY9XEdOe9DHTCM6X1v_1NZWmEeiF_01TC9A0GCgxq0RwwX6Yp4CSqlSnLKtFkyGGX8iZDHFBFCNSdMAu8Q3DHDo8yzFBeQbVus_2Rjd57fqbaOdzH00BoxZEk38bmG_l7kl6A_Zhz2f92MH1lpsddGwBq69ZSknZJZB6hQCgNxa0kfDksgzZoQjuzHkhYfqYH6ytokFxR-A4UqsWhUpd8fDlP4ZkkF4U8Q0DSBTxS1obY2WfOcO00kqrDiF7dC83CYq7YgXJjqLyBh39Mi985j0d1YYSuyi_iikaoG1cKruArZw2GS1iz5smKFT5S7qbo2qZcVc9aSeVS1ZFnkWXtnC0f7DoWIp157Q2H41P5uq7O1y4uAKyN0US1hjQYmuQZqKLkW5u1aEOPJzyBK1ywcYQaTPFEJLVW26sDJc2i00NWoQOjOrSHkGljoGf2PbC4UkSMMaY0Vahm930zVZvQKGZd82S65nTeUZx1dpRQ_teuba2yV3S6yEDeS1QjvFMJX6R7qVyYaW647n10q89nZYD5YDJalee2oHG0lLL6INjxOT5CnzCeqyTCnZWIWADJXjkAhxEjEat0O-DcWyaJATpcnuO5aU__u-z_HJKI_lFrBmJ6Ks84QkSp16wwRVSoR52lY8k9pZP3EHIsEy1Pr8em8tbzNOo3cdbl2HW-jkIlQsjvO_Y8UnBWQT-OrE4d_rNSsEYeXSURkqjOmpMm5LfrpvPXGPbV2rXGvSiaCXQ1l_hahjymMoJR6-mzKfDRvLWWmau06ylTIRBitMeT_EoD7s0Pz6cy14pK4pnqduESlExF5s0InuUiYnk2RT2DapsBJrj-p2mbFTQu95eVFAN8d6_wMMlkDcvZY57GcdWKdUKlbJ9apUtWHw45uOKwrUGPYPG8Im-s2g0pBMzkkQW7JXl0JtmGWjr2TTFrxz8hSAruJ8jX48jKqulcwLk5JNRILAfWInTLxnugOkq7doSphBDdAj7DS3P22R4Ai2Ek5qtdSF9m8TAaCADO2AYhu3f2mACW9t-gOaKBgo-xBTBfqKkq8T8Nfnarlu7UQ0LodtD2RzKR5l05sN7sBXNNOndhVqtq3CJq7IKere32g5VEONUcpMxhxRHTRdERUOe9Xu3N_xIeCNhAkpUdPlhLD3VbOcH3EB1c416us9hLv0hMCyizhq9HI8ybjmvQ20otLR3wcJ3sVhkJvW6sf_qvGM0rys0vdryjN5NIneW4nF0p5bsdWXyrSeIwiV0M3yVxRRTfNuKQQ_X1__qaJ25mPXoxgq41bhlzjuRRWEoXtJ0PsoqgyMDWGL6xwAmPZOTbrgpUFlAJvG7a8FyJOIXd5mnGHp5kdz-ZByEZrXV_jHJlvf2_jeaPRuMPbLOzjGmaLjs7qbyanEOvL9TirY3qc-fRoncNm1xEcfog3et4rf4CI1VWzsaT6sGV-EuOq8kuHM8tfZUGf8lxr4qB0l0ebdHi0471FuA-zia5LGx-bcX-fthqtJivQ4dOOHGy0aemsTm16Erm-XK_mfSFx1LTN6ygvn5x-ytY4JTnplI1zfcyUbT73V-d1OBvgbyB9fg7nIJcJo567w5lpORz7bA4nDtLN-R0O59rf4aznq9kYnneNqHEN7XNeI-JCmTWi5-5q5lqu5nzL0SCOyfldDef6uAWixfS8rqZRP5-zq-FCmWnUc3c1Cy1XMz2bq_m0wp_O72o419OsRTd2puoa2u5yKn6h-Zpw2Ss0Q-_ZmnCbXT5DC17qWLDTMS854pYJPxxGaHb1TNeQ56dgfpqdbGPPxp5Pa8_2SGtIXnQMycfb19ghpGnIR4yrGdPTrGsaAzYGfGIDtnUMeDw94xmPAHy6v8QZD873NKuFxoyNGZ_YjB0tM16ebRzeUu_84zBjagJpY8CfpQGPdQx4Yp9tHKY8_Q49_zi852vWt4wZf5ZmPNEy445x-IiLxfw2VbzFEdTeE5udgLcZlY05f5bmrHVwb9p1cO94p7h2gOqf3LOPy9bMjY0Rf5ZGrHUYbny-EyqrNFidf9uYcz3NCRVjwsaET2zCWofMpl0mvDiaMRHopQmi2uvUk6NzNjNkY8qfnynXJgRc6D5VoPcGQbMZNr590KFnYyrGVC5gKrVpfCbyaw5y7kt7NLDtge0M7PHAngzs6cCeDex56QG6mZRDxd2nEXL5FcSWZ0xracrZ58WxrZGa-kQmEpn22y6-5RmBSoRZkp62aXee2EUmO-SraaMrt5ONyPCTy5_jWCd1aaDs0ci6hyAhDXw9EHhpACh0d1vpWbY8v5S3hX4aQL-BfM9elFJ8lA9QcK-CSSTq2YHElxLFjEWimHl34rbXWV4a640P1Xu9JEg3qttaZRSojuBUJ_UyAa8CzrcU9MwkvqfJPyBxrh4xP8sxQYl_TdCX9-JeAeVWLEena0VZF_sEbvM8f9v3vPghedsjIaiTLNEA0QCxqoschOQ1v-56-H7FACqyDS-6U4P-gHFgyfQNLnGZR5A4AAXmz7Czs9fDCgSBFgzHJ2DcA4WzE7DvC8LF8mSNqGpCDNqrEoKq0dUjnWYLRnXyUnKormqRbaD6BUK13l2qYBXZxTWA-RoSav0nCFJN57li5e9qi592EPcwishFBvGM8wUH8Yz_xQdxqRVHiSZb8KfjGA0MDQx7xZKTdnfoyHD8-x1M6h52Lk-wbckj4gaKE4MxwbvoMmDknC8JRs7_8mAsWnEUn9gKQp38-QaKBoo9_eK0fY49z1P230JiAWuDsW9t0Jp2OMf8GXNOBzhVLdFpZzGc5UVmMZzxBWcxnP3FZzFFI8464e4GrM5Dpwa2BrZPmXzP9Cbff4Pw1vq-dimydvK9YeUvsXK5BZF_EURyxhdEJGd_cUQWjTjqxLsGezoTbwNBA0Ht4HLeHlyKb_-RnTa4gzqbONJm-p5IZ3ncObo-fmf_04Lj9CSsewBycZIG9J7w2CdsxlkDzA7Q6jw9baBroOs_PchctHtXodi_8RnQGicWTcAdDAL5fFa9ixWN4PMfRthCd9pFpf2DFZdYVMo4X3BRKeN_8UUlqRVndbE6yG3xs_mrwQa_Br_w6X522e5nxQj9Lsb49l4rhhVcSEaiEwYce1YTp2F8i_RW7Gen4d0DnsvTtKA3QJ1TtuOsLrYVtDpbTQa6BrqN0O3jXbO0lC3udVw8AO_hJNLyr07xrDunuQRKD6wvgtID7wui9NCCi6O01I6zOth22LZ4WEHoGvAa8DaDt5eLtXU3-X1oocj67d331krDzRab_D5EUUqABlbnR-8tArRw6hyfbw-MTo_PvfeuwOJUbbjQBn8TWPU2-A1kDWQf407rrsXPGuHaeD-95EkfeXu-ATdPuWXf2h2a156dqXTteVxz8Vm65rws7ebhqLgWuxC3YqeNyhWd9u_4DlpvOo6kFXfo7yCiytdxAgmkyh10GOKrTYKik_TM4YrbtHuz_g1_ghkSCn3NW79IJWiQNJ8scVHpNg1XJI1PJe6kVVxRzc9wOxwO2yUd505-2ynhRJLQRyQOICDQP5WMs1YZhW381xbQb9tFFF_utqATr_m0gctI0iRO0AlFnGuZ5b8Bqm-WTEYNs1xKUoJok9yfSsJFq4RC2e-Ar4dSAjrtcC6J5p1KsLrBa3rswatBwJMOck1jUW4X-xQc-3FIGn-E1gnaRHi9zpWS52Rwuk8C_MDiERxa70pVFFUfIpICE05BmIAVomliwQ2kVpRGngVTi30RI2LFaXCHIpBYMQwCVgEkH1M4tN5B3yKIWqwp7J8AfUxBaMEA0aH1ln3KfwpBAEkKfMA-pcwbDK0fU1ZryvkEAdizXDNCYqEwhomP2N8E-5xsaL2JrC3w2H-sfYAQaMU8WQawfOTRNCR03w4EvJRXkCYRsCIcWXe8IRbwPFYGRFaME0oRxcnQ-iUBDA0R5SH7gYsVIYJYq7PmrIGHguzvrJkBXjFa9oEy4VOyF5Q3MCUeihkPBi6K0nDYhmxHH9lNnaUJswJlNbEN8F2eXhcm3REaK7rGmLYVzRHNyq6Ad7tJcBq1pRqaFOUDjGN3Bdc4gXoMMgKwbm_Qoigfgg34nUXi3UzmNUQ9GBG4Dxd7McqJuhhNCxqKGFK7uUyqFF0spGYxzEWsZd1MZipNDzY7lD0o3ouNoOliI5FQHDML6uYyVUh6dD9NYOSjaNOr-3OiHkpLWW8iet9LaYKmi42EmS0Ng16GmRF0MZC8io8SBhecaEiyqKPqYOWUnEy0YSxCZgg06PYEzriRtIupBKEVCoIVZlN8Xa6TZtoutlKDvYANjsjTZeo0UfZwSHfIh7iXQ9pT9GCRZV3rxWJP0cOufEgBCvo5I0HTw094CWJlEejlJ3Kifj6cxw19fXhG04W3UUECUrrFCYrWGghY1pJ1MbMLKmb7WVo2H_rd0B7V0_Xpq30OuH5Dek7UwUhSe5SGyvLeSF0O4aiOvAS2Jf4rBW4s9i_NyJWqi6STPKjyXVsnP-W-qKNfdKxfdKJfdKpfdKZfdK5fdKFfdKmVzPPQByMNB-fh-D5LXuZS-Kkta6TADoONjxgoO4oLl7uG0F-xORRMXL1ZSkKImyaBhpuhO0SZYbgpgUntWhuM7gDFaowRixXEEiOnsiCflzqswvO6rtXxboPxJoAxm8i5cYLXLEAu1TqTD_4RXi0rOdwTZRWn16Nr23acxWw2m86d6Ww8WUyc0WzxbQKDb_beTZWd5yNNV1CVvXnVOydpVa4Qi81Z4Qrj21LxsV0Rhm9aiJKSmlSXm1eX5UfNskvWLBwGpQy0uZfPickW71z-l5Rhki9mjKQVF5rkiVMeZBzmdbBQGYTa9MsK2PazBiaGL-2sTXM9BsxdWln88837EuEVz-cZsJng4Wtrm8C1KCPUeSibafJNvhf0K9eVIPMBBVf7RlwhX1Qwns9te-LY05kzXoyns8ni8MOfN_TVrzvI5jnW6t76rlIpb-414GX-9V_4Z8LCg5jyv_-0TqNsQvnCH5AB8l_-cQcS6wMZrD-Qb_wh4_7XIBs-yOv7X8HmLYPgC_Lyf0b_O4i_uf6_vQUNKTOeF_4wwF72uPTLb99_zX95__XN_gN5__UrtH7xJ7m-1_dv_BeM38s_MkbM2wAKD78xDq8-kCETG_n8A0m8b-K_HCTNTAtQHmIPZT0epnnDD-RQ8BUTYRgDNmGib1kvDlHEbIi-zgbmF3sJX756eHjhYy_lXAfvRd5UrpzDH4NK7-7y6l9mILguVMlU2-i61oCF58C7VdYNp6PFWBkE1gHybhNXSk1tFznnZ_bcnk6_eztaqA7vQMiig5UUSAjbXCo7QwR7iL9_pm5YTeRlp_JOclk0xRl72UqiRnJrFN12Z8re8lQgHXtPzW1dVJzChRs7bm1sPq4XI82F2ztpbe-yOspeuLVTrdYyL0DBJgHhhVs70zKyw1h-0bbWrUp_Hu5BZz9F7O3LacuLRYEQRW6W3l6Ojo54L5zXfZWxKR8oyVUb8u0HF8fyuYK26eQOs-nximnFXaUoKNbrDxsJNa8hOLqvIQidZ6s9x9oR68q4n8d1-4L5Onv3_IIgH66ARor-Q0GnWyS-YNnNmPewfE-rOfaOMaEkhknc_kDAKFd8CFlphDWerGDhN-ltFxVwPFRwtYIBC8uruJqIkKERSQVwAkgMnJ4dnPZRWGP3S9uSfLvHAOAZ-hMzrhgcNI4r8lFKeWxZSGPLQVnVF3gEaVJeMKqjTCN-ukJ7vSd_WiRWRrRp54hWaJpp-Kp63sIA-gsZ2Uxg89y7f2wCGxPYmMDG4KA2sJmX1pHqps0sumiIbJqXcCSanjHNsqghgRtE-M6Dj0hphb-1jod_Ag';	
				
			} else {
				
				#default settings code
				$options = '1VtPj-smEP8qKz2pt0qJs5vs7ju29_bQS08WtolD1zYp4M3mPfW7F2yDwWAPPK0q9erMj_nDzDAzEPSaHbLX7_x1n73-9HdPxdeK8GuD7nlDazp--cpfd9OP5sPz9EFR5T1r1in3O5v0Ripx2SDe28QXTOqLiKWWUuSXaoP6caYmXR4huV7-jN5JSTuA-mn6wgUp3-4x6z-HEdtanAIgyKohRqBx9ZcO3_Irw2fyEcFBETeowE1O3zFjpMIRG6JAgrT4ihmhMTuIOtTcpS48L-kmB_2l7LmgbV5yvkF88Ijzpo7Q2aJvq5x2zT0NxFsIFJDsI1GTtkqj5-06_YtL_hePcVdcCiJjqMVdn4v7dd41vaktrtGqmw-wwbFi3NzmBYCy3QyiXYVYHK_sEIZdKSeKtyHUWQGdBWazqDrblrShLEdlibutWHTJLxhV9mLTj7uAl1n0ucph3EP9GoiWAaX08aj3_g7N1HlBWUiwRz9JWiCBP8SWVEcfEtbk5GfuEUPFJSBVtkZe0A9ceeRnPw1Z5HlRewi8th8FaZqCIgah3F0_Uyq21TgF6MPm_WVNtAkUNvDRZ3WmncjteNsImWwNk3PyDUfE9Qhs5KFCewFxO6yCIHaObr0gDRH3JN1sDMDMpB_HKGPAbqTV7GkDBunnsGxI98ZjaiBFLIho8Bb10aeGDHAIQrq6wZAejzZSRjhNBKBKKm9FlA94CQIgNo7J0E3GebTJRmqIgeNsXd8Wm77iaqF9BdrK7HEdBcjn8HPSD7AlijbJebksGM8Rq18pExeqJJdZbjOv-QhGbzGllsUCiSR6geoI-sG_5dK4puwO92UD-fbKe5t09KJYOUgnD4l3FNMNjYIw1C3qovWdHRBQqaej5opqXNWyz5CIu0zwW8fpo4cB9vbJAwz-HxFqpDuTjsjNipPsFIIBwj2HMJB8jtkawsWacGjFCgMm1QqRjJ5DMIjX7Gc6O8d2CTMCDivL_TUoLmxlHdXJ3AkKZZyiRaQBqbW_4I9rQ0oiYjTIlphtBZ6W5GCG0AwEvQrcLXTQjvfHb78_7Hc-mwnE-yJW9wkRmBto26Om8YNfMNxVsryItrEBRMj2ssRETTWelqiFqbVvPPva3EhVY8HXgqv0RWsoveb4o8TsKr0Sd_Xm4Ghno95VDT39hiSP7-R1Z_XvgvU6UP-xoCpt5DL_1zEl90yct1S2SVuF497GcClbgZjff5tInCh-Zs7YK7MPhfyMkeiZ7OZIOy5qlSJ6ncNx54etZOtnOICzg_1R-WfLqVXUXCWv8Bn1jfDmK9b-2HorXLLeVnqTQSgd0GmPtEwMvxNsHWJZFoIFdDalAGXkm6S1Kg3DGjFBStkkQAfEsxEGDTUs9_np5FRQIWjrB-YMjT2NZgQX98afdUlOF78FGvaC4ZK20i6V3I4WyxqxCmQKVHOd8wkOGNhbyc1cJhv8SfuHVjnUA2o4fWjIm-Uiz2uLnUkjVJ8p89TGrMe0th4-hDv6wqFeas9yVLWky3GHCmNIMPsY3tMauL3KfrwiPGWRedw8LfJRNn0V0_lNAEatrbeGTJ1gpOgFDcwDJ6Q7RzPDdVJKM7SBknaMNBn6QpYP3Cg43uxMNF9svWednQUXKv0zk73hu984fQlYfBKFIaGOMunBjJTeNJQLZLWsVtgM2JLJ6o8RBOmRxeoxh_ht89YjRd99SF--nM2uD3wmHK6I8Ee6W2lsgPWy9YV5LewqqMykmmPsgNvBDmwTkZNT-mn3MFdyq2Bt1jhw5oJH0DvmoMgLYIdrFAdciDueIKpBiL53WFgJKjHNjH6Z_Vej5Kg-awXJRiGqXV8Nf-FIUlRbF5UvFt3tIgM6Im-OawLUzhXCJ0TyfrGHzsRu26pxuUdT8aav_-fWTzDry7IUgiz69NkWNcMOhjkW_43lEwxkbjhJ3dHz-RMPu5NbZn_iZqIKPHQs0sWN1Xq_LGnneWiBz5TFVPQOyr1f3Xg6UI1XBSCXwwIAMbB1MfPCJF0MCmBlTisJcqZ_EDdTsyyBqboNk7lk3QbUD-gWxS2kWwxDW0oz6wJVOwVACUacp4gpRpxRECtLPD2ABBkdfUwCGz2ISmGjMRAbCzIN_UAuTx4kwQ3MHC7FDQwowWj6njjFaBqT4G0Vkc23LPnvSd42oxJYWS8bEljNKCg77BwnFbLBVkMzkNdLEAbpZTuRmvvAfB6XiIRTqUWkSzqVBkCCDsP4NkmHEZHAYhwfprAYEdC2H9z9m0eH4KmQrSETwlS3ZilhqjEJaUdPPZLSjgGlHQnKEKlHwoBJ0EeN_eRJTJP0MSDIJazj3h4tgg6xC-NS9mkAxZx1pwAIYmS5eoHKt5rRvqvgrgSFp6q7wIRR1Uclw-3mi0MrY8rexG6f_KWdqyUJ2MPLatIsnvQQT_oYT_oUT3qMJz3Fkz7Hk77EXtSpPdhF-Nj02G_l8rAKPXi83of7J-hZj7noYWrayCFy85oN46romSpx4zpcxjnwxl4HiLgRoZRVE8zttecnOsMzFyx7iG0W2jo1pXWDr00PyGQOJQtwZfRMZMEQp4zar77AMcqYt2KoxAWlb4Bo-yX5-KQ33xy22Tffk5nH2h54nm8eiskG6o3lJOaJ-kQLvUYwT8FpSZw7n5XpyqM9XXEGQSdXMy_zjo_g4emSemgJDzUv6m8TnzHUnGusNu7PLkb5Vg1Vctk1EfsN8FKUfwE';
				
			}
			
			# Decode options and unserialize			
			$options = it_decode( $options, $serialize = true );
			if(is_array($options))
				foreach( $options as $key => $value )
					if( is_array( $value ) )
						foreach( $value as $key2 => $value2 )
							$options[$key][$key2] = str_replace( '%site_url%', THEME_IMAGES . '/activation', $value2 );
		
		break;
		case "widgets":
		
			if($state=='demo') {
				
				#demo widgets code
				$options = 'lZXRkqMgFET_Zt-siqgZh3wMRfDGoQrBBUxmNpV_X5wRRCUb9yUvOelLN80NxXmN7wbn7_jX70HZ060nXFJm-RXIjTctWPPzxYniA74_HIomtKctZIY3cKY6MCW-c3w4GYwOE8YtEdSCsYRqy5kAkx0nnOP8BfkWSPSCrANZvCDfJ3L08jZhQqnee8kEXGwwhPYYQgtDeT6TVvWczcA4s07N1Lz9-M-hxe4Uy2h6NWEdbekfLiHrqQRhtqPzYhY04Bqh5MYoWhglFuTC6dGX6qdIGVNi6GSWrxv1hEM7uWInV645nwVTUjqH2brweciijrJQjFNBmBqkXV6sl7soZUFv3eapaK0G2XDZBhfPlVBCKbp1y-MqPpcpEjJoc9c7hMqEUBW1kLsOjkuiWWj5x3FTarM9jqH31Uwx1XWgmd9HhLler4pYoAQ-NlK7l9CQXqtmYDau7_c6OSZ-1WvuPi9cjEbRcqmkDiXoF2g3Q9JrhJfjoeoEroGBtOKLXDnc0kerxlEoebQV-4gKcO43aVYhzTJArrfa2dPctc5Mia5fdT3TrVZDn-BQSrWD7gw6QRcLVYeP5hPcmFr4Jxq5i9JDtwEfEeV6xgZjVUcm92R-b4X37-_Zwqf9xwrzzS92_-lU0Yn8m6ZaU3e9LginFUXw-As';
				
			} else {
				
				#default widgets code
				$options = 'jdLRboMgFAbgt9kdiaJ2Hb7DXsGc0VNKosCAtlmN717M0MBmUq-4-L_k_JwcYGXDRsfKD_b2fdW-vZtOKuBe3rC7y5NA736DFljBxilQGqkBgcTJE36BXc2BjZIVrWPHqByC5RdCo5CsDGFZx9QiR-WJ0c67xNDZvOeG62EIb8qqmRWRzWNC6zSv26QuB49CW5mJJohDBAN6WKMpKdBrbZaPkh7P_u9GjlvQSnH5J5tlFgh4SIXEgMJ-WfHn3HepE7cf_t1fB0XKHYbuMNUOU6dmqcy1Usg9ya8iI2etPdrNxtuEvibVa5LVrdZzsPDT3dA6qVVyMdMT';
				
			}
			
			# Decode options and unserialize
			$options = it_decode( $options, $serialize = true );
			foreach( $options as $key => $value )
				if( is_array( $value ) )
					foreach( $value as $key2 => $value2 )
						$options[$key][$key2] = str_replace( '%site_url%', THEME_IMAGES . '/activation', $value2 );
		
		break;
		case 'widget':
		
			if($state=='demo') {
				
				#demo individual widget code
				$options = '3VpLb-M2EP4rvLSndqGHFdvKqQ2w7aEtFrtpexRoaWwTkUitSMXrLvLfSz2oFy3TjJNtUCAIEnI4nPnmG3JICof-IvzKQzcIv_9cMnF7IMkORISLeE8egTeNtzh0aymvlYqyMhWkkW1FSOjePvHQW4wVbTZ5tGVFmfFoJI5DL_xKQq_5g4dqfkFECq0MD2_a1ve1hq65syPHBVDRTND3tp1O2_J0meG-bjgXWGh2X4zECYWPBA7zQLjzQDhtq51L64kFeRSzAqJtQYAm83b4tYcKxgx_USM69UEFs4p00xklsMXSEi12OBaSSX10lCspoQ_R0MtN6Fzq2PK0YynbETp2S4NuHpYMsg0U87As5sOj7Pm9UdE7OwQxm3a2fUHfoiBtRL8lpP4MpAXEMsPSY9TMaQTX90_rOewZjxiVFpp1eLqOXcHK_FmhUZj9UmvQMasis5v06YFRFtWS3zIuC0_DIgPO8Q54xIkA2QwRZYLEwI3AupMgx1jAjsn0tV_n3UkSkWoVTlN2iMpnbBqBpiyVpvFqH5KepQP71irkN_Mhd1U8fquVoF8BJxXx-givWoEWgKOegT-lKbqbwiPprSbEu56uii6Bnve0zCYu9PTyu5Yu7WWmDBjSEyvFR1YKzXzFCbUrkNA3IKPmvmc5-iTGvr0OKDd2oKzOg-JeDAruQVmcB0Ux-SPZ7QX6gx1eGZGlHSI3L4UI9IgEBpoo--9KLliG_h6vKy8Ky2ow5B7vuB04i1egy81byaHroHmNTFq-rUxa2SESvNSCO8ik1XlEtC34TSyy3ksBkfRArP9LIK7LlOVLwRHbHc8cveohsubJZXGX6DXuuSVJafpQDUU_y3PQW8TZ0wpmLiUfjtcQ77JidakhzUHW6ox2MPujo8Q8aFrtZrmHd1sr38taeaK_WZXXQ4nJmt2WeOeNVIC43nM302utvCwqq7mo8BN3I-t5-is9n8ajrb22W_wHaacAirwRlfV-39C_GPe7037XMQm4JgHPJBCMwqwLeK5BwF0ZBPzAYIO_NmhYmNxcmDQEJiR9z6TBs7-38nXGs5jgNIpZSQW3W_RV8x2jVDJfVulij_7k2qWUOBAhoBi5ozi9xTFsGHsYdSrFuzztDvNNj1JZ5Ue5gZMLQk6onE2eu63hcfVrAEGGh_9-NVg-ZzU4k66uIV1dQ7q6pnR1TenqmtLVNaWrY0pXx5SuztXp6lydrs7V6eq8VLp290gDPrKcxNzqArJj9X0BNCF0h-6HSnrCyk1KJk6m71BrXddwX-6LthM7s-irNIudeXnK8UgAPZGKZ14tgsFJOXiLFWmgPyklhONNCtEesBhTyp9IpOShq9NbkcVEpHrvmcrcTGQKOCU1nayQcExElhORmGUZUE3M6UKTQQ4FYYkGKE5Tu3par9xES-4xQXwDQVaT1Hjbp0M7KP-3nLqQI-6YIxkIbP8wMHn9aF6jfmytuv7VotWXM_4cZZPze8F7HU749WmAeCvBoXpXt59oAqWAL3OvtjNb0GJ8n_uJJLDBhXbtMVBcXU8o_-72ED8gIpCs-n5AYk84kj8YyTDkKQhIjyhuFPNGMYoLqLiFNkckS0WEaYIw52RHZZtg8v8jqm450KYkaQJFrYlRGQrEaN9bDT1gKt6h-2pOsYcMqpldx_munZH8U9EUEYrgEYpjpWhDKK4Kv3p4jGllMH1AbPtOO-htSTosjC9-K50E_sBYzcgilgmMj7L4TSKKH61CpIj5vjZJAndyFcml-kgcc9BCV90c6QvV51KCMh7QLVTU7g7Fd-edzgsif4_QvOyzhq6e6dxGtS67e7TlOctYUsanTlYXnCA-jAf3vJHbxQaKCx4jqhJs_gpU2c0KmQODiHcrvswhrZaqhXVR4LHGpX31NrwtADq8RqVxXR9KmaQr68jlz9L-PObdJwPNHnQmBme-_Om-I_rYakN_1dqQbVACO5af4VJVAtdb5vM86oqmqhr-WC-Pr-zLmWWq-wCgsPsgq9tI-oGDba4mHNlGkOXiaMmpp38B';
				
			} else {
				
				#default individual widget code
				$options = 'xZLNDoIwEITfxpsJFH9weRhTy0aaUIrtoibEdxe0oEUOmGA8tZmZZL_ZlkMMtYVwDYtTpSm5yPSItOdGZPKM9ikmHBjUElhzidp4lyZJObqMhcCpvdDFhK4KcqqEoHFi56RGl6m-FG_mraFhzt6rKif5ZOoj4SOy9YEFJzxqI8eQV7Mg91CZRPPYj-D5zKVCv5RC4p91wsl1pk1lzJ9qUGBBS6GVas6RfbIv9rlxQlGpA5p-8HrqRnajbKW2_wYLBmD2xRNA3SYiP2Gx_TS_f87hJyK8kod2uwM';
				
			}
		
		break;
		case 'mods':
		
			if($state=='demo') {
				
				#demo theme_mods code
				$options = 'S7QysqrOtDKwTgLiYitDCyu1wtL8Euu8xLL43NS80vic_OTEksz8vGKIuHWilbFVNVChEVRhcWoySFoXpBiqJNPKDGiUJVRBbmImmqyhsTWSAaUlmTmZJZUYSmprAQ';
				
			} else {
				
				#default theme_mods code
				$options = 'S7QytKrOtDKwTgLiWgA';
				
			}
			
			# Decode options and unserialize
			$options = it_decode( $options, $serialize = true );
			foreach( $options as $key => $value )
				if( is_array( $value ) )
					foreach( $value as $key2 => $value2 )
						$options[$key][$key2] = str_replace( '%site_url%', THEME_IMAGES . '/activation', $value2 );
		
		break;
		case "internal":
		
			$options = array();
			
			if( defined( 'FRAMEWORK_VERSION' ) )
				$options['framework_version'] = FRAMEWORK_VERSION;
				
			if( defined( 'DOCUMENTATION_URL' ) )
				$options['documentation_url'] = DOCUMENTATION_URL;
				
			if( defined( 'SUPPORT_URL' ) )
				$options['support_url'] = SUPPORT_URL;
		
		break;	
	}
	
	return $options;
}

# turn variables into proper class types
function it_prep_data( $data ) {							
	#create itCriteria objects based on entered details	
	$criteria = $data[IT_SETTINGS]['review_criteria'];		
	if ( isset($criteria['keys']) && $criteria['keys'] != '#' ) {
		$criteria_keys = explode(',',$criteria['keys']);
		foreach ($criteria_keys as $id) {
			$val = ( ( $id != '#' ) && ( isset( $data[$id] ) ) ) ? $data[$id] : '';
			if ( $id != '#') {
				$criteria_name = ( !empty( $criteria[$id]['name'] ) ) ? $criteria[$id]['name'] : '';
				$criteria_weight = ( !empty( $criteria[$id]['weight'] ) ) ? $criteria[$id]['weight'] : '';
				if(is_array($data)) array_push($data[IT_SETTINGS]['review_criteria'][$id],new itCriteria($criteria_name, $criteria_weight));						
			}
		}
	}
	#create itDetail objects based on entered details	
	$details = $data[IT_SETTINGS]['review_details'];		
	if ( isset($details['keys']) && $details['keys'] != '#' ) {
		$details_keys = explode(',',$details['keys']);
		foreach ($details_keys as $id) {
			$val = ( ( $id != '#' ) && ( isset( $data[$id] ) ) ) ? $data[$id] : '';
			if ( $id != '#') {
				$details_name = ( !empty( $details[$id]['name'] ) ) ? $details[$id]['name'] : '';
				if(is_array($data)) array_push($data[IT_SETTINGS]['review_details'][$id],new itDetail($details_name));						
			}
		}
	}	
	#create itAward objects based on entered awards
	$awards = $data[IT_SETTINGS]['review_awards'];			
	if ( isset($awards['keys']) && $awards['keys'] != '#' ) {
		$awards_keys = explode(',',$awards['keys']);
		foreach ($awards_keys as $id) {
			$val = ( ( $id != '#') && ( isset( $data[$id] ) ) ) ? $data[$id] : '';
			if ( $id != '#') {
				$award_name = ( !empty( $awards[$id]['name'] ) ) ? $awards[$id]['name'] : '';
				$award_slug = ( !empty( $awards[$id]['slug'] ) ) ? $awards[$id]['slug'] : '';
				$award_icon = ( !empty( $awards[$id]['icon'] ) ) ? $awards[$id]['icon'] : '';
				$award_iconhd = ( !empty( $awards[$id]['iconhd'] ) ) ? $awards[$id]['iconhd'] : '';
				$award_iconwhite = ( !empty( $awards[$id]['iconwhite'] ) ) ? $awards[$id]['iconwhite'] : '';
				$award_iconhdwhite = ( !empty( $awards[$id]['iconhdwhite'] ) ) ? $awards[$id]['iconhdwhite'] : '';
				$award_badge = ( !empty( $awards[$id]['badge'] ) ) ? $awards[$id]['badge'] : false;
				if(is_array($data)) array_push($data[IT_SETTINGS]['review_awards'][$id],new itAward($award_name, $award_slug, $award_icon, $award_iconhd, $award_iconwhite, $award_iconhdwhite, $award_badge));						
			}
		}
	}			
	#die (var_export($data));
	return $data;
}

/**
 * 
 */
function it_icons() {
	$icons = array(		
		'emo-happy' => __( 'Smily Happy', IT_TEXTDOMAIN ),
		'picture' => __( 'Picture', IT_TEXTDOMAIN ),
		'emo-wink2' => __( 'Smily Wink', IT_TEXTDOMAIN ),
		'emo-unhappy' => __( 'Smily Unhappy', IT_TEXTDOMAIN ),
		'emo-sleep' => __( 'Smily Sleep', IT_TEXTDOMAIN ),
		'emo-thumbsup' => __( 'Smily Thumbs Up', IT_TEXTDOMAIN ),
		'emo-devil' => __( 'Smily Devil', IT_TEXTDOMAIN ),
		'emo-surprised' => __( 'Smily Surprised', IT_TEXTDOMAIN ),
		'emo-tongue' => __( 'Smily Tongue', IT_TEXTDOMAIN ),
		'emo-coffee' => __( 'Smily Coffee', IT_TEXTDOMAIN ),
		'emo-sunglasses' => __( 'Smily Sunglasses', IT_TEXTDOMAIN ),
		'emo-displeased' => __( 'Smily Displeased', IT_TEXTDOMAIN ),
		'emo-beer' => __( 'Smily Beer', IT_TEXTDOMAIN ),
		'emo-grin' => __( 'Smily Grin', IT_TEXTDOMAIN ),
		'emo-angry' => __( 'Smily Angry', IT_TEXTDOMAIN ),
		'emo-saint' => __( 'Smily Saint', IT_TEXTDOMAIN ),
		'emo-cry' => __( 'Smily Cry', IT_TEXTDOMAIN ),
		'emo-shoot' => __( 'Smily Shoot', IT_TEXTDOMAIN ),
		'emo-squint' => __( 'Smily Squint', IT_TEXTDOMAIN ),
		'emo-laugh' => __( 'Smily Laugh', IT_TEXTDOMAIN ),
		'spin2' => __( 'Spinner', IT_TEXTDOMAIN ),
		'firefox' => __( 'Firefox', IT_TEXTDOMAIN ),
		'chrome' => __( 'Chrome', IT_TEXTDOMAIN ),
		'opera' => __( 'Opera', IT_TEXTDOMAIN ),
		'ie' => __( 'IE', IT_TEXTDOMAIN ),
		'star-full' => __( 'Star Full', IT_TEXTDOMAIN ),
		'star' => __( 'Star', IT_TEXTDOMAIN ),
		'star-half-empty' => __( 'Star Half Empty', IT_TEXTDOMAIN ),
		'star-half' => __( 'Star Half', IT_TEXTDOMAIN ),
		'check' => __( 'Check', IT_TEXTDOMAIN ),
		'plus' => __( 'Plus', IT_TEXTDOMAIN ),
		'minus' => __( 'Minus', IT_TEXTDOMAIN ),
		'password' => __( 'Password', IT_TEXTDOMAIN ),
		'pin' => __( 'Pin', IT_TEXTDOMAIN ),
		'doc' => __( 'Document', IT_TEXTDOMAIN ),
		'docs' => __( 'Documents', IT_TEXTDOMAIN ),
		'folder-open' => __( 'Folder Open', IT_TEXTDOMAIN ),
		'cog-alt' => __( 'Gears', IT_TEXTDOMAIN ),
		'wrench' => __( 'Wrench', IT_TEXTDOMAIN ),
		'basket' => __( 'Basket', IT_TEXTDOMAIN ),
		'right-hand' => __( 'Right Hand', IT_TEXTDOMAIN ),
		'left-hand' => __( 'Left Hand', IT_TEXTDOMAIN ),
		'signal' => __( 'Signal', IT_TEXTDOMAIN ),
		'laptop' => __( 'Laptop', IT_TEXTDOMAIN ),
		'tablet' => __( 'Tablet', IT_TEXTDOMAIN ),
		'globe' => __( 'Globe', IT_TEXTDOMAIN ),
		'scissors' => __( 'Scissors', IT_TEXTDOMAIN ),
		'fire' => __( 'Fire', IT_TEXTDOMAIN ),
		'credit-card' => __( 'Credit Card', IT_TEXTDOMAIN ),
		'beaker' => __( 'Beaker', IT_TEXTDOMAIN ),
		'truck' => __( 'Truck', IT_TEXTDOMAIN ),
		'dollar' => __( 'Dollar', IT_TEXTDOMAIN ),
		'sort' => __( 'Sort', IT_TEXTDOMAIN ),
		'coffee' => __( 'Coffee', IT_TEXTDOMAIN ),
		'food' => __( 'Food', IT_TEXTDOMAIN ),
		'search' => __( 'Search', IT_TEXTDOMAIN ),
		'email' => __( 'Email', IT_TEXTDOMAIN ),
		'liked' => __( 'Liked', IT_TEXTDOMAIN ),
		'username' => __( 'Username', IT_TEXTDOMAIN ),
		'users' => __( 'Users', IT_TEXTDOMAIN ),
		'register' => __( 'Register', IT_TEXTDOMAIN ),
		'camera' => __( 'Camera', IT_TEXTDOMAIN ),
		'grid' => __( 'Grid', IT_TEXTDOMAIN ),
		'list' => __( 'List', IT_TEXTDOMAIN ),
		'x' => __( 'X', IT_TEXTDOMAIN ),
		'plus-squared' => __( 'Plus Squared', IT_TEXTDOMAIN ),
		'minus-squared' => __( 'Minus Squared', IT_TEXTDOMAIN ),
		'help-circled' => __( 'Help Circled', IT_TEXTDOMAIN ),
		'info-circled' => __( 'Info Circled', IT_TEXTDOMAIN ),
		'home' => __( 'Home', IT_TEXTDOMAIN ),
		'link' => __( 'Link', IT_TEXTDOMAIN ),
		'attach' => __( 'Attach', IT_TEXTDOMAIN ),
		'tag' => __( 'Tag', IT_TEXTDOMAIN ),
		'bookmark' => __( 'Bookmark', IT_TEXTDOMAIN ),
		'flag' => __( 'Flag', IT_TEXTDOMAIN ),
		'thumbs-up' => __( 'Thumbs Up', IT_TEXTDOMAIN ),
		'thumbs-down' => __( 'Thumbs Down', IT_TEXTDOMAIN ),
		'forward' => __( 'Forward', IT_TEXTDOMAIN ),
		'pencil' => __( 'Pencil', IT_TEXTDOMAIN ),
		'signoff' => __( 'Signoff', IT_TEXTDOMAIN ),
		'commented' => __( 'Commented', IT_TEXTDOMAIN ),
		'comments' => __( 'Comments', IT_TEXTDOMAIN ),
		'attention' => __( 'Attention', IT_TEXTDOMAIN ),
		'alert' => __( 'Alert', IT_TEXTDOMAIN ),
		'book' => __( 'Book', IT_TEXTDOMAIN ),
		'category' => __( 'Category', IT_TEXTDOMAIN ),
		'rss' => __( 'RSS', IT_TEXTDOMAIN ),
		'cog' => __( 'Gear', IT_TEXTDOMAIN ),
		'emo-wink' => __( 'Smily Wink 2', IT_TEXTDOMAIN ),
		'login' => __( 'Login', IT_TEXTDOMAIN ),
		'logout' => __( 'Logout', IT_TEXTDOMAIN ),
		'recent' => __( 'Recent', IT_TEXTDOMAIN ),
		'window' => __( 'Window', IT_TEXTDOMAIN ),
		'down-open' => __( 'Arrow Down Open', IT_TEXTDOMAIN ),
		'left-open' => __( 'Arrow Left Open', IT_TEXTDOMAIN ),
		'right-open' => __( 'Arrow Right Open', IT_TEXTDOMAIN ),
		'up-open' => __( 'Arrow Up Open', IT_TEXTDOMAIN ),
		'down' => __( 'Arrow Down', IT_TEXTDOMAIN ),
		'left' => __( 'Arrow Left', IT_TEXTDOMAIN ),
		'right' => __( 'Arrow Right', IT_TEXTDOMAIN ),
		'up' => __( 'Arrow Up', IT_TEXTDOMAIN ),
		'down-bold' => __( 'Arrow Down Bold', IT_TEXTDOMAIN ),
		'up-bold' => __( 'Arrow Up Bold', IT_TEXTDOMAIN ),
		'right-thin' => __( 'Arrow Right Thin', IT_TEXTDOMAIN ),
		'random' => __( 'Random', IT_TEXTDOMAIN ),
		'loop' => __( 'Loop', IT_TEXTDOMAIN ),
		'play' => __( 'Play', IT_TEXTDOMAIN ),
		'stop' => __( 'Stop', IT_TEXTDOMAIN ),
		'pause' => __( 'Pause', IT_TEXTDOMAIN ),
		'last' => __( 'Last', IT_TEXTDOMAIN ),
		'first' => __( 'First', IT_TEXTDOMAIN ),
		'next' => __( 'Next', IT_TEXTDOMAIN ),
		'previous' => __( 'Previous', IT_TEXTDOMAIN ),
		'target' => __( 'Target', IT_TEXTDOMAIN ),
		'style' => __( 'Style', IT_TEXTDOMAIN ),
		'sidebar' => __( 'Sidebar', IT_TEXTDOMAIN ),
		'wifi' => __( 'Wifi', IT_TEXTDOMAIN ),
		'awarded' => __( 'Awarded', IT_TEXTDOMAIN ),
		'battery' => __( 'Battery', IT_TEXTDOMAIN ),
		'monitor' => __( 'Monitor', IT_TEXTDOMAIN ),
		'mobile' => __( 'Mobile', IT_TEXTDOMAIN ),
		'cloud' => __( 'Cloud', IT_TEXTDOMAIN ),
		'moon' => __( 'Moon', IT_TEXTDOMAIN ),
		'leaf' => __( 'Leaf', IT_TEXTDOMAIN ),
		'suitcase' => __( 'Suitcase', IT_TEXTDOMAIN ),
		'brush' => __( 'Brush', IT_TEXTDOMAIN ),
		'magnet' => __( 'Magnet', IT_TEXTDOMAIN ),
		'chart-pie' => __( 'Pie Chart', IT_TEXTDOMAIN ),
		'trending' => __( 'Trending', IT_TEXTDOMAIN ),
		'reviewed' => __( 'Reviewed', IT_TEXTDOMAIN ),
		'water' => __( 'Water', IT_TEXTDOMAIN ),
		'floppy' => __( 'Disk', IT_TEXTDOMAIN ),
		'key' => __( 'Key', IT_TEXTDOMAIN ),
		'gauge' => __( 'Gauge', IT_TEXTDOMAIN ),
		'cc' => __( 'License', IT_TEXTDOMAIN ),
		'flickr' => __( 'Flickr', IT_TEXTDOMAIN ),
		'vimeo' => __( 'Vimeo', IT_TEXTDOMAIN ),
		'twitter' => __( 'Twitter', IT_TEXTDOMAIN ),
		'googleplus' => __( 'Google Plus', IT_TEXTDOMAIN ),
		'pinterest' => __( 'Pinterest', IT_TEXTDOMAIN ),
		'tumblr' => __( 'Tumblr', IT_TEXTDOMAIN ),
		'linkedin' => __( 'LinkedIn', IT_TEXTDOMAIN ),
		'stumbleupon' => __( 'StumbleUpon', IT_TEXTDOMAIN ),
		'lastfm' => __( 'LastFM', IT_TEXTDOMAIN ),
		'spotify' => __( 'Spotify', IT_TEXTDOMAIN ),
		'instagram' => __( 'Instagram', IT_TEXTDOMAIN ),
		'dropbox' => __( 'Dropbox', IT_TEXTDOMAIN ),
		'skype' => __( 'Skype', IT_TEXTDOMAIN ),
		'paypal' => __( 'Paypal', IT_TEXTDOMAIN ),
		'picasa' => __( 'Picasa', IT_TEXTDOMAIN ),
		'footer' => __( 'Footer', IT_TEXTDOMAIN ),
		'pages' => __( 'Pages', IT_TEXTDOMAIN ),
		'settings' => __( 'Settings', IT_TEXTDOMAIN ),
		'builder' => __( 'Builder', IT_TEXTDOMAIN ),
		'viewed' => __( 'Viewed', IT_TEXTDOMAIN ),
		'zoom-in' => __( 'Zoom In', IT_TEXTDOMAIN ),
		'zoom-out' => __( 'Zoom Out', IT_TEXTDOMAIN ),
		'lock' => __( 'Lock', IT_TEXTDOMAIN ),
		'lock-open' => __( 'Lock Open', IT_TEXTDOMAIN ),
		'down-fat' => __( 'Arrow Down Fat', IT_TEXTDOMAIN ),
		'left-fat' => __( 'Arrow Left Fat', IT_TEXTDOMAIN ),
		'right-fat' => __( 'Arrow Right Fat', IT_TEXTDOMAIN ),
		'up-fat' => __( 'Arrow Up Fat', IT_TEXTDOMAIN ),
		'facebook' => __( 'Facebook', IT_TEXTDOMAIN ),
		'wikipedia' => __( 'Wikipedia', IT_TEXTDOMAIN ),
		'html5' => __( 'HTML5', IT_TEXTDOMAIN ),
		'reddit' => __( 'Reddit', IT_TEXTDOMAIN ),
		'appstore' => __( 'Appstore', IT_TEXTDOMAIN ),
		'youtube' => __( 'Youtube', IT_TEXTDOMAIN ),
		'windows' => __( 'Windows', IT_TEXTDOMAIN ),
		'yahoo' => __( 'Yahoo', IT_TEXTDOMAIN ),
		'gmail' => __( 'Gmail', IT_TEXTDOMAIN ),
		'wordpress' => __( 'WordPress', IT_TEXTDOMAIN ),
		'acrobat' => __( 'Acrobat', IT_TEXTDOMAIN ),
		'quote-circled' => __( 'Quote Circled', IT_TEXTDOMAIN ),
		'video' => __( 'Video', IT_TEXTDOMAIN ),
		'tools' => __( 'Tools', IT_TEXTDOMAIN ),
		'flame' => __( 'Flame', IT_TEXTDOMAIN),
		'backward' => __( 'Backward', IT_TEXTDOMAIN ),
		'magazine' => __( 'Magazine', IT_TEXTDOMAIN )	
		);
	asort($icons);
	return $icons;
}

/**
 * 
 */
function it_fonts() {
	$fonts = array(
		"Arial, Helvetica, sans-serif" => "Arial",
		"Verdana, Geneva, Tahoma, sans-serif" => "Verdana",
		"'Lucida Sans', 'Lucida Grande', 'Lucida Sans Unicode', sans-serif" => "Lucida",
		"Georgia, Times, 'Times New Roman', serif" => "Georgia",
		"'Times New Roman', Times, Georgia, serif" => "Times New Roman",
		"'Trebuchet MS', Tahoma, Arial, sans-serif" => "Trebuchet",
		"'Courier New', Courier, monospace" => "Courier New",
		"Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif" => "Impact",
		"Tahoma, Geneva, Verdana, sans-serif" => "Tahoma",	
		"spacer" => "                ",
		"ABeeZee, sans-serif" => "ABeeZee",
		"Abel, sans-serif" => "Abel",
		"'Abril Fatface', sans-serif" => "Abril Fatface",
		"Aclonica, sans-serif" => "Aclonica",
		"Acme, sans-serif" => "Acme",
		"Actor, sans-serif" => "Actor",
		"Adamina, sans-serif" => "Adamina",
		"'Advent Pro', sans-serif" => "Advent Pro",
		"'Aguafina Script', sans-serif" => "Aguafina Script",
		"Akronim, sans-serif" => "Akronim",
		"Aladin, sans-serif" => "Aladin",
		"Aldrich, sans-serif" => "Aldrich",
		"Alef, sans-serif" => "Alef",
		"Alegreya, sans-serif" => "Alegreya",
		"'Alegreya SC', sans-serif" => "Alegreya SC",
		"'Alegreya Sans', sans-serif" => "Alegreya Sans",
		"'Alegreya Sans SC', sans-serif" => "Alegreya Sans SC",
		"'Alex Brush', sans-serif" => "Alex Brush",
		"'Alfa Slab One', sans-serif" => "Alfa Slab One",
		"Alice, sans-serif" => "Alice",
		"Alike, sans-serif" => "Alike",
		"'Alike Angular', sans-serif" => "Alike Angular",
		"Allan, sans-serif" => "Allan",		
		"Allerta, sans-serif" => "Allerta",
		"'Allerta Stencil', sans-serif" => "Allerta Stencil",
		"Allura, sans-serif" => "Allura",
		"Almendra, sans-serif" => "Almendra",
		"'Almendra Display', sans-serif" => "Almendra Display",
		"'Almendra SC', sans-serif" => "Almendra SC",
		"Amarante, sans-serif" => "Amarante",
		"Amaranth, sans-serif" => "Amaranth",
		"'Amatic SC', sans-serif" => "Amatic SC",
		"Amethysta, sans-serif" => "Amethysta",
		"Anaheim, sans-serif" => "Anaheim",
		"Andada, sans-serif" => "Andada",
		"Andika, sans-serif" => "Andika",
		"Angkor, sans-serif" => "Angkor",
		"'Annie Use Your Telescope', sans-serif" => "Annie Use Your Telescope",
		"'Anonymous Pro', sans-serif" => "Anonymous Pro",
		"Antic, sans-serif" => "Antic",
		"'Antic Didone', sans-serif" => "Antic Didone",
		"'Antic Slab', sans-serif" => "Antic Slab",
		"Anton, sans-serif" => "Anton",
		"Arapey, sans-serif" => "Arapey",
		"Arbutus, sans-serif" => "Arbutus",
		"'Arbutus Slab', sans-serif" => "Arbutus Slab",
		"'Architects Daughter', sans-serif" => "Architects Daughter",
		"'Archivo Black', sans-serif" => "Archivo Black",
		"'Archivo Narrow', sans-serif" => "Archivo Narrow",
		"Arimo, sans-serif" => "Arimo",
		"Arizonia, sans-serif" => "Arizonia",
		"Armata, sans-serif" => "Armata",
		"Artifika, sans-serif" => "Artifika",
		"Arvo, sans-serif" => "Arvo",
		"Asap, sans-serif" => "Asap",
		"Asset, sans-serif" => "Asset",
		"Astloch, sans-serif" => "Astloch",
		"Asul, sans-serif" => "Asul",
		"'Atomic Age', sans-serif" => "Atomic Age",
		"Aubrey, sans-serif" => "Aubrey",
		"Audiowide, sans-serif" => "Audiowide",
		"'Autour One', sans-serif" => "Autour One",
		"Average, sans-serif" => "Average",
		"'Average Sans', sans-serif" => "Average Sans",
		"'Averia Gruesa Libre', sans-serif" => "Averia Gruesa Libre",
		"'Averia Libre', sans-serif" => "Averia Libre",
		"'Averia Sans Libre', sans-serif" => "Averia Sans Libre",
		"'Averia Serif Libre', sans-serif" => "Averia Serif Libre",
		"'Bad Script', sans-serif" => "Bad Script",
		"Balthazar, sans-serif" => "Balthazar",
		"Bangers, sans-serif" => "Bangers",
		"Basic, sans-serif" => "Basic",
		"Battambang, sans-serif" => "Battambang",
		"Baumans, sans-serif" => "Baumans",
		"Bayon, sans-serif" => "Bayon",
		"Belgrano, sans-serif" => "Belgrano",
		"Belleza, sans-serif" => "Belleza",
		"BenchNine, sans-serif" => "BenchNine",
		"Bentham, sans-serif" => "Bentham",
		"'Berkshire Swash', sans-serif" => "Berkshire Swash",
		"Bevan, sans-serif" => "Bevan",
		"'Bigelow Rules', sans-serif" => "Bigelow Rules",
		"'Bigshot One', sans-serif" => "Bigshot One",
		"Bilbo, sans-serif" => "Bilbo",
		"'Bilbo Swash Caps', sans-serif" => "Bilbo Swash Caps",
		"Bitter, sans-serif" => "Bitter",
		"'Black Ops One', sans-serif" => "Black Ops One",
		"Bokor, sans-serif" => "Bokor",
		"Bonbon, sans-serif" => "Bonbon",
		"Boogaloo, sans-serif" => "Boogaloo",
		"'Bowlby One', sans-serif" => "Bowlby One",
		"'Bowlby One SC', sans-serif" => "Bowlby One SC",
		"Brawler, sans-serif" => "Brawler",
		"'Bree Serif', sans-serif" => "Bree Serif",
		"'Bubblegum Sans', sans-serif" => "Bubblegum Sans",
		"'Bubbler One', sans-serif" => "Bubbler One",
		"Buda, sans-serif" => "Buda",
		"Buenard, sans-serif" => "Buenard",
		"Butcherman, sans-serif" => "Butcherman",
		"'Butterfly Kids', sans-serif" => "Butterfly Kids",
		"Cabin, sans-serif" => "Cabin",
		"'Cabin Condensed', sans-serif" => "Cabin Condensed",
		"'Cabin Sketch', sans-serif" => "Cabin Sketch",
		"'Caesar Dressing', sans-serif" => "Caesar Dressing",
		"Cagliostro, sans-serif" => "Cagliostro",
		"Calligraffitti, sans-serif" => "Calligraffitti",
		"Cambo, sans-serif" => "Cambo",
		"Candal, sans-serif" => "Candal",
		"Cantarell, sans-serif" => "Cantarell",
		"'Cantata One', sans-serif" => "Cantata One",
		"'Cantora One', sans-serif" => "Cantora One",
		"Capriola, sans-serif" => "Capriola",
		"Cardo, sans-serif" => "Cardo",
		"Carme, sans-serif" => "Carme",
		"'Carrois Gothic', sans-serif" => "Carrois Gothic",
		"'Carrois Gothic SC', sans-serif" => "Carrois Gothic SC",
		"'Carter One', sans-serif" => "Carter One",
		"Caudex, sans-serif" => "Caudex",
		"'Cedarville Cursive', sans-serif" => "Cedarville Cursive",
		"'Ceviche One', sans-serif" => "Ceviche One",
		"'Changa One', sans-serif" => "Changa One",
		"Chango, sans-serif" => "Chango",
		"'Chau Philomene One', sans-serif" => "Chau Philomene One",
		"'Chela One', sans-serif" => "Chela One",
		"'Chelsea Market', sans-serif" => "Chelsea Market",
		"Chenla, sans-serif" => "Chenla",
		"'Cherry Cream Soda', sans-serif" => "Cherry Cream Soda",
		"'Cherry Swash', sans-serif" => "Cherry Swash",
		"Chewy, sans-serif" => "Chewy",
		"Chicle, sans-serif" => "Chicle",
		"Chivo, sans-serif" => "Chivo",
		"Cinzel, sans-serif" => "Cinzel",
		"'Cinzel Decorative', sans-serif" => "Cinzel Decorative",
		"'Clicker Script', sans-serif" => "Clicker Script",
		"Coda, sans-serif" => "Coda",
		"'Coda Caption', sans-serif" => "Coda Caption",
		"Codystar, sans-serif" => "Codystar",
		"Combo, sans-serif" => "Combo",
		"Comfortaa, sans-serif" => "Comfortaa",
		"'Coming Soon', sans-serif" => "Coming Soon",
		"'Concert One', sans-serif" => "Concert One",
		"Condiment, sans-serif" => "Condiment",
		"Content, sans-serif" => "Content",
		"'Contrail One', sans-serif" => "Contrail One",
		"Convergence, sans-serif" => "Convergence",
		"Cookie, sans-serif" => "Cookie",
		"Copse, sans-serif" => "Copse",
		"Corben, sans-serif" => "Corben",
		"Courgette, sans-serif" => "Courgette",
		"Cousine, sans-serif" => "Cousine",
		"Coustard, sans-serif" => "Coustard",
		"'Covered By Your Grace', sans-serif" => "Covered By Your Grace",
		"'Crafty Girls', sans-serif" => "Crafty Girls",
		"Creepster, sans-serif" => "Creepster",
		"'Crete Round', sans-serif" => "Crete Round",
		"'Crimson Text', sans-serif" => "Crimson Text",
		"'Croissant One', sans-serif" => "Croissant One",
		"Crushed, sans-serif" => "Crushed",
		"Cuprum, sans-serif" => "Cuprum",
		"Cutive, sans-serif" => "Cutive",
		"'Cutive Mono', sans-serif" => "Cutive Mono",
		"Damion, sans-serif" => "Damion",
		"'Dancing Script', sans-serif" => "Dancing Script",
		"Dangrek, sans-serif" => "Dangrek",
		"'Dawning of a New Day', sans-serif" => "Dawning of a New Day",
		"'Days One', sans-serif" => "Days One",
		"Delius, sans-serif" => "Delius",
		"'Delius Swash Caps', sans-serif" => "Delius Swash Caps",
		"'Delius Unicase', sans-serif" => "Delius Unicase",
		"'Della Respira', sans-serif" => "Della Respira",
		"'Denk One', sans-serif" => "Denk One",
		"Devonshire, sans-serif" => "Devonshire",
		"'Didact Gothic', sans-serif" => "Didact Gothic",
		"Diplomata, sans-serif" => "Diplomata",
		"'Diplomata SC', sans-serif" => "Diplomata SC",
		"Domine, sans-serif" => "Domine",
		"'Donegal One', sans-serif" => "Donegal One",
		"'Doppio One', sans-serif" => "Doppio One",
		"Dorsa, sans-serif" => "Dorsa",
		"Dosis, sans-serif" => "Dosis",
		"'Dr Sugiyama', sans-serif" => "Dr Sugiyama",
		"'Droid Sans', sans-serif" => "Droid Sans",
		"'Droid Sans Mono', sans-serif" => "Droid Sans Mono",
		"'Droid Serif', sans-serif" => "Droid Serif",
		"'Duru Sans', sans-serif" => "Duru Sans",
		"Dynalight, sans-serif" => "Dynalight",
		"'EB Garamond', sans-serif" => "EB Garamond",
		"'Eagle Lake', sans-serif" => "Eagle Lake",
		"Eater, sans-serif" => "Eater",
		"Economica, sans-serif" => "Economica",
		"'Ek Mukta', sans-serif" => "Ek Mukta",
		"Electrolize, sans-serif" => "Electrolize",
		"Elsie, sans-serif" => "Elsie",
		"'Elsie Swash Caps', sans-serif" => "Elsie Swash Caps",
		"'Emblema One', sans-serif" => "Emblema One",
		"'Emilys Candy', sans-serif" => "Emilys Candy",
		"Engagement, sans-serif" => "Engagement",
		"Englebert, sans-serif" => "Englebert",
		"Enriqueta, sans-serif" => "Enriqueta",
		"'Erica One', sans-serif" => "Erica One",
		"Esteban, sans-serif" => "Esteban",
		"'Euphoria Script', sans-serif" => "Euphoria Script",
		"Ewert, sans-serif" => "Ewert",
		"Exo, sans-serif" => "Exo",
		"'Exo 2', sans-serif" => "Exo 2",
		"'Expletus Sans', sans-serif" => "Expletus Sans",
		"'Fanwood Text', sans-serif" => "Fanwood Text",
		"Fascinate, sans-serif" => "Fascinate",
		"'Fascinate Inline', sans-serif" => "Fascinate Inline",
		"'Faster One', sans-serif" => "Faster One",
		"Fasthand, sans-serif" => "Fasthand",
		"'Fauna One', sans-serif" => "Fauna One",
		"Federant, sans-serif" => "Federant",
		"Federo, sans-serif" => "Federo",
		"Felipa, sans-serif" => "Felipa",
		"Fenix, sans-serif" => "Fenix",
		"'Finger Paint', sans-serif" => "Finger Paint",
		"'Fira Mono', sans-serif" => "Fira Mono",
		"'Fira Sans', sans-serif" => "Fira Sans",
		"'Fjalla One', sans-serif" => "Fjalla One",
		"'Fjord One', sans-serif" => "Fjord One",
		"Flamenco, sans-serif" => "Flamenco",
		"Flavors, sans-serif" => "Flavors",
		"Fondamento, sans-serif" => "Fondamento",
		"'Fontdiner Swanky', sans-serif" => "Fontdiner Swanky",
		"Forum, sans-serif" => "Forum",
		"'Francois One', sans-serif" => "Francois One",
		"'Freckle Face', sans-serif" => "Freckle Face",
		"'Fredericka the Great', sans-serif" => "Fredericka the Great",
		"'Fredoka One', sans-serif" => "Fredoka One",
		"Freehand, sans-serif" => "Freehand",
		"Fresca, sans-serif" => "Fresca",
		"Frijole, sans-serif" => "Frijole",
		"Fruktur, sans-serif" => "Fruktur",
		"'Fugaz One', sans-serif" => "Fugaz One",
		"'GFS Didot', sans-serif" => "GFS Didot",
		"'GFS Neohellenic', sans-serif" => "GFS Neohellenic",
		"Gabriela, sans-serif" => "Gabriela",
		"Gafata, sans-serif" => "Gafata",
		"Galdeano, sans-serif" => "Galdeano",
		"Galindo, sans-serif" => "Galindo",
		"'Gentium Basic', sans-serif" => "Gentium Basic",
		"'Gentium Book Basic', sans-serif" => "Gentium Book Basic",
		"Geo, sans-serif" => "Geo",
		"Geostar, sans-serif" => "Geostar",
		"'Geostar Fill', sans-serif" => "Geostar Fill",
		"'Germania One', sans-serif" => "Germania One",
		"'Gilda Display', sans-serif" => "Gilda Display",
		"'Give You Glory', sans-serif" => "Give You Glory",
		"'Glass Antiqua', sans-serif" => "Glass Antiqua",
		"Glegoo, sans-serif" => "Glegoo",
		"'Gloria Hallelujah', sans-serif" => "Gloria Hallelujah",
		"'Goblin One', sans-serif" => "Goblin One",
		"'Gochi Hand', sans-serif" => "Gochi Hand",
		"Gorditas, sans-serif" => "Gorditas",
		"'Goudy Bookletter 1911', sans-serif" => "Goudy Bookletter 1911",
		"Graduate, sans-serif" => "Graduate",
		"'Grand Hotel', sans-serif" => "Grand Hotel",
		"'Gravitas One', sans-serif" => "Gravitas One",
		"'Great Vibes', sans-serif" => "Great Vibes",
		"Griffy, sans-serif" => "Griffy",
		"Gruppo, sans-serif" => "Gruppo",
		"Gudea, sans-serif" => "Gudea",
		"Habibi, sans-serif" => "Habibi",
		"Halant, sans-serif" => "Halant",
		"'Hammersmith One', sans-serif" => "Hammersmith One",
		"Hanalei, sans-serif" => "Hanalei",
		"'Hanalei Fill', sans-serif" => "Hanalei Fill",
		"Handlee, sans-serif" => "Handlee",
		"Hanuman, sans-serif" => "Hanuman",
		"'Happy Monkey', sans-serif" => "Happy Monkey",
		"'Headland One', sans-serif" => "Headland One",
		"'Henny Penny', sans-serif" => "Henny Penny",
		"'Herr Von Muellerhoff', sans-serif" => "Herr Von Muellerhoff",
		"Hind, sans-serif" => "Hind",
		"'Holtwood One SC', sans-serif" => "Holtwood One SC",
		"'Homemade Apple', sans-serif" => "Homemade Apple",
		"Homenaje, sans-serif" => "Homenaje",
		"'IM Fell DW Pica', sans-serif" => "IM Fell DW Pica",
		"'IM Fell DW Pica SC', sans-serif" => "IM Fell DW Pica SC",
		"'IM Fell Double Pica', sans-serif" => "IM Fell Double Pica",
		"'IM Fell Double Pica SC', sans-serif" => "IM Fell Double Pica SC",
		"'IM Fell English', sans-serif" => "IM Fell English",
		"'IM Fell English SC', sans-serif" => "IM Fell English SC",
		"'IM Fell French Canon', sans-serif" => "IM Fell French Canon",
		"'IM Fell French Canon SC', sans-serif" => "IM Fell French Canon SC",
		"'IM Fell Great Primer', sans-serif" => "IM Fell Great Primer",
		"'IM Fell Great Primer SC', sans-serif" => "IM Fell Great Primer SC",
		"Iceberg, sans-serif" => "Iceberg",
		"Iceland, sans-serif" => "Iceland",
		"Imprima, sans-serif" => "Imprima",
		"Inconsolata, sans-serif" => "Inconsolata",
		"Inder, sans-serif" => "Inder",
		"'Indie Flower', sans-serif" => "Indie Flower",
		"Inika, sans-serif" => "Inika",
		"'Irish Grover', sans-serif" => "Irish Grover",
		"'Istok Web', sans-serif" => "Istok Web",
		"Italiana, sans-serif" => "Italiana",
		"Italianno, sans-serif" => "Italianno",
		"'Jacques Francois', sans-serif" => "Jacques Francois",
		"'Jacques Francois Shadow', sans-serif" => "Jacques Francois Shadow",
		"'Jim Nightshade', sans-serif" => "Jim Nightshade",
		"'Jockey One', sans-serif" => "Jockey One",
		"'Jolly Lodger', sans-serif" => "Jolly Lodger",
		"'Josefin Sans', sans-serif" => "Josefin Sans",
		"'Josefin Slab', sans-serif" => "Josefin Slab",
		"'Joti One', sans-serif" => "Joti One",
		"Judson, sans-serif" => "Judson",
		"Julee, sans-serif" => "Julee",
		"'Julius Sans One', sans-serif" => "Julius Sans One",
		"Junge, sans-serif" => "Junge",
		"Jura, sans-serif" => "Jura",
		"'Just Another Hand', sans-serif" => "Just Another Hand",
		"'Just Me Again Down Here', sans-serif" => "Just Me Again Down Here",
		"Kalam, sans-serif" => "Kalam",
		"Kameron, sans-serif" => "Kameron",
		"Karla, sans-serif" => "Karla",
		"Karma, sans-serif" => "Karma",
		"'Kaushan Script', sans-serif" => "Kaushan Script",
		"Kavoon, sans-serif" => "Kavoon",
		"'Keania One', sans-serif" => "Keania One",
		"'Kelly Slab', sans-serif" => "Kelly Slab",
		"Kenia, sans-serif" => "Kenia",
		"Khand, sans-serif" => "Khand",
		"Khmer, sans-serif" => "Khmer",
		"'Kite One', sans-serif" => "Kite One",
		"Knewave, sans-serif" => "Knewave",
		"'Kotta One', sans-serif" => "Kotta One",
		"Koulen, sans-serif" => "Koulen",
		"Kranky, sans-serif" => "Kranky",
		"Kreon, sans-serif" => "Kreon",
		"Kristi, sans-serif" => "Kristi",
		"'Krona One', sans-serif" => "Krona One",
		"'La Belle Aurore', sans-serif" => "La Belle Aurore",
		"Laila, sans-serif" => "Laila",
		"Lancelot, sans-serif" => "Lancelot",
		"Lato, sans-serif" => "Lato",
		"'League Script', sans-serif" => "League Script",
		"'Leckerli One', sans-serif" => "Leckerli One",
		"Ledger, sans-serif" => "Ledger",
		"Lekton, sans-serif" => "Lekton",
		"Lemon, sans-serif" => "Lemon",
		"'Libre Baskerville', sans-serif" => "Libre Baskerville",
		"'Life Savers', sans-serif" => "Life Savers",
		"'Lilita One', sans-serif" => "Lilita One",
		"'Lily Script One', sans-serif" => "Lily Script One",
		"Limelight, sans-serif" => "Limelight",
		"'Linden Hill', sans-serif" => "Linden Hill",
		"Lobster, sans-serif" => "Lobster",
		"'Lobster Two', sans-serif" => "Lobster Two",
		"'Londrina Outline', sans-serif" => "Londrina Outline",
		"'Londrina Shadow', sans-serif" => "Londrina Shadow",
		"'Londrina Sketch', sans-serif" => "Londrina Sketch",
		"'Londrina Solid', sans-serif" => "Londrina Solid",
		"Lora, sans-serif" => "Lora",
		"'Love Ya Like A Sister', sans-serif" => "Love Ya Like A Sister",
		"'Loved by the King', sans-serif" => "Loved by the King",
		"'Lovers Quarrel', sans-serif" => "Lovers Quarrel",
		"'Luckiest Guy', sans-serif" => "Luckiest Guy",
		"Lusitana, sans-serif" => "Lusitana",
		"Lustria, sans-serif" => "Lustria",
		"Macondo, sans-serif" => "Macondo",
		"'Macondo Swash Caps', sans-serif" => "Macondo Swash Caps",
		"Magra, sans-serif" => "Magra",
		"'Maiden Orange', sans-serif" => "Maiden Orange",
		"Mako, sans-serif" => "Mako",
		"Marcellus, sans-serif" => "Marcellus",
		"'Marcellus SC', sans-serif" => "Marcellus SC",
		"'Marck Script', sans-serif" => "Marck Script",
		"Margarine, sans-serif" => "Margarine",
		"'Marko One', sans-serif" => "Marko One",
		"Marmelad, sans-serif" => "Marmelad",
		"Marvel, sans-serif" => "Marvel",
		"Mate, sans-serif" => "Mate",
		"'Mate SC', sans-serif" => "Mate SC",
		"'Maven Pro', sans-serif" => "Maven Pro",
		"McLaren, sans-serif" => "McLaren",
		"Meddon, sans-serif" => "Meddon",
		"MedievalSharp, sans-serif" => "MedievalSharp",
		"'Medula One', sans-serif" => "Medula One",
		"Megrim, sans-serif" => "Megrim",
		"'Meie Script', sans-serif" => "Meie Script",
		"Merienda, sans-serif" => "Merienda",
		"'Merienda One', sans-serif" => "Merienda One",
		"Merriweather, sans-serif" => "Merriweather",
		"'Merriweather Sans', sans-serif" => "Merriweather Sans",
		"Metal, sans-serif" => "Metal",
		"'Metal Mania', sans-serif" => "Metal Mania",
		"Metamorphous, sans-serif" => "Metamorphous",
		"Metrophobic, sans-serif" => "Metrophobic",
		"Michroma, sans-serif" => "Michroma",
		"Milonga, sans-serif" => "Milonga",
		"Miltonian, sans-serif" => "Miltonian",
		"'Miltonian Tattoo', sans-serif" => "Miltonian Tattoo",
		"Miniver, sans-serif" => "Miniver",
		"'Miss Fajardose', sans-serif" => "Miss Fajardose",
		"'Modern Antiqua', sans-serif" => "Modern Antiqua",
		"Molengo, sans-serif" => "Molengo",
		"Molle, sans-serif" => "Molle",
		"Monda, sans-serif" => "Monda",
		"Monofett, sans-serif" => "Monofett",
		"Monoton, sans-serif" => "Monoton",
		"'Monsieur La Doulaise', sans-serif" => "Monsieur La Doulaise",
		"Montaga, sans-serif" => "Montaga",
		"Montez, sans-serif" => "Montez",
		"Montserrat, sans-serif" => "Montserrat",
		"'Montserrat Alternates', sans-serif" => "Montserrat Alternates",
		"'Montserrat Subrayada', sans-serif" => "Montserrat Subrayada",
		"Moul, sans-serif" => "Moul",
		"Moulpali, sans-serif" => "Moulpali",
		"'Mountains of Christmas', sans-serif" => "Mountains of Christmas",
		"'Mouse Memoirs', sans-serif" => "Mouse Memoirs",
		"'Mr Bedfort', sans-serif" => "Mr Bedfort",
		"'Mr Dafoe', sans-serif" => "Mr Dafoe",
		"'Mr De Haviland', sans-serif" => "Mr De Haviland",
		"'Mrs Saint Delafield', sans-serif" => "Mrs Saint Delafield",
		"'Mrs Sheppards', sans-serif" => "Mrs Sheppards",
		"Muli, sans-serif" => "Muli",
		"'Mystery Quest', sans-serif" => "Mystery Quest",
		"Neucha, sans-serif" => "Neucha",
		"Neuton, sans-serif" => "Neuton",
		"'New Rocker', sans-serif" => "New Rocker",
		"'News Cycle', sans-serif" => "News Cycle",
		"Niconne, sans-serif" => "Niconne",
		"'Nixie One', sans-serif" => "Nixie One",
		"Nobile, sans-serif" => "Nobile",
		"Nokora, sans-serif" => "Nokora",
		"Norican, sans-serif" => "Norican",
		"Nosifer, sans-serif" => "Nosifer",
		"'Nothing You Could Do', sans-serif" => "Nothing You Could Do",
		"'Noticia Text', sans-serif" => "Noticia Text",
		"'Nova Cut', sans-serif" => "Nova Cut",
		"'Nova Flat', sans-serif" => "Nova Flat",
		"'Nova Mono', sans-serif" => "Nova Mono",
		"'Nova Oval', sans-serif" => "Nova Oval",
		"'Nova Round', sans-serif" => "Nova Round",
		"'Nova Script', sans-serif" => "Nova Script",
		"'Nova Slim', sans-serif" => "Nova Slim",
		"'Nova Square', sans-serif" => "Nova Square",
		"Numans, sans-serif" => "Numans",
		"Nunito, sans-serif" => "Nunito",
		"'Odor Mean Chey', sans-serif" => "Odor Mean Chey",
		"Offside, sans-serif" => "Offside",
		"'Old Standard TT', sans-serif" => "Old Standard TT",
		"Oldenburg, sans-serif" => "Oldenburg",
		"'Oleo Script', sans-serif" => "Oleo Script",
		"'Oleo Script Swash Caps', sans-serif" => "Oleo Script Swash Caps",
		"'Open Sans', sans-serif" => "Open Sans",
		"'Open Sans Condensed', sans-serif" => "Open Sans Condensed",
		"Oranienbaum, sans-serif" => "Oranienbaum",
		"Orbitron, sans-serif" => "Orbitron",
		"Oregano, sans-serif" => "Oregano",
		"Orienta, sans-serif" => "Orienta",
		"'Original Surfer', sans-serif" => "Original Surfer",
		"Oswald, sans-serif" => "Oswald",
		"'Over the Rainbow', sans-serif" => "Over the Rainbow",
		"Overlock, sans-serif" => "Overlock",
		"'Overlock SC', sans-serif" => "Overlock SC",
		"Ovo, sans-serif" => "Ovo",
		"Oxygen, sans-serif" => "Oxygen",
		"'Oxygen Mono', sans-serif" => "Oxygen Mono",
		"'PT Mono', sans-serif" => "PT Mono",
		"'PT Sans', sans-serif" => "PT Sans",
		"'PT Sans Caption', sans-serif" => "PT Sans Caption",
		"'PT Sans Narrow', sans-serif" => "PT Sans Narrow",
		"'PT Serif', sans-serif" => "PT Serif",
		"'PT Serif Caption', sans-serif" => "PT Serif Caption",
		"Pacifico, sans-serif" => "Pacifico",
		"Paprika, sans-serif" => "Paprika",
		"Parisienne, sans-serif" => "Parisienne",
		"'Passero One', sans-serif" => "Passero One",
		"'Passion One', sans-serif" => "Passion One",
		"'Pathway Gothic One', sans-serif" => "Pathway Gothic One",
		"'Patrick Hand', sans-serif" => "Patrick Hand",
		"'Patrick Hand SC', sans-serif" => "Patrick Hand SC",
		"'Patua One', sans-serif" => "Patua One",
		"'Paytone One', sans-serif" => "Paytone One",
		"Peralta, sans-serif" => "Peralta",
		"'Permanent Marker', sans-serif" => "Permanent Marker",
		"'Petit Formal Script', sans-serif" => "Petit Formal Script",
		"Petrona, sans-serif" => "Petrona",
		"Philosopher, sans-serif" => "Philosopher",
		"Piedra, sans-serif" => "Piedra",
		"'Pinyon Script', sans-serif" => "Pinyon Script",
		"'Pirata One', sans-serif" => "Pirata One",
		"Plaster, sans-serif" => "Plaster",
		"Play, sans-serif" => "Play",
		"Playball, sans-serif" => "Playball",
		"'Playfair Display', sans-serif" => "Playfair Display",
		"'Playfair Display SC', sans-serif" => "Playfair Display SC",
		"Podkova, sans-serif" => "Podkova",
		"'Poiret One', sans-serif" => "Poiret One",
		"'Poller One', sans-serif" => "Poller One",
		"Poly, sans-serif" => "Poly",
		"Pompiere, sans-serif" => "Pompiere",
		"'Pontano Sans', sans-serif" => "Pontano Sans",
		"'Port Lligat Sans', sans-serif" => "Port Lligat Sans",
		"'Port Lligat Slab', sans-serif" => "Port Lligat Slab",
		"Prata, sans-serif" => "Prata",
		"Preahvihear, sans-serif" => "Preahvihear",
		"'Press Start 2P', sans-serif" => "Press Start 2P",
		"'Princess Sofia', sans-serif" => "Princess Sofia",
		"Prociono, sans-serif" => "Prociono",
		"'Prosto One', sans-serif" => "Prosto One",
		"Puritan, sans-serif" => "Puritan",
		"'Purple Purse', sans-serif" => "Purple Purse",
		"Quando, sans-serif" => "Quando",
		"Quantico, sans-serif" => "Quantico",
		"Quattrocento, sans-serif" => "Quattrocento",
		"'Quattrocento Sans', sans-serif" => "Quattrocento Sans",
		"Questrial, sans-serif" => "Questrial",
		"Quicksand, sans-serif" => "Quicksand",
		"Quintessential, sans-serif" => "Quintessential",
		"Qwigley, sans-serif" => "Qwigley",
		"'Racing Sans One', sans-serif" => "Racing Sans One",
		"Radley, sans-serif" => "Radley",
		"Rajdhani, sans-serif" => "Rajdhani",
		"Raleway, sans-serif" => "Raleway",
		"'Raleway Dots', sans-serif" => "Raleway Dots",
		"Rambla, sans-serif" => "Rambla",
		"'Rammetto One', sans-serif" => "Rammetto One",
		"Ranchers, sans-serif" => "Ranchers",
		"Rancho, sans-serif" => "Rancho",
		"Rationale, sans-serif" => "Rationale",
		"Redressed, sans-serif" => "Redressed",
		"'Reenie Beanie', sans-serif" => "Reenie Beanie",
		"Revalia, sans-serif" => "Revalia",
		"Ribeye, sans-serif" => "Ribeye",
		"'Ribeye Marrow', sans-serif" => "Ribeye Marrow",
		"Righteous, sans-serif" => "Righteous",
		"Risque, sans-serif" => "Risque",
		"Roboto, sans-serif" => "Roboto",
		"'Roboto Condensed', sans-serif" => "Roboto Condensed",
		"'Roboto Slab', sans-serif" => "Roboto Slab",
		"Rochester, sans-serif" => "Rochester",
		"'Rock Salt', sans-serif" => "Rock Salt",
		"Rokkitt, sans-serif" => "Rokkitt",
		"Romanesco, sans-serif" => "Romanesco",
		"'Ropa Sans', sans-serif" => "Ropa Sans",
		"Rosario, sans-serif" => "Rosario",
		"Rosarivo, sans-serif" => "Rosarivo",
		"'Rouge Script', sans-serif" => "Rouge Script",
		"'Rozha One', sans-serif" => "Rozha One",
		"'Rubik Mono One', sans-serif" => "Rubik Mono One",
		"'Rubik One', sans-serif" => "Rubik One",
		"Ruda, sans-serif" => "Ruda",
		"Rufina, sans-serif" => "Rufina",
		"'Ruge Boogie', sans-serif" => "Ruge Boogie",
		"Ruluko, sans-serif" => "Ruluko",
		"'Rum Raisin', sans-serif" => "Rum Raisin",
		"'Ruslan Display', sans-serif" => "Ruslan Display",
		"'Russo One', sans-serif" => "Russo One",
		"Ruthie, sans-serif" => "Ruthie",
		"Rye, sans-serif" => "Rye",
		"Sacramento, sans-serif" => "Sacramento",
		"Sail, sans-serif" => "Sail",
		"Salsa, sans-serif" => "Salsa",
		"Sanchez, sans-serif" => "Sanchez",
		"Sancreek, sans-serif" => "Sancreek",
		"'Sansita One', sans-serif" => "Sansita One",
		"Sarina, sans-serif" => "Sarina",
		"Sarpanch, sans-serif" => "Sarpanch",
		"Satisfy, sans-serif" => "Satisfy",
		"Scada, sans-serif" => "Scada",
		"Schoolbell, sans-serif" => "Schoolbell",
		"'Seaweed Script', sans-serif" => "Seaweed Script",
		"Sevillana, sans-serif" => "Sevillana",
		"'Seymour One', sans-serif" => "Seymour One",
		"'Shadows Into Light', sans-serif" => "Shadows Into Light",
		"'Shadows Into Light Two', sans-serif" => "Shadows Into Light Two",
		"Shanti, sans-serif" => "Shanti",
		"Share, sans-serif" => "Share",
		"'Share Tech', sans-serif" => "Share Tech",
		"'Share Tech Mono', sans-serif" => "Share Tech Mono",
		"Shojumaru, sans-serif" => "Shojumaru",
		"'Short Stack', sans-serif" => "Short Stack",
		"Siemreap, sans-serif" => "Siemreap",
		"'Sigmar One', sans-serif" => "Sigmar One",
		"Signika, sans-serif" => "Signika",
		"'Signika Negative', sans-serif" => "Signika Negative",
		"Simonetta, sans-serif" => "Simonetta",
		"Sintony, sans-serif" => "Sintony",
		"'Sirin Stencil', sans-serif" => "Sirin Stencil",
		"'Six Caps', sans-serif" => "Six Caps",
		"Skranji, sans-serif" => "Skranji",
		"'Slabo 13px', sans-serif" => "Slabo 13px",
		"'Slabo 27px', sans-serif" => "Slabo 27px",
		"Slackey, sans-serif" => "Slackey",
		"Smokum, sans-serif" => "Smokum",
		"Smythe, sans-serif" => "Smythe",
		"Sniglet, sans-serif" => "Sniglet",
		"Snippet, sans-serif" => "Snippet",
		"'Snowburst One', sans-serif" => "Snowburst One",
		"'Sofadi One', sans-serif" => "Sofadi One",
		"Sofia, sans-serif" => "Sofia",
		"'Sonsie One', sans-serif" => "Sonsie One",
		"'Sorts Mill Goudy', sans-serif" => "Sorts Mill Goudy",
		"'Source Code Pro', sans-serif" => "Source Code Pro",
		"'Source Serif Pro', sans-serif" => "Source Serif Pro",
		"'Source Sans Pro', sans-serif" => "Source Sans Pro",
		"'Special Elite', sans-serif" => "Special Elite",
		"'Spicy Rice', sans-serif" => "Spicy Rice",
		"Spinnaker, sans-serif" => "Spinnaker",
		"Spirax, sans-serif" => "Spirax",
		"'Squada One', sans-serif" => "Squada One",
		"Stalemate, sans-serif" => "Stalemate",
		"'Stalinist One', sans-serif" => "Stalinist One",
		"'Stardos Stencil', sans-serif" => "Stardos Stencil",
		"'Stint Ultra Condensed', sans-serif" => "Stint Ultra Condensed",
		"'Stint Ultra Expanded', sans-serif" => "Stint Ultra Expanded",
		"Stoke, sans-serif" => "Stoke",
		"Strait, sans-serif" => "Strait",
		"'Sue Ellen Francisco', sans-serif" => "Sue Ellen Francisco",
		"Sunshiney, sans-serif" => "Sunshiney",
		"'Supermercado One', sans-serif" => "Supermercado One",
		"Suwannaphum, sans-serif" => "Suwannaphum",
		"'Swanky and Moo Moo', sans-serif" => "Swanky and Moo Moo",
		"Syncopate, sans-serif" => "Syncopate",
		"Tangerine, sans-serif" => "Tangerine",
		"Taprom, sans-serif" => "Taprom",
		"Tauri, sans-serif" => "Tauri",
		"Teko, sans-serif" => "Teko",
		"Telex, sans-serif" => "Telex",
		"'Tenor Sans', sans-serif" => "Tenor Sans",
		"'Text Me One', sans-serif" => "Text Me One",
		"'The Girl Next Door', sans-serif" => "The Girl Next Door",
		"Tienne, sans-serif" => "Tienne",
		"Tinos, sans-serif" => "Tinos",
		"'Titan One', sans-serif" => "Titan One",
		"'Titillium Web', sans-serif" => "Titillium Web",
		"'Trade Winds', sans-serif" => "Trade Winds",
		"Trocchi, sans-serif" => "Trocchi",
		"Trochut, sans-serif" => "Trochut",
		"Trykker, sans-serif" => "Trykker",
		"'Tulpen One', sans-serif" => "Tulpen One",
		"Ubuntu, sans-serif" => "Ubuntu",
		"'Ubuntu Condensed', sans-serif" => "Ubuntu Condensed",
		"'Ubuntu Mono', sans-serif" => "Ubuntu Mono",
		"Ultra, sans-serif" => "Ultra",
		"'Uncial Antiqua', sans-serif" => "Uncial Antiqua",
		"Underdog, sans-serif" => "Underdog",
		"'Unica One', sans-serif" => "Unica One",
		"UnifrakturCook, sans-serif" => "UnifrakturCook",
		"UnifrakturMaguntia, sans-serif" => "UnifrakturMaguntia",
		"Unkempt, sans-serif" => "Unkempt",
		"Unlock, sans-serif" => "Unlock",
		"Unna, sans-serif" => "Unna",
		"VT323, sans-serif" => "VT323",
		"'Vampiro One', sans-serif" => "Vampiro One",
		"Varela, sans-serif" => "Varela",
		"'Varela Round', sans-serif" => "Varela Round",
		"'Vast Shadow', sans-serif" => "Vast Shadow",
		"'Vesper Libre', sans-serif" => "Vesper Libre,",
		"Vibur, sans-serif" => "Vibur",
		"Vidaloka, sans-serif" => "Vidaloka",
		"Viga, sans-serif" => "Viga",
		"Voces, sans-serif" => "Voces",
		"Volkhov, sans-serif" => "Volkhov",
		"Vollkorn, sans-serif" => "Vollkorn",
		"Voltaire, sans-serif" => "Voltaire",
		"'Waiting for the Sunrise', sans-serif" => "Waiting for the Sunrise",
		"Wallpoet, sans-serif" => "Wallpoet",
		"'Walter Turncoat', sans-serif" => "Walter Turncoat",
		"Warnes, sans-serif" => "Warnes",
		"Wellfleet, sans-serif" => "Wellfleet",
		"'Wendy One', sans-serif" => "Wendy One",
		"'Wire One', sans-serif" => "Wire One",
		"'Yanone Kaffeesatz', sans-serif" => "Yanone Kaffeesatz",
		"Yellowtail, sans-serif" => "Yellowtail",
		"'Yeseva One', sans-serif" => "Yeseva One",
		"Yesteryear, sans-serif" => "Yesteryear",
		"Zeyada, sans-serif" => "Zeyada",
		"pfbeausanspro-thin, sans-serif" => "pfbeausanspro-thin",
		"pfbeausanspro-light, sans-serif" => "pfbeausanspro-light",
		"pfbeausanspro-bold, sans-serif" => "pfbeausanspro-bold",
		"PFBeauSansProRegular, sans-serif" => "PFBeauSansProRegular"
		);		
	return $fonts;
}


?>