<?php
/**
 *
 */
class itTiles {	
	
	public static function tiles( $atts = null, $content = null ) {
		if( $atts == 'generator' ) {
			$option = array(
			'name' => __( 'Tiles', IT_TEXTDOMAIN ),
				'value' => 'tiles',
				'options' => array(					
					array(
						'name' => __( 'Included Categories', IT_TEXTDOMAIN ),
						'id' => 'managed_categories',
						'target' => 'cat_managed',
						'type' => 'multidropdown'
					),
				'shortcode_has_atts' => true
				)
			);
			
			return $option;
		}
		
		extract(shortcode_atts(array(			
			'managed_categories'	=> ''
		), $atts));		
		
		$out = '';
		
		$managed_categories = empty($managed_categories) ? array() : explode(',',$managed_categories);
		
		$tileargs = array('ids' => $managed_categories, 'cols' => 8);
		
		$tiles = it_get_tiles($tileargs);
				
		$out = '<div class="container-fluid no-padding builder-section builder-tiles">';
		
			$out .= '<div class="row">';
			
				$out .= '<div class="col-md-12">';
				
					$out .= it_background_ad();
			
					$out .= '<div class="container-inner content-panel shadowed clearfix">';
					
						$out .= it_ad_action('tiles_before');
							
						if(!empty($content)) $out .= '<div class="html-content clearfix">' . do_shortcode(stripslashes($content)) . '</div>';
			
						$out .= $tiles;
						
						$out .= it_ad_action('tiles_after');	
					
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
			'name' => __( 'Category Tiles', IT_TEXTDOMAIN ),
			'value' => 'tiles',
			'options' => $shortcode
		);

		return $options;
	}

}

?>
