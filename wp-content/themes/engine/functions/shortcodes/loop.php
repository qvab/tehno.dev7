<?php
/**
 *	@var string $layout
 */
class itLoop {

	public static function loop( $atts = null, $content = null ) {

		if( $atts == 'generator' ) {
			$option = array(
				'name' => __( 'Loop', IT_TEXTDOMAIN ),
				'value' => 'loop',
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
						'name' => __( 'Post Loading', IT_TEXTDOMAIN ),
						'desc' => __( 'How should subsequent pages of posts load', IT_TEXTDOMAIN ),
						'id' => 'loading',
						'default' => 'paged',
						'options' => array(
							'paged' => __('Paged', IT_TEXTDOMAIN ),
							'infinite' => __('Infinite', IT_TEXTDOMAIN ),
							'' => __('None', IT_TEXTDOMAIN ),
						),
						'type' => 'radio'
					),
					array(
						'name' => __( 'Title', IT_TEXTDOMAIN ),
						'desc' => __( 'Displays to the left of the sort controls.', IT_TEXTDOMAIN ),
						'id' => 'title',
						'type' => 'text'
					),	
					array(
						'name' => __( 'Layout', IT_TEXTDOMAIN ),
						'id' => 'layout',
						'options' => array(
							'a' => THEME_ADMIN_ASSETS_URI . '/images/loop_layout_a.png',
							'b' => THEME_ADMIN_ASSETS_URI . '/images/loop_layout_b.png',
							'c' => THEME_ADMIN_ASSETS_URI . '/images/loop_layout_c.png',
							'd' => THEME_ADMIN_ASSETS_URI . '/images/loop_layout_d.png',
						),
						'default' => 'a',
						'type' => 'layout'
					),
					array(
						'name' => __( 'Sidebar', IT_TEXTDOMAIN ),
						'desc' => __( "Select the custom sidebar that you'd like to be displayed with this panel. Leave this blank to use the Loop Sidebar Left sidebar. Note: You will need to first create a custom sidebar under the Sidebar tab in the theme options panel before it will show up here.", IT_TEXTDOMAIN ),
						'id' => 'sidebar',
						'target' => 'custom_sidebars',
						'type' => 'select'
					),
					array(
						'name' => __( 'Sidebar 2', IT_TEXTDOMAIN ),
						'desc' => __( "Select the custom sidebar that you'd like to be displayed with this panel. Leave this blank to use the Loop Sidebar Right sidebar. Note: You will need to first create a custom sidebar under the Sidebar tab in the theme options panel before it will show up here.", IT_TEXTDOMAIN ),
						'id' => 'sidebar2',
						'target' => 'custom_sidebars',
						'type' => 'select'
					),
					array(
						'name' => __( 'Disable Filter Buttons', IT_TEXTDOMAIN ),
						'desc' => __( 'You can disable individual filter buttons.', IT_TEXTDOMAIN ),
						'id' => 'disabled_filters',
						'options' => array(
							'liked' => __('Liked',IT_TEXTDOMAIN),
							'viewed' => __('Viewed',IT_TEXTDOMAIN),
							'reviewed' => __('Reviewed',IT_TEXTDOMAIN),
							'rated' => __('Rated',IT_TEXTDOMAIN),
							'commented' => __('Commented',IT_TEXTDOMAIN),
							'awarded' => __('Awarded',IT_TEXTDOMAIN),
							'title' => __('Alphabetical',IT_TEXTDOMAIN)
						),
						'type' => 'checkbox'
					),
					array(
						'name' => __( 'Posts Per Page', IT_TEXTDOMAIN ),
						'desc' => __( 'The number of total posts to display before pagination or the load more button is displayed.', IT_TEXTDOMAIN ),
						'id' => 'postsperpage',
						'target' => 'recommended_filters_number',
						'type' => 'select'
					),
					array(
						'name' => __( 'Disable Ads', IT_TEXTDOMAIN ),
						'id' => 'disable_ads',
						'options' => array( 'true' => __( 'Do not display ads within this loop', IT_TEXTDOMAIN ) ),
						'type' => 'checkbox'
					),
				'shortcode_has_atts' => true
				)
			);
			
			return $option;
		}
		
		extract(shortcode_atts(array(	
			'loading' 				=> 'paged',
			'icon'					=> '',
			'title'					=> '',
			'postsperpage'			=> 5,
			'layout'				=> 'a',
			'sidebar'				=> '',
			'sidebar2'				=> '',
			'disable_ads'			=> '',
			'disabled_filters'		=> '',
			'included_categories'	=> '',
			'excluded_categories'	=> '',
			'included_tags'			=> '',
			'excluded_tags'			=> '',
		), $atts));

		$current_query['category__in'] = '';
		$args['category__in'] = '';
		$current_query['tag__in'] = '';
		$args['tag__in'] = '';
		$current_query['category__not_in'] = '';
		$args['category__not_in'] = '';
		$current_query['tag__not_in'] = '';
		$args['tag__not_in'] = '';
		
		$out = '';
		
		global $wp, $wp_query;
		#get the current query to pass it to the ajax functions through the html data tag
		if(!is_single() && !is_page()) $current_query = $wp->query_vars;
		
		#default settings
		$args = array();
		$disabled_filters = !empty($disabled_filters) ? explode(',',$disabled_filters) : array();
		$disabled_count = !empty($disabled_filters) ? count($disabled_filters) : 0;
		$disable_filters = $disabled_count > 6 ? true : false;
		$title = empty($title) ? it_archive_title() : $title;
		$cssadmin = is_admin_bar_showing() ? ' admin-bar' : '';
		
		$loop = 'main';
		$location = 'loop';
		$disable_title = empty($title) ? true : false;
		$cssload = ' load-sort';
		
		$sidebar_left = empty($sidebar) ? 'Loop Sidebar Left' : $sidebar;
		$sidebar_right = empty($sidebar2) ? 'Loop Sidebar Right' : $sidebar2;
		
		#query args
		$args = array('posts_per_page' => $postsperpage, 'ignore_sticky_posts' => false);
		
		#check and see if we care about excludes and limits
		$ignore_excludes = !(is_archive() || is_search()) ? false : true;
		if(!$ignore_excludes) {
			#limits
			if(!empty($included_categories)) $current_query['category__in'] = explode(',',$included_categories);
			if(!empty($included_categories)) $args['category__in'] = explode(',',$included_categories);	
			if(!empty($included_tags)) $current_query['tag__in'] = explode(',',$included_tags);	
			if(!empty($included_tags)) $args['tag__in'] = explode(',',$included_tags);
			#excludes
			if(!empty($excluded_categories)) $current_query['category__not_in'] = explode(',',$excluded_categories);
			if(!empty($excluded_categories)) $args['category__not_in'] = explode(',',$excluded_categories);
			if(!empty($excluded_tags)) $current_query['tag__not_in'] = explode(',',$excluded_tags);
			if(!empty($excluded_tags)) $args['tag__not_in'] = explode(',',$excluded_tags);
		}
		
		#setup loop format
		$format = array('loop' => $loop, 'location' => $location, 'sort' => 'recent', 'paged' => 1, 'thumbnail' => true, 'rating' => false, 'icon' => true, 'nonajax' => true, 'meta' => true, 'award' => false, 'badge' => false, 'excerpt' => false, 'authorship' => false, 'numarticles' => $postsperpage, 'disable_ads' => $disable_ads);
		
		if(!is_single() && !is_page()) $args = array_merge($args, $current_query);
		
		#adjust args for default filter
		$setup_filters = it_setup_filters($disabled_filters, $args, $format);
		$default_metric = $setup_filters['default_metric'];
		$default_label = $setup_filters['default_label'];
		$args = $setup_filters['args'];
		$format = $setup_filters['format'];
		
		#setup sortbar
		$sortbarargs = array('title' => $title, 'loop' => $loop, 'location' => $location, 'numarticles' => $postsperpage, 'disabled_filters' => $disabled_filters, 'disable_filters' => $disable_filters, 'disable_title' => $disable_title, 'thumbnail' => true, 'rating' => false, 'meta' => true, 'icon' => true, 'award' => false, 'badge' => false, 'excerpt' => false, 'authorship' => false, 'theme_icon' => $icon, 'metric_text' => $default_label);
		
		#get correct page number count
		$itposts = new WP_Query($args);
		$numpages = $itposts->max_num_pages;
		wp_reset_postdata();
		
		#setup load more button
		if($loading=='infinite') {
			$loadmoreargs = $format;
			$loadmoreargs['numpages'] = $numpages;
			$cssload = ' load-infinite';
		}
		
		$current_query_encoded = json_encode($current_query);
				
		#column content
		$sidebar_left = it_widget_panel($sidebar_left);
		
		$sidebar_right = it_widget_panel($sidebar_right);
				
		$loop_content = "<div class='post-container panel-style main-post-container' data-currentquery='" . $current_query_encoded . "'>";            
        
            $loop_content .= it_get_sortbar($sortbarargs);
			
			$loop_content .= '<div class="loop-placeholder content-panel shadowed"></div>';
            
            $loop_content .= '<div class="content-inner clearfix">';
            
                $loop_content .= '<div class="loading load-sort"><span class="theme-icon-spin2"></span></div>';
            
                $loop_content .= '<div class="loop">';
                
                    $loop = it_loop($args, $format); 
					$loop_content .= $loop['content'];
                    
                $loop_content .= '</div>';
                
                $loop_content .= '<div class="loading' . $cssload . '"><span class="theme-icon-spin2"></span></div>';
                
            $loop_content .= '</div>';
            
            if($loading=='infinite') {
			
				$loop_content .= it_get_loadmore($loadmoreargs);
				
				$loop_content .= '<div class="last-page">' . __('End of the line!',IT_TEXTDOMAIN) . '</div>';
				
			} elseif($loading=='paged') {
				
				$loop_content .= '<div class="pagination-wrapper">';
        
					$loop_content .= it_pagination($numpages, $format, it_get_setting('page_range'));
					
				$loop_content .= '</div>';
				
			}
                
        $loop_content .= '</div>';
		
		#which content goes in which column
		switch($layout) {
			case 'a':
				$csscol1 = 'col-md-2';
				$csscol2 = 'col-md-7';
				$csscol3 = 'col-md-3';
				//$col1 = '<div class="content-panel shadowed loop-sidebar-left fixed-object' . $cssadmin . '">' . $sidebar_left . '</div>';
				$col1 = '';
				$col2 = $loop_content;
				$col3 = '<div class="content-panel shadowed loop-sidebar-right fixed-object' . $cssadmin . '">' . $sidebar_right . '</div>';
			break;
			case 'b':
				$csscol1 = 'col-md-3';
				$csscol2 = 'col-md-7';
				$csscol3 = 'col-md-2';
				//$col1 = '<div class="content-panel shadowed loop-sidebar-left fixed-object' . $cssadmin . '">' . $sidebar_left . '</div>';
				$col1 = '';
				$col2 = $loop_content;
				$col3 = '<div class="content-panel shadowed loop-sidebar-right fixed-object' . $cssadmin . '">' . $sidebar_right . '</div>';
			break;
			case 'c':
				$csscol1 = 'col-md-2';
				$csscol2 = 'col-md-3';
				$csscol3 = 'col-md-7';
				//$col1 = '<div class="content-panel shadowed loop-sidebar-left fixed-object' . $cssadmin . '">' . $sidebar_left . '</div>';
				$col1 = '';
				$col2 = '<div class="content-panel shadowed loop-sidebar-right fixed-object' . $cssadmin . '">' . $sidebar_right . '</div>';
				$col3 = $loop_content;
			break;
			case 'd':
				$csscol1 = 'col-md-7';
				$csscol2 = 'col-md-2';
				$csscol3 = 'col-md-3';
				$col1 = $loop_content;
				//$col2 = '<div class="content-panel shadowed loop-sidebar-left fixed-object' . $cssadmin . '">' . $sidebar_left . '</div>';
				$col2 = '';
				$col3 = '<div class="content-panel shadowed loop-sidebar-right fixed-object' . $cssadmin . '">' . $sidebar_right . '</div>';
			break;	
		}
		
		#begin output	
		$out = '<div class="container-fluid no-padding builder-section builder-loop">';
		
			$out .= '<div class="row">';
			
				$out .= '<div class="col-md-12">';
				
					$out .= it_background_ad();
			
					$out .= '<div class="container-inner">';
					
						$out .= it_ad_action('loop_before');
					
						$out .= '<div class="row loop-row layout-' . $layout . '" style="display: flex; justify-content: space-between; flex-wrap: wrap;">';
							
							if(!empty($content)) $out .= '<div class="html-content clearfix">' . do_shortcode(stripslashes($content)) . '</div>';

							if (!empty($col1))
							{
								$out .= '<div class="' . $csscol1 . ' loop-column">' . $col1 . '</div>';
							}

							if (! empty($col2))
							{
								$out .= '<div class="' . $csscol2 . ' loop-column">' . $col2 . '</div>';
							}
							
							$out .= '<div class="' . $csscol3 . ' loop-column">' . $col3 . '</div>';
							
						$out .= '</div>';
						
						$out .= it_ad_action('loop_after');
						
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
			'name' => __( 'Loop', IT_TEXTDOMAIN ),
			'value' => 'loop',
			'options' => $shortcode
		);

		return $options;
	}

}

?>
