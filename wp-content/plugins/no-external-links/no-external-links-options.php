<?php
if (!defined('DB_NAME'))
    die('Error');

class no_external_links_admin extends no_external_links
{
    function __construct()
    {
        $this->init_lang();
        $this->load_options();
        add_action('save_post', array($this, 'save_postdata'));
        add_action('do_meta_boxes', array($this, 'add_custom_box'), 15, 2);
        add_action('admin_menu', array($this, 'modify_menu'));
    }

    function save_postdata($post_id)
    {
        if (!wp_verify_nonce($_REQUEST['no_external_links_noncename'], plugin_basename(__FILE__)))
            return $post_id;

        if ('page' == $_REQUEST['post_type']) {
            if (!current_user_can('edit_page', $post_id))
                return $post_id;
        } else {
            if (!current_user_can('edit_post', $post_id))
                return $post_id;
        }
        $mask = (int)$_REQUEST['no_external_links_mask_links'];
        update_post_meta($post_id, 'no_external_links_mask_links', $mask);
    }

    function add_custom_box($page, $context)
    {
        add_meta_box('no_external_links_sectionid1', __('Link masking for this post', NEL_I18N), array($this, 'inner_custom_box1'), 'post', 'advanced');
        add_meta_box('no_external_links_sectionid1', __('Link masking for this post', NEL_I18N), array($this, 'inner_custom_box1'), 'page', 'advanced');
    }

    function inner_custom_box1()
    {
        global $post;
        echo '<input type="hidden" name="no_external_links_noncename" id="no_external_links_noncename" value="' .
            wp_create_nonce(plugin_basename(__FILE__)) . '" />';
        $mask = get_post_meta($post->ID, 'no_external_links_mask_links', true);
        if ($mask === '')
            $mask = 0;
        echo '<input type="radio" name="no_external_links_mask_links" value="0"';
        if ($mask == 0) echo ' checked';
        echo '>' . __('Use default policy from plugin settings', NEL_I18N) . '<br><input type="radio" name="no_external_links_mask_links" value="2"';
        if ($mask == 2) echo ' checked';
        echo '>' . __('Don`t mask links', NEL_I18N);
    }

    function update()
    {
        if (!empty($_REQUEST['options']) && is_array($_REQUEST['options'])) {
            $this->options = (array)$_REQUEST['options'];
            $this->update_options();
            echo '<div class="updated">' . __('Options updated.', NEL_I18N) . '</div>';
        }
        $this->load_options();
    }

    function modify_menu()
    {
        add_options_page(
            'No External Links&nbsp;<img src="' . plugin_dir_url(__FILE__) . 'externallink.png">',
            'No External Links&nbsp;<img src="' . plugin_dir_url(__FILE__) . 'externallink.png">',
            'manage_options',
            __FILE__,
            array($this, 'admin_options')
        );
    }

    function get_admin_page()
    {
        return get_admin_url(null, 'options-general.php?page=no-external-links%2Fno-external-links-options.php');
    }

    function show_navi()
    {
        $page = $this->get_admin_page();
        if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'stats') {
            ?>
            <a href="<?php echo $page ?>"
               class="button-primary"><?php _e('View options', NEL_I18N); ?></a>
        <?php } else { ?>
            <a href="<?php echo $page; ?>&action=stats"
               class="button-primary"><?php _e('View Stats', NEL_I18N); ?></a>
        <?php } ?>
        <?php
    }

    function view_stats()
    {
        global $wpdb;
        ?>
        <form method="post" action="">
        <input type="hidden" name="page" value="no-external-links%2Fno-external-links-options.php">
        <?php wp_nonce_field(NEL_I18N, 'view-stats');
        $this->show_navi(); ?><br><br>
        <?php

        if (!$this->options['stats']) {
            _e('Statistic for plugin is disabled! Please, go to options page and enable it via checkbox "Log all outgoing clicks".', NEL_I18N);
            echo '</form>';
            //echo '<br/><a href="'.$this->get_admin_page().'" class="button-primary">'.__('View options', NEL_I18N).'</a>';
        } else {
            if (!empty($_REQUEST['date1']))
                $date1 = $_REQUEST['date1'];
            else
                $date1 = date('Y-m-d');
            if (!empty($_REQUEST['date2']))
                $date2 = $_REQUEST['date2'];
            else
                $date2 = date('Y-m-d');
            if (!empty($_REQUEST['date1']) || !empty($_REQUEST['date2'])) {
                check_admin_referer(NEL_I18N, 'view-stats');
            }
            _e('View stats from', NEL_I18N);
            ?>
            <input type="text" name="date1" value="<?php echo $date1; ?>"> <?php _e('to', NEL_I18N); ?>
            <input type="text" name="date2" value="<?php echo $date2; ?>"><input type="submit"
                                                                                 value="<?php _e('View', NEL_I18N); ?>"
                                                                                 class="button-primary">
            </form><br>
            <style>.urlul {
                    padding: 5px 0px 0px 25px;
                }</style>
            <?php
            $sql = 'select * from ' . $wpdb->prefix . NEL_TABLE_LINKS_STATS.' where `date` between %s and DATE_ADD(%s,INTERVAL 1 DAY)';
            $sql = $wpdb->prepare($sql, $date1, $date2);
            $result = $wpdb->get_results($sql, ARRAY_A);
            if (is_array($result) && sizeof($result)) {
                $out = array();
                foreach ($result as $row) {
                    $nfo = parse_url($row['url']);
                    if (!empty($row['url']) && !empty($nfo['host'])) {
                        @$out[$nfo['host']][$row['url']]++;
                    }
                }
                foreach ($out as $host => $arr) {
                    echo '<br>● ' . $host . '<ul class="urlul">';
                    foreach ($arr as $url => $outs)
                        echo '<li><a href="' . $url . '">' . $url . '</a> (' . $outs . ')</li>';
                    echo '</ul>';
                }
            } else
                _e('No statistic for given period.', NEL_I18N);
        }

    }

    function option_page()
    {
        ?>
        <p><?php _e('This plugins allows you to mask all external links and make them internal or hidden - using PHP redirect or special link tags and attributes. It does not change anything in the database - only replaces links on output. If you disabled this plugin and still have links masked - check your chaching plugins.', NEL_I18N); ?></p>
        <p>
            <?php echo __('If you need to make custom modifications for plugin - you can simply extend it, according to', NEL_I18N) . ' <a href="http://jehy.ru/articles/2014/12/08/custom-parser-for-wp-noexternallinks/">' . __('this article.', NEL_I18N) . '</a>.'; ?>
        </p>
        <p>
            <?php echo __('If you need to mask links in posts`s custom field, take a look at', NEL_I18N) . ' <a href="http://jehy.ru/articles/2015/03/06/masking-links-in-custom-fields-with-wp-noexternallinks/">' . __('this article.', NEL_I18N) . '</a>.'; ?>
        </p>
        <form method="post" action="">
            <?php wp_nonce_field(NEL_I18N, 'update-options');
            $this->show_navi(); ?>
            <br>
            <?php echo '<h2>' . __('Global links masking settings', NEL_I18N) . '</h2>' . '(' . __('You can also disable plugin on per-post basis', NEL_I18N) . ')'; ?>
            <br>
            <?php
            $opt = $this->GetOptionInfo();
            //echo '<h3>' . __('Choose masking type', NEL_I18N) . '</h3><p>' . __('Default masking type is via 302 redirects. Please choose one of the following mods if you do not like it:', NEL_I18N) . '</p>';
            //$this->show_option_group($opt, 'type');
            echo '<h3>' . __('What to mask', NEL_I18N) . '</h3>';
            $this->show_option_group($opt, 'what');
            echo '<h3>' . __('What to exclude from masking', NEL_I18N) . '</h3>';
            $this->show_option_group($opt, 'exclude');
            echo '<h3>' . __('Common configuration', NEL_I18N) . '</h3>';
            $this->show_option_group($opt, 'common');
            echo '<h3>' . __('Link encoding', NEL_I18N) . '</h3><p>' . __('Theese options are not secure enough if you want to protect your data from someone but are quite enough to make link not human-readable. Please choose one of them:', NEL_I18N) . '</p>';
            $this->show_option_group($opt, 'encode');
            echo '<h3>' . __('Configuration for javascript redirects (if enabled)', NEL_I18N) . '</h3>';
            $this->show_option_group($opt, 'type');
            $this->show_option_group($opt, 'java');

            ?><input type="submit" name="submit" value="<?php _e('Save Changes', NEL_I18N) ?>"
                     class="button-primary"/>
        </form>
        <?php
    }

    function show_option_group($opt, $name)
    {
        foreach ($opt as $arr) {
            if ($arr['grp'] === $name) {
                $this->show_option($arr);
                //echo '<br>';
            }
        }
    }

    function show_option($arr)
    {
        if ($arr['type'] == 'chk') {
            echo '<p><input type="checkbox" name="options[' . $arr['new_name'] . ']" value="1"';
            if ($this->options[$arr['new_name']])
                echo ' checked';
            echo '>' . $arr['name'].'</p>';
        } elseif ($arr['type'] == 'txt') {
            echo '<p>'.$arr['name'] . ': <input type="text" name="options[' . $arr['new_name'] . ']" value="' . $this->options[$arr['new_name']] . '"></p>';
        } elseif ($arr['type'] == 'text') {
            echo '<p>' . $arr['name'] . ':</p>';
            echo '<textarea name="options[' . $arr['new_name'] . ']" class="large-text code" rows="6" cols="50">' . $this->options[$arr['new_name']] . '</textarea>';
        }
    }

    function admin_options()
    {
        echo '<div class="wrap"><h2>No External LInks</h2>';
        if (!empty($_REQUEST['submit'])) {
            check_admin_referer(NEL_I18N, 'update-options');
            $this->update();
        }
        if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'stats')
        {
            $this->view_stats();
        }
        else
            $this->option_page();
        echo '</div>';
    }
}
