<?php
/**
 *
 */
class itMagazine {	
	
	public static function magazine( $atts = null, $content = null ) {
		if( $atts == 'generator' ) {
			$option = array(
				'name' => __( 'Magazine Panels', IT_TEXTDOMAIN ),
				'value' => 'magazine',
				'options' => array(					
					array(
						'name' => __( 'Included Categories', IT_TEXTDOMAIN ),
						'id' => 'included_categories',
						'target' => 'cat',
						'type' => 'multidropdown'
					),
					array(
						'name' => __( 'Included Tags', IT_TEXTDOMAIN ),
						'id' => 'included_tags',
						'target' => 'tag',
						'type' => 'multidropdown'
					),
					array(
						'name' => __( 'Excluded Categories', IT_TEXTDOMAIN ),
						'id' => 'excluded_categories',
						'target' => 'cat',
						'type' => 'multidropdown'
					),	
					array(
						'name' => __( 'Excluded Tags', IT_TEXTDOMAIN ),
						'id' => 'excluded_tags',
						'target' => 'tag',
						'type' => 'multidropdown'
					),	
					array(
						'name' => __( 'Layout', IT_TEXTDOMAIN ),
						'id' => 'layout_magazine',
						'options' => array(
							'a' => THEME_ADMIN_ASSETS_URI . '/images/magazine_layout_a.png',
							'b' => THEME_ADMIN_ASSETS_URI . '/images/magazine_layout_b.png',
							'c' => THEME_ADMIN_ASSETS_URI . '/images/magazine_layout_c.png',
						),
						'default' => 'a',
						'type' => 'layout'
					),
					array(
						'name' => __( 'Custom Sidebar', IT_TEXTDOMAIN ),
						'desc' => __( "Select the custom sidebar that you'd like to be displayed with this panel. Leave this blank to use the Magazine Panels sidebar. Note: You will need to first create a custom sidebar under the Sidebar tab in the theme options panel before it will show up here.", IT_TEXTDOMAIN ),
						'id' => 'sidebar',
						'target' => 'custom_sidebars',
						'type' => 'select'
					),
					array(
						'name' => __( 'Disable Category Icon', IT_TEXTDOMAIN ),
						'id' => 'disable_icon',
						'options' => array( 'true' => __( 'Do not display the category icon within the colored title at the top', IT_TEXTDOMAIN ) ),
						'type' => 'checkbox'
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
			'disable_icon'			=> '',
			'disable_more'			=> '',
			'layout_magazine'		=> 'a',
			'sidebar'				=> '',
			'included_categories'	=> '',
			'excluded_categories'	=> '',
			'included_tags'			=> '',
			'excluded_tags'			=> '',
			
		), $atts));			
		
		$sidebar = empty($sidebar) ? 'Magazine Panels' : $sidebar;
		$sidebar = it_widget_panel($sidebar);
		
		$panels = '';	
		$term_counter = 0;				
		
		$categories = explode(',',$included_categories);
				
		foreach ($categories as $category) {
		
			$editor_tag = it_get_setting('editor_tag');
			$popular_metric = it_get_setting('popular_metric');
			$popular_tag = it_get_setting('popular_tag');
			$header_left = it_get_setting('magazine_left_label');
			$header_mid = it_get_setting('magazine_middle_label');
			$header_right = it_get_setting('magazine_right_label');
			$header_left = !empty($header_left) ? $header_left : __("Popular Now",IT_TEXTDOMAIN);
			$header_mid = !empty($header_mid) ? $header_mid : __("The Latest",IT_TEXTDOMAIN);
			$header_right = !empty($header_right) ? $header_right : __("Editor's Picks",IT_TEXTDOMAIN);
		
			#left loop
			$format_left = array('loop' => 'main', 'location' => 'widget_a', 'thumbnail' => false, 'disable_category' => true);
			$args_left = array('posts_per_page' => 5, 'cat' => $category, 'post_status' => 'publish');						
			switch($popular_metric) {
				case 'heat':
					$args_left['orderby'] = 'meta_value_num';
					$args_left['meta_key'] = IT_HEAT_INDEX;
				break;	
				case 'likes':
					$args_left['orderby'] = 'meta_value_num';
					$args_left['meta_key'] = IT_META_TOTAL_LIKES;
				break;
				case 'views':
					$args_left['orderby'] = 'meta_value_num';
					$args_left['meta_key'] = IT_META_TOTAL_VIEWS;
				break;
				case 'comments':
					$args_left['orderby'] = 'comment_count';
				break;
				case 'tag':					
					if(!empty($popular_tag)) $args_left['tag_id'] = $popular_tag;
				break;
			}	
			$loop_left = it_loop($args_left, $format_left);
			
			#middle loop
			$format_mid = array('loop' => 'main', 'location' => 'widget_b', 'disable_category' => true, 'size' => 'square-large');
			$args_mid = array('posts_per_page' => 1, 'cat' => $category, 'post_status' => 'publish');
			$loop_mid = it_loop($args_mid, $format_mid);
			$post_count = $loop_mid['posts'];
		
			#right loop
			$format_right = array('loop' => 'main', 'location' => 'compact', 'thumbnail' => true);
			$args_right = array('posts_per_page' => 4, 'cat' => $category, 'post_status' => 'publish');
			if(!empty($editor_tag)) $args_right['tag_id'] = $editor_tag;
			$loop_right = it_loop($args_right, $format_right);
			
			#get category icon
			$categoryargs = array('catid' => $category, 'label' => false, 'icon' => true, 'white' => true, 'single' => true, 'wrapper' => false, 'id' => false, 'size' => 16);
			$cats = it_get_primary_categories($categoryargs);
			
			#get subcategories
			$terms = get_terms('category', array('parent' => $category, 'hide_empty' => 0));
			$term_count = count($terms);
			
			#get category permalink
			$link = get_category_link($category);
							
			$panels .= '<div class="magazine-panel category-' . $category . '">'; 
				
				$panels .= '<div class="content-inner shadowed content-panel">';
				
					$panels .= '<div class="magazine-header">';
					
						$panels .= '<div class="magazine-title add-active">';
						
							if(!$disable_icon) $panels .= $cats	;
							
							$panels .= '<a class="styled" href="' . $link . '">' . get_cat_name($category) . '</a>';
							
						$panels .= '</div>';
						
						if($term_count > 0) {
							$panels .= '<div class="magazine-categories">'; 	
								foreach ( $terms as $term ) { 
									$term_counter++;
									$term_name = $term->name;
									$term_slug = $term->slug;
									$term_link = get_term_link($term_slug, 'category');	
									$panels .= '<a class="magazine-category styled" href="' . $term_link . '">' . $term_name . '</a>';							
								}
							$panels .= '</div>';
						}
						
						$panels .= '<div class="magazine-more">';
						
							$panels .= '<div class="sort-wrapper">';
						
								$panels .= '<div class="sort-toggle">';
								
									$panels .= __('MORE',IT_TEXTDOMAIN) . '<span class="theme-icon-sort-down"></span>';
								
								$panels .= '</div>';
								
								$panels .= '<div class="sort-buttons">';
								
									
								
								$panels .= '</div>';
						
							$panels .= '</div>';
							
						$panels .= '</div>';	
					
					$panels .= '</div>';
					
					$panels .= '<div class="magazine-content clearfix">';					
										
						$panels .= '<div class="magazine-left">';
						
							$panels .= '<div class="magazine-label"><span class="theme-icon-signal"></span>' . $header_left . '</div>';
							   
							$panels .= $loop_left['content'];
						
						$panels .= '</div>';
						
						$panels .= '<div class="magazine-mid">';
						
							$panels .= '<div class="magazine-label">' . $header_mid . '</div>';
							   
							$panels .= $loop_mid['content'];
						
						$panels .= '</div>';
						
						$panels .= '<div class="magazine-right">';
						
							$panels .= '<div class="magazine-label"><span class="theme-icon-check"></span>' . $header_right . '</div>';
							   
							$panels .= $loop_right['content'];
						
						$panels .= '</div>';
						
						if(!$disable_more) $panels .= '<a class="more-link styled" href="' . $link . '">' . __('Go To ',IT_TEXTDOMAIN) . get_cat_name($category) . '<span class="theme-icon-forward"></span></a>';
						
					$panels .= '</div>';
					
				$panels .= '</div>';
					
			$panels .= '</div>';
			
		}	
		
		#which content goes in which column		
		switch($layout_magazine) {
			case 'a':
				$col1 = '<div class="col-md-9">' . $panels . '</div>';
				$col2 = '<div class="col-md-3"><div class="content-panel shadowed">' . $sidebar . '<br class="clearer hidden-lg hidden-md" /></div></div>';
			break;
			case 'b':
				$col1 = '<div class="col-md-3"><div class="content-panel shadowed">' . $sidebar . '<br class="clearer hidden-lg hidden-md" /></div></div>';
				$col2 = '<div class="col-md-9">' . $panels . '</div>';
			break;
			case 'c':
				$col1 = '<div class="col-md-12">' . $panels . '</div>';
				$col2 = '';			
			break;	
		}		
		
		#begin output	
		$out = '<div class="container-fluid no-padding builder-section builder-magazine">';
		
			$out .= '<div class="row">';
			
				$out .= '<div class="col-md-12">';
				
					$out .= it_background_ad();
			
					$out .= '<div class="container-inner">';
					
						$out .= it_ad_action('magazine_before');
					
						$out .= '<div class="row">';
							
							if(!empty($content)) $out .= '<div class="html-content clearfix">' . do_shortcode(stripslashes($content)) . '</div>'; 
							
							$out .= $col1;	
							
							$out .= $col2;
							
						$out .= '</div>';
						
						$out .= it_ad_action('magazine_after');
						
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
			'name' => __( 'Magazine Panels', IT_TEXTDOMAIN ),
			'value' => 'magazine',
			'options' => $shortcode
		);

		return $options;
	}

}

?>
