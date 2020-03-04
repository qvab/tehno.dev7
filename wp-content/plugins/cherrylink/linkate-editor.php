<?php


// add_action( 'after_wp_tiny_mce', 'custom_after_wp_tiny_mce' );
// function custom_after_wp_tiny_mce() {
//     printf( '<script type="text/javascript" src="%s"></script>',  plugins_url('/js/linkate.js', __FILE__) );
// }

function linkate_scripts() {
    // Register the style like this for a plugin:
    wp_register_style( 'linkate-style', plugins_url( '/css/linkate.css', __FILE__ ), '', LinkatePosts::get_linkate_version() );

    wp_enqueue_style ('linkate-style');

    // Register the script like this for a plugin:
    wp_register_script( 'linkate-script', plugins_url( '/js/linkate.js', __FILE__ ), array( 'jquery' ), LinkatePosts::get_linkate_version() );

    // For either a plugin or a theme, you can then enqueue the script:
    wp_enqueue_script( 'linkate-script' );
}

function linkate_meta_box() {
    add_meta_box(
        'linkate-box',
        'CherryLink '.LinkatePosts::get_linkate_version(),
        'linkate_meta_box_callback'
    );
}

function is_version_old( $operator = '<', $version = '4.9.6' ) {
    global $wp_version;
    return version_compare( $wp_version, $version, $operator ) ? 1 : 0;
}

function linkate_meta_box_callback($post) {
    
    // Add a nonce field so we can check for it later.
    wp_nonce_field( 'linkate_nonce', 'linkate_nonce' );
    
    $options = (array) get_option('linkate-posts');
    echo '<span id="link_template" data-before="'.$options['link_before'].'" data-after="'.$options['link_after'].'" hidden></span>';
    echo '<span id="term_template" data-before="'.$options['term_before'].'" data-after="'.$options['term_after'].'" hidden></span>';
    echo '<span id="multilink" data-value="'.$options['multilink'].'" hidden></span>';
    echo '<span id="wp_ver" data-value="'.is_version_old('<', '4.9.6').'" hidden></span>';
    echo getLinkateLinks($post);
    echo '<div class="linkate-box-container container-taxonomy">' . hierarchical_term_tree() . '</div>';
    
}

// function linkate_meta_box_term_template($post) {
//     echo '<div id="linkate-box"><h2 class="hndle"><span>CherryLink '.LinkatePosts::get_linkate_version().'</span></h2><div class="inside">'..'</div></div>';
// }

// add_action( 'category_add_form_fields', 'linkate_meta_box_term_template');


// The media button to dislay links box
function add_linkate_button () {
    echo '<a id="linkate-button" class="button"><span class="wp-media-buttons-icon linkate-media-btn"></span>Перелинковка</a>';
}


// Using linkateposts to get relevant results
function getLinkateLinks($post) {

    $def_template =  '<li class="linkate-link" data-url="{url}" data-title="{title_seo}"><span class="link-counter"></span><span class="link-title">{title_seo}</span></li>';

    $a = array(
        'limit' => '15',
        'prefix' => '<div class="linkate-box-container"><ol id="linkate-links-list">',
        'suffix' => '</ol></div>',
        'match_cat' => 'true'
    );
  
    ob_start(); // start a buffer
    linkate_posts("manual_ID=".$post->ID."&"); 
    $out = ob_get_clean(); // get the buffer contents and clean it
 
    return empty($out) ? "Ничего не найдено..." : $out;
}

// function tinymce_init() {
//     // Hook to tinymce plugins filter
//     add_filter( 'mce_external_plugins', 'tinymce_plugin', 999 );
// }


function tinymce_plugin($init) {
    $init['cherrylink_change'] = plugins_url( '/js/linkate.js', __FILE__ );
    return $init;
}

global $pagenow;
if ( $pagenow == 'post.php' || $pagenow == 'post-new.php') {
    add_action('media_buttons', 'add_linkate_button', 15);
    add_action( 'add_meta_boxes', 'linkate_meta_box' );
    add_action( 'admin_enqueue_scripts', 'linkate_scripts' );
}


// DO NOT DELETE! THIS IS USED IN linkate-posts.PHP func "execute"
function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}
