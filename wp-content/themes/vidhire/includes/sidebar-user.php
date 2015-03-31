<?php if (current_user_can('manage_options')) { ?>
    <li class="widget widget_user_info myjobs_dashboard_admin">

        <a href="http://vidhire.net/wp-admin/">Admin</a>

    </li>

    <?php the_widget('WP_Widget_Recent_Posts'); ?> 
<?php } ?>

<?php if (current_user_can('can_submit_job')) { ?>
    <li class="widget widget_user_info myjobs_dashboard_admin">

        <?php
        global $wpdb;

        $employer_id = get_current_user_id();

        $resumes = $wpdb->get_row("SELECT count(distinct(resume_id)) as count from wp_resume_statuses WHERE job_owner = $employer_id AND job_applied_to <> ''", ARRAY_A);

        //$resumes = wp_count_posts( APP_POST_TYPE_RESUME );
        ?>

        Live Resumes: <?php echo $resumes['count']; ?>

    </li>
<?php } ?>

<?php if (is_user_logged_in() && current_user_can('can_submit_job')) : ?>
    <li class="widget widget-nav">

        <ul class="display_section">
            <!--
      <li><a href="#browseby" class="noscroll"><?php _e('Browse by&hellip;', APP_TD); ?></a></li>
            -->
            <li><a href="#groups" class="noscroll"><?php _e('Status', APP_TD); ?></a></li>
            <li><a href="#specialities" class="noscroll"><?php _e('Skills', APP_TD); ?></a></li>
        </ul>



        <!--
        <div id="browseby" class="tabbed_section"><div class="contents">
            <ul>
        <?php
        // By Cat
        $args = array(
            'hierarchical' => false,
            'parent' => 0
        );
        $terms = get_terms('resume_category', $args);
        if ($terms) :
            echo '<li><a class="top" href="#open">' . __('Job Category', APP_TD) . '</a> <ul>';

            foreach ($terms as $term) :
                echo '<li class="page_item ';
                if (isset($wp_query->queried_object->slug) && $wp_query->queried_object->slug == $term->slug)
                    echo 'current_page_item';
                echo '"><a href="' . get_term_link($term->slug, 'resume_category') . '">' . $term->name . '</a></li>';
            endforeach;

            echo '</ul></li>';
        endif;


        // By Job Type
        $args = array(
            'hierarchical' => false,
            'parent' => 0
        );
        $terms = get_terms('resume_job_type', $args);
        if ($terms) :
            echo '<li><a class="top" href="#open">' . __('Job Type', APP_TD) . '</a> <ul>';

            foreach ($terms as $term) :
                echo '<li class="page_item ';
                if (isset($wp_query->queried_object->slug) && $wp_query->queried_object->slug == $term->slug)
                    echo 'current_page_item';
                echo '"><a href="' . get_term_link($term->slug, 'resume_job_type') . '">' . $term->name . '</a></li>';
            endforeach;

            echo '</ul></li>';
        endif;

        // By Spoken Languages
        $args = array(
            'hierarchical' => false,
            'parent' => 0
        );
        $terms = get_terms('resume_languages', $args);
        if ($terms) :
            echo '<li><a class="top" href="#open">' . __('Spoken Languages', APP_TD) . '</a> <ul>';

            foreach ($terms as $term) :
                echo '<li class="page_item ';
                if (isset($wp_query->queried_object->slug) && $wp_query->queried_object->slug == $term->slug)
                    echo 'current_page_item';
                echo '"><a href="' . get_term_link($term->slug, 'resume_languages') . '">' . $term->name . '</a></li>';
            endforeach;

            echo '</ul></li>';
        endif;
        ?>
                        
        <?php jr_sidebar_resume_nav_browseby(); ?>
                        
                        <li><a class="top" href="<?php echo get_post_type_archive_link('resume'); ?>"><?php _e('View all resumes', APP_TD); ?></a></li>
                        
            </ul>
        </div></div>
        -->

        <div id="groups" class="tabbed_section"><div class="contents">
                <?php
                /*
                  Get the current statuses of all resumes with no duplicates
                 */
                global $wpdb;

                $employer_id = get_current_user_id();

                $resume_statuses = $wpdb->get_results("SELECT distinct(fast_tracked),reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM wp_resume_statuses where employer_id = $employer_id", ARRAY_N);

                $unique_resume_statuses = [];

                for ($i = 0; $i < count($resume_statuses); ++$i) {
                    for ($j = 0; $j < 6; ++$j) {
                        if (!in_array($resume_statuses[$i][$j], $unique_resume_statuses)) {
                            $unique_resume_statuses[] = $resume_statuses[$i][$j];
                        }
                    }
                }


                //var_dump($unique_resume_statuses);

                $args = array(
                    'hierarchical' => false,
                    'parent' => 0
                );
                $terms = get_terms('resume_groups', $args);
                if ($terms) :
                    echo '<ul class="job_tags">';

                    foreach ($terms as $term) :
                        if (in_array($term->name, $unique_resume_statuses)) {
                            echo '<li><a href="' . get_term_link($term->slug, 'resume_groups') . '">' . $term->name . '</a></li>';
                        }
                    endforeach;

                    echo '</ul>';
                endif;
                ?>
            </div></div>
        <div id="specialities" class="tabbed_section"><div class="contents">
                <?php
                $args = array(
                    'hierarchical' => false,
                    'parent' => 0
                );
                $terms = get_terms('resume_specialities', $args);
                if ($terms) :
                    echo '<ul class="job_tags">';

                    foreach ($terms as $term) :
                        echo '<li><a href="' . get_term_link($term->slug, 'resume_specialities') . '">' . $term->name . '</a></li>';
                    endforeach;

                    echo '</ul>';
                endif;
                ?>
            </div></div>
        <?php if (current_user_can('can_submit_resume')): ?>
        <li class="widget widget_user_info widget_latest_jobs">

            <?php the_widget('JR_Widget_Recent_Jobs', 'title=Latest Jobs'); ?>

        </li>
    <?php else : ?>
        <li class="widget widget_user_info widget_applicants_per_job">

            <!-?php the_widget('JR_Widget_Resume_Categories','title=Applicants Per Job'); ?-->

            <!--
            <div class="widget widget_resume_categories">
                <h2 class="widgettitle">Applicants Per Job</h2>
                <ul class="resume_categories_ul">
            <?php
            global $wpdb;

            $args = array(
                'hierarchical' => false,
                'parent' => 0
            );

            $employer_id = get_current_user_id();

            $get_employer_jobs = $wpdb->get_results("SELECT distinct(post_name) FROM wp_posts WHERE post_author in ('" . $employer_id . "') AND post_type = 'job_listing' AND post_status in ('publish')", ARRAY_A);

            $employer_jobs = [];

            foreach ($get_employer_jobs as $position) {
                $employer_jobs[] = $position["post_name"];
            }

            $job_count = $wpdb->get_results("SELECT a.job_applied_to, a.job_slug, COUNT(a.resume_id ) AS count FROM (SELECT DISTINCT (rs.job_applied_to), rs.job_slug, rs.resume_id, rs.job_owner
FROM wp_resume_statuses rs, wp_posts post WHERE rs.job_owner = $employer_id AND rs.job_applied_to !=  ''
AND post.post_author = rs.job_owner
AND post.post_name IN ('" . implode('\',\' ', $employer_jobs) . "'))a GROUP BY a.job_applied_to", ARRAY_A);


            /* var_dump("SELECT a.job_applied_to, a.job_slug, COUNT(a.resume_id 	) AS count FROM (SELECT DISTINCT (rs.job_applied_to), rs.job_slug, rs.resume_id, rs.job_owner
              FROM wp_resume_statuses rs, wp_posts post WHERE rs.job_owner = $employer_id AND rs.job_applied_to !=  ''
              AND post.post_author = rs.job_owner
              AND post.post_name IN ('".implode('\',\' ',$employer_jobs)."'))a GROUP BY a.job_applied_to"); */

            foreach ($job_count as $job) {

                echo '<li class="cat-item"><a href="/resume/category/' . $job["job_slug"] . '">' . $job["job_applied_to"] . '</a> (' . $job["count"] . ')</li>';
            }
            ?>	  

                </ul>
            </div>
            -->
        </li>
    <?php endif; ?>
<?php endif; ?>  

<?php if (current_user_can('can_submit_job')) { ?> 
    <li class="widget widget_recent_comments">
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
LIMIT 0 , 3", ARRAY_A);
        ?>	
        <h2 class="widget_title">Jobs Discussions</h2>
        <div class="widget_content">
            <ul id="recentcomments">
                <?php for ($i = 0; $i < count($get_comments); ++$i) { ?>
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
            </ul>  
        </div> 
    </li>
    <li class="widget widget_recent_comments">
        <?php
        global $wpdb;
        global $current_user;
        get_currentuserinfo();

        $employer_id = get_current_user_id();

        $get_resume_comments = $wpdb->get_results("SELECT a.comment_post_id,a.comment_author, "
                . "a.comment_content, b.post_title, a.user_id, a.comment_date, c.user_login, b.guid,a.comment_id "
                . "FROM wp_comments a, wp_posts b,wp_users c "
                . "WHERE b.post_type = 'resume' "
                . "AND b.ID = a.comment_post_id AND b.post_author in (SELECT distinct(post_author) FROM wp_posts where post_type = 'resume' AND ID in ( SELECT distinct(resume_id) FROM wp_resume_statuses where job_owner = $employer_id )) "
                . "AND c.ID = a.user_id ORDER BY a.comment_date DESC LIMIT 0 , 3", ARRAY_A);
        ?>
        <h2 class="widget_title">Applicants Discussions</h2>
        <div class="widget_content">
            <ul id="recentcomments">
                <?php for ($i = 0; $i < count($get_resume_comments); ++$i) { ?>
                    <!--
                    <li class="recentcomments">
                        <span class="comment-author-link"><strong><?php echo $get_resume_comments[$i]['comment_author'] ?>.. </strong></span>    
                        <p>
                    <?php echo $get_resume_comments[$i]['comment_content']; ?>
                            <br />
                            <a href="<?php echo $get_resume_comments[$i]['guid'] . "#comment-" . $get_resume_comments[$i]['comment_id'] ?>"><?php echo $get_resume_comments[$i]['post_title'] ?></a>
                        </p>
                    </li>
                    -->

                    <li class="recentcomments">
                         <div class="comment_container">
                            <div class="avatar-container">
                                <?php echo get_avatar($get_resume_comments[$i]['user_id'], $size = '48'); ?>
                                <br />
                                <text>
                                <span class="comment-author-link"><text>By:</text> <strong><?php echo $get_resume_comments[$i]['comment_author'] ?> </strong></span>
                                <br />
                                <a href="<?php echo $get_resume_comments[$i]['guid'] . "#comment-" . $get_resume_comments[$i]['comment_id'] ?>"><?php echo $get_resume_comments[$i]['post_title'] ?></a>
                                </text>    
                            </div>
                            <div class="comment-text">
                                <p><?php echo trim($get_resume_comments[$i]['comment_content']); ?></p>
                            </div>                            
                        </div>
                        <br />
                    </li>

                <?php } ?>
            </ul>  
    </li>

<?php } ?>

<script type="text/javascript">
    /* <![CDATA[ */
    jQuery('ul.widgets li.widget.widget-nav div ul li ul, ul.widgets li.widget.widget-nav div').hide();
    jQuery('.widget-nav div.tabbed_section:eq(0), .widget-nav div.tabbed_section:eq(0) .contents').show();
    jQuery('.widget-nav ul.display_section li:eq(0)').addClass('active');

    // Tabs
    jQuery('.widget-nav ul.display_section li a').click(function () {

        jQuery('.widget-nav div.tabbed_section .contents').fadeOut();
        jQuery('.widget-nav div.tabbed_section').hide();

        jQuery(jQuery(this).attr('href')).show();
        jQuery(jQuery(this).attr('href') + ' .contents').fadeIn();

        jQuery('.widget-nav ul.display_section li').removeClass('active');
        jQuery(this).parent().addClass('active');

        return false;
    });

    // Sliding
    jQuery('ul.widgets li.widget.widget-nav div ul li a.top').click(function () {
        jQuery(this).parent().find('ul').slideToggle();
    });

    /* ]]> */
</script>
</li>

