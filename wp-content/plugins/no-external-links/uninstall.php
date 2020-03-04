<?php
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

require_once('no-external-links.php');

$nel_external_links = new no_external_links();
$nel_external_links->uninstall();