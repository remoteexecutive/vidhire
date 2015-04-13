<?php
/**
 * @package AppThemes
 * @subpackage JobRoller
 * Comments for the job posts
 *This is the editable comments.php
 */
// Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die(__('Please do not load this page directly. Thanks!', APP_TD));

if (post_password_required()) {
    ?>
    <p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.', APP_TD); ?></p>
    <?php
    return;
}
?>

<div class="section">

    <div class="section_content comment-container">

        <?php appthemes_before_pings(); ?>

        <?php if (!empty($comments_by_type['pings'])) : // if have pings  ?>

            <h2><?php _e('Trackbacks/Pingbacks', APP_TD); ?></h2>

            <ol id="comment-list" class="commentlist">

                <?php appthemes_list_pings(); ?>

            </ol>

        <?php endif; ?>

        <?php appthemes_after_pings(); ?>



        <?php appthemes_before_comments(); ?>

        <?php if (have_comments()) : ?>

            <!--
            <h2><?php comments_number(__('No Responses', APP_TD), __('One Response', APP_TD), __('% Responses', APP_TD)); ?> <?php _e('to', APP_TD); ?> &#8220;<?php the_title(); ?>&#8221;</h2>
            -->

            <?php if (is_singular('job_listing')) { ?>
                <h2 class="comment-header">Job Discussions</h2>
                <ol id="comment-list" class="commentlist">

                    <?php
                    global $wpdb;

                    $employer_id = get_current_user_id();

                    $get_comments = $wpdb->get_results("SELECT a.comment_author, a.comment_content, b.post_title, a.user_id, a.comment_date, c.user_login, b.guid
FROM wp_comments a, wp_posts b,wp_users c
WHERE b.post_type =  'job_listing'
AND b.ID = a.comment_post_id
AND b.post_author = $employer_id
AND c.ID = a.user_id
ORDER BY a.comment_date DESC 
LIMIT 0 , 5", ARRAY_A);
                    for ($i = 0; $i < count($get_comments); ++$i) {
                        ?>
                        <!--
                            <li class="recentcomments">
                                <span class="comment-author-link"><strong><?php echo $get_comments[$i]['comment_author'] ?>.. </strong></span> 
                          <p>
                        <?php echo $get_comments[$i]['comment_content']; ?>
                                <br />
                            <a href="<?php echo $get_comments[$i]['guid'] ?>"><?php echo $get_comments[$i]['post_title'] ?></a>
                          </p>
                          
                        </li>
                        -->
                        <li class="recentcomments">
                            <div class="comment_container">
                                <div class="avatar-container">
                                    <?php echo get_avatar($get_comments[$i]['user_id'], $size = '48'); ?>
                                    <br />
                                    <text>
                                    <span class="comment-author-link"><text>By:</text> <strong><?php echo $get_comments[$i]['comment_author'] ?> </strong></span>
                                    <br />
                                    <a href="<?php echo $get_comments[$i]['guid'] . "#comment-" . $get_comments[$i]['comment_id'] ?>"><?php echo $get_comments[$i]['post_title'] ?></a>
                                    </text>    
                                </div>
                                <div class="comment-text">
                                    <p><?php echo trim($get_comments[$i]['comment_content']); ?></p>
                                </div>                            
                            </div>
                            <br />
                        </li>
                    <?php } ?>

                </ol>
            <?php } else if (is_singular('resume')) { ?>
                <h2 class="comment-header">Resume Discussions</h2>
                <ol id="comment-list" class="commentlist">

                    <?php
                    global $wpdb;

                    $employer_id = get_current_user_id();

                    $get_comments = $wpdb->get_results("SELECT a.comment_post_id,a.comment_author, "
                    . "a.comment_content, b.post_title, a.user_id, a.comment_date, c.user_login, b.guid,a.comment_id "
                    . "FROM wp_comments a, wp_posts b,wp_users c "
                    . "WHERE b.post_type = 'resume' "
                    . "AND b.ID = a.comment_post_id AND b.post_author in (SELECT distinct(post_author) FROM wp_posts where post_type = 'resume' AND ID in ( SELECT distinct(resume_id) FROM wp_resume_statuses where job_owner = $employer_id )) "
                    . "AND c.ID = a.user_id ORDER BY a.comment_date DESC", ARRAY_A);
                    
                    for ($i = 0; $i < count($get_comments); ++$i) {
                        ?>
                        <!--
                            <li class="recentcomments">
                                <span class="comment-author-link"><strong><?php echo $get_comments[$i]['comment_author'] ?>.. </strong></span> 
                          <p>
                        <?php echo $get_comments[$i]['comment_content']; ?>
                                <br />
                            <a href="<?php echo $get_comments[$i]['guid'] ?>"><?php echo $get_comments[$i]['post_title'] ?></a>
                          </p>
                          
                        </li>
                        -->
                        <li class="recentcomments">
                            <div class="comment_container">
                                <div class="avatar-container">
                                    <?php echo get_avatar($get_comments[$i]['user_id'], $size = '48'); ?>
                                    <br />
                                    <text>
                                    <span class="comment-author-link"><text>By:</text> <strong><?php echo $get_comments[$i]['comment_author'] ?> </strong></span>
                                    <br />
                                    <a href="<?php echo $get_comments[$i]['guid'] . "#comment-" . $get_comments[$i]['comment_id'] ?>"><?php echo $get_comments[$i]['post_title'] ?></a>
                                    </text>    
                                </div>
                                <div class="comment-text">
                                    <p><?php echo trim($get_comments[$i]['comment_content']); ?></p>
                                </div>                            
                            </div>
                            <br />
                        </li>
                    <?php } ?>
                </ol>  
            <?php } else { ?>
                <h2 class="comment-header">Discussions</h2>
                <ol id="comment-list" class="commentlist">
                    <?php appthemes_list_comments(); ?>
                </ol>
            <?php } ?>

            <div class="comment-paging">

                <?php paginate_comments_links(); ?>

            </div><!-- end comment-paging -->

        <?php endif; ?>

        <?php appthemes_after_comments(); ?>



        <?php if (!comments_open() && have_comments()) : ?>

            <p><?php _e('Sorry, the comment form is closed at this time.', APP_TD); ?></p>

        <?php endif; ?>		



        <?php if ('open' == $post->comment_status) : ?>

            <?php appthemes_before_respond(); ?>

            <div id="respond">

                <?php if (get_option('comment_registration') && !$user_ID) : ?>

                                                    <!--h3><?php _e('Send Message to Email', APP_TD); ?></h3-->

                    <p><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.', APP_TD), wp_login_url(get_permalink())); ?></p>

                <?php else : ?>

                    <?php appthemes_before_comments_form(); ?>

                    <?php appthemes_comments_form(); ?>

                    <?php appthemes_after_comments_form(); ?>

                <?php endif; ?>

            </div><!-- end respond -->

            <?php appthemes_after_respond(); ?>

        <?php endif; ?>

    </div><!-- end section_content -->

</div><!-- end section -->
