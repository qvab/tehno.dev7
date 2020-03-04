<?php
class it_follow_us extends WP_Widget {
	function __construct() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'Follow Us', 'description' => __( 'Displays your social badges as are defined in the theme options.',IT_TEXTDOMAIN) );
		/* Widget control settings. */
		$control_ops = array( 'width' => 290, 'height' => 350, 'id_base' => 'it_follow_us' );
		/* Create the widget. */
		parent::__construct( 'it_follow_us', 'Follow Us', $widget_ops, $control_ops );
	}	
	function widget( $args, $instance ) {		

		extract( $args );

		/* User-selected settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		    
        #Before widget (defined by themes)
        echo $before_widget;

		#HTML output
        echo '<div class="widget-panel widget-follow-us">';
		
			if(!empty($title)) {
		
				echo '<div class="bar-header"><div class="bar-label">';
					
					echo '<div class="label-text">' . $title . '</div>';
				
				echo '</div></div>';
				
			}					
			
			echo it_social_badges();
			
		echo '</div>';
        
		# After widget (defined by themes)
        echo $after_widget; ?>		
		
	<?php
	}
	function update( $new_instance, $old_instance ) {
			
		$instance = $old_instance;		
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		
		return $instance;
		
	}
	function form( $instance ) {	

		#set up some default widget settings.
		$defaults = array( 'title' => __('Follow Us', IT_TEXTDOMAIN) );	
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
        
        <p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:',IT_TEXTDOMAIN); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:160px" />
		</p>        
        
		<?php
	}
}
?>