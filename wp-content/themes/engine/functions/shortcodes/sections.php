<?php
/**
 *
 */
class itSections {	
	
	public static function sections( $atts = null, $content = null ) {
		if( $atts == 'generator' ) {
			$option = array(
				'name' => __( 'Sections', IT_TEXTDOMAIN ),
				'value' => 'sections',
				'options' => array(					
					array(
						'name' => __( 'Included Categories', IT_TEXTDOMAIN ),
						'id' => 'included_categories',
						'target' => 'cat',
						'type' => 'multidropdown'
					),
					array(
						'name' => __( 'Columns', IT_TEXTDOMAIN ),
						'id' => 'cols',
						'options' => array(
							'2' => THEME_ADMIN_ASSETS_URI . '/images/column_layout_2.png',
							'3' => THEME_ADMIN_ASSETS_URI . '/images/column_layout_3.png',
							'4' => THEME_ADMIN_ASSETS_URI . '/images/column_layout_4.png',
							'5' => THEME_ADMIN_ASSETS_URI . '/images/column_layout_5.png',
							'6' => THEME_ADMIN_ASSETS_URI . '/images/column_layout_6.png',
						),
						'default' => '5',
						'type' => 'layout'
					),
					array(
						'name' => __( 'Disable Category Icon', IT_TEXTDOMAIN ),
						'id' => 'disable_icon',
						'options' => array( 'true' => __( 'Do not display the category icon within the colored circle at the top', IT_TEXTDOMAIN ) ),
						'type' => 'checkbox'
					),
					array(
						'name' => __( 'Posts Per Section', IT_TEXTDOMAIN ),
						'desc' => __( 'The number of latest posts to display within each section.', IT_TEXTDOMAIN ),
						'id' => 'postsperpage',
						'target' => 'recommended_filters_number',
						'type' => 'select'
					),
					array(
						'name' => __( 'Disable More Link', IT_TEXTDOMAIN ),
						'id' => 'disable_more',
						'options' => array( 'true' => __( 'Do not display the More link at the bottom of the posts', IT_TEXTDOMAIN ) ),
						'type' => 'checkbox'
					),
				'shortcode_has_atts' => true
				)
			);
			
			return $option;
		}
		
		extract(shortcode_atts(array(				
			'postsperpage'			=> 4,
			'cols'					=> '',
			'disable_icon'			=> '',
			'disable_more'			=> '',
			'included_categories'	=> '',
			
		), $atts));	
		
		$out = '<div class="container-fluid no-padding builder-section builder-sections">';
		
			$out .= '<div class="row">';
			
				$out .= '<div class="col-md-12">';
				
					$out .= it_background_ad();
				
					$out .= '<div class="container-inner">';
					
						$out .= it_ad_action('sections_before');
					
						$out .= '<div class="widget-section-wrapper">';
								
							if(!empty($content)) $out .= '<div class="html-content clearfix">' . do_shortcode(stripslashes($content)) . '</div>'; 					
			
							$categories = explode(',',$included_categories);
							
							$i = 0;
							
							foreach ($categories as $category) { $i++;
							
								#setup the query            
								$args=array('posts_per_page' => $postsperpage, 'cat' => $category);
								
								#setup loop format
								$format = array('loop' => 'main', 'location' => 'section', 'numarticles' => $postsperpage);				
									
								#fetch the loop
								$loop = it_loop($args, $format); 
								
								#get category icon
								$categoryargs = array('catid' => $category, 'label' => false, 'icon' => true, 'white' => true, 'single' => true, 'wrapper' => false, 'id' => false, 'size' => 28);
								$cats = it_get_primary_categories($categoryargs);
								$csscat = $disable_icon ? ' no-icon' : '';
								$csslast = $i % $cols == 0 ? ' last' : '';
								$csscols = ' cols-' . $cols;
								
								$link = get_category_link($category);
									
								$out .= '<div class="widget-panel add-active widget-section category-' . $category . $csscat . $csslast . $csscols . '">'; 
									
									$out .= '<div class="content-inner shadowed content-panel">';
									
										$out .= '<a class="header-link" href="' . $link . '"></a>';
									
										if(!$disable_icon) $out .= '<div class="category-circle">' . $cats . '</div>';
										
										$out .= '<div class="category-name">' . get_cat_name($category) . '</div>';
										
										$out .= '<div class="border"></div>';
										
										$out .= '<div class="loop">';
											   
											$out .= $loop['content'];
										
										$out .= '</div>';	
										
										if(!$disable_more) $out .= '<a class="more-link styled" href="' . $link . '">' . __('More',IT_TEXTDOMAIN) . '<span class="theme-icon-forward"></span></a>';
										
									$out .= '</div>';
										
								$out .= '</div>';
								
								if($i % $cols == 0) $out .= '<br class="clearer" />';
								
								if($i % 2 == 0) $out .= '<br class="clearer hidden-lg hidden-md" />';
								
							}					
							
						$out .= '</div>';
						
						$out .= it_ad_action('sections_after');	
					
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
			'name' => __( 'Sections', IT_TEXTDOMAIN ),
			'value' => 'sections',
			'options' => $shortcode
		);

		return $options;
	}

}

?>
