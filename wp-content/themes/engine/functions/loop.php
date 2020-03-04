<?php
    if (!function_exists('it_loop'))
    {
        function it_loop($args, $format, $timeperiod = '')
        {

            if (!is_array($format)) $format = array();
            extract($format);
            if (empty($location)) $location = $loop; #a specified location overrides the loop parameter

            #don't care about pagename if we're displaying a post loop on a content page
            $args['pagename'] = '';

            #add a filter if this loop needs a time constraint (can't add to query args directly)
            global $timewhere;
            $timewhere = $timeperiod;
            if (!empty($timeperiod))
            {
                add_filter('posts_where', 'filter_where');
            }
            #query the posts
            $itposts = new WP_Query($args);
            #remove the filter after we're done
            if (!empty($timeperiod))
            {
                remove_filter('posts_where', 'filter_where');
            }

            #setup ads array
            $ads = array();
            $ad1 = it_get_setting('loop_ad_1');
            $ad2 = it_get_setting('loop_ad_2');
            $ad3 = it_get_setting('loop_ad_3');
            $ad4 = it_get_setting('loop_ad_4');
            $ad5 = it_get_setting('loop_ad_5');
            $ad6 = it_get_setting('loop_ad_6');
            $ad7 = it_get_setting('loop_ad_7');
            $ad8 = it_get_setting('loop_ad_8');
            $ad9 = it_get_setting('loop_ad_9');
            $ad10 = it_get_setting('loop_ad_10');
            if (!empty($ad1)) array_push($ads, $ad1);
            if (!empty($ad2)) array_push($ads, $ad2);
            if (!empty($ad3)) array_push($ads, $ad3);
            if (!empty($ad4)) array_push($ads, $ad4);
            if (!empty($ad5)) array_push($ads, $ad5);
            if (!empty($ad6)) array_push($ads, $ad6);
            if (!empty($ad7)) array_push($ads, $ad7);
            if (!empty($ad8)) array_push($ads, $ad8);
            if (!empty($ad9)) array_push($ads, $ad9);
            if (!empty($ad10)) array_push($ads, $ad10);
            if (it_get_setting('ad_shuffle')) shuffle($ads);

            #counters
            $i = 0; #incremented after post display
            $p = 0; #incremented before post display
            $a = 0; #ad counter
            $r = 0; #row counter
            $flag = false;
            $right = false;
            $out = '';
            $the_ad = array();
            #sometimes the following variables are not passed
            $width = isset($width) ? $width : '';
            $height = isset($height) ? $height : '';
            $size = isset($size) ? $size : '';
            $nonajax = isset($nonajax) ? $nonajax : '';
            $disable_ads = isset($disable_ads) ? $disable_ads : false;
            $disable_category = isset($disable_category) ? $disable_category : it_get_setting('loop_category_disable');
            $disable_trending = isset($disable_trending) ? $disable_trending : it_get_setting('loop_trending_disable');
            $disable_sharing = isset($disable_sharing) ? $disable_sharing : it_get_setting('loop_sharing_disable');
            $rating = isset($rating) ? $rating : false;
            $disable_reviewlabel = isset($disable_reviewlabel) ? $disable_reviewlabel : false;
            #defaults
            $cats = '';
            $updatepagination = 1;
            $perpage = $args['posts_per_page'];
            $posts_shown = $itposts->found_posts;
            if ($posts_shown > $perpage) $posts_shown = $perpage;
            $percol = ceil($posts_shown / 4); #articles per column for new articles panel
            $first = true;
            if ($itposts->have_posts()) : while ($itposts->have_posts()) : $itposts->the_post();
                $p++;

                #get featured video
                $video = get_post_meta(get_the_ID(), "_featured_video", $single = true);

                #get just the primary category id
                $categoryargs = array('postid' => get_the_ID(), 'label' => false, 'icon' => false, 'white' => true, 'single' => true, 'wrapper' => false, 'id' => true);
                $category_id = it_get_primary_categories($categoryargs);

                #re-setup category args for actual display
                $categoryargs = array('postid' => get_the_ID(), 'label' => true, 'icon' => true, 'white' => false, 'single' => false, 'wrapper' => false, 'id' => false, 'size' => 28);

                $awardsargs = array('postid' => get_the_ID(), 'single' => true, 'badge' => false, 'white' => false, 'wrapper' => true);

                $editorargs = array('postid' => get_the_ID(), 'single' => false, 'meter' => false, 'label' => false);

                $userargs = array('postid' => get_the_ID(), 'single' => false, 'user_icon' => false, 'label' => false);

                $likesargs = array('postid' => get_the_ID(), 'label' => false, 'icon' => true, 'clickable' => false, 'showifempty' => false);

                $viewsargs = array('postid' => get_the_ID(), 'label' => false, 'icon' => true);

                $heatargs = array('postid' => get_the_ID(), 'icon' => true, 'tooltip' => false);

                $commentsargs = array('postid' => get_the_ID(), 'label' => false, 'icon' => true, 'showifempty' => false, 'anchor_link' => false);

                $imageargs = array('postid' => get_the_ID(), 'size' => $size, 'width' => $width, 'height' => $height, 'wrapper' => false, 'itemprop' => false, 'link' => false, 'type' => 'normal', 'caption' => false);

                $videoargs = array('url' => $video, 'video_controls' => it_get_setting('loop_video_controls'), 'parse' => true, 'width' => $width, 'height' => $height, 'frame' => true, 'autoplay' => 0, 'type' => 'link');

                $trendingargs = array('postid' => get_the_ID(), 'views' => true, 'likes' => true, 'comments' => true, 'heat' => true, 'label' => true);

                $sharingargs = array('postid' => get_the_ID());

                #get review identifier
                $reviewlabel = (it_has_rating(get_the_ID(), 'user') || it_has_rating(get_the_ID(), 'editor')) && !$disable_reviewlabel ? '<div class="review-star"><span class="theme-icon-star-full"></span></div>' : '';
                #get subtitle
                $subtitle = get_post_meta(get_the_ID(), "_subtitle", $single = true);
                #get rating
                $rating = (it_has_rating(get_the_ID(), 'user') || it_has_rating(get_the_ID(), 'editor')) && $rating ? '<div class="rating-wrapper">' . it_show_editor_rating($editorargs) . it_show_user_rating($userargs) . '</div>' : '';
                #get highlighted label
                $highlighted = it_highlighted(get_the_ID());
                #get sticky indicator
                $stickypost = is_sticky(get_the_ID()) ? '<div class="sticky-post"><span class="theme-icon-pin"></span></div>' : '';

                switch ($location)
                {
                    case 'menu': #articles within the menu

                        $cssimage = has_post_thumbnail(get_the_ID()) ? '' : ' no-image';

                        $out .= '<div class="post-panel ' . $csscol . ' add-active clearfix category-' . $category_id . $cssimage . '">';

                        $out .= '<a class="overlay-link" href="' . get_permalink() . '">&nbsp;</a>';

                        if (has_post_thumbnail(get_the_ID()))
                        {

                            $out .= '<div class="article-image-wrapper">';

                            $out .= '<div class="overlay-hover"><div class="color-line"></div><span class="theme-icon-forward"></span><span class="more-text">' . __('Read More', IT_TEXTDOMAIN) . '</span></div>';

                            $out .= it_featured_image($imageargs);

                            $out .= '</div>';

                        }

                        $out .= '<div class="article-info">';

                        $out .= '<div class="article-title">' . it_title($len) . '</div>';

                        $out .= '</div>';

                        $out .= '</div>';

                        break;
                    case 'compact': #COMPACT

                        $imageargs['size'] = 'square-small';
                        $imageargs['width'] = 68;
                        $imageargs['height'] = 60;

                        $len_title = 70;

                        $cssimage = has_post_thumbnail(get_the_ID()) && $thumbnail ? '' : ' no-image';

                        $out .= '<div class="compact-panel add-active clearfix category-' . $category_id . $cssimage . '">';

                        $out .= '<a class="overlay-link" href="' . get_permalink() . '">&nbsp;</a>';

                        if (has_post_thumbnail(get_the_ID()) && $thumbnail)
                        {

                            $out .= '<div class="article-image-wrapper">';

                            $out .= '<div class="overlay-hover"><span class="theme-icon-forward"></span></div>';

                            $out .= it_featured_image($imageargs);

                            $out .= '</div>';

                        }

                        $out .= '<div class="article-info">';

                        $out .= '<div class="article-title">' . $highlighted . it_title($len_title) . '</div>';

                        $out .= '</div>';

                        $out .= '</div>';

                        break;
                    case 'loop': #LOOP

                        #setup variables
                        $layout = empty($layout) ? '' : $layout;
                        $len_title = 300;
                        $len_excerpt = it_get_setting('loop_excerpt_length');
                        $len_excerpt = !empty($len_excerpt) ? $len_excerpt : 700;
                        $width = 712;
                        $height = 400;
                        $size = 'loop';
                        $labels = 'hide';

                        $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), $size);
                        $csscolor = empty($featured_image[0]) ? ' solid' : '';

                        #loop specific arg adjustments
                        $categoryargs['label'] = false;
                        $categoryargs['wrapper'] = true;
                        $categoryargs['single'] = true;
                        $videoargs['width'] = $width;
                        $videoargs['height'] = $height;

                        #show ads in the loop
                        if (!$disable_ads)
                        {
                            $r++;
                            $the_ad = it_get_ad($ads, $r, $a, $nonajax);
                            $a = $the_ad['adcount']; #get updated ad count
                        }

                        if (!it_get_setting('loop_category_disable')) $cats = it_get_primary_categories($categoryargs);
                        $csscat = !empty($cats) ? '' : ' no-margin';

                        #get title
                        $title = '<h2 class="article-title">';
                        $title .= '<span class="title-text">' . it_title($len_title) . '</span>';
                        $title .= '</h2>';

                        $video_disable = false;
                        if (empty($video) || !it_get_setting('loop_video')) $video_disable = true;
                        $cssvideo = $video_disable ? '' : ' video';

                        #begin html output

                        $out .= $the_ad['ad'];

                        $out .= '<div class="loop-panel content-panel shadowed add-active clearfix category-' . $category_id . $cssvideo . $csscolor . '">';

                        $out .= '<a class="loop-link" href="' . get_permalink() . '">&nbsp;</a>';

                        if (!$video_disable)
                        {

                            $out .= '<a class="styled loop-play colorbox-iframe" title="' . __('Video', IT_TEXTDOMAIN) . '" href="' . it_video($videoargs) . '" data-type="video">';

                            $out .= '<span class="theme-icon-play"></span>';

                            $out .= '</a>';

                        }

                        $out .= '<div class="loop-image-wrapper">';

                        $out .= '<div class="loop-layer"></div>';

                        $out .= '<div class="loop-image" style="background-image:url(' . $featured_image[0] . ');"></div>';

                        $out .= '<div class="loop-hover">';

                        $out .= '<div class="loop-hover-inner">';

                        $out .= '<span class="theme-icon-forward"></span><span class="more-text">' . __('Read More', IT_TEXTDOMAIN) . '</span>';

                        $out .= '</div>';

                        $out .= '</div>';

                        $out .= $reviewlabel;

                        $out .= $stickypost;

                        $out .= '</div>';

                        $out .= '<div class="loop-info-wrapper">';

                        $out .= '<div class="loop-info">';

                        if (!it_get_setting('loop_authorship_disable')) $out .= it_get_authorship('date', false, false);

                        $out .= $title;

                        if (!it_get_setting('loop_excerpt_disable')) $out .= '<div class="excerpt">' . it_excerpt($len_excerpt) . '</div>';

                        $out .= '</div>';

                        $out .= '</div>';

                        $out .= $cats;

                        if (!it_get_setting('loop_trending_disable'))
                            $out .= '<div class="trending-toggle add-active popover-meta" data-content="' . __('Trending', IT_TEXTDOMAIN) . '" data-postid="' . get_the_ID() . '"><span class="theme-icon-trending"></span></div>';

                        if (!it_get_setting('loop_sharing_disable'))
                            $out .= '<div class="sharing-toggle add-active popover-sharing" data-content="' . __('Share This Article', IT_TEXTDOMAIN) . '" data-postid="' . get_the_ID() . '"><span class="theme-icon-share"></span></div>';

                        $out .= it_get_compare_toggle(get_the_ID());

                        if (!it_get_setting('loop_heat_disable')) $out .= it_get_heat_index($heatargs);

                        $out .= '<div class="color-line"></div>';

                        $out .= '</div>';

                        break;
                    case 'widget_a': #WIDGET A

                        $imageargs['size'] = 'square-small';
                        $imageargs['width'] = 68;
                        $imageargs['height'] = 60;

                        $len_title = 80;

                        #category arg adjustments
                        $categoryargs['label'] = false;
                        $categoryargs['wrapper'] = true;
                        $categoryargs['single'] = true;
                        $categoryargs['size'] = 16;
                        if (!it_get_setting('loop_category_disable') && !$disable_category) $cats = it_get_primary_categories($categoryargs);

                        $cssimage = has_post_thumbnail(get_the_ID()) && $thumbnail ? '' : ' no-image';

                        $out .= '<div class="compact-panel widget_a add-active clearfix category-' . $category_id . $cssimage . '">';

                        $out .= $reviewlabel;

                        $out .= '<div class="color-line"></div>';

                        $out .= '<a class="overlay-link" href="' . get_permalink() . '">&nbsp;</a>';

                        if (has_post_thumbnail(get_the_ID()) && $thumbnail)
                        {

                            $out .= '<div class="article-image-wrapper">';

                            $out .= '<div class="overlay-hover"><span class="theme-icon-forward"></span></div>';

                            $out .= it_featured_image($imageargs);

                            $out .= '</div>';

                        }

                        $out .= '<div class="article-info">';

                        $out .= '<div class="article-title">' . $highlighted . it_title($len_title) . '</div>';

                        $out .= $cats;

                        $out .= $rating;

                        if (!it_get_setting('loop_heat_disable')) $out .= it_get_heat_index($heatargs);

                        $out .= '</div>';

                        $out .= '</div>';

                        break;
                    case 'widget_b': #WIDGET B

                        $categoryargs['label'] = false;
                        $categoryargs['wrapper'] = true;
                        $categoryargs['single'] = true;
                        $categoryargs['size'] = 16;
                        if (!it_get_setting('loop_category_disable') && !$disable_category) $cats = it_get_primary_categories($categoryargs);

                        $cols = 3; #recommended is the only place there is currently more than one column, so we don't bother passing this through for now
                        $csscol = isset($csscol) ? $csscol : '';
                        $len_title = 120;

                        $cssimage = has_post_thumbnail(get_the_ID()) ? '' : ' no-image';

                        $out .= '<div class="overlay-panel ' . $csscol . ' add-active clearfix category-' . $category_id . $cssimage . '">';

                        $out .= '<a class="overlay-link" href="' . get_permalink() . '">&nbsp;</a>';

                        if (has_post_thumbnail(get_the_ID()))
                        {

                            $out .= '<div class="article-image-wrapper">';

                            $out .= '<div class="overlay-hover"><span class="theme-icon-forward"></span><span class="more-text">' . __('Read More', IT_TEXTDOMAIN) . '</span></div>';

                            $out .= '<div class="overlay-layer">';

                            $out .= '<div class="color-line"></div>';

                            $out .= '</div>';

                            if (!it_get_setting('loop_heat_disable')) $out .= it_get_heat_index($heatargs);

                            $out .= $reviewlabel;

                            $out .= it_featured_image($imageargs);

                            $out .= '</div>';

                        }

                        $out .= '<div class="article-info">';

                        $out .= '<div class="article-title">' . $highlighted . it_title($len_title) . '</div>';

                        $out .= $cats;

                        $out .= $rating;

                        if (!$disable_trending) $out .= '<div class="trending-toggle add-active popover-meta" data-content="' . __('Trending', IT_TEXTDOMAIN) . '" data-postid="' . get_the_ID() . '"><span class="theme-icon-trending"></span></div>';

                        if (!$disable_sharing) $out .= '<div class="sharing-toggle add-active popover-sharing" data-content="' . __('Share This Article', IT_TEXTDOMAIN) . '" data-postid="' . get_the_ID() . '"><span class="theme-icon-share"></span></div>';

                        $out .= it_get_compare_toggle(get_the_ID());

                        $out .= '</div>';

                        $out .= '</div>';

                        if ($p % $cols == 0) $out .= '<br class="clearer" />';

                        break;
                    case 'widget_c': #WIDGET C

                        $len_title = 120;

                        $out .= '<div class="compact-panel add-active clearfix">';

                        $out .= '<a class="overlay-link" href="' . get_permalink() . '">&nbsp;</a>';

                        $out .= '<div class="article-info">';

                        $out .= '<div class="article-title">' . $highlighted . it_title($len_title) . '</div>';

                        $out .= $rating;

                        $out .= '</div>';

                        $out .= '</div>';

                        break;
                    case 'widget_d': #WIDGET D

                        $imageargs['width'] = 300;
                        $imageargs['height'] = 200;

                        $len_title = 120;

                        if (has_post_thumbnail(get_the_ID()))
                        {

                            $out .= '<div class="overlay-panel add-active clearfix">';

                            $out .= '<a class="overlay-link" href="' . get_permalink() . '">&nbsp;</a>';

                            $out .= '<div class="article-image-wrapper">';

                            $out .= '<div class="overlay-gradient"></div>';

                            $out .= it_featured_image($imageargs);

                            $out .= '</div>';

                            $out .= $rating;

                            $out .= '<div class="article-title">' . it_title($len_title) . '</div>';

                            $out .= '</div>';

                        } else
                        {

                            $out .= '<div class="compact-panel add-active clearfix">';

                            $out .= '<a class="overlay-link" href="' . get_permalink() . '">&nbsp;</a>';

                            $out .= '<div class="article-info">';

                            $out .= '<div class="article-title">' . $highlighted . it_title($len_title) . '</div>';

                            $out .= $rating;

                            $out .= '</div>';

                            $out .= '</div>';

                        }

                        break;
                    case 'widget_e': #WIDGET E

                        $imageargs['width'] = 300;
                        $imageargs['height'] = 200;

                        $len_title = 120;

                        $large = $i == 0 ? true : false;

                        if (has_post_thumbnail(get_the_ID()) && $large)
                        {

                            $out .= '<div class="overlay-panel add-active clearfix">';

                            $out .= '<a class="overlay-link" href="' . get_permalink() . '">&nbsp;</a>';

                            $out .= '<div class="article-image-wrapper">';

                            $out .= '<div class="overlay-gradient"></div>';

                            $out .= it_featured_image($imageargs);

                            $out .= '</div>';

                            $out .= $rating;

                            $out .= '<div class="article-title">' . it_title($len_title) . '</div>';

                            $out .= '</div>';

                        } else
                        {

                            $out .= '<div class="compact-panel add-active clearfix">';

                            $out .= '<a class="overlay-link" href="' . get_permalink() . '">&nbsp;</a>';

                            $out .= '<div class="article-info">';

                            $out .= '<div class="article-title">' . $highlighted . it_title($len_title) . '</div>';

                            $out .= $rating;

                            $out .= '</div>';

                            $out .= '</div>';

                        }

                        break;

                    case 'trending': #TRENDING WIDGET

                        switch ($metric)
                        {
                            case 'heat':
                                $meta = it_get_heat_index($heatargs);
                                break;
                            case 'liked':
                                $meta = it_get_likes($likesargs);
                                break;
                            case 'viewed':
                                $meta = it_get_views($viewsargs);
                                break;
                            case 'commented':
                                $meta = it_get_comments($commentsargs);
                                break;
                            case 'users':
                                $meta = it_show_user_rating($userargs);
                                break;
                            case 'reviewed':
                                $meta = it_show_editor_rating($editorargs);
                                break;
                        }

                        $out .= '<div class="trending-bar add-active bar-' . $i . '">';

                        $out .= '<a class="trending-link" href="' . get_permalink() . '">&nbsp;</a>';

                        $out .= '<div class="title">' . it_title('200') . '</div>';

                        $out .= '<div class="trending-color-wrapper">';
                        $out .= '<div class="trending-color-layer"></div>';
                        $out .= '<div class="trending-color"></div>';
                        $out .= '<div class="trending-meta">' . $meta . '</div>';
                        $out .= '</div>';

                        $out .= '</div>';

                        break;

                    case 'top ten': #TOP TEN WIDGET

                        $len_title = 110;

                        $number = $i + 1;

                        $out .= '<div class="center-panel add-active">';

                        $out .= '<a class="overlay-link" href="' . get_permalink() . '">&nbsp;</a>';

                        $out .= '<div class="topten-number">' . $number . '</div>';

                        $out .= '<div class="topten-hover"><span class="theme-icon-forward"></span></div>';

                        $out .= '<div class="article-title">' . $highlighted . it_title($len_title) . '</div>';

                        $out .= '<div class="border"></div>';

                        $out .= '</div>';

                        break;

                    case 'section': #SECTION WIDGET

                        $len_title = 120;

                        $out .= '<div class="center-panel add-active">';

                        $out .= '<a class="overlay-link" href="' . get_permalink() . '">&nbsp;</a>';

                        $out .= '<div class="article-title">' . $highlighted . it_title($len_title) . '</div>';

                        $out .= '<div class="border"></div>';

                        $out .= '</div>';

                        break;
                }

                $i++; endwhile;
            else:

                $out .= '<div class="filter-error">' . __('Try a different filter', IT_TEXTDOMAIN) . '</div>';
                $updatepagination = 0;

            endif;

            $pages = $itposts->max_num_pages;
            $posts = $posts_shown;
            wp_reset_postdata();

            return array('content' => $out, 'pages' => $pages, 'updatepagination' => $updatepagination, 'posts' => $posts);
        }
    }
?>