<?php
class it_section extends WP_Widget {
	function __construct() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'Section', 'description' => __( 'Displays latest posts in a centered style from one category with the category icon in a colored circle at the top.',IT_TEXTDOMAIN) );
		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'it_section' );
		/* Create the widget. */
		parent::__construct( 'it_section', 'Section (Single)', $widget_ops, $control_ops );
	}	
	function widget( $args, $instance ) {
	
		extract( $args );

		/* User-selected settings. */
		$selected_category = $instance['category'];		
		$numarticles = $instance['numarticles'];
		$show_category = $instance['show_category'];
		$show_more = $instance['show_more'];
		
		#clear out unselected values
		if($selected_category=='All Categories') $selected_category = '';			
		
		#setup the query            
        $args=array('posts_per_page' => $numarticles, 'cat' => $selected_category);
		
		#setup loop format
		$format = array('loop' => 'main', 'location' => 'section', 'numarticles' => $numarticles);				
			
		#fetch the loop
		$loop = it_loop($args, $format); 
		
		#get category icon
		$categoryargs = array('catid' => $selected_category, 'label' => false, 'icon' => true, 'white' => true, 'single' => true, 'wrapper' => false, 'id' => false, 'size' => 28);
		$cats = it_get_primary_categories($categoryargs);
		$csscat = $show_category ? '' : ' no-icon';
		
		$link = get_category_link($selected_category);
		    
        #Before widget (defined by themes)
        echo $before_widget;

        #HTML output
		echo '<div class="widget-panel widget-section add-active category-' . $selected_category . $csscat . '">'; 
			
			echo '<div class="content-inner">';
			
				echo '<a class="header-link" href="' . $link . '"></a>';
			
				if($show_category) echo '<div class="category-circle">' . $cats . '</div>';
				
				echo '<div class="category-name">' . get_cat_name($selected_category) . '</div>';
				
				echo '<div class="border"></div>';
				
				echo '<div class="loop">';
					   
					echo $loop['content'];
				
				echo '</div>';	
				
				if($show_more) echo '<a class="more-link" href="' . $link . '">' . __('More',IT_TEXTDOMAIN) . '<span class="theme-icon-forward"></span></a>';
				
			echo '</div>';
				
		echo '</div>';
		
		wp_reset_query();			
        
		# After widget (defined by themes)
        echo $after_widget; ?>		
		
	<?php
	}
	function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;
		
		$instance['category'] = strip_tags( $new_instance['category'] );
		$instance['numarticles'] = strip_tags( $new_instance['numarticles'] );
		$instance['show_category'] = isset( $new_instance['show_category'] );
		$instance['show_more'] = isset( $new_instance['show_more'] );				
		
		return $instance;
		
	}
	function form( $instance ) {	

		#set up some default widget settings.
		$defaults = array( 'category' => '', 'numarticles' => 4, 'show_category' => true, 'show_more' => true );		
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>	
        
        <p>
			<?php _e( 'Category:',IT_TEXTDOMAIN); ?>
			<select name="<?php echo $this->get_field_name( 'category' ); ?>">
				<?php 
				$catargs = array('orderby' => 'name', 'order' => 'ASC', 'hide_empty' => 0);
				$categories = get_categories($catargs);
				foreach($categories as $category){ ?>
                	<option<?php if($instance['category']==$category->term_id) { ?> selected<?php } ?> value="<?php echo $category->term_id; ?>"><?php echo $category->name; ?></option>
				<?php } ?>
			</select>
		</p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked(isset( $instance['show_category']) ? $instance['show_category'] : 0  ); ?> name="<?php echo $this->get_field_name('show_category'); ?>" id="<?php echo $this->get_field_id('show_category'); ?>" />
            <label for="<?php echo $this->get_field_id('show_category'); ?>"><?php _e('Show Category Icon',IT_TEXTDOMAIN); ?></label>
        </p>
        
        <p>                
			<?php _e( 'Display',IT_TEXTDOMAIN); ?>
			<input id="<?php echo $this->get_field_id( 'numarticles' ); ?>" name="<?php echo $this->get_field_name( 'numarticles' ); ?>" value="<?php echo $instance['numarticles']; ?>" style="width:30px" />  
			<?php _e( 'articles',IT_TEXTDOMAIN); ?>
		</p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked(isset( $instance['show_more']) ? $instance['show_more'] : 0  ); ?> name="<?php echo $this->get_field_name('show_more'); ?>" id="<?php echo $this->get_field_id('show_more'); ?>" />
            <label for="<?php echo $this->get_field_id('show_more'); ?>"><?php _e('Show More Link',IT_TEXTDOMAIN); ?></label>
        </p>
        
		
		<?php
	}
}
?>