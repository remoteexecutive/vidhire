<?php
/**
 * Main loop for displaying jobs
 *
 * @package JobRoller
 * @author AppThemes
 *
 */
?>

<?php appthemes_before_loop('job_listing'); ?>

<?php if (have_posts()) : $alt = 1; ?>

    <ol class="jobs">

        <?php while (have_posts()) : the_post(); ?>

            <?php appthemes_before_post('job_listing'); ?>

            <?php
            $post_class = array('job');
            $expired = jr_check_expired($post);

            if ($expired)
                $post_class[] = 'job-expired';

            $alt = $alt * -1;

            if ($alt == 1)
                $post_class[] = 'job-alt';

            if (!empty($main_wp_query) && jr_is_listing_featured($post->ID, $main_wp_query))
                $post_class[] = 'job-featured';
            ?>

            <li class="<?php echo implode(' ', $post_class); ?>">

                <!--
                  <dl>

                      <dt><?php _e('Type', APP_TD); ?></dt>
                      <dd class="type"><?php jr_get_custom_taxonomy($post->ID, 'job_type', 'jtype'); ?></dd>

                      <dt><?php _e('Job', APP_TD); ?></dt>
                                          
                <?php appthemes_before_post_title('job_listing'); ?>

                      <dd class="title">
                                                  <strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong>
                <?php jr_job_author(); ?>
                      </dd>

                <?php appthemes_after_post_title('job_listing'); ?>

                      <dt><?php _e('Location', APP_TD); ?></dt>
                                          <dd class="location"><?php jr_location(); ?></dd>

                      <dt><?php _e('Date Posted', APP_TD); ?></dt>
                      <dd class="date"><strong><?php echo date_i18n(__('j M', APP_TD), strtotime($post->post_date)); ?></strong> <span class="year"><?php echo date_i18n(__('Y', APP_TD), strtotime($post->post_date)); ?></span></dd>

                  </dl>
                -->

                <div class="job-details-title">
                    <div class="title">
                        <strong><a class="job-title-color" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong>&nbsp;&nbsp;<?php jr_get_custom_taxonomy($post->ID, 'job_type', 'jtype') ?>
                    </div>  

                </div> 

                <div class="job-details">
                    <div>
                        <?php jr_job_author(); ?>
                        <!-- 		
                           <div class="jobs_date"> Posted On : <?php echo date_i18n('j M', strtotime($post->post_date)); ?>,&nbsp;<span class="resume_year"><?php echo date_i18n('Y', strtotime($post->post_date)); ?></span></div> 
                         </div>
                        
                         <div class="location">
                                     <label>Location:</label>
                                 <strong><?php jr_location(); ?></strong>
                                                             </div>        
                        -->      




                    </div>

            </li>

            <?php appthemes_after_post('job_listing'); ?>

        <?php endwhile; ?>

        <?php appthemes_after_endwhile('job_listing'); ?>

    </ol>

