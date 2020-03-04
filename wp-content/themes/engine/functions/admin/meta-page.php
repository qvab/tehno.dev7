<?php
$meta_boxes = array(
	'title' => sprintf( __( 'Layout Options', IT_TEXTDOMAIN ), THEME_NAME ),
	'id' => 'it_page_meta_box',
	'pages' => array( 'page' ),
	'callback' => '',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(			
		array(
			'name' => __( 'Sidebar Layout', IT_TEXTDOMAIN ),
			'id' => '_sidebar_layout',
			'options' => array(
				'right' => THEME_ADMIN_ASSETS_URI . '/images/sidebar_layout_right.png',
				'left' => THEME_ADMIN_ASSETS_URI . '/images/sidebar_layout_left.png',
				'full' => THEME_ADMIN_ASSETS_URI . '/images/sidebar_layout_full.png',
			),
			'type' => 'layout'
		),
		array(
			'name' => __( 'Disable View Count', IT_TEXTDOMAIN ),
			'id' => '_view_count_disable',
			'options' => array( 'true' => __( 'Do not display the view count at the top of this page.', IT_TEXTDOMAIN ) ),
			'type' => 'checkbox'
		),
		array(
			'name' => __( 'Disable Like Count', IT_TEXTDOMAIN ),
			'id' => '_like_count_disable',
			'options' => array( 'true' => __( 'Do not display the like button/count at the top of this page.', IT_TEXTDOMAIN ) ),
			'type' => 'checkbox'
		),
		array(
			'name' => __( 'Disable Sharing', IT_TEXTDOMAIN ),
			'id' => '_sharing_disable',
			'options' => array( 'true' => __( 'Disable the +AddThis social sharing buttons', IT_TEXTDOMAIN ) ),
			'type' => 'checkbox'
		),
		array(
			'name' => __( 'Disable Comparison', IT_TEXTDOMAIN ),
			'id' => IT_META_DISABLE_COMPARISON,
			'options' => array( 'true' => __( 'Do not allow this post to be compared.', IT_TEXTDOMAIN ) ),
			'type' => 'checkbox'
		),
		array(
			'name' => __( 'Disable Featured Image', IT_TEXTDOMAIN ),
			'id' => '_image_disable',
			'options' => array( 'true' => __( 'Do not display the featured image for this post.', IT_TEXTDOMAIN ) ),
			'type' => 'checkbox'
		),
		array(
			'name' => __( 'Display Featured Image', IT_TEXTDOMAIN ),
			'id' => '_image_display',
			'options' => array( 'true' => __( 'Force display featured image even if it is disabled site-wide.', IT_TEXTDOMAIN ) ),
			'type' => 'checkbox'
		),
		array(
			'name' => __( 'Disable Title', IT_TEXTDOMAIN ),
			'id' => IT_META_DISABLE_TITLE,
			'options' => array( 'true' => __( 'Do not display the main page title for this page.', IT_TEXTDOMAIN ) ),
			'type' => 'checkbox'
		),
		array(
			'name' => __( 'Custom Sidebar', IT_TEXTDOMAIN ),
			'desc' => __( "Select the custom sidebar that you'd like to be displayed on this page. Note: You will need to first create a custom sidebar under the Sidebar tab in the theme options panel before it will show up here.", IT_TEXTDOMAIN ),
			'id' => '_custom_sidebar',
			'target' => 'custom_sidebars',
			'type' => 'select'
		),
		array(
			'name' => __( 'Subtitle', IT_TEXTDOMAIN ),
			'desc' => __( 'You can specify a subtitle for this page which will display in the container bar above the page title and content.', IT_TEXTDOMAIN ),
			'id' => '_subtitle',
			'type' => 'text'
		),	
		array(
			'name' => __( 'Display Contents Menu', IT_TEXTDOMAIN ),
			'id' => '_contents_menu',
			'options' => array( 'true' => __( 'Show a contents menu for this page',IT_TEXTDOMAIN) ),
			'type' => 'checkbox'
		),	
		array(
			'name' => __( 'Category Front Page', IT_TEXTDOMAIN ),
			'desc' => __( 'Use this page as the layout for archives within the selected category.', IT_TEXTDOMAIN ),
			'id' => '_category_frontpage',
			'target' => 'cat',
			'type' => 'select'
		),		
		array(
			'name' => __( 'Background Color', IT_TEXTDOMAIN ),
			'desc' => __( 'Use a specific background color for this page', IT_TEXTDOMAIN ),
			'id' => '_bg_color',
			'default' => '000000',
			'type' => 'color'
		),		
		array(
			'name' => __( 'Override Site Background', IT_TEXTDOMAIN ),
			'desc' => __( 'This is useful if you have an image as your main site background but you want this color to show instead for this page', IT_TEXTDOMAIN ),
			'id' => '_bg_color_override',
			'options' => array( 'true' => __( 'Display this color instead of your main site background image', IT_TEXTDOMAIN ) ),
			'type' => 'checkbox'
		),
		array(
			'name' => __( 'Background Image', IT_TEXTDOMAIN ),
			'desc' => __( 'Use an image for the background of this specific page', IT_TEXTDOMAIN ),
			'id' => '_bg_image',
			'type' => 'upload'
		),	
		array(
			'name' => __( 'Background Position', IT_TEXTDOMAIN ),
			'id' => '_bg_position',
			'options' => array( 
				'' => __( 'Not Set (use value from theme options)', IT_TEXTDOMAIN),
				'left' => __( 'Left', IT_TEXTDOMAIN ),
				'center' => __( 'Center', IT_TEXTDOMAIN ),
				'right' => __( 'Right', IT_TEXTDOMAIN )
			),
			'default' => 'center',
			'type' => 'radio'
		),		
		array(
			'name' => __( 'Background Repeat', IT_TEXTDOMAIN ),
			'id' => '_bg_repeat',
			'options' => array( 
				'' => __( 'Not Set (use value from theme options)', IT_TEXTDOMAIN),
				'no-repeat' => __( 'No Repeat', IT_TEXTDOMAIN ),
				'repeat' => __( 'Tile', IT_TEXTDOMAIN ),
				'repeat-x' => __( 'Tile Horizontally', IT_TEXTDOMAIN ),
				'repeat-y' => __( 'Tile Vertically', IT_TEXTDOMAIN )
			),
			'default' => 'no-repeat',
			'type' => 'radio'
		),	
		array(
			'name' => __( 'Background Attachment', IT_TEXTDOMAIN ),
			'id' => '_bg_attachment',
			'options' => array( 
				'' => __( 'Not Set (use value from theme options)', IT_TEXTDOMAIN),
				'scroll' => __( 'Scroll', IT_TEXTDOMAIN ),
				'fixed' => __( 'Fixed', IT_TEXTDOMAIN )
			),
			'default' => 'scroll',
			'type' => 'radio'
		),
		array(
			'name' => __( 'Unwrap Page', IT_TEXTDOMAIN ),
			'desc' => __( 'This is useful if you are using page builders on this page and you want the builders to appear as separate content panels instead of all wrapped together in the same content panel.', IT_TEXTDOMAIN ),
			'id' => '_unwrap_page',
			'options' => array( 'true' => __( 'Do not contain the page within a content panel "wrapper"', IT_TEXTDOMAIN ) ),
			'type' => 'checkbox'
		),		
	)
);
return array(
	'load' => true,
	'options' => $meta_boxes
);

?>
