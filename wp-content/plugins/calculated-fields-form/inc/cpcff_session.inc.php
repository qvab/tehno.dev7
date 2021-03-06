<?php
/**
 * CodePeople Session.
 *
 * Standardizes WordPress session data using database-backed options for storage.
 * for storing user session information.
 */

if( !defined( 'CP_COOKIE_NAME' ) )
{
	define( 'CP_COOKIE_NAME', 'CP5XKN6QLDFWUC' );
}

if( !class_exists( 'CP_SESSION' ) )
{
	class CP_SESSION
	{
		
		/************** STATIC PROPERTIES **************/
		private static $instance = false;
		
		/************** INSTANCE PROPERTIES **************/
		private $session_id;
		private $expiration;
		private $expiration_interval = 86400; // 24 Hours
		
		/************** CONSTRUCT **************/
		
		private function __construct()
		{
			if( session_id() == "" ) @session_start();

			if( isset( $_SESSION[CP_COOKIE_NAME] ) || isset( $_COOKIE[CP_COOKIE_NAME] ) ) 
			{
				$cookie = stripslashes( ( isset( $_SESSION[CP_COOKIE_NAME] ) ) ? $_SESSION[CP_COOKIE_NAME] : $_COOKIE[CP_COOKIE_NAME] );
				
				$cookie_crumbs = explode( '||', $cookie );

				$this->session_id = $cookie_crumbs[0];
				$this->expiration = $cookie_crumbs[1];
			} 
			else 
			{
				$this->session_id = $this->_generate_session_id();
				$this->expiration = time()+$this->expiration_interval;
				$this->_set_cookie();
			}

		}
		
		/************** PRIVATE INSTANCE METHODS **************/
		private function _generate_session_id() 
		{
			require_once( ABSPATH . 'wp-includes/class-phpass.php' );
			$hash = new PasswordHash( 8, false );

			return md5( $hash->get_random_bytes( 32 ) );
		}
		
		private function _set_cookie() 
		{
			try
			{
				$_SESSION[CP_COOKIE_NAME] = $this->session_id . '||' . $this->expiration;
				if(!headers_sent())
					@setcookie( CP_COOKIE_NAME, $this->session_id . '||' . $this->expiration, 0, '/' );
			}
			catch( Exception $err ){}
		}
		
		private function _get_var_name( $name )
		{
			return CP_COOKIE_NAME.'_'.$this->session_id.'_'.$name;
		}
		
		private function _set_var( $name, $value )
		{
			$_SESSION[ $name ] = $value;
			$transient = $this->_get_var_name( $name );
			set_transient( $transient, $value, $this->expiration );
		}
		
		private function _get_var( $name )
		{
			if( isset( $_SESSION[ $name ] ) ) return $_SESSION[ $name ];
			$transient = $this->_get_var_name( $name );
			return get_transient( $transient );
		}
		
		private function _unset_var( $name )
		{
			unset( $_SESSION[ $name ] );
			$transient = $this->_get_var_name( $name );
			delete_transient( $transient );
		}
		
		private function _clean_expired_vars()
		{
			global $wpdb;
			
			$expiration = time()-$this->expiration_interval;
			try
			{
				$transients = $wpdb->get_col(
					$wpdb->prepare( "SELECT REPLACE(option_name, '_transient_timeout_', '') AS transient_name FROM {$wpdb->options} WHERE option_name LIKE %s AND option_value < %s", "_transient_timeout_".$wpdb->esc_like(CP_COOKIE_NAME)."%", $expiration)
				);
				
				$options_names = array();
				foreach($transients as $transient) 
				{
					if( strpos( $transient, $this->session_id ) === false )
					{	
						$options_names[] = '_transient_' . $transient;
						$options_names[] = '_transient_timeout_' . $transient;
					}
				}

				if ( !empty($options_names) ) 
				{
					$options_names = array_map('esc_sql', $options_names);
					$options_names = "'". implode("','", $options_names) ."'";
					$result = $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name IN ({$options_names})" );
					
				}
			}
			catch( Exception $err )	{}
		}
		/************** PUBLIC INSTANCE METHODS **************/
		
		/************** PRIVATE STATIC METHODS **************/
		
		private static function _get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
		
		
		/************** PUBLIC STATIC METHODS **************/
		
		public static function session_start()
		{
			$instance = self::_get_instance();
			$instance->_clean_expired_vars();
		}
		
		public static function session_id()
		{
			$instance = self::_get_instance();
			return $instance->session_id;
		}
		
		public static function set_var( $name, $value )
		{
			$instance = self::_get_instance();
			$instance->_set_var( $name, $value );
		}
		
		public static function get_var( $name )
		{
			$instance = self::_get_instance();
			return $instance->_get_var( $name );
		}
		
		public static function unset_var( $name )
		{
			$instance = self::_get_instance();
			$instance->_unset_var( $name );
		}
		
	} // End clss
}	
?>