<?php if(current_user_can("can_submit_job")) {?>
    <div id="live" class="myjobs_section">
        <!--
                          <h2><?php _e('Jobs', APP_TD); ?></h2>
        -->

        <?php
        global $user_ID;

        if (get_query_var('tab') && 'live' == get_query_var('tab')) {
            $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        } else {
            $paged = 1;
        }

        $args = array(
            'ignore_sticky_posts' => true,
            'author' => $user_ID,
            'post_type' => APP_POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => jr_get_jobs_per_page(),
            'paged' => $_POST['paged-live'],
        );
        $my_query = new WP_Query($args);
        ?>
        <?php if ($my_query->have_posts()) : ?>

            <?php while ($my_query->have_posts()) : ?>

                <?php $my_query->the_post(); ?>

                <?php
                if (get_post_meta($my_query->post->ID, 'jr_total_count', true))
                    $job_views = number_format(get_post_meta($my_query->post->ID, 'jr_total_count', true));
                else
                    $job_views = '-';
                ?>

                <?php if (jr_check_expired($post)) continue; ?>

            <?php endwhile; ?>

            <ol class="jobs">

                <?php while ($my_query->have_posts()) : ?>

                    <?php $my_query->the_post(); ?>

                    <?php
                    if (get_post_meta($my_query->post->ID, 'jr_total_count', true))
                        $job_views = number_format(get_post_meta($my_query->post->ID, 'jr_total_count', true));
                    else
                        $job_views = '-';
                    ?>

                    <?php if (jr_check_expired($post)) continue; ?>

                    <li class="job job-live">

                        <div class="job-details-title">
                            <div class="title">
                                <strong>
                                    <a class="job-title-color" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </strong>
                                <?php jr_get_custom_taxonomy($post->ID, 'job_type', 'jtype'); ?>              
                            </div><!--title-->  

                        </div> <!--job-details-title-->

                        <div class="job-details">
                            <div>
                                <a href="" rel="nofollow"></a>
                                <div class="location">
                                    <strong></strong>
                                </div>        
                                <div class="posted-by">
                                    <?php jr_job_author(); ?>
                                </div>               

                            </div><!--job-details-->

                            <div class="actions">
                                <?php the_job_edit_link($my_query->post->ID); ?>
                                <?php the_job_end_link($my_query->post->ID); ?>
                                <input type="hidden" class="actions_job_id" value="<?php echo $my_query->post->ID; ?>" />
                            </div>
                        </div>
                        <?php
                        $employer_id = get_current_user_id();
                        //$job_title = the_title();

                         $get_employer_jobs = $wpdb->get_results("SELECT distinct(job_applied_to) from wp_resume_statuses where job_owner = $user_ID AND job_applied_to <> ''", ARRAY_N);

                        $employer_jobs = [];

                        if (is_array($get_employer_job)) {
                            foreach ($get_employer_job as $position) {
                                $employer_jobs[] = strval($position[0]);
                            }
                        }
                        $job_count = $wpdb->get_results("SELECT a.job_applied_to, 
                                                                    a.job_slug, 
                                                                    COUNT(a.resume_id) AS count FROM (SELECT DISTINCT (rs.job_applied_to), rs.job_slug, rs.resume_id, rs.job_owner
FROM wp_resume_statuses rs, wp_posts post WHERE rs.job_owner = $employer_id AND rs.job_applied_to !=  ''
AND post.post_author = rs.job_owner
AND post.post_title IN ('" . implode('\',\' ', $employer_jobs) . "'))a GROUP BY a.job_applied_to", ARRAY_A);

                        if (is_array($job_count)) {
                            foreach ($job_count as $count) {
                                if ($count['job_slug'] == $get_employer_job['post_name']) {
                                    ?>                                   
                                    <div class="total_applicants_jobs"><strong>Total Applicants: <span><?php echo $count['count']; ?></span></strong></div>                       
                                    <?php
                                }//inner if statement
                            } //if statement
                        } //foreach count
                        ?>  
                    </li>

                <?php endwhile; ?>
            </ol>        
            <?php jr_paging($my_query, 'paged', array('add_args' => array('tab' => 'live'))); ?>


        <?php else: ?>
            <p><?php _e('No live jobs found.', APP_TD); ?></p>
        <?php endif; ?>

    </div>



    <div id="ended" class="myjobs_section">

        <h2><?php _e('Ended/Expired Jobs', APP_TD); ?></h2>

        <?php
        global $user_ID;

        if (get_query_var('tab') && 'ended' == get_query_var('tab')) {
            $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        } else {
            $paged = 1;
        }

        $args = array(
            'ignore_sticky_posts' => true,
            'author' => $user_ID,
            'post_type' => APP_POST_TYPE,
            'post_status' => 'expired',
            'posts_per_page' => jr_get_jobs_per_page(),
            'paged' => $_POST['paged-ended'],
        );

        $my_query = new WP_Query($args);
        ?>

        <?php if ($my_query->have_posts()): ?>

            <p><?php _e('The following jobs have expired or have been ended and are not visible to users.', APP_TD); ?></p>

            <ol class="jobs">

                <?php while ($my_query->have_posts()) : ?>

                    <?php $my_query->the_post(); ?>

                    <?php
                    if (get_post_meta($my_query->post->ID, 'jr_total_count', true))
                        $job_views = number_format(get_post_meta($my_query->post->ID, 'jr_total_count', true));
                    else
                        $job_views = '-';
                    ?>

                    <!--?php if (jr_check_expired($post)) { continue; } ?-->

                    <li class="job job-ended">

                        <div class="job-details-title">
                            <div class="title">
                                <strong>
                                    <a class="job-title-color" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </strong>
                                <?php jr_get_custom_taxonomy($post->ID, 'job_type', 'jtype'); ?>
                            </div><!--title-->  

                        </div> <!--job-details-title-->

                        <div class="job-details">
                            <div>
                                <a href="" rel="nofollow"></a>
                                <div class="location">
                                    <strong></strong>
                                </div>        
                                <div class="posted-by">
                                    <?php jr_job_author(); ?>
                                </div>               

                            </div><!--job-details-->

                            <div class="actions">
                                <?php the_job_edit_link($my_query->post->ID); ?>
                                <a class="delete" href="#">Delete</a>
                                <a class="relist" href="#">Relist</a>
                                <input type="hidden" class="actions_job_id" value="<?php echo $my_query->post->ID; ?>" />
                            </div>
                        </div></li>

                <?php endwhile; ?>
            </ol>
            <?php jr_paging($my_query, 'paged', array('add_args' => array('tab' => 'ended'))); ?>     
        <?php else: ?>
            <p><?php _e('No expired jobs found.', APP_TD); ?></p>
        <?php endif; ?>

    </div>

<?php } ?>


<?php else: ?>

    <?php appthemes_loop_else('job_listing'); ?>        

<?php endif; ?>

<?php appthemes_after_loop('job_listing'); ?>
