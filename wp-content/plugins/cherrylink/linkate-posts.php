<?php
/*
Plugin Name: CherryLink
Plugin URI: http://seocherry.ru/dev/cherrylink/
Description: Плагин для упрощения ручной внутренней перелинковки. ВАЖНО: 1.2.01 - переходная версия, обновитесь, пожалуйста, до последней доступной.
Version: 1.2.01
Author: SeoCherry.ru
Author URI: http://seocherry.ru/
Text Domain: linkate-posts
*/

function linkate_posts($args = '') {
	echo LinkatePosts::execute($args);
}

function linkate_posts_mark_current(){
	global $post, $linkate_posts_current_ID;
	$linkate_posts_current_ID = $post->ID;
}

// define ('LINKATE_POST_PLUGIN_LIBRARY', true);

if ( ! defined( 'WP_CONTENT_URL' ) )
	define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

if (!defined('LINKATE_CF_LIBRARY')) require(WP_PLUGIN_DIR.'/cherrylink/common_functions.php');
if (!defined('LINKATE_ACF_LIBRARY')) require(WP_PLUGIN_DIR.'/cherrylink/admin_common_functions.php');
if (!defined('LP_OT_LIBRARY')) require(WP_PLUGIN_DIR.'/cherrylink/output_tags.php');
if (!defined('LP_ADMIN_SUBPAGES_LIBRARY')) require(WP_PLUGIN_DIR.'/cherrylink/admin-subpages.php');
if (!defined('LINKATE_TERMS_LIBRARY')) require(WP_PLUGIN_DIR.'/cherrylink/linkate-terms.php');

if (!defined('DSEP')) define('DSEP', DIRECTORY_SEPARATOR);
// if (!defined('LINKATE_POST_PLUGIN_LIBRARY')) LinkatePosts::install_post_plugin_library();

$linkate_posts_current_ID = -1;

class LinkatePosts {
  static $version = 0;

  static function get_linkate_version() {
    $plugin_data = get_file_data(__FILE__, array('version' => 'Version'), 'plugin');
    LinkatePosts::$version = $plugin_data['version'];

    return $plugin_data['version'];
  } // get_linkate_version

  // check if plugin's admin page is shown
  static function linkate_is_plugin_admin_page($page = 'settings') {
    $current_screen = get_current_screen();

    if ($page == 'settings' && $current_screen->id == 'settings_page_linkate-posts') {
      return true;
    }

    return false;
  } // linkate_is_plugin_admin_page

  // add settings link to plugins page
  static function linkate_plugin_action_links($links) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=linkate-posts') . '" title="Настройки CherryLink">Настройки</a>';

    array_unshift($links, $settings_link);

    return $links;
  } // linkate_plugin_action_links


    
	static function execute($args='', $default_output_template='<li>{link}</li>', $option_key='linkate-posts'){
		global $table_prefix, $wpdb, $wp_version, $linkate_posts_current_ID;
		$start_time = link_cf_microtime();
		
		$output_template_item_prefix = '
		<div class="linkate-item-container">
			<div class="linkate-controls">
				<div class="link-counter" title="Найдено в тексте / переход к ссылке">[ 0 ]</div>
				<div class="link-preview" title="Что за статья? Откроется в новой вкладке">&#128279;</div>
			</div>
			<div class="linkate-link" title="Нажмите для вставки в текст" data-url="{url}" data-titleseo="{title_seo}" data-title="{title}" data-category="{categorynames}" data-date="{date}" data-author="{author}" data-postid="{postid}" data-imagesrc="{imagesrc}" data-anons="{anons}" ><span class="link-title" >';
        
        $output_template_item_suffix = '</span></div></div>';
        $list_prefix = '<div class="linkate-box-container container-articles"><div id="linkate-links-list">';
        $list_suffix = '</div></div>';
		
		// Manually throws id of the current post if set
		$arg_id = 0;
		if (function_exists('get_string_between')) {
		    $linkate_posts_current_ID = get_string_between ($args, "manual_ID=", "&");
		}

		$postid = link_cf_current_post_id($linkate_posts_current_ID);
		
		if (defined('POC_CACHE_4')) {
			$cache_key = $option_key.$postid.$args;
			$result = poc_cache_fetch($cache_key);
			if ($result !== false) return $result . sprintf("<!-- Linkate Posts took %.3f ms (cached) -->", 1000 * (link_cf_microtime() - $start_time));
		}
		$table_name = $table_prefix . 'linkate_posts';
		// First we process any arguments to see if any defaults have been overridden
		$options = link_cf_parse_args($args);
		// Next we retrieve the stored options and use them unless a value has been overridden via the arguments
		$options = link_cf_set_options($option_key, $options, $default_output_template);
		if (0 < $options['limit']) {
			$match_tags = ($options['match_tags'] !== 'false' && $wp_version >= 2.3);
			$exclude_cats = ($options['excluded_cats'] !== '');
			$include_cats = ($options['included_cats'] !== '');
			$exclude_authors = ($options['excluded_authors'] !== '');
			$include_authors = ($options['included_authors'] !== '');
			$exclude_posts = (trim($options['excluded_posts']) !== '');
			$include_posts = (trim($options['included_posts']) !== '');
			$match_category = ($options['match_cat'] === 'true');
			$match_author = ($options['match_author'] === 'true');
			$use_tag_str = ('' != trim($options['tag_str']) && $wp_version >= 2.3);
			$omit_current_post = ($options['omit_current_post'] !== 'false');
			$hide_pass = ($options['show_private'] === 'false');
			$check_age = ('none' !== $options['age']['direction']);
			$check_custom = (trim($options['custom']['key']) !== '');
			$limit = $options['skip'].', '.$options['limit'];

	 		//get the terms to do the matching
			if ($options['term_extraction'] === 'pagerank') {
				list( $contentterms, $titleterms, $tagterms) = linkate_sp_terms_by_textrank($postid, $options['num_terms']);
			} else {
				list( $contentterms, $titleterms, $tagterms) = linkate_sp_terms_by_freq($postid, $options['num_terms']);
			}
	 		// these should add up to 1.0
			$weight_content = $options['weight_content'];
			$weight_title = $options['weight_title'];
			$weight_tags = $options['weight_tags'];
			// below a threshold we ignore the weight completely and save some effort
			if ($weight_content < 0.001) $weight_content = (int) 0;
			if ($weight_title < 0.001) $weight_title = (int) 0;
			if ($weight_tags < 0.001) $weight_tags = (int) 0;

			$count_content = substr_count($contentterms, ' ') + 1;
			$count_title = substr_count($titleterms, ' ') + 1;
			$count_tags  = substr_count($tagterms, ' ') + 1;
			if ($weight_content) $weight_content = 57.0 * $weight_content / $count_content;
			if ($weight_title) $weight_title = 18.0 * $weight_title / $count_title;
			if ($weight_tags) $weight_tags = 24.0 * $weight_tags / $count_tags;
			if ($options['hand_links'] === 'true') {
				// check custom field for manual links
				$forced_ids = $wpdb->get_var("SELECT meta_value FROM $wpdb->postmeta WHERE post_id = $postid AND meta_key = 'linkate_sp_linkate' ") ;
			} else {
				$forced_ids = '';
			}
			// the workhorse...
			$sql = "SELECT *, ";
			$sql .= link_cf_score_fulltext_match($table_name, $weight_title, $titleterms, $weight_content, $contentterms, $weight_tags, $tagterms, $forced_ids);

			if ($check_custom) $sql .= "LEFT JOIN $wpdb->postmeta ON post_id = ID ";

			// build the 'WHERE' clause
			$where = array();
			$where[] = link_cf_where_fulltext_match($weight_title, $titleterms, $weight_content, $contentterms, $weight_tags, $tagterms);
			if (!function_exists('get_post_type')) {
				$where[] = link_cf_link_cf_where_hide_future();
			} else {
				$where[] = link_cf_where_show_status($options['status'], $options['show_attachments']);
			}
			if ($match_category) $where[] = link_cf_where_match_category();
			if ($match_tags) $where[] = link_cf_where_match_tags($options['match_tags']);
			if ($match_author) $where[] = link_cf_where_match_author();
			$where[] = link_cf_where_show_pages($options['show_pages'], $options['show_attachments']);
			if ($include_cats) $where[] = link_cf_where_included_cats($options['included_cats']);
			if ($exclude_cats) $where[] = link_cf_where_excluded_cats($options['excluded_cats']);
			if ($exclude_authors) $where[] = link_cf_where_excluded_authors($options['excluded_authors']);
			if ($include_authors) $where[] = link_cf_where_included_authors($options['included_authors']);
			if ($exclude_posts) $where[] = link_cf_where_excluded_posts(trim($options['excluded_posts']));
			if ($include_posts) $where[] = link_cf_where_included_posts(trim($options['included_posts']));
			if ($use_tag_str) $where[] = link_cf_where_tag_str($options['tag_str']);
			if ($omit_current_post) $where[] = link_cf_where_omit_post($linkate_posts_current_ID);
			if ($hide_pass) $where[] = link_cf_where_hide_pass();
			if ($check_age) $where[] = link_cf_where_check_age($options['age']['direction'], $options['age']['length'], $options['age']['duration']);
			if ($check_custom) $where[] = link_cf_where_check_custom($options['custom']['key'], $options['custom']['op'], $options['custom']['value']);
			$sql .= "WHERE ".implode(' AND ', $where);
			if ($check_custom) $sql .= " GROUP BY $wpdb->posts.ID";
			$sql .= " ORDER BY score DESC LIMIT $limit";
			//echo $sql;
			$results = $wpdb->get_results($sql);
		} else {
			$results = false;
		}
	    if ($results) {
	        $out_final = $output_template_item_prefix . $options['output_template'] . $output_template_item_suffix;
			$translations = link_cf_prepare_template($out_final);
			foreach ($results as $result) {
				$items[] = link_cf_expand_template($result, $out_final, $translations, $option_key);
			}
			if ($options['sort']['by1'] !== '') $items = link_cf_sort_items($options['sort'], $results, $option_key, $options['group_template'], $items);
			$output = implode(($options['divider']) ? $options['divider'] : "\n", $items);
			$output = $list_prefix . $output . $list_suffix;
		} else {
			// if we reach here our query has produced no output ... so what next?
			if ($options['no_text'] !== 'false') {
				$output = ''; // we display nothing at all
			} else {
				// we display the blank message, with tags expanded if necessary
				$translations = link_cf_prepare_template($options['none_text']);
				$output = "<p>" . link_cf_expand_template(array(), $options['none_text'], $translations, $option_key) . "</p>";
			}
		}
		if (defined('POC_CACHE_4')) poc_cache_store($cache_key, $output);
		return ($output) ? $output . sprintf("<!-- Linkate Posts took %.3f ms -->", 1000 * (link_cf_microtime() - $start_time)) : '';
	}

  // save some info
  static function lp_activate() {
    $options = get_option('linkate_posts_meta', array());

    if (empty($options['first_version'])) {
      $options['first_version'] = LinkatePosts::get_linkate_version();
      $options['first_install'] = current_time('timestamp');
      update_option('linkate_posts_meta', $options);
    }
  } // lp_activate

} // linkateposts class


function linkate_sp_terms_by_freq($ID, $num_terms = 20) {
	if (!$ID) return array('', '', '');
	global $wpdb, $table_prefix;
	$table_name = $table_prefix . 'linkate_posts';
	$terms = '';
	$results = $wpdb->get_results("SELECT title, content, tags FROM $table_name WHERE pID=$ID LIMIT 1", ARRAY_A);
	if ($results) {
		$word = strtok($results[0]['content'], ' ');
		$n = 0;
		$wordtable = array();
		while ($word !== false) {
			if(!array_key_exists($word,$wordtable)){
				$wordtable[$word]=0;
			}
			$wordtable[$word] += 1;
			$word = strtok(' ');
		}
		arsort($wordtable);
		if ($num_terms < 1) $num_terms = 1;
		$wordtable = array_slice($wordtable, 0, $num_terms);

		foreach ($wordtable as $word => $count) {
			$terms .= ' ' . $word;
		}

		$res[] = $terms;
		$res[] = $results[0]['title'];
		$res[] = $results[0]['tags'];
 	}
	return $res;
}

function linkate_sp_terms_by_textrank($ID, $num_terms = 20) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix . 'linkate_posts';
	$terms = '';
	$results = $wpdb->get_results("SELECT title, content, tags FROM $table_name WHERE pID=$ID LIMIT 1", ARRAY_A);
	if ($results) {
		// build a directed graph with words as vertices and, as edges, the words which precede them
 		$prev_word = 'aaaaa';
		$graph = array();
		$word = strtok($results[0]['content'], ' ');
		while ($word !== false) {
			isset($graph[$word][$prev_word]) ? $graph[$word][$prev_word] += 1 : $graph[$word][$prev_word] = 1; // list the incoming words and keep a tally of how many times words co-occur
			isset($out_edges[$prev_word]) ? $out_edges[$prev_word] += 1 : $out_edges[$prev_word] = 1; // count the number of different words that follow each word
			$prev_word = $word;
			$word = strtok(' ');
		}
 		// initialise the list of PageRanks-- one for each unique word
		reset($graph);
		while (list($vertex, $in_edges) =  each($graph)) {
			$oldrank[$vertex] = 0.25;
		}
		$n = count($graph);
		if ($n > 0) {
			$base = 0.15 / $n;
			$error_margin = $n * 0.005;
			do {
				$error = 0.0;
				// the edge-weighted PageRank calculation
				reset($graph);
				while (list($vertex, $in_edges) =  each($graph)) {
					$r = 0;
					reset($in_edges);
					while (list($edge, $weight) =  each($in_edges)) {
						if (isset($oldrank[$edge])) {
							$r += ($weight * $oldrank[$edge]) / $out_edges[$edge];
						}
					}
					$rank[$vertex] = $base + 0.95 * $r;
					$error += abs($rank[$vertex] - $oldrank[$vertex]);
				}
				$oldrank = $rank;
				//echo $error . '<br>';
			} while ($error > $error_margin);
			arsort($rank);
			if ($num_terms < 1) $num_terms = 1;
			$rank = array_slice($rank, 0, $num_terms);
			foreach ($rank as $vertex => $score) {
				$terms .= ' ' . $vertex;
			}
		}
		$res[] = $terms;
		$res[] = $results[0]['title'];
		$res[] = $results[0]['tags'];
 	}
	return $res;
}

function linkate_sp_save_index_entry($postID) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix . 'linkate_posts';
	$post = $wpdb->get_row("SELECT post_content, post_title, post_type FROM $wpdb->posts WHERE ID = $postID", ARRAY_A);
	if ($post['post_type'] === 'revision') return $postid;
	//extract its terms
	$options = get_option('linkate-posts');
	$utf8 = ($options['utf8'] === 'true');
	$cjk = ($options['cjk'] === 'true');
	$content = linkate_sp_get_post_terms($post['post_content'], true, false, false);
	// Seo title is more relevant, usually
	// Extracting terms from the custom titles, if present
	if ($options['compare_seotitle'] === 'checked') { 
	    $seotitle = get_post_meta( $postID, "_yoast_wpseo_title", true);
	    if (!$seotitle || $seotitle === $post['post_title'])
	        $seotitle = get_post_meta( $postID, "_aioseop_title", true);
	    if (!$seotitle || $seotitle === $post['post_title']) {
	        $title = linkate_sp_get_title_terms($post['post_title'], true, false, false);
	    } else {
	    	$title = linkate_sp_get_title_terms($seotitle, true, false, false);
	    }
	} else {
		$title = linkate_sp_get_title_terms($post['post_title'], true, false, false);
	}
	//$title = linkate_sp_get_title_terms($post['post_title'], true, false, false);
	$tags = linkate_sp_get_tag_terms($postID, $utf8);
	//check to see if the field is set
	$pid = $wpdb->get_var("SELECT pID FROM $table_name WHERE pID=$postID limit 1");
	//then insert if empty
	if (is_null($pid)) {
		$wpdb->query("INSERT INTO $table_name (pID, content, title, tags) VALUES ($postID, \"$content\", \"$title\", \"$tags\")");
	} else {
		$wpdb->query("UPDATE $table_name SET content=\"$content\", title=\"$title\", tags=\"$tags\" WHERE pID=$postID" );
	}
	return $postID;
}



function linkate_sp_delete_index_entry($postID) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix . 'linkate_posts';
	$wpdb->query("DELETE FROM $table_name WHERE pID = $postID ");
	return $postID;
}

function linkate_sp_clean_words($text) {
	$text = strip_tags($text);
	$text = strtolower($text);
	$text = str_replace("’", "'", $text); // convert MSWord apostrophe
	$text = preg_replace(array('/\[(.*?)\]/', '/&[^\s;]+;/', '/‘|’|—|“|”|–|…/', "/'\W/"), ' ', $text); //anything in [..] or any entities or MS Word droppings
	return $text;
}

function linkate_sp_mb_clean_words($text) {
	mb_regex_encoding('UTF-8');
	mb_internal_encoding('UTF-8');
	$text = strip_tags($text);
	$text = mb_strtolower($text);
	$text = str_replace("’", "'", $text); // convert MSWord apostrophe
	$text = preg_replace(array('/\[(.*?)\]/u', '/&[^\s;]+;/u', '/‘|’|—|“|”|–|…/u', "/'\W/u"), ' ', $text); //anything in [..] or any entities
	return 	$text;
}

function linkate_sp_mb_str_pad($text, $n, $c) {
	mb_internal_encoding('UTF-8');
	$l = mb_strlen($text);
	if ($l > 0 && $l < $n) {
		$text .= str_repeat($c, $n-$l);
	}
	return $text;
}

function linkate_sp_cjk_digrams($string) {
	mb_internal_encoding("UTF-8");
    $strlen = mb_strlen($string);
	$ascii = '';
	$prev = '';
	$result = array();
	for ($i = 0; $i < $strlen; $i++) {
		$c = mb_substr($string, $i, 1);
		// single-byte chars get combined
		if (strlen($c) > 1) {
			if ($ascii) {
				$result[] = $ascii;
				$ascii = '';
				$prev = $c;
			} else {
				$result[] = linkate_sp_mb_str_pad($prev.$c, 4, '_');
				$prev = $c;
			}
		} else {
			$ascii .= $c;
		}
    }
	if ($ascii) $result[] = $ascii;
    return implode(' ', $result);
}

function linkate_sp_get_post_terms($text, $utf8, $use_stemmer, $cjk) {
	global $linkate_overusedwords;
	$options = get_option('linkate-posts');
	if ($utf8) {
		//echo "got utf8 tree<br>";
		mb_regex_encoding('UTF-8');
		mb_internal_encoding('UTF-8');
		$wordlist = mb_split("\W+", linkate_sp_mb_clean_words($text));
		$words = '';

		reset($wordlist);
		while (list($n, $word) =  each($wordlist)) {
			//if (isset($linkate_overusedwords[iconv('Windows-1251','UTF-8','этот')])) echo "cicle ". $word."<br>";
			
			if ( mb_strlen($word) > $options['term_length_limit'] && !isset($linkate_overusedwords[$word])) {
				$words .= $word . ' ';
			}
		}
	} else {
		//echo "got NOTutf8 tree<br>";
		$wordlist = str_word_count(linkate_sp_clean_words($text), 1);
		$words = '';
		reset($wordlist);
		while (list($n, $word) =  each($wordlist)) {
			echo $word."  ".isset($linkate_overusedwords[$word]);
			if ( strlen($word) > $options['term_length_limit'] && !isset($linkate_overusedwords[$word])) {
				echo "cicle ". $word . " key" . $linkate_overusedwords[$word] . "<br>";
				$words .= $word . ' ';
			}
		}
	}
	//if ($cjk) $words = linkate_sp_cjk_digrams($words);
	return $words;
}

$tinywords = array('и' => 1, 'что' => 1, 'как' => 1, 'а' => 1, 'за' => 1, 'кто' => 1, 'чем' => 1, 'все' => 1, 'не' => 1, 'это' => 1, 'эти' => 1, 'где' => 1, 'на' => 1, 'у' => 1, 'еще' => 1, 'кем' => 1, 'под' => 1, 'над' => 1);

function linkate_sp_get_title_terms($text, $utf8, $use_stemmer, $cjk) {
	global $tinywords;
	if ($utf8) {
		mb_regex_encoding('UTF-8');
		mb_internal_encoding('UTF-8');
		$wordlist = mb_split("\W+", linkate_sp_mb_clean_words($text));
		$words = '';
		foreach ($wordlist as $word) {
			if (!isset($tinywords[$word])) {
				$words .= linkate_sp_mb_str_pad($word, 4, '_') . ' ';
			}
		}
	} else {
		$wordlist = str_word_count(linkate_sp_clean_words($text), 1);
		$words = '';
		foreach ($wordlist as $word) {
			if (!isset($tinywords[$word])) {
				$words .= str_pad($word, 4, '_') . ' ';
			}
		}
	}
	if ($cjk) $words = linkate_sp_cjk_digrams($words);
	return $words;
}

function linkate_sp_get_tag_terms($ID, $utf8) {
	global $wpdb;
	if (!function_exists('get_object_term_cache')) return '';
	$tags = array();
	$query = "SELECT t.name FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = t.term_id INNER JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy = 'post_tag' AND tr.object_id = '$ID'";
	$tags = $wpdb->get_col($query);
	if (!empty ($tags)) {
		if ($utf8) {
			mb_internal_encoding('UTF-8');
			foreach ($tags as $tag) {
				$newtags[] = linkate_sp_mb_str_pad(mb_strtolower(str_replace('"', "'", $tag)), 4, '_');
			}
		} else {
			foreach ($tags as $tag) {
				$newtags[] = str_pad(strtolower(str_replace('"', "'", $tag)), 4, '_');
			}
		}
		$newtags = str_replace(' ', '_', $newtags);
		$tags = implode (' ', $newtags);
	} else {
		$tags = '';
	}
	return $tags;
}

if ( is_admin() ) {
	require(dirname(__FILE__).'/linkate-posts-admin.php');
	if (callDelay() && lastStatus()) {
		$r = true;
	}
	if (callDelay() && !lastStatus()) {
		$r = false;
	}
	if (!callDelay()) {
		$r = checkNeededOption();
	}
	if ($r)
		require(WP_PLUGIN_DIR . '/cherrylink/linkate-editor.php');
}


/*
	now some language specific stuff
*/

require_once(WP_PLUGIN_DIR . '/cherrylink/stopwords.php');


global $linkate_overusedwords;
if(is_array($linkate_overusedwords)) {
	$options = get_option('linkate-posts');
	if (!empty($options['custom_stopwords'])) {
		$customwords = explode("\n", str_replace("\r", "", $options['custom_stopwords']));
		$linkate_overusedwords = array_merge($linkate_overusedwords, $customwords);
	}

		//$linkate_overusedwords = array_map("utf8_encode", $linkate_overusedwords );
	$linkate_overusedwords = array_flip($linkate_overusedwords);
}

function linkate_posts_wp_admin_style() {
  if (LinkatePosts::linkate_is_plugin_admin_page('settings')) {
        wp_register_style( 'linkate-posts-admin', plugins_url('', __FILE__) . '/css/linkate-posts-admin.css', false, LinkatePosts::$version );
        wp_enqueue_style( 'linkate-posts-admin' );
  }
}

function linkate_posts_init () {
	global $linkate_overusedwords, $wp_db_version;
	load_plugin_textdomain('linkate_posts');

  LinkatePosts::get_linkate_version();

	$options = get_option('linkate-posts');
	if ($options['content_filter'] === 'true' && function_exists('link_cf_register_content_filter')) link_cf_register_content_filter('LinkatePosts');
	if ($options['append_condition']) {
		$condition = $options['append_condition'];
	} else {
		$condition = 'true';
	}
	$condition = (stristr($condition, "return")) ? $condition : "return ".$condition;
	$condition = rtrim($condition, '; ') . ';';
	if ($options['append_on'] === 'true' && function_exists('link_cf_register_post_filter')) link_cf_register_post_filter('append', 'linkate-posts', 'LinkatePosts', $condition);

	//install the actions to keep the index up to date
	add_action('save_post', 'linkate_sp_save_index_entry', 1);
	add_action('delete_post', 'linkate_sp_delete_index_entry', 1);
	if ($wp_db_version < 3308 ) {
		add_action('edit_post', 'linkate_sp_save_index_entry', 1);
		add_action('publish_post', 'linkate_sp_save_index_entry', 1);
	}
	add_action( 'admin_enqueue_scripts', 'linkate_posts_wp_admin_style' );

  // aditional links in plugin description
  add_filter('plugin_action_links_' . basename(dirname(__FILE__)) . '/' . basename(__FILE__),
             array('LinkatePosts', 'linkate_plugin_action_links'));
} // init

function linkate_check_update(){
    if (!class_exists('Puc_v4_Factory')) {
        require 'updater/plugin-update-checker.php';
    }

    $update_file= 'http://seocherry.ru/plugin-updates/cherrylink/1200/cherrylink.json';

    $update_checker= Puc_v4_Factory::buildUpdateChecker(
        $update_file,
        __FILE__,
        'cherrylink'
    );
}

add_action('admin_init', 'linkate_check_update');
add_action ('init', 'linkate_posts_init', 1);
register_activation_hook(__FILE__, array('LinkatePosts', 'lp_activate'));
