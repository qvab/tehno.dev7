<?php
/**
 *
 */
class itConnect {	
	
	public static function connect( $atts = null, $content = null ) {
		if( $atts == 'generator' ) {
			$option = array(
				'name' => __( 'Connect', IT_TEXTDOMAIN ),
				'value' => 'connect',
				'options' => array(
					array(
						'name' => __( 'Title', IT_TEXTDOMAIN ),
						'desc' => __( 'Displays to the left of the connect bar.', IT_TEXTDOMAIN ),
						'id' => 'title',
						'type' => 'text'
					),
					array(
						'name' => __( 'Icon', IT_TEXTDOMAIN ),
						'desc' => __( 'Displays to the left of the title', IT_TEXTDOMAIN ),
						'id' => 'icon',
						'target' => 'icons',
						'type' => 'select'
					),
				'shortcode_has_atts' => true
				)
			);
			
			return $option;
		}
		
		extract(shortcode_atts(array(	
			'title'					=> '',
			'icon'					=> ''
		), $atts));	
		
		$title = ( !empty( $title ) ) ? $title : __('Connect', IT_TEXTDOMAIN);
		
		$out = '';
		    
        $out = '<div class="container-fluid no-padding builder-section builder-connect">';
		
			$out .= '<div class="row">';
		
				$out .= '<div class="col-md-12">';
				
					$out .= it_background_ad();
			
					$out .= '<div class="container-inner">';
					
						$out .= it_ad_action('connect_before');
				
						if(!empty($content)) $out .= '<div class="html-content clearfix">' . do_shortcode(stripslashes($content)) . '</div>'; 
					
						$out .= '<div class="connect-inner shadowed boxed">';
								
							$out .= '<div class="bar-header">';
							
								$out .= '<div class="bar-label-wrapper">';
						
									$out .= '<div class="bar-label has-icon">';
								
										$out .= '<div class="label-text">';
										
											if(!empty($icon)) $out .= '<span class="theme-icon-' . $icon . '"></span>';
											
											if(!empty($title)) $out .= $title;
											
										$out .= '</div>';
										
									$out .= '</div>';
								
								$out .= '</div>';
								
							$out .= '</div>';
							
							if(!it_get_setting('connect_email_disable')) $out .= it_email_form();
							
							if(!it_get_setting('connect_counts_disable')) {
							
								$out .= '<div class="connect-counts">';
									
									$out .= it_widget_panel('Connect Widgets', '', false);
								
								$out .= '</div>';
								
							}
							
							if(!it_get_setting('connect_social_disable')) {
							
								$out .= '<div class="connect-social">';
								
									$out .= '<div class="follow-label">' . __('Follow Us',IT_TEXTDOMAIN) . '</div>';
									
									$out .= it_social_badges();
								
								$out .= '</div>';
								
							}
							
						$out .= '</div>';
						
						$out .= it_ad_action('connect_after');
						
					$out .= '</div>';
					
				$out .= '</div>';
				
			$out .= '</div>';
        
        $out .= '</div>';
        
        wp_reset_query();
				
		return $out;
		
	}
		
	/**
	 *
	 */
	public static function _options( $class ) {
		$shortcode = array();
		
		$class_methods = get_class_methods($class);

		foreach( $class_methods as $method ) {
			if( $method[0] != '_' )
				$shortcode[] = call_user_func(array( &$class, $method ), $atts = 'generator' );
		}

		$options = array(
			'name' => __( 'Connect', IT_TEXTDOMAIN ),
			'value' => 'connect',
			'options' => $shortcode
		);

		return $options;
	}

}

?>
