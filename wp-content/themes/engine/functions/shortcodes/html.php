<?php
/**
 *
 */
class itHTML {	
	
	public static function html( $atts = null, $content = null ) {
		if( $atts == 'generator' ) {
			$option = array(
				'name' => __( 'Custom Content', IT_TEXTDOMAIN ),
				'value' => 'html',
				'options' => array(	
					array(
						'name' => __( 'Content', IT_TEXTDOMAIN ),
						'desc' => __( 'This is shortcode and HTML/CSS/Javascript syntax enabled', IT_TEXTDOMAIN ),
						'id' => 'content',
						'type' => 'textarea'
					),					
				'shortcode_has_atts' => true
				)
			);
			
			return $option;
		}
		
		extract(shortcode_atts(array(), $atts));
				
		$out = '';
		
		$out = '<div class="container-fluid no-padding builder-section builder-html">';
		
			$out .= '<div class="row">';
		
				$out .= '<div class="col-md-12">';
				
					$out .= it_background_ad();
			
					$out .= '<div class="container-inner">';
					
						$out .= it_ad_action('html_before');
						
						$out .= '<div class="html-inner shadowed boxed">';
			
							$out .= '<div class="html-content clearfix">'; 
							
								$out .= do_shortcode(stripslashes($content));                
							   
							$out .= '</div>';
							
						$out .= '</div>';
        
        				$out .= it_ad_action('html_after');
						
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
			'name' => __( 'Custom Content', IT_TEXTDOMAIN ),
			'value' => 'html',
			'options' => $shortcode
		);

		return $options;
	}

}

?>
