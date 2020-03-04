<?php
/**
 *
 */
class itWidgets {	
	
	public static function widgets( $atts = null, $content = null ) {
		if( $atts == 'generator' ) {
			$option = array();
			
			return $option;
		}
		
		extract(shortcode_atts(array(), $atts));	
		
		$out = '';
		
		$col1 = __('Widgets Column 1',IT_TEXTDOMAIN);
		$col2 = __('Widgets Column 2',IT_TEXTDOMAIN);
		$col3 = __('Widgets Column 3',IT_TEXTDOMAIN);
		$col4 = __('Widgets Column 4',IT_TEXTDOMAIN);
		$class = 'widgets';	
				
		$out = '<div class="container-fluid no-padding builder-section builder-widgets">';
		
			$out .= '<div class="row">';
		
				$out .= '<div class="col-md-12">';
				
					$out .= it_background_ad();
			
					$out .= '<div class="container-inner">';
					
						$out .= it_ad_action('widgets_before');
						
						if(!empty($content)) $out .= '<div class="html-content clearfix">' . do_shortcode(stripslashes($content)) . '</div>'; 
						
						$out .= '<div class="widgets-inner shadowed">';
						
							$out .= '<div class="row" id="widgets">';			
															
								$out .= '<div class="widget-panel left col-md-3">';
								
									$out .= it_widget_panel($col1, $class);
									
								$out .= '</div>';							
														
								$out .= '<div class="widget-panel mid mid-left col-md-3">';
								
									$out .= it_widget_panel($col2, $class);
									
								$out .= '</div>';
								
								$out .= '<br class="clearer hidden-lg hidden-md" />';
							
								$out .= '<div class="widget-panel mid mid-right col-md-3">';
								
									$out .= it_widget_panel($col3, $class);
									
								$out .= '</div>';
								
								$out .= '<div class="widget-panel right col-md-3">';
								
									$out .= it_widget_panel($col4, $class);
									
								$out .= '</div>';
								
							$out .= '</div>';
						
						$out .= '</div>';
						
						$out .= it_ad_action('widgets_after');
						
					$out .= '</div>';
					
				$out .= '</div>';
				
			$out .= '</div>';
        
        $out .= '</div>';		
				
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
			'name' => __( 'Widgets', IT_TEXTDOMAIN ),
			'value' => 'widgets',
			'options' => $shortcode
		);

		return $options;
	}

}

?>
