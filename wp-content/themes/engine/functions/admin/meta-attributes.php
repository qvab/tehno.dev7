<?php
$mustread = it_get_setting('highlighted_label');
$mustread = empty($mustread) ? __( 'Must Read',IT_TEXTDOMAIN ) : $mustread;
$meta_boxes = array(
	'title' => sprintf( __( 'Attributes', IT_TEXTDOMAIN ), THEME_NAME ),
	'id' => 'it_post_attributes',
	'pages' => array( 'post' ),
	'callback' => '',
	'context' => 'side',
	'priority' => 'low',
	'fields' => array(
		array(
			'id' => IT_META_HIGHLIGHTED,	
			'options' => array('true' => __( 'Highlighted', IT_TEXTDOMAIN ) . ' ("' . $mustread . '" post)'),
			'type' => 'checkbox'
		)
	)
);

return array(
	'load' => true,
	'options' => $meta_boxes
);
?>