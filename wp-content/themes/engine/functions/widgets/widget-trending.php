<?php
class it_trending extends WP_Widget {
	function __construct() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'Trending Articles', 'description' => __( 'Displays sortable trending articles in bar graph style with metric counts.',IT_TEXTDOMAIN) );
		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'it_trending' );
		/* Create the widget. */
		parent::__construct( 'it_trending', 'Trending Articles', $widget_ops, $control_ops );
	}	
	function widget( $args, $instance ) {
	
		extract( $args );

		/* User-selected settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$disable_heat = $instance['disable_heat'];
		$disable_liked = $instance['disable_liked'];
		$disable_viewed = $instance['disable_viewed'];
		$disable_reviewed = $instance['disable_reviewed'];
		$disable_rated = $instance['disable_rated'];
		$disable_commented = $instance['disable_commented'];
		$selected_category = $instance['category'];
		$selected_tag = $instance['tag'];
		$numarticles = $instance['numarticles'];
		$timeperiod = $instance['timeperiod'];
		$timeperiod_label = it_timeperiod_label($timeperiod);
		if(empty($timeperiod_label)) $timeperiod_label = 'This Month';
		$trending_label = it_get_setting("trending_label");
		$trending_label = ( !empty( $trending_label ) ) ? $trending_label : __('Trending', IT_TEXTDOMAIN);
		$trending_label .= ' ' . $timeperiod_label;
		if(empty($title)) $title = $trending_label;	
		
		#clear out unselected values
		if($selected_category=='All Categories') $selected_category = '';		
		if($selected_tag=='All Tags') $selected_tag = '';	
		
		#setup the query            
        $args=array('posts_per_page' => $numarticles, 'orderby' => 'meta_value_num', 'meta_key' => IT_HEAT_INDEX, 'cat' => $selected_category, 'tag_id' => $selected_tag);
		
		#setup loop format
		$format = array('loop' => 'trending', 'nonajax' => true, 'numarticles' => $numarticles, 'metric' => 'heat');	
				
		$disabled_filters = array('recent', 'awarded', 'title');
		if($disable_heat) $disabled_filters[] = 'heat';
		if($disable_liked) $disabled_filters[] = 'liked';
		if($disable_viewed) $disabled_filters[] = 'viewed';
		if($disable_reviewed) $disabled_filters[] = 'reviewed';
		if($disable_rated) $disabled_filters[] = 'rated';
		if($disable_commented) $disabled_filters[] = 'commented';
		
		#adjust args for default filter
		$setup_filters = it_setup_filters($disabled_filters, $args, $format);
		$default_metric = $setup_filters['default_metric'];
		$default_label = $setup_filters['default_label'];
		$args = $setup_filters['args'];
		$format = $setup_filters['format'];
		
		$sortbarargs = array('title' => $title, 'loop' => 'trending', 'location' => '', 'cols' => 1, 'class' => '', 'disabled_filters' => $disabled_filters, 'numarticles' => $numarticles, 'disable_filters' => false, 'disable_title' => false, 'prefix' => false, 'thumbnail' => false, 'rating' => false, 'meta' => true, 'award' => false, 'badge' => false, 'excerpt' => false, 'authorship' => false, 'icon' => false, 'timeperiod' => $timeperiod, 'metric_text' => $default_label);
		
		$week = date('W');
		$month = date('n');
		$year = date('Y');
		switch($timeperiod) {
			case 'This Week':
				$args['year'] = $year;
				$args['w'] = $week;
				$timeperiod='';
			break;	
			case 'This Month':
				$args['monthnum'] = $month;
				$args['year'] = $year;
				$timeperiod='';
			break;
			case 'This Year':
				$args['year'] = $year;
				$timeperiod='';
			break;
			case 'all':
				$timeperiod='';
			break;			
		}
		
		#encode current query for ajax purposes
		$current_query = array();
		if(!empty($selected_category)) $current_query['category__in'] = array($selected_category);
		if(!empty($selected_tag)) $current_query['tag__in'] = array($selected_tag);
		$current_query_encoded = json_encode($current_query);
		
		#fetch the loop
		$loop = it_loop($args, $format, $timeperiod); 
		    
        #Before widget (defined by themes)
        echo $before_widget;
		
		echo "<div class='post-container widget-panel widget-trending' data-currentquery='" . $current_query_encoded . "'>";

			#display the sortbar
			echo it_get_sortbar($sortbarargs);
			
			echo '<div class="content-inner">';
			
				echo '<div class="loading load-sort"><span class="theme-icon-spin2"></span></div>';	
				
				echo '<div class="loop list">';
					
					echo $loop['content'];
					
				echo '</div>';
				
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
		$instance['category'] = strip_tags( $new_instance['category'] );
		$instance['tag'] = strip_tags( $new_instance['tag'] );	
		$instance['numarticles'] = strip_tags( $new_instance['numarticles'] );
		$instance['timeperiod'] = strip_tags( $new_instance['timeperiod'] );
		$instance['disable_heat'] = isset( $new_instance['disable_heat'] );
		$instance['disable_liked'] = isset( $new_instance['disable_liked'] );
		$instance['disable_viewed'] = isset( $new_instance['disable_viewed'] );
		$instance['disable_reviewed'] = isset( $new_instance['disable_reviewed'] );
		$instance['disable_rated'] = isset( $new_instance['disable_rated'] );
		$instance['disable_commented'] = isset( $new_instance['disable_commented'] );
		
		return $instance;
		
	}
	function form( $instance ) {	

		#set up some default widget settings.
		$defaults = array( 'title' => __('Trending', IT_TEXTDOMAIN), 'category' => 'All Categories', 'tag' => 'All Tags', 'numarticles' => 8, 'disable_heat' => false, 'disable_liked' => false, 'disable_viewed' => false, 'disable_reviewed' => false, 'disable_rated' => false, 'disable_commented' => false, 'timeperiod' => 'This Year' );		
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>	
        
        <p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:',IT_TEXTDOMAIN); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:160px" />
		</p>
        
        <p>
			<?php _e( 'Category:',IT_TEXTDOMAIN); ?>
			<select name="<?php echo $this->get_field_name( 'category' ); ?>">
				<option<?php if($instance['category']=='All Categories') { ?> selected<?php } ?> value="All Categories"><?php _e( 'All Categories', IT_TEXTDOMAIN ); ?></option>
				<?php 
				$catargs = array('orderby' => 'name', 'order' => 'ASC', 'hide_empty' => 0);
				$categories = get_categories($catargs);
				foreach($categories as $category){ ?>
                	<option<?php if($instance['category']==$category->term_id) { ?> selected<?php } ?> value="<?php echo $category->term_id; ?>"><?php echo $category->name; ?></option>
				<?php } ?>
			</select>
		</p>
        
         <p>
			<?php _e( 'Tag:',IT_TEXTDOMAIN); ?>
			<select name="<?php echo $this->get_field_name( 'tag' ); ?>">
				<option<?php if($instance['tag']=='All Tags') { ?> selected<?php } ?> value="All Tags"><?php _e( 'All Tags', IT_TEXTDOMAIN ); ?></option>
				<?php 
				$tagargs = array('orderby' => 'name', 'order' => 'ASC', 'hide_empty' => 0);
				$tags = get_tags($tagargs);
				foreach($tags as $tag){ ?>
                	<option<?php if($instance['tag']==$tag->term_id) { ?> selected<?php } ?> value="<?php echo $tag->term_id; ?>"><?php echo $tag->name; ?></option>
				<?php } ?>
			</select>
		</p>	
	
		<p>                
			<?php _e( 'Display',IT_TEXTDOMAIN); ?>
			<input id="<?php echo $this->get_field_id( 'numarticles' ); ?>" name="<?php echo $this->get_field_name( 'numarticles' ); ?>" value="<?php echo $instance['numarticles']; ?>" style="width:30px" />  
			<?php _e( 'articles',IT_TEXTDOMAIN); ?>
		</p>
        
        <p><?php _e( 'Disable Filters:',IT_TEXTDOMAIN); ?></p>	
        
        <input class="checkbox" type="checkbox" <?php checked(isset( $instance['disable_heat']) ? $instance['disable_heat'] : 0  ); ?> name="<?php echo $this->get_field_name('disable_heat'); ?>" id="<?php echo $this->get_field_id('disable_heat'); ?>" />
        <label for="<?php echo $this->get_field_id('disable_heat'); ?>"><?php _e('Heat Index',IT_TEXTDOMAIN); ?></label><br />        
                          
        <input class="checkbox" type="checkbox" <?php checked(isset( $instance['disable_liked']) ? $instance['disable_liked'] : 0  ); ?> name="<?php echo $this->get_field_name('disable_liked'); ?>" id="<?php echo $this->get_field_id('disable_liked'); ?>" />
        <label for="<?php echo $this->get_field_id('disable_liked'); ?>"><?php _e('Liked',IT_TEXTDOMAIN); ?></label><br />
                      
        <input class="checkbox" type="checkbox" <?php checked(isset( $instance['disable_viewed']) ? $instance['disable_viewed'] : 0  ); ?> name="<?php echo $this->get_field_name('disable_viewed'); ?>" id="<?php echo $this->get_field_id('disable_viewed'); ?>" />
        <label for="<?php echo $this->get_field_id('disable_viewed'); ?>"><?php _e('Viewed',IT_TEXTDOMAIN); ?></label><br />
                     
        <input class="checkbox" type="checkbox" <?php checked(isset( $instance['disable_reviewed']) ? $instance['disable_reviewed'] : 0  ); ?> name="<?php echo $this->get_field_name('disable_reviewed'); ?>" id="<?php echo $this->get_field_id('disable_reviewed'); ?>" />
        <label for="<?php echo $this->get_field_id('disable_reviewed'); ?>"><?php _e('Reviewed',IT_TEXTDOMAIN); ?></label><br />
                      
        <input class="checkbox" type="checkbox" <?php checked(isset( $instance['disable_rated']) ? $instance['disable_rated'] : 0  ); ?> name="<?php echo $this->get_field_name('disable_rated'); ?>" id="<?php echo $this->get_field_id('disable_rated'); ?>" />
        <label for="<?php echo $this->get_field_id('disable_rated'); ?>"><?php _e('Rated',IT_TEXTDOMAIN); ?></label><br />
                       
        <input class="checkbox" type="checkbox" <?php checked(isset( $instance['disable_commented']) ? $instance['disable_commented'] : 0  ); ?> name="<?php echo $this->get_field_name('disable_commented'); ?>" id="<?php echo $this->get_field_id('disable_commented'); ?>" />
        <label for="<?php echo $this->get_field_id('disable_commented'); ?>"><?php _e('Commented',IT_TEXTDOMAIN); ?></label>  
        
        <p>
			<?php _e( 'Time Period:',IT_TEXTDOMAIN); ?>
			<select name="<?php echo $this->get_field_name( 'timeperiod' ); ?>">
                <option<?php if($instance['timeperiod']=='This Week') { ?> selected<?php } ?> value="This Week"><?php _e( 'This Week', IT_TEXTDOMAIN ); ?></option>
				<option<?php if($instance['timeperiod']=='This Month') { ?> selected<?php } ?> value="This Month"><?php _e( 'This Month', IT_TEXTDOMAIN ); ?></option>
                <option<?php if($instance['timeperiod']=='This Year') { ?> selected<?php } ?> value="This Year"><?php _e( 'This Year', IT_TEXTDOMAIN ); ?></option>
                <option<?php if($instance['timeperiod']=='-7 days') { ?> selected<?php } ?> value="-7 days"><?php _e( 'Within Past Week', IT_TEXTDOMAIN ); ?></option>
                <option<?php if($instance['timeperiod']=='-30 days') { ?> selected<?php } ?> value="-30 days"><?php _e( 'Within Past Month', IT_TEXTDOMAIN ); ?></option>
                <option<?php if($instance['timeperiod']=='-60 days') { ?> selected<?php } ?> value="-60 days"><?php _e( 'Within Past 2 Months', IT_TEXTDOMAIN ); ?></option>
                <option<?php if($instance['timeperiod']=='-90 days') { ?> selected<?php } ?> value="-90 days"><?php _e( 'Within Past 3 Months', IT_TEXTDOMAIN ); ?></option>
                <option<?php if($instance['timeperiod']=='-180 days') { ?> selected<?php } ?> value="-180 days"><?php _e( 'Within Past 6 Months', IT_TEXTDOMAIN ); ?></option>
                <option<?php if($instance['timeperiod']=='-365 days') { ?> selected<?php } ?> value="-365 days"><?php _e( 'Within Past Year', IT_TEXTDOMAIN ); ?></option>
                <option<?php if($instance['timeperiod']=='all') { ?> selected<?php } ?> value="all"><?php _e( 'All Time', IT_TEXTDOMAIN ); ?></option>
			</select>
		</p>
        
		<?php
	}
}
?>