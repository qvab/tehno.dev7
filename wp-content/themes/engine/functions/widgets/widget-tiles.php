<?php
class it_tiles extends WP_Widget {
	function __construct() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'Category Tiles', 'description' => __( 'Displays a grid of your categories with icons.',IT_TEXTDOMAIN) );
		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'it_tiles' );
		/* Create the widget. */
		parent::__construct( 'it_tiles', 'Category Tiles', $widget_ops, $control_ops );
	}	
	function widget( $args, $instance ) {		
		
		extract( $args );

		/* User-selected settings. */	
		$title = apply_filters('widget_title', $instance['title'] );
		$category_ids = array();
		$categories = it_get_setting('categories');		
		foreach($categories as $category) {
			if(is_array($category)) {
				if(array_key_exists('id',$category)) {
					$id = $category['id'];
					$formid = 'category_' . $id;
					if(!empty($id)) {
						if($instance[$formid]) $category_ids[] = $id;
					}
				}
			}
		}
		
		$tileargs = array('ids' => $category_ids, 'cols' => 2);
		
		$tiles = it_get_tiles($tileargs);
				    
        #Before widget (defined by themes)
        echo $before_widget;
		
		echo '<div class="widget-panel widget-tiles">';

			echo '<div class="bar-header"><div class="bar-label">';
			
				echo '<div class="label-text">' . $title . '</div>';
			
			echo '</div></div>';
			
			echo '<div class="content-inner clearfix">';				
				
				echo $tiles;    
				
			echo '</div>';
			
		echo '</div>';
		
		wp_reset_query();			
        
		# After widget (defined by themes)
        echo $after_widget; ?>		
		
	<?php
	}
	function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;
		
		$instance['title'] = strip_tags( $new_instance['title'] );
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
		$defaults = array( 'title' => __('Sections', IT_TEXTDOMAIN) );	
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
       		
		<?php
	}
}
?>