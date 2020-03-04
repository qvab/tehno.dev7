<?php
if(!function_exists('itajax_view')) {	
	#user rating
	function itajax_view() {
		
		$postid=$_POST['postID'];
			
		$views = get_post_meta($postid, IT_META_TOTAL_VIEWS, $single = true);
		
		#don't count bots and crawlers as views
		if(!is_bot() && !empty($postid)) {	
		
			#get the user's ip address
			$ip=it_get_ip();
			
			#get meta info
			$ips = get_post_meta($postid, IT_META_VIEW_IP_LIST, $single = true);
			
			$do_update=true;
			if(strpos($ips,$ip) !== false && !it_get_setting('unique_views_disable')) $do_update=false;
			
			#$do_update=true; #testing purposes only
			
			if($do_update) {
				$ip.=';'; #add delimiter	
				$ips.=$ip; #add ip to string
				$views+=1; #increase views	
					
				update_post_meta($postid, IT_META_VIEW_IP_LIST, $ips);				
				update_post_meta($postid, IT_META_TOTAL_VIEWS, $views);					
			}
			
		}
		
		#generate the response
		$response = json_encode( array( 'content' => $views ) );
	 
		#response output
		header( "Content-Type: application/json" );
		echo $response;
		
		exit;
	}
}
if(!function_exists('itajax_trending')) {	
	#trending meta
	function itajax_trending() {
		
		$postid=$_POST['postID'];
		
		$likesargs = array('postid' => $postid, 'label' => false, 'icon' => true, 'clickable' => true, 'showifempty' => true, 'tooltip_hide' => true);			
		$viewsargs = array('postid' => $postid, 'label' => false, 'icon' => true, 'tooltip_hide' => true);		
		$commentsargs = array('postid' => $postid, 'label' => false, 'icon' => true, 'showifempty' => false, 'anchor_link' => false, 'tooltip_hide' => true);
		$heatargs = array('postid' => $postid, 'icon' => true, 'tooltip' => false);
		
		#begin output
		$out = '';
		
		$out .= '<div class="trending-label">' . __('Trending',IT_TEXTDOMAIN) . '</div>';
		
		if(!it_get_setting('trending_views_disable')) $out .= it_get_views($viewsargs);
	
		if(!it_get_setting('trending_likes_disable')) $out .= it_get_likes($likesargs);
	
		if(!it_get_setting('trending_heat_disable')) $out .= it_get_heat_index($heatargs);
	
		if(!it_get_setting('trending_comments_disable')) $out .= it_get_comments($commentsargs);		
		
		#generate the response
		$response = json_encode( array( 'content' => $out ) );
	 
		#response output
		header( "Content-Type: application/json" );
		echo $response;
		
		exit;	
	}
}
if(!function_exists('itajax_sharing')) {	
	#sharing meta
	function itajax_sharing() {
		
		$postid=$_POST['postID'];
		
		$sharingargs = array('title' => get_the_title($postid), 'description' => '', 'url' => get_permalink($postid), 'showmore' => false, 'tooltip_hide' => true, 'style' => 'hidden');
		
		#begin output
		$out = '';
		
		$out .= '<div class="trending-label">' . __('Share This Article',IT_TEXTDOMAIN) . '</div>';
		
		$out .= it_get_sharing($sharingargs);			
		
		#generate the response
		$response = json_encode( array( 'content' => $out ) );
	 
		#response output
		header( "Content-Type: application/json" );
		echo $response;
		
		exit;	
	}
}
if(!function_exists('itajax_comparepanel')) {
	#compare panel display
	function itajax_comparepanel() {
		
		$out = '';
		
		$arr = it_compared_items();
		$cssactive = empty($arr) ? '' : ' active';
		
		foreach($arr as $postid) {
			$out .= it_compare_block($postid);
		}
		
		#generate the response
		$response = json_encode( array( 'content' => $out, 'cssactive' => $cssactive, 'comparing' => $arr ) );
	 
		#response output
		header( "Content-Type: application/json" );
		echo $response;
		
		exit;	
		
	}
}
if(!function_exists('itajax_compare')) {	
	#compare toggling
	function itajax_compare() {
		
		$postid = $_POST['postID'];
		$perform = $_POST['perform'];			
		#user identity
		$ip = it_get_ip();
		$userid = get_current_user_id();
		$userid = empty($userid) ? $ip : $userid;
		#existing option
		$option = get_option('compare_' . $userid);
		if($option === false) $option = get_option('compare_' . $ip);
		$arr = array();
		switch($perform) {
			case 'add':
				if($option === false) {
					add_option('compare_' . $userid, $postid . ',', NULL, 'no');
				} else {
					update_option('compare_' . $userid, $option . $postid . ',');
				}
			break;
			case 'remove':
				$option = substr_replace($option,'',-1);
				$arr = explode(',',$option);
				if(($key = array_search($postid, $arr)) !== false) {
					unset($arr[$key]);
				}
				$value = implode(',',$arr);
				$value = empty($value) ? '' : $value . ',';
				update_option('compare_' . $userid, $value);
			break;	
		}
				
		#html output
		$out = it_compare_block($postid);
		
		#generate the response
		$response = json_encode( array( 'content' => $out ) );
	 
		#response output
		header( "Content-Type: application/json" );
		echo $response;
		
		exit;	
	}
}
if(!function_exists('itajax_like')) {
	#like button
	function itajax_like() {
		
		$postid=$_POST["postID"];
		$likeaction=$_POST["likeaction"];
		$location=$_POST["location"];
		
		$ip=it_get_ip();
		
		#get meta info
		$ips = get_post_meta($postid, IT_META_LIKE_IP_LIST, $single = true);
		$likes = get_post_meta($postid, IT_META_TOTAL_LIKES, $single = true);
		
		$ip.=';'; #add delimiter
		
		if($likeaction=='like') {
			$ips.=$ip; #add ip to string
			$likes+=1; #increase likes
		} else {
			$ips = str_replace($ip,'',$ips); #remove ip from string
			$likes-=1; #decrease likes
		}
		
		#update post meta
		update_post_meta($postid, IT_META_LIKE_IP_LIST, $ips);
		update_post_meta($postid, IT_META_TOTAL_LIKES, $likes);
		
		if($likes=='') $likes=0;
		#determine label
		if($location=='single-page') {
			if($likes==1) {
				$likes.='<span class="labeltext">'.__(' like',IT_TEXTDOMAIN).'</span>';
			} else {
				$likes.='<span class="labeltext">'.__(' likes',IT_TEXTDOMAIN).'</span>';
			}
		} else {
			if($likes==0) $likes=''; #don't display 0 count
		}
		
		#generate the response
		$response = json_encode( array( 'content' => $likes ) );
	 
		#response output
		header( "Content-Type: application/json" );
		echo $response;
		
		exit;	
	}
}
if(!function_exists('itajax_reaction')) {
	#user reaction
	function itajax_reaction() {
		
		$postid=$_POST["postID"];
		$reaction=$_POST["reaction"];
		$unlimitedreactions=$_POST["unlimitedreactions"];
		
		$ip = it_get_ip();
		$ip .= ';';
		$response_number = 0;
		$addflag = false;
		$removeflag = false;
		$response = array();
		$numbers = array();
		
		#get total reactions for this post
		$total_reactions = get_post_meta($postid, IT_META_TOTAL_REACTIONS, $single = true);
		$total_reactions = !empty($total_reactions) ? $total_reactions : 0;
		
		#are there any reactions in the theme options?
		$reactions = it_get_setting('reactions');
		if ( isset($reactions['keys']) && $reactions['keys'] != '#' ) {
			
			#get excluded reactions for this post
			$excluded_reactions = get_post_meta($postid, IT_META_REACTIONS, $single = true);
			if(unserialize($excluded_reactions)) $excluded_reactions = unserialize($excluded_reactions);
			
			#loop through all possible reaction metas and adjust as necessary
			$reactions_keys = explode(',',$reactions['keys']);
			foreach ($reactions_keys as $rkey) {
				if ( $rkey != '#') {
					$reaction_name = ( !empty( $reactions[$rkey]['name'] ) ) ? stripslashes($reactions[$rkey]['name']) : '#';
					$reaction_slug = ( !empty( $reactions[$rkey]['slug'] ) ) ? $reactions[$rkey]['slug'] : it_get_slug($reaction_name, $reaction_name);	
					
					#check to see if this reaction is excluded for this post
					if(!empty($reaction_slug) && !in_array($reaction_slug,$excluded_reactions)) {	
					
						#get current reaction ips
						$ips = get_post_meta($postid, '_'.$reaction_slug.'_ips', $single = true);
						#$ips = get_post_meta($postid, $reaction_slug.'_ips', $single = true);
						#get current reaction number
						$number = get_post_meta($postid, '_'.$reaction_slug, $single = true);
						#$number = get_post_meta($postid, $reaction_slug, $single = true);
						$number = !empty($number) ? $number : 0;
						
						#see if ip already exists (might exist if unlimited reactions is turned on)
						$pos = strpos($ips,$ip);
						#is this the reaction that was clicked?
						if($reaction_slug == $reaction) {
							if($pos === false) {
								$ips .= $ip;
							}
							$addflag = true; #we added a reaction
							#increment number by one
							$number += 1;
							#this is the number we return to the ajax call
							$response_number = $number;
						} else {
							if(!$unlimitedreactions && $pos !== false) {
								$ips = str_replace($ip,'',$ips); #remove ip from string
								if($number > 0) $number -= 1;
								$removeflag = true; #we removed a reaction
							}
						}
						
						#update post meta
						update_post_meta($postid, '_'.$reaction_slug.'_ips', $ips);
						update_post_meta($postid, '_'.$reaction_slug, $number);
						#update_post_meta($postid, $reaction_slug.'_ips', $ips);
						#update_post_meta($postid, $reaction_slug, $number);
						
						$numbers[$reaction_slug] = $number;
						
					}
				}
			}
		}
		
		#increase and update total reactions if this is a new reaction and not a "switch"
		if($addflag && !$removeflag) {
			$total_reactions += 1;
			update_post_meta($postid, IT_META_TOTAL_REACTIONS, $total_reactions);
		}
		
		foreach($numbers as $reaction => $number) {
		
			#calculate new percentage
			$percentage = $total_reactions != 0 ? round(($number / $total_reactions) * 100, 0) : 0;
			$percentage .= '%';
			$response[$reaction] = $percentage;
			
		}
		
		#generate the response
		$response = json_encode( $response );
	 
		#response output
		header( "Content-Type: application/json" );
		echo $response;
		
		exit;	
	}
}
if(!function_exists('itajax_user_rate')) {	
	#user rating
	function itajax_user_rate() {
		
		$postid=$_POST["postID"];
		$meta=$_POST["meta"];
		$metric=$_POST["metric"];
		$rating=$_POST["rating"];
		$divID=$_POST["divID"];
		
		#setup the args
		$ratingargs = array('postid' => $postid, 'meta' => $meta, 'metric' => $metric, 'rating' => $rating);
		
		#perform the actual meta updates
		$ratings = it_save_user_ratings($ratingargs);
		
		#generate the response
		$response = json_encode(array('newrating' => $ratings['new_rating'], 'totalrating' => $ratings['total_rating'], 'normalized' => $ratings['normalized'], 'divID' => $divID, 'unlimitedratings' => $ratings['unlimitedratings'], 'cssfill' => $ratings['cssfill'], 'amount' => $ratings['amount']));
	 
		#response output
		header( "Content-Type: application/json" );
		echo $response;
		
		exit;
	}
}
if (!function_exists('itajax_share_count')) {
	#store addthis share count
	function itajax_share_count() {
		
		$postid=$_POST['postID'];
		$count=$_POST['shareCount'];
		
		#don't worry about bots and crawlers
		if(!is_bot() && !empty($postid)) {	
				
			#store the new heat index		
			update_post_meta($postid, IT_SHARE_COUNT, $count);
			
		}
		
		#generate the response
		$response = json_encode( array( 'content' => $count ) );
	 
		#response output
		header( "Content-Type: application/json" );
		echo $response;
		
		exit;
		
	}
}
if (!function_exists('itajax_heat_index')) {
	#update heat index
	function itajax_heat_index() {
		
		$postid=$_POST['postID'];
					
		$index = get_post_meta($postid, IT_HEAT_INDEX, $single = true);	
		
		#don't worry about bots and crawlers
		if(!is_bot() && !empty($postid)) {			
			
			#stored values
			$views = get_post_meta($postid, IT_META_TOTAL_VIEWS, $single = true);
			$likes = get_post_meta($postid, IT_META_TOTAL_LIKES, $single = true);
			$reactions = get_post_meta($postid, IT_META_TOTAL_REACTIONS, $single = true);
			$ratings = get_post_meta($postid, IT_META_TOTAL_USER_RATINGS, $single = true);
			$comments = get_comments_number($postid);
			$shares = get_post_meta($postid, IT_SHARE_COUNT, $single = true);
			#defaults
			if(empty($views)) $views = 0;
			if(empty($likes)) $likes = 0;
			if(empty($reactions)) $reactions = 0;
			if(empty($ratings)) $ratings = 0;
			if(empty($comments)) $comments = 0;
			if(empty($shares)) $shares = 0;
			
			#multipliers
			$view = it_get_setting('heat_weight_view');
			$like = it_get_setting('heat_weight_like');
			$reaction = it_get_setting('heat_weight_reaction');
			$rating = it_get_setting('heat_weight_rating');
			$comment = it_get_setting('heat_weight_comment');
			$share = it_get_setting('heat_weight_share');
			#defaults
			if(empty($view)) $view = 1;
			if(empty($like)) $like = 20;
			if(empty($reaction)) $reaction = 10;
			if(empty($rating)) $rating = 50;
			if(empty($comment)) $comment = 50;
			if(empty($share)) $share = 70;
			
			#calculate
			$view_total = $views * $view;
			$like_total = $likes * $like;
			$reaction_total = $reactions * $reaction;
			$rating_total = $ratings * $rating;
			$comment_total = $comments * $comment;
			$share_total = $shares * $share;
			$index = $view_total + $like_total + $reaction_total + $rating_total + $comment_total + $share_total;		
				
			#store the new heat index		
			update_post_meta($postid, IT_HEAT_INDEX, $index);
			
			#update the category and tag heat indicies
			if(it_get_setting('heat_calculate_when') == 'index_changed' || it_get_setting('heat_calculate_when') == 'both') {
				if(!it_get_setting('heat_category_disable')) it_update_heat_index($postid, 'category', 'assigned');
				if(!it_get_setting('heat_tag_disable')) it_update_heat_index($postid, 'tag', 'assigned');
			}
			
		}
		
		#generate the response
		$response = json_encode( array( 'content' => $index ) );
	 
		#response output
		header( "Content-Type: application/json" );
		echo $response;
		
		exit;
		
	}
}
if(!function_exists('itajax_menu_terms')) {	
	#mega menu hovers
	function itajax_menu_terms() {
		
		$object=$_POST["object"];
		$objectid=$_POST["objectid"];
		$object_name=$_POST["object_name"];
		$loop=$_POST["loop"];
		$type=$_POST["type"];
		
		$menu_args = array('object' => $object, 'objectid' => $objectid, 'object_name' => $object_name, 'loop' => $loop, 'type' => $type, 'useparent' => true);
		
		$menu_content = it_mega_menu_item($menu_args);	
		
		#generate the response
		$response = json_encode( array( 'content' => $menu_content ) );
	 
		#response output
		header( "Content-Type: application/json" );
		echo $response;
		
		exit;	
	}
}
if(!function_exists('itajax_sort')) {	
	#loop sorting
	function itajax_sort() {
		
		$postid=isset($_POST["postID"]) ? $_POST["postID"] : '';
		$loop=isset($_POST["loop"]) ? $_POST["loop"] : '';
		$location=isset($_POST["location"]) ? $_POST["location"] : '';
		$layout=isset($_POST["layout"]) ? $_POST["layout"] : '';
		$thumbnail=isset($_POST["thumbnail"]) ? $_POST["thumbnail"] : '';
		$icon=isset($_POST["icon"]) ? $_POST["icon"] : '';
		$meta=isset($_POST["meta"]) ? $_POST["meta"] : '';
		$icon=isset($_POST["icon"]) ? $_POST["icon"] : '';
		$award=isset($_POST["award"]) ? $_POST["award"] : '';
		$badge=isset($_POST["badge"]) ? $_POST["badge"] : '';
		$authorship=isset($_POST["authorship"]) ? $_POST["authorship"] : '';
		$excerpt=isset($_POST["excerpt"]) ? $_POST["excerpt"] : '';
		$paginated=isset($_POST["paginated"]) ? $_POST["paginated"] : '';
		$rating=isset($_POST["rating"]) ? $_POST["rating"] : '';
		$numarticles=isset($_POST["numarticles"]) ? $_POST["numarticles"] : '';
		$sorter=isset($_POST["sorter"]) ? $_POST["sorter"] : '';
		$method=isset($_POST["method"]) ? $_POST["method"] : '';
		$title=isset($_POST["title"]) ? $_POST["title"] : '';	
		$query=isset($_POST["currentquery"]) ? $_POST["currentquery"] : '';
		$object=isset($_POST["object"]) ? $_POST["object"] : '';
		$object_name=isset($_POST["object_name"]) ? $_POST["object_name"] : '';
		$timeperiod=isset($_POST["timeperiod"]) ? $_POST["timeperiod"] : '';
		$size=isset($_POST["size"]) ? $_POST["size"] : '';
		$csscol=isset($_POST["csscol"]) ? $_POST["csscol"] : '';
		$len=isset($_POST["len"]) ? $_POST["len"] : '';
		$disable_category=isset($_POST["disablecategory"]) ? $_POST["disablecategory"] : '';
		$disable_trending=isset($_POST["disabletrending"]) ? $_POST["disabletrending"] : '';
		$disable_sharing=isset($_POST["disablesharing"]) ? $_POST["disablesharing"] : '';
		$disable_reviewlabel=isset($_POST["disablereviewlabel"]) ? $_POST["disablereviewlabel"] : '';
		
		#defaults
		$out = '';
		$args = array();
		$format = array();
		$content_before = '';
		$content_after = '';
		$label = '';
		$href = '';
		
		switch ($loop) {
			case 'trending':
				#setup loop format
				$format = array('loop' => $loop, 'location' => $location, 'numarticles' => $numarticles, 'thumbnail' => $thumbnail, 'metric' => $sorter);
				switch($sorter) {
					case 'heat':
						$args = array('orderby' => 'meta_value_num', 'meta_key' => IT_HEAT_INDEX, 'meta_query' => array(array( 'key' => IT_HEAT_INDEX, 'value' => '0', 'compare' => 'NOT IN')));	
						break;					
					case 'liked':
						$args = array('orderby' => 'meta_value_num', 'meta_key' => IT_META_TOTAL_LIKES, 'meta_query' => array(array( 'key' => IT_META_TOTAL_LIKES, 'value' => '0', 'compare' => 'NOT IN')));	
						break;
					case 'viewed':
						$args = array('orderby' => 'meta_value_num', 'meta_key' => IT_META_TOTAL_VIEWS, 'meta_query' => array(array( 'key' => IT_META_TOTAL_VIEWS, 'value' => '0', 'compare' => 'NOT IN')));	
						break;					
					case 'reviewed':
						$args = array('orderby' => 'meta_value_num', 'meta_key' => IT_META_TOTAL_SCORE_NORMALIZED, 'meta_query' => array(array( 'key' => IT_META_DISABLE_REVIEW, 'value' => 'true', 'compare' => '!=' ), array( 'key' => IT_META_TOTAL_SCORE_NORMALIZED, 'value' => '0', 'compare' => 'NOT IN')));	
						break;
					case 'users':
						$args = array('orderby' => 'meta_value_num', 'meta_key' => IT_META_TOTAL_USER_SCORE_NORMALIZED, 'meta_query' => array(array( 'key' => IT_META_DISABLE_REVIEW, 'value' => 'true', 'compare' => '!=' ), array( 'key' => IT_META_TOTAL_USER_SCORE_NORMALIZED, 'value' => '0', 'compare' => 'NOT IN')));	
						break;
					case 'commented':
						$args = array('orderby' => 'comment_count');	
						break;					
				}
				$args['posts_per_page'] = $numarticles;
				#add current query to new query args
				if(!empty($query) && is_array($query)) $args = array_merge($args, $query);
			break;			
			case 'main':
				#setup loop format
				$format = array('loop' => $loop, 'location' => $location, 'layout' => $layout, 'sort' => $sorter, 'paged' => $paginated, 'thumbnail' => $thumbnail, 'rating' => $rating, 'meta' => $meta, 'award' => $award, 'icon' => $icon, 'badge' => $badge, 'excerpt' => $excerpt, 'authorship' => $authorship, 'numarticles' => $numarticles, 'size' => $size, 'disable_category' => $disable_category, 'disable_reviewlabel' => $disable_reviewlabel);
				switch($sorter) {
					case 'recent':
						$args = array('orderby' => 'date');
						break;
					case 'heat':
						$args = array('orderby' => 'meta_value_num', 'meta_key' => IT_HEAT_INDEX);	
						break;
					case 'liked':
						$args = array('orderby' => 'meta_value_num', 'meta_key' => IT_META_TOTAL_LIKES);	
						break;
					case 'viewed':
						$args = array('orderby' => 'meta_value_num', 'meta_key' => IT_META_TOTAL_VIEWS);	
						break;
					case 'reviewed':
						$args = array('orderby' => 'meta_value_num', 'meta_key' => IT_META_TOTAL_SCORE_NORMALIZED, 'meta_query' => array(array( 'key' => IT_META_DISABLE_REVIEW, 'value' => 'true', 'compare' => '!=' ), array( 'key' => IT_META_TOTAL_SCORE_NORMALIZED, 'value' => '0', 'compare' => 'NOT IN')));	
						break;
					case 'users':
						$args = array('orderby' => 'meta_value_num', 'meta_key' => IT_META_TOTAL_USER_SCORE_NORMALIZED, 'meta_query' => array(array( 'key' => IT_META_DISABLE_REVIEW, 'value' => 'true', 'compare' => '!=' ), array( 'key' => IT_META_TOTAL_USER_SCORE_NORMALIZED, 'value' => '0', 'compare' => 'NOT IN')));	
						break;
					case 'commented':
						$args = array('orderby' => 'comment_count');	
						break;
					case 'awarded':
						$args = array('orderby' => 'date', 'order' => 'DESC', 'meta_query' => array( array( 'key' => IT_META_AWARDS, 'value' => array(''), 'compare' => 'NOT IN') ));	
						break;
					case 'title':
						$args = array('orderby' => 'title', 'order' => 'ASC');
						break;
				}
				$args['posts_per_page'] = $numarticles;
				$args['paged'] = $paginated;
				#add current query to new query args
				if(!empty($query) && is_array($query)) $args = array_merge($args, $query);
			break;			
			case 'menu':
				#setup loop format
				$format = array('loop' => $loop, 'location' => $location, 'size' => $size, 'csscol' => $csscol, 'len' => $len);
				$args = array('posts_per_page' => $numarticles, $object_name => $sorter);	
				if($object_name=='category_name') {
					$term = get_term_by('slug', $sorter, 'category');
					$label = __('Go to',IT_TEXTDOMAIN) . '&nbsp;' . $term->name . '<span class="theme-icon-forward"></span>';
					$href = get_term_link($term);
				}			
			break;
			case 'sections':
				#setup loop format
				$format = array('loop' => 'sections', 'location' => $location, 'layout' => $layout, 'thumbnail' => $thumbnail, 'rating' => $rating, 'meta' => $meta, 'award' => $award, 'icon' => $icon, 'badge' => $badge, 'excerpt' => $excerpt, 'authorship' => $authorship, 'nonajax' => true, 'numarticles' => $numarticles, 'sort' => 'recent', 'size' => $size);
				$args = array('posts_per_page' => $numarticles, 'order' => 'DESC', 'ignore_sticky_posts' => true, 'cat' => $sorter);
				
				#add more link to content
				$link = get_category_link( $sorter );
				$content_after = '<div class="load-more-wrapper compact add-active"><a class="load-more" href="' . esc_url($link) . '">' . __('View All',IT_TEXTDOMAIN) . '<span class="theme-icon-forward"></span></a></div>';
				
			break;
			case 'recommended':									
				#format				
				$format = array('loop' => $loop, 'location' => $location, 'sort' => $sorter, 'layout' => $location, 'paged' => $pagination, 'numarticles' => $numarticles, 'size' => 'square-large', 'disable_category' => $disable_category, 'disable_trending' => $disable_trending, 'disable_sharing' => $disable_sharing);
				switch($method) {
					case 'tags':
						$args=array('tag_id' => $sorter);	
						break;
					case 'categories':
						$args=array('cat' => $sorter);	
						break;
				}
				$args['posts_per_page'] = $numarticles;
				$args['post__not_in'] = array($postid);
				#recommended targeted
				if(!empty($targeted)) $args['post_type'] = $targeted;
			break;
		}
		#add the time period to the args
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
		#WP AJAX calls by default include draft posts so we need to always exclude them
		$args['post_status'] = 'publish';
		#build the loop html and return to ajax call	
		$loop = it_loop($args, $format, $timeperiod);
		$loop_content = '';
		$loop_pages = 0;
		$loop_updatepagination = '';
		$buildquery = '';
		if(array_key_exists('content',$loop)) $loop_content = $loop['content'];
		if(array_key_exists('pages',$loop)) $loop_pages = $loop['pages'];
		if(array_key_exists('updatepagination',$loop)) $loop_updatepagination = $loop['updatepagination'];
		if(!empty($query)) $buildquery = http_build_query($query);
		#add in before and after content
		$loop_content = $content_before . $loop_content . $content_after;
		
		#generate the response
		$response = json_encode(array('content' => $loop_content, 'pagination' => it_pagination($loop_pages, $format, it_get_setting('page_range')), 'paginationmobile' => it_pagination($loop_pages, $format, it_get_setting('page_range_mobile')), 'pages' => $loop_pages, 'updatepagination' => $loop_updatepagination, 'utility' => $buildquery, 'label' => $label, 'href' => $href));
	 
		#response output
		header( "Content-Type: application/json" );
		echo $response;
		
		exit;	
	}
}
?>