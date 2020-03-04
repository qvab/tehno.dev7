<?php
class it_topics extends WP_Widget {
	function __construct() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'Topics', 'description' => __( 'Displays list of trending categories, tags, or both, ranked by heat index.',IT_TEXTDOMAIN) );
		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'it_topics' );
		/* Create the widget. */
		parent::__construct( 'it_topics', 'Topics', $widget_ops, $control_ops );
	}	
	function widget( $args, $instance ) {
	
		extract( $args );

		/* User-selected settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$numterms = $instance['numterms'];
		$show_categories = $instance['show_categories'];
		$show_tags = $instance['show_tags'];
		$taxarray = array();
		if($show_categories) $taxarray[] = 'category';
		if($show_tags) $taxarray[] = 'post_tag';
		
		$termargs = array('num' => $numterms, 'tax' => $taxarray);	
		$terms = it_get_trending_terms($termargs);
		
		$heatargs = array('icon' => true, 'tooltip' => false);
		    
        #Before widget (defined by themes)
        echo $before_widget;
		
		echo '<div class="widget-panel widget-topics">';

			echo '<div class="bar-header"><div class="bar-label">';
			
				echo '<div class="label-text">' . $title . '</div>';
			
			echo '</div></div>';
			
			echo '<div class="content-inner">';				
				
				$i = 0;
					
				foreach($terms as $term) { 
				
					$heatargs['termid'] = $term->term_id;	
					$name = $term->name;
					#if (mb_strlen($name)>12) $name = mb_substr($name, 0, 9) . "...";
				
					echo '<div class="topic-panel add-active">';					
					
						echo '<a class="topic-link" href="' . get_term_link($term) . '"></a>';
						
						echo '<div class="topic-name">' . $name . '</div>';
						
						echo '<div class="trending-bar bar-' . $i . '">';
						
							echo '<div class="trending-color-wrapper">';
								echo '<div class="trending-color-layer"></div>';
								echo '<div class="trending-color"></div>';
								echo '<div class="trending-meta">' . it_get_heat_index($heatargs) . '</div>';
							echo '</div>';
						
						echo '</div>';
					
					echo '</div>';	
					
					$i++;			
				
				}      
				
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
		$instance['numterms'] = strip_tags( $new_instance['numterms'] );
		$instance['show_categories'] = isset( $new_instance['show_categories'] );
		$instance['show_tags'] = isset( $new_instance['show_tags'] );		
		return $instance;
		
	}
	function form( $instance ) {	

		#set up some default widget settings.
		$defaults = array( 'title' => __('Trending Topics', IT_TEXTDOMAIN), 'numterms' => 7, 'show_categories' => true, 'show_tags' => true );		
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>	
        
        <p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:',IT_TEXTDOMAIN); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:160px" />
		</p>
        
		<p>                
			<?php _e( 'Display',IT_TEXTDOMAIN); ?>
			<input id="<?php echo $this->get_field_id( 'numterms' ); ?>" name="<?php echo $this->get_field_name( 'numterms' ); ?>" value="<?php echo $instance['numterms']; ?>" style="width:30px" />  
			<?php _e( 'terms',IT_TEXTDOMAIN); ?>
		</p>
        
        <p><?php _e( 'Included Terms:',IT_TEXTDOMAIN); ?></p>	
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked(isset( $instance['show_categories']) ? $instance['show_categories'] : 0  ); ?> name="<?php echo $this->get_field_name('show_categories'); ?>" id="<?php echo $this->get_field_id('show_categories'); ?>" />
            <label for="<?php echo $this->get_field_id('show_categories'); ?>"><?php _e('Categories',IT_TEXTDOMAIN); ?></label><br />        
                              
            <input class="checkbox" type="checkbox" <?php checked(isset( $instance['show_tags']) ? $instance['show_tags'] : 0  ); ?> name="<?php echo $this->get_field_name('show_tags'); ?>" id="<?php echo $this->get_field_id('show_tags'); ?>" />
            <label for="<?php echo $this->get_field_id('show_tags'); ?>"><?php _e('Tags',IT_TEXTDOMAIN); ?></label>
        </p>                
        
		<?php
	}
}
?>