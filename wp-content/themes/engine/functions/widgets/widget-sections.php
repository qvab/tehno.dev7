<?php
class it_sections extends WP_Widget {
	function __construct() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'Sections', 'description' => __( 'Displays selected categories represented by their corresponding icons in a tabbed format.',IT_TEXTDOMAIN) );
		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'it_sections' );
		/* Create the widget. */
		parent::__construct( 'it_sections', 'Sections', $widget_ops, $control_ops );
	}	
	function widget( $args, $instance ) {		
		
		extract( $args );

		/* User-selected settings. */			
		$numarticles = $instance['numarticles'];		
		$layout = $instance['layout'];	
		$sections = array();
		$categories = it_get_setting('categories');		
		foreach($categories as $category) {
			if(is_array($category)) {
				if(array_key_exists('id',$category)) {
					$id = $category['id'];
					$formid = 'category_' . $id;
					if(!empty($id)) {
						if($instance[$formid]) $sections[] = $id;
					}
				}
			}
		}
		$loop = 'sections';
		$location = $layout;
		$size = '';
		$style = $location;
		switch($location) {
			case 'widget_a':
				#defaults
			break;
			case 'widget_b':
				$size = 'square-large';
			break;
			case 'widget_c':
				#no image
			break;
			case 'widget_d':
				$size = 'rectangle';
				$style = 'widget_c';
			break;
			case 'widget_e':
				$size = 'rectangle';
				$style = 'widget_c';
			break;
		}	
		
		#setup the query            
        $args=array('posts_per_page' => $numarticles, 'order' => 'DESC', 'ignore_sticky_posts' => true);
		
		$current_query_encoded = json_encode($args);	
		
		#add first section to query
		if(!empty($sections)) {
			$args['cat'] = $sections[0];	
			$link = get_category_link( $sections[0] );
		}		
		
		#setup loop format
		$format = array('loop' => $loop, 'location' => $location, 'layout' => $layout, 'sort' => 'recent', 'paged' => 1, 'thumbnail' => true, 'icon' => true, 'nonajax' => true, 'meta' => true, 'award' => false, 'badge' => false, 'excerpt' => false, 'authorship' => false, 'numarticles' => $numarticles, 'disable_ads' => true, 'size' => $size);	
			
		#setup sortbar	
		$sortbarargs = array('type' => 'sections', 'sections' => $sections, 'loop' => $loop, 'location' => $location, 'layout' => $layout, 'disabled_filters' => array(), 'numarticles' => 0, 'disable_filters' => false, 'disable_title' => true, 'thumbnail' => true, 'meta' => false, 'icon' => true, 'award' => false, 'badge' => false, 'excerpt' => false, 'authorship' => false, 'size' => $size, 'numarticles' => $numarticles);		
		
		#fetch the loop
		$loop = it_loop($args, $format); 
		    
        #Before widget (defined by themes)
        echo $before_widget;
		
		echo "<div class='post-container widgets-sections widget-panel compact-header " . $style . "' data-currentquery='" . $current_query_encoded . "'>";
		
			if(!empty($sections)) {

				#display the sortbar
				echo it_get_sortbar($sortbarargs);
				
				echo '<div class="content-inner clearfix">';
				
					echo '<div class="loading load-sort"><span class="theme-icon-spin2"></span></div>';	
					
					echo '<div class="loop">';
						
						echo $loop['content'];
						
						echo '<div class="load-more-wrapper compact add-active">';
							echo '<a class="load-more" href="' . esc_url($link) . '">' . __('View All',IT_TEXTDOMAIN) . '<span class="theme-icon-forward"></span></a>';
						echo '</div>';
						
					echo '</div>';	
					
				echo '</div>';
				
			} else {
				
				echo __('You need to choose at least one section to display in this widget',IT_TEXTDOMAIN);
				
			}
			
		echo '</div>';
		
		wp_reset_query();			
        
		# After widget (defined by themes)
        echo $after_widget; ?>		
		
	<?php
	}
	function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;
		
		$instance['title'] = strip_tags( $new_instance['title'] );			
		$instance['numarticles'] = strip_tags( $new_instance['numarticles'] );
		$instance['layout'] = strip_tags( $new_instance['layout'] );
		$categories = it_get_setting('categories');	 
		foreach($categories as $category) {
			if(is_array($category)) {
				if(array_key_exists('id',$category)) {
					$id = $category['id'];
					$formid = 'category_' . $id;
					if(!empty($id)) {
						$instance[$formid] = isset( $new_instance[$formid] );
					}
				}
			}
		}
		
		return $instance;
		
	}
	function form( $instance ) {	

		#set up some default widget settings.
		$defaults = array( 'title' => __('Sections', IT_TEXTDOMAIN), 'numarticles' => 4, 'layout' => 'widget_a' );	
		$categories = it_get_setting('categories');	 
		foreach($categories as $category) {
			if(is_array($category)) {
				if(array_key_exists('id',$category)) {
					$id = $category['id'];
					$formid = 'category_' . $id;
					if(!empty($id)) {
						$defaults[$formid] = true;
					}
				}
			}
		}	
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>	
        
        <p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:',IT_TEXTDOMAIN); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:160px" />
		</p>
        
        <p>                
			<?php _e( 'Display',IT_TEXTDOMAIN); ?>
			<input id="<?php echo $this->get_field_id( 'numarticles' ); ?>" name="<?php echo $this->get_field_name( 'numarticles' ); ?>" value="<?php echo $instance['numarticles']; ?>" style="width:30px" />  
			<?php _e( 'articles in each tab',IT_TEXTDOMAIN); ?>
		</p>
        
        <p>        
        	<?php #loop through all managed categories 
			foreach($categories as $category) {
				if(is_array($category)) {
					if(array_key_exists('id',$category)) {
						$id = $category['id'];
						if(!empty($id)) {
							$name = get_cat_name($id);
							$formid = 'category_' . $id;
							?>
							
							<input class="checkbox" type="checkbox" <?php checked(isset( $instance[$formid]) ? $instance[$formid] : 0  ); ?> id="<?php echo $this->get_field_id( $formid ); ?>" name="<?php echo $this->get_field_name( $formid ); ?>" />
							<label for="<?php echo $this->get_field_id( $formid ); ?>"><?php echo $name; ?></label><br />
							
							<?php
						}
					}
				}
			}
			?>        
        </p>         	
        
        <p><?php _e( 'Layout:',IT_TEXTDOMAIN); ?></p>	
                 
        <div style="position:relative;border:1px solid #DDD;background:#F7F7F7;height:100px;width:50px;padding:10px 10px 0px 10px;float:left;margin-right:10px;margin-bottom:10px;border-radius:5px;">
            <div style="float:left;margin-top:70px;margin-left:18px;position:absolute;">
            	<input class="radio" type="radio" <?php if($instance['layout']=='widget_a') { ?>checked <?php } ?>name="<?php echo $this->get_field_name( 'layout' ); ?>" value="widget_a" id="<?php echo $this->get_field_id( 'layout' ); ?>_widget_a" />  
            </div>
            <div style="float:left;">
            	<label for="<?php echo $this->get_field_id( 'layout' ); ?>_widget_a"><img src="<?php echo THEME_ADMIN_ASSETS_URI . '/images/post_layout_a.png' ?>" /></label>
            </div>
        </div>
          
        <div style="position:relative;border:1px solid #DDD;background:#F7F7F7;height:100px;width:50px;padding:10px 10px 0px 10px;margin-bottom:10px;margin-right:10px;float:left;border-radius:5px;">
            <div style="float:left;margin-top:70px;margin-left:18px;position:absolute;">
        		<input class="radio" type="radio" <?php if($instance['layout']=='widget_b') { ?>checked <?php } ?>name="<?php echo $this->get_field_name( 'layout' ); ?>" value="widget_b" id="<?php echo $this->get_field_id( 'layout' ); ?>_widget_b" /> 
        	</div>
            <div style="float:left;">               
        		<label for="<?php echo $this->get_field_id( 'layout' ); ?>_widget_b"><img src="<?php echo THEME_ADMIN_ASSETS_URI . '/images/post_layout_b.png' ?>" /></label>  
        	</div>
        </div>
        
        <div style="position:relative;border:1px solid #DDD;background:#F7F7F7;height:100px;width:50px;padding:10px 10px 0px 10px;float:left;margin-right:10px;margin-bottom:10px;border-radius:5px;">
            <div style="float:left;margin-top:70px;margin-left:18px;position:absolute;">
            	<input class="radio" type="radio" <?php if($instance['layout']=='widget_c') { ?>checked <?php } ?>name="<?php echo $this->get_field_name( 'layout' ); ?>" value="widget_c" id="<?php echo $this->get_field_id( 'layout' ); ?>_widget_c" />  
            </div>
            <div style="float:left;">
            	<label for="<?php echo $this->get_field_id( 'layout' ); ?>_widget_c"><img src="<?php echo THEME_ADMIN_ASSETS_URI . '/images/post_layout_c.png' ?>" /></label>
            </div>
        </div>
          
        <div style="position:relative;border:1px solid #DDD;background:#F7F7F7;height:100px;width:50px;padding:10px 10px 0px 10px;margin-bottom:10px;margin-right:10px;float:left;border-radius:5px;">
            <div style="float:left;margin-top:70px;margin-left:18px;position:absolute;">
        		<input class="radio" type="radio" <?php if($instance['layout']=='widget_d') { ?>checked <?php } ?>name="<?php echo $this->get_field_name( 'layout' ); ?>" value="widget_d" id="<?php echo $this->get_field_id( 'layout' ); ?>_widget_d" /> 
        	</div>
            <div style="float:left;">               
        		<label for="<?php echo $this->get_field_id( 'layout' ); ?>_widget_d"><img src="<?php echo THEME_ADMIN_ASSETS_URI . '/images/post_layout_d.png' ?>" /></label>  
        	</div>
        </div>  
        
        <div style="position:relative;border:1px solid #DDD;background:#F7F7F7;height:100px;width:50px;padding:10px 10px 0px 10px;margin-bottom:10px;margin-right:10px;float:left;border-radius:5px;">
            <div style="float:left;margin-top:70px;margin-left:18px;position:absolute;">
        		<input class="radio" type="radio" <?php if($instance['layout']=='widget_e') { ?>checked <?php } ?>name="<?php echo $this->get_field_name( 'layout' ); ?>" value="widget_e" id="<?php echo $this->get_field_id( 'layout' ); ?>_widget_e" /> 
        	</div>
            <div style="float:left;">               
        		<label for="<?php echo $this->get_field_id( 'layout' ); ?>_widget_e"><img src="<?php echo THEME_ADMIN_ASSETS_URI . '/images/post_layout_e.png' ?>" /></label>  
        	</div>
        </div> 
        
        <div style="clear:both;">&nbsp;</div> 
		
		<?php
	}
}
?>