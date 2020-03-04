<?php
/*
 * Linkate Posts
 */
 
 
	if (defined('ABSPATH') && defined('WP_UNINSTALL_PLUGIN')) {
		global $wpdb, $table_prefix;
    
		delete_option('linkate-posts');
		delete_option('linkate-posts-feed');
		delete_option('widget_rrm_linkate_posts');
		
    $table_name = $table_prefix . 'linkate_posts';
		$wpdb->query("DROP TABLE `$table_name`");
	}
