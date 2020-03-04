<?php
/*
  Plugin Name: WP No External Links
  Plugin URI: 
  Description: Convert all external links into internal.
  Version: 1.0.2
  Text Domain: no-external-links
  Author: nicolly
  Author URI: 
 */

if (!defined('DB_NAME'))
    die('Error');

define('NEL_I18N', 'no-external-links');
define('NEL_TABLE_LINKS_STATS', 'nel_links_stats');
define('NEL_TABLE_MASK_LINKS', 'nel_mask_links');

class no_external_links
{
    var $options;/*all plugin options*/
    function init_lang()
    {
        $plugin_dir = basename(dirname(__FILE__));
        load_plugin_textdomain(NEL_I18N, false, $plugin_dir . '/lang');
    }

    function activate()
    {
        global $wpdb;

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $charset_collate = $wpdb->get_charset_collate();

        $table_name = $wpdb->prefix . NEL_TABLE_LINKS_STATS;

        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `url` VARCHAR(255),
            `date` DATETIME NOT NULL,
            PRIMARY KEY (`ID`)
        ) $charset_collate;";
        dbDelta($sql);

        $table_name = $wpdb->prefix . NEL_TABLE_MASK_LINKS;
        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `url` varchar(255) DEFAULT NULL,
            `url_hash` varchar(32) DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `url_hash` (`url_hash`),
            KEY `url` (`url`)
        ) $charset_collate;";
        dbDelta($sql);
    }

    function uninstall()
    {
        global $wpdb;
        $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . NEL_TABLE_LINKS_STATS );
        $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . NEL_TABLE_MASK_LINKS );
    }

    function update_options()
    {
        $opt = $this->GetOptionInfo();
        foreach ($opt as $key => $arr) {
            $name = $arr['new_name'];
            if (!isset($this->options[$name]))
                $this->options[$name] = '0';//for damn checkboxes
        }

        foreach ($this->options as $i => $val)
            $this->options[$i] = stripslashes($val);
        $r = update_option('no_external_links', $this->options);
        if (!$r) {
            if (serialize($this->options) != serialize(get_option('no_external_links'))) {
                init_lang();
                echo '<div class="error">' . __('Failed to update options!', NEL_I18N) . '</div>';
            }
            /*else echo 'nothing changed ;_;';*/
        }
    }

    function GetOptionInfo()
    {
        return array(
            array('new_name' => 'mask_mine', 'def_value' => 1, 'type' => 'chk', 'name' => __('Mask links in posts and pages', NEL_I18N), 'grp' => 'what'),
            array('new_name' => 'mask_comment', 'def_value' => 1, 'type' => 'chk', 'name' => __('Mask links in comments', NEL_I18N), 'grp' => 'what'),
            array('new_name' => 'mask_author', 'def_value' => 1, 'type' => 'chk', 'name' => __('Mask comments authors`s homepage links', NEL_I18N), 'grp' => 'what'),
            array('new_name' => 'mask_rss', 'def_value' => 0, 'type' => 'chk', 'name' => __('Mask links in your RSS post content', NEL_I18N) . ' ' . __('(may result in invalid RSS if used with some masking options)', NEL_I18N), 'grp' => 'what'),
            array('new_name' => 'mask_rss_comment', 'def_value' => 0, 'type' => 'chk', 'name' => __('Mask links in RSS comments', NEL_I18N) . ' ' . __('(may result in invalid RSS if used with some masking options)', NEL_I18N), 'grp' => 'what'),
            array('new_name' => 'add_nofollow', 'def_value' => 1, 'type' => 'chk', 'name' => __('Add <b>rel=nofollow</b> for masked links (for google)', NEL_I18N), 'grp' => 'common'),
            array('new_name' => 'add_blank', 'def_value' => 1, 'type' => 'chk', 'name' => __('Add <b>target="blank"</b> for all links to other sites (links will open in new window)', NEL_I18N), 'grp' => 'common'),
            array('new_name' => 'put_noindex', 'def_value' => 0, 'type' => 'chk', 'name' => __('Surround masked links with <b>&lt;noindex&gt;link&lt;/noindex&gt;</b> tag (for yandex search engine)', NEL_I18N), 'grp' => 'common'),
            array('new_name' => 'put_noindex_comment', 'def_value' => 0, 'type' => 'chk', 'name' => __('Surround masked links with comment <b>&lt;!--noindex--&gt;link&lt;!--/noindex--&gt;</b> (for yandex search engine, better then noindex tag because valid)', NEL_I18N), 'grp' => 'common'),
            //array('new_name' => 'disable_mask_links', 'def_value' => 0, 'type' => 'chk', 'name' => __('No redirect', NEL_I18N), 'grp' => 'type'),
            array('new_name' => 'LINK_SEP', 'def_value' => 'goto', 'type' => 'txt', 'name' => __('Link separator for redirects (default="goto")', NEL_I18N), 'grp' => 'common'),
            array('new_name' => 'exclude_links', 'def_value' => '', 'type' => 'text', 'name' => __('Exclude URLs that you don`t want to mask (all urls, beginning with those, won`t be masked).', NEL_I18N).'<br/>● '.__('Put one adress on each line, including protocol prefix (for example,', NEL_I18N) . ' "<b>http://</b>likebtn.com" ' . __('or', NEL_I18N) . ' <b>https://</b>google.com ' . __('or', NEL_I18N) . ' <b>ftp://</b>microsoft.com). ' .'<br>● '. __('Skype, javascript and mail links are excluded by default.', NEL_I18N).'<br/>● '.__('To exclude full protocol, just add line with it`s prefix - for example,', NEL_I18N) . ' "<b>ftp://</b>" '.'<br>● '. __('Please note that domains with "www" and without it are considered different. So if you want to disable masking for "pinterest.com" and "www.pinterest.com", you should specify both domains', NEL_I18N), 'grp' => 'exclude'),
            array('new_name' => 'fullmask', 'def_value' => '', 'type' => 'chk', 'name' => __('Mask ALL links on the website (can slow down your blog and conflict with some cache and other plugins. Not recommended).', NEL_I18N), 'grp' => 'what'),
            array('new_name' => 'stats', 'def_value' => 0, 'type' => 'chk', 'name' => __('Log all outgoing clicks.', NEL_I18N), 'grp' => 'common'),
            array('new_name' => 'keep_stats', 'def_value' => 30, 'type' => 'txt', 'name' => __('Days to keep clicks stats', NEL_I18N), 'grp' => 'common'),
            array('new_name' => 'no302', 'def_value' => 0, 'type' => 'chk', 'name' => __('Use javascript redirect', NEL_I18N).' ('.__('if you want links with # symbol to redirect correctly, make sure to enable base64 encoding for links', NEL_I18N).')', 'grp' => 'type'),
            array('new_name' => 'redtime', 'def_value' => 3, 'type' => 'txt', 'name' => __('Redirect time (seconds)', NEL_I18N), 'grp' => 'java'),
            array('new_name' => 'redtxt', 'def_value' => 'This page demonstrates link redirect with "No External Links" plugin. You will be redirected in 3 seconds. Otherwise, please click on <a href="LINKURL">this link</a>.', 'type' => 'text', 'name' => __('Custom redirect text. Use word "LINKURL" where you want to use redirect url. For example, <b>CLICK &lt;a href="LINKURL"&gt;HERE NOW&lt;/a&gt;</b>', NEL_I18N), 'grp' => 'java'),
            array('new_name' => 'noforauth', 'def_value' => 0, 'type' => 'chk', 'name' => __('Do not mask links when registered users visit site', NEL_I18N), 'grp' => 'exclude'),
            array('new_name' => 'maskurl', 'def_value' => 0, 'type' => 'chk', 'name' => __('Mask url with special numeric code. Be careful, this option may slow down your blog.', NEL_I18N), 'grp' => 'encode'),
            array('new_name' => 'remove_links', 'def_value' => 0, 'type' => 'chk', 'name' => __('Completely remove links from your posts.', NEL_I18N), 'grp' => 'common'),
            array('new_name' => 'link2text', 'def_value' => 0, 'type' => 'chk', 'name' => __('Turn all links into text.', NEL_I18N), 'grp' => 'common'),
            array('new_name' => 'base64', 'def_value' => 0, 'type' => 'chk', 'name' => __('Use base64 encoding for links.', NEL_I18N), 'grp' => 'encode'),
            array('new_name' => 'debug', 'def_value' => 0, 'type' => 'chk', 'name' => __('Debug mode (Adds comments lines like "&lt;!--no-external-links debug: some info--&gt;" to output. For testing only!)', NEL_I18N), 'grp' => 'common'),
            array('new_name' => 'restrict_referer', 'def_value' => 1, 'type' => 'chk', 'name' => __('Check for document referer and restrict redirect if it is not your own web site. Useful against spoofing attacks. User will be redirected to main page of your web site.', NEL_I18N), 'grp' => 'common'),
            array('new_name' => 'dont_mask_admin_follow', 'def_value' => 0, 'type' => 'chk', 'name' => __('Do not mask links which have <b>rel="follow"</b> atribute and are posted by admin', NEL_I18N), 'grp' => 'exclude'),
        );
    }

    function load_options()
    {
        global $wpdb;
        $opt = $this->GetOptionInfo();
        $update = false;
        $this->options = get_option('no_external_links');
        if (!$this->options)
            $this->options = array();
        /*check if options are fine*/
        foreach ($opt as $key => $arr) {
            $name = $arr['new_name'];
            if (!isset($this->options[$name]) && $arr['def_value'])/* no option value, but it should be*/ {
                /*try to get old version*/
                if ($arr['old_name']) {
                    $val = get_option($arr['old_name'], 'omg');
                    /*set default value. we can't use default false return because user value could be set to false*/
                    if ($val === 'omg')
                        $val = $arr['def_value'];
                } else
                    $val = $arr['def_value'];
                $this->options[$name] = $val;
                $update = true;
            }
        }


        if ($update)/*upgrade or just some kind of shit*/ {
            /*if we're going back from old version - let's check for excludes...*/
            if (!$this->options['exclude_links']) {
                $val = get_option('noexternallinks_exclude_links');
                if ($val)
                    $this->options['exclude_links'] = $val;
            }
            $this->update_options();
        }
        /*add values to exclude*/
        $exclude_links = array();
        $site = get_option('home');
        if (!$site)
            $site = get_option('siteurl');
        $this->options['site'] = $site;
        $site = str_replace(array("http://", "https://"), '', $site);
        $p = strpos($site, '/');
        if ($p !== FALSE)
            $site = substr($site, 0, $p);/*site root is excluded from masking, not only blog url*/
        $exclude_links[] = "http://" . $site;
        $exclude_links[] = "https://" . $site;
        $exclude_links[] = 'javascript';
        $exclude_links[] = 'mailto';
        $exclude_links[] = 'skype';
        $exclude_links[] = '/';/* for relative links*/
        $exclude_links[] = '#';/*for internal links*/

        $a = @explode("\n", $this->options['exclude_links']);
        for ($i = 0; $i < sizeof($a); $i++)
            $a[$i] = trim($a[$i]);
        $this->options['exclude_links_'] = @array_merge($exclude_links, $a);

        /*statistic*/
        if ($this->options['stats']) {
            $flush = get_option('no_external_links_flush');
            if (!$flush || $flush < time() - 3600 * 24)/*flush every 24 hours*/ {
                $sql = 'delete from ' . $wpdb->prefix . NEL_TABLE_LINKS_STATS.' where `date`<DATE_SUB(curdate(), INTERVAL %d DAY)';
                $wpdb->query($wpdb->prepare($sql, $this->options['keep_stats']));
                update_option('no_external_links_flush', time());
            }
        }
    }
}

function nel_activation()
{
    require_once(plugin_dir_path(__FILE__) . 'no-external-links-options.php');
    $no_external_links_admin = new no_external_links_admin();
    $no_external_links_admin->activate();
}
register_activation_hook(__FILE__, 'nel_activation');

$upload_dir = wp_upload_dir();

if (is_admin()) {
    require_once(plugin_dir_path(__FILE__) . 'no-external-links-options.php');
    new no_external_links_admin();
} elseif (file_exists($upload_dir['basedir'] . '/custom-parser.php')) {
    require_once($upload_dir['basedir'] . '/custom-parser.php');
    if (class_exists('custom_parser')) {
        new custom_parser();
    } else {
        echo '<div class="error">' . __('Custom parser file found but <b>custom_parser</b> class not defined!', NEL_I18N) . '</div>';
    }
} else {
    require_once(plugin_dir_path(__FILE__) . 'no-external-links-parser.php');
    new no_external_links_parser();
}
