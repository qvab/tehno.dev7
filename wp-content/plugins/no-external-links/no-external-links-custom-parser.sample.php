<?php
if (!defined('DB_NAME'))
    die('Error');

#include base parser
require_once('no-external-links-parser.php');

class no_external_links_custom_parser extends no_external_links_parser
{
    #let's redefine redirect function as a sample
    function redirect($url)
    {
        global $wp_rewrite, $wpdb, $hyper_cache_stop;
        // disable Hyper Cache plugin (http://www.satollo.net/plugins/hyper-cache) from caching this page
        $hyper_cache_stop = true;
        // disable WP Super Cache caching
        if (!defined('DONOTCACHEPAGE'))
            define('DONOTCACHEPAGE', 1);

        if ($this->options['base64']) {
            $url = base64_decode($url);
        } elseif ($this->options['maskurl']) {
            $sql = 'select url from ' . $wpdb->prefix . 'masklinks where id= %s limit 1';
            $url = $wpdb->get_var($wpdb->prepare($sql, addslashes($url)));
        }
        die('<a href="' . $url . '">just click the link!</a>');
    }
}
