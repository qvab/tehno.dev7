<?php

	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if ( post_password_required() ) { ?>
		<?php _e( 'This post is password protected. Enter the password to view comments.', IT_TEXTDOMAIN ); ?>
	<?php
		return;
	}
?> 

<?php 
$type = 'comment'; //all
if(have_comments()) {
	$label = get_comments_number() . __(' Comments', IT_TEXTDOMAIN);
} else {
	$label = __('Comments', IT_TEXTDOMAIN);	
}
?>
 
<?php echo it_ad_action('comments_before'); ?>
     
<div id="comments" class="magazine-panel">

	<div class="magazine-header">
    
    	<div class="magazine-title"><span class="theme-icon-commented"></span><?php echo esc_attr($label); ?></div>				
					
        <div class="magazine-categories">	
        
            <a class="reply-link" href="#reply-form"><?php _e('Leave a response ',IT_TEXTDOMAIN); ?></a>
            
        </div>
                
		<?php if(get_comment_pages_count() > 1) { ?>
        
            <div class="comment-pagination clearfix" data-number="<?php echo get_option('comments_per_page'); ?>" data-type="<?php echo esc_attr($type); ?>">
            
                <?php $args = array(
                    'prev_text'    => '<span class="theme-icon-previous"></span>',
                    'next_text'    => '<span class="theme-icon-next"></span>',
                    'mid_size'     => '3',
                    'prev_next'	=> true );
                ?> 
                <?php paginate_comments_links($args); ?>
               
            </div>
            
        <?php } ?>
     
     </div>
     
     <div class="magazine-content clearfix">
    
         <ul class="comment-list">            
            <?php wp_list_comments('type='.$type.'&callback=it_comment&max_depth='.get_option('thread_comments_depth')); ?> 
         </ul> 
         
     </div>  
    
</div>

<?php if ( comments_open() ) : ?>

	<div id="reply-form" class="clearfix">

		<?php if ( get_option('comment_registration') && !is_user_logged_in() ) : ?>
        
            <p><?php _e('You must',IT_TEXTDOMAIN); ?>&nbsp;<a href="<?php echo wp_login_url(); ?>" title="<?php _e('log in',IT_TEXTDOMAIN); ?>"><?php _e('log in',IT_TEXTDOMAIN); ?></a>&nbsp;<?php _e('to post a comment',IT_TEXTDOMAIN); ?> </p>
            
        <?php else : ?>
        
            <?php //dislay comment form		
			if(!it_get_setting('review_user_rating_disable') && it_get_setting('review_allow_blank_comments')) {
				$comment_placeholder = __('Additional Comments (optional)',IT_TEXTDOMAIN);
			} else {
				$comment_placeholder = __('Comment',IT_TEXTDOMAIN);
			}
			$fields = array();
			$fields['author'] = '<input id="author" class="form-control" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" placeholder="'.__('Name',IT_TEXTDOMAIN).'" />';
			$comment_field = '<textarea id="comment" class="form-control" name="comment" aria-required="true" rows="8" placeholder="'.$comment_placeholder.'"></textarea>';
			$title_reply = __( 'Leave a Response',IT_TEXTDOMAIN ).'';	
			$title_reply_to = __( 'Leave a Reply to %s',IT_TEXTDOMAIN ).'';	
            $commentargs = array(
                'comment_notes_before' => '',
                'comment_notes_after'  => '',
				'fields'               => $fields,
                'comment_field' 	   => $comment_field,
                'title_reply'          => $title_reply,
                'title_reply_to'       => $title_reply_to,
				'label_submit'         => __('Post',IT_TEXTDOMAIN),
            );
            ?>
        
            <?php comment_form($commentargs); ?> 
    
        <?php endif; // If registration required and not logged in ?>
    
    </div>

<?php endif; ?>

<?php echo it_ad_action('comments_after'); ?>