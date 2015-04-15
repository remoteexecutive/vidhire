<?php
jr_resume_page_auth();

$errors = new WP_Error();
$resume_access_level = 'all';

### Visibility check
if (!jr_resume_is_visible('single') && $post->post_author != get_current_user_id()) :

    $errors->add('resume_error', __('Sorry, you do not have permission to view individual resumes.', APP_TD));

    if (jr_current_user_can_subscribe_for_resumes())
        $resume_access_level = 'subscribe';
    else
        $resume_access_level = 'none';

endif;

### Publish

if (isset($_GET['publish']) && $_GET['publish'] && $post->post_author == get_current_user_id()) :

    $post_id = $post->ID;
    $post_to_edit = get_post($post_id);

    global $user_ID;

    if ($post_to_edit->ID == $post_id && $post_to_edit->post_author == $user_ID) :
        $update_resume = array();
        $update_resume['ID'] = $post_to_edit->ID;
        if ($post_to_edit->post_status == 'private') :
            $update_resume['post_status'] = 'publish';
        else :
            $update_resume['post_status'] = 'private';
        endif;
        wp_update_post($update_resume);
        wp_safe_redirect(get_permalink($post_to_edit->ID));
    endif;

endif;

$show_contact_form = (get_option('jr_resume_show_contact_form') == 'yes');
?>

<div class="section single">

    <?php do_action('appthemes_notices'); ?>

    <?php appthemes_before_loop(); ?>

    <?php if ($resume_access_level != 'none' && have_posts()): ?>

        <?php while (have_posts()) : the_post(); ?>

            <?php appthemes_before_post(); ?>

            <?php jr_resume_header($post); ?>

            <?php appthemes_stats_update($post->ID); //records the page hit ?>

            <div class="section_header resume_header">

                <?php appthemes_before_post_title(); ?>

                <?php
                if ($resume_access_level == 'subscribe'):

                    if ($notice = get_option('jr_resume_subscription_notice'))
                        echo '<p>' . wptexturize($notice) . '</p>';

                    the_resume_purchase_plan_link();

                    echo '<div class="clear"></div>';

                else:
                    ?>


                    <?php
                    if (has_post_thumbnail()) {
                        the_post_thumbnail('thumbnail');
                    }
                    ?>


                    <?php
                    global $post;

                    $job_terms = wp_get_post_terms($post->ID, 'resume_category');

                    $get_job_owner = $wpdb->get_row("SELECT distinct(post_author) as job_owner, ID as job_id FROM wp_posts WHERE post_name in ('" . $job_terms[0]->slug . "')");

                    $job_owner = $get_job_owner->job_owner;
                    ?>
                    <input class="resume_id" type="hidden" value="<?php echo $post->ID; ?> "/>
                    <input class="employer_id" type="hidden" value="<?php echo $job_owner; ?> "/>
                    <h1 class="title resume-title"><span><?php the_title(); ?></span></h1>

                    <?php
                    echo '<div class="posted-by-container">';
                    if ($posted_by = get_the_author_meta('display_name')):
                        echo __('Posted by: ', APP_TD);
                        echo '<strong>' . wptexturize($posted_by) . '</strong> on ';
                        echo the_date();
                        echo '</div>';
                    endif;
                    ?>
                    <br />
                    <table class="toggle-processing-status" style="font-size: 12px;">

                        <?php
                        /*
                          Queries for toggling resume statuses
                         */
                        global $wpdb, $post;

                        $employer_id = get_current_user_id();

                        $resume_options = $wpdb->get_row("SELECT fast_tracked,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM wp_resume_statuses where resume_id = $post->ID AND employer_id = $employer_id", ARRAY_A);

                        /* Fast Tracked HTML */

                        if (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['fast_tracked'] == 'Standard Tracked') {
                            ?>
                            <tr>        
                                <td class="fast-track"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/orange-check-mark.png" /><a href="<?php echo add_query_arg('fast-track', 'true', '') . '&resume_id=' . $post->ID; ?>" class="fast-track"><?php echo $resume_options['fast_tracked']; ?></a></td>

                            <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['fast_tracked'] == 'Fast Tracked') { ?>
                            <tr>
                                <td class="fast-track"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/green-check-mark.png" /><a href="<?php echo add_query_arg('fast-track', 'insufficient', '') . '&resume_id=' . $post->ID; ?>" class="fast-track"><?php echo $resume_options['fast_tracked']; ?></a></td>

                            <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['fast_tracked'] == 'Insufficient Skills') { ?>

                                <td class="fast-track"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/red-flag-check.gif" /><a href="<?php echo add_query_arg('fast-track', 'false', '') . '&resume_id=' . $post->ID; ?>" class="fast-track"><?php echo $resume_options['fast_tracked']; ?></a></td>    


                                <?php
                            }

                            /* Reference Checked HTML */
                            if (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['reference_checked'] == 'Check Reference') {
                                ?>

                                <td class="reference-checked"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/orange-check-mark.png" /><a href="<?php echo add_query_arg('reference-checked', 'true', '') . '&resume_id=' . $post->ID; ?>" class="reference-checked"><?php echo $resume_options['reference_checked']; ?></a></td>

                            <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['reference_checked'] == 'References Checked') { ?>


                                <td class="reference-checked"><a href="<?php echo add_query_arg('reference-checked', 'false', '') . '&resume_id=' . $post->ID; ?>" class="reference-checked"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/green-check-mark.png" /><?php echo $resume_options['reference_checked']; ?></a></td>

                                <?php
                            }
                            /* Highest Rated HTML */
                            if (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['starred'] == 'Pick') {
                                ?>  

                                <td class="highest-rated"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/orange-check-mark.png" /><a href="<?php echo add_query_arg('star-resume', 'second', '') . '&resume_id=' . $post->ID; ?>" class="highest-rated"><?php echo $resume_options['starred']; ?></a></td>

                            <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['starred'] == '2nd Highest Rated') { ?>

                                <td class="highest-rated"><a href="<?php echo add_query_arg('star-resume', 'first', '') . '&resume_id=' . $post->ID; ?>" class="highest-rated"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/green-check-mark.png" /><?php echo $resume_options['starred']; ?></a></td>

                            <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['starred'] == 'Highest Rated') { ?>
                                <td class="highest-rated"><a href="<?php echo add_query_arg('star-resume', 'unrated', '') . '&resume_id=' . $post->ID; ?>" class="highest-rated"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/green-check-mark.png" /><?php echo $resume_options['starred']; ?></a></td>						




                                <?php
                            }

                            /* Video Interview Evaluated HTML */
                            if (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['video_interview'] == 'No Video') {
                                ?>
                            </tr>		
                            <tr>          
                                <td class="video-interview-evaluated"><a href="<?php echo add_query_arg('video-interview-evaluated', 'submitted', '') . '&resume_id=' . $post->ID; ?>" class="video-interview-evaluated"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/red-flag-check.gif" /><?php echo $resume_options['video_interview']; ?></a></td>

                            <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['video_interview'] == 'Video Submitted') { ?>
                            </tr>		
                            <tr> 
                                <td class="video-interview-evaluated"><a href="<?php echo add_query_arg('video-interview-evaluated', 'evaluated', '') . '&resume_id=' . $post->ID; ?>" class="video-interview-evaluated"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/orange-check-mark.png" /><?php echo $resume_options['video_interview']; ?></a></td>

                            <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['video_interview'] == 'Video Evaluated') { ?>
                            </tr>		
                            <tr>
                                <td class="video-interview-evaluated"><a href="<?php echo add_query_arg('video-interview-evaluated', 'false', '') . '&resume_id=' . $post->ID; ?>" class="video-interview-evaluated"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/green-check-mark.png" /><?php echo $resume_options['video_interview']; ?></a></td>
                                <?php
                            }

                            /* No Red Flags HTML */
                            if (is_user_logged_in() && current_user_can('can_submit_job') && trim($resume_options['red_flagged']) == 'Check For Red Flags') {
                                ?>
                                <td class="no-red-flags"><a href="<?php echo add_query_arg('no-red-flags', 'false', '') . '&resume_id=' . $post->ID; ?>" class="no-red-flags"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/orange-check-mark.png" /><?php echo $resume_options['red_flagged']; ?></a></td>
                            <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && trim($resume_options['red_flagged']) == 'Red Flagged') { ?>
                                <td class="no-red-flags"><a href="<?php echo add_query_arg('no-red-flags', 'true', '') . '&resume_id=' . $post->ID; ?>" class="no-red-flags"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/red-flag-check.gif" /><?php echo $resume_options['red_flagged']; ?></a></td>
                            <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && trim($resume_options['red_flagged']) == 'No Red Flags') { ?>
                                <td class="no-red-flags"><a href="<?php echo add_query_arg('no-red-flags', 'checking', '') . '&resume_id=' . $post->ID; ?>" class="no-red-flags"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/green-check-mark.png" /><?php echo $resume_options['red_flagged']; ?></a></td>

                                <?php
                            }

                            /* Completed Evaluation HTML */
                            if (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['completed_evaluation'] == 'Evaluate') {
                                ?>  
                                <td class="completed-evaluation"><a href="<?php echo add_query_arg('completed-evaluation', 'true', '') . '&resume_id=' . $post->ID; ?>" class="completed-evaluation"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/orange-check-mark.png" /><?php echo $resume_options['completed_evaluation']; ?></a></td>
                            <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['completed_evaluation'] == 'Completed Evaluation') { ?>
                                <td class="completed-evaluation"><a href="<?php echo add_query_arg('completed-evaluation', 'false', '') . '&resume_id=' . $post->ID; ?>" class="completed-evaluation"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/green-check-mark.png" /><?php echo $resume_options['completed_evaluation']; ?></a></td>

                            <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['completed_evaluation'] == 'First') { ?>   

                                <td class="completed-evaluation"><a href="<?php echo add_query_arg('completed-evaluation', 'second', '') . '&resume_id=' . $post->ID; ?>" class="completed-evaluation"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/green-check-mark.png" /><?php echo $resume_options['completed_evaluation']; ?></a></td>

                            <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['completed_evaluation'] == 'Second') { ?>

                                <td class="completed-evaluation"><a href="<?php echo add_query_arg('completed-evaluation', 'third', '') . '&resume_id=' . $post->ID; ?>" class="completed-evaluation"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/green-check-mark.png" /><?php echo $resume_options['completed_evaluation']; ?></a></td>

                            <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['completed_evaluation'] == 'Third') { ?>   

                                <td class="completed-evaluation"><a href="<?php echo add_query_arg('completed-evaluation', 'true', '') . '&resume_id=' . $post->ID; ?>" class="completed-evaluation"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/green-check-mark.png" /><?php echo $resume_options['completed_evaluation']; ?></a></td>	

                            <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['completed_evaluation'] == 'Hired') { ?>
                                <td class="completed-evaluation"><a href="<?php echo add_query_arg('completed-evaluation', 'true', '') . '&resume_id=' . $post->ID; ?>" class="completed-evaluation"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/green-check-mark.png" /><?php echo $resume_options['completed_evaluation']; ?></a></td>
                            <?php } ?>    
                        </tr> 
                    </table><!--toggle-processing-status-->


                    <div class="user_prefs_wrap" style="display: none"><?php echo jr_seeker_prefs(get_the_author_meta('ID')); ?></div>

                    <?php
                    if ($post->post_status == 'private' && $post->post_author == get_current_user_id())
                        appthemes_display_notice('success', sprintf(__('Your resume is currently hidden &mdash; <a href="%s">click here to publish it</a>.', APP_TD), add_query_arg('publish', 'true')));
                    ?>

                    <p class="meta"><?php
                        /* echo __('Resume of ',APP_TD) . '<strong>' .wptexturize(get_the_author_meta('display_name')) . '</strong>'; */

                        $terms = wp_get_post_terms($post->ID, 'resume_category');
                        $currency = get_post_meta($post->ID, 'currency', true);

                        if ($terms) :
                            //echo '<br />';
                            _e(' Applying For: ', APP_TD);
                            echo '<strong>' . $terms[0]->name . '</strong> ';
                        endif;

                        if ($desired_salary = get_post_meta($post->ID, '_desired_salary', true)) :
                            echo sprintf(__('<br/>Minimum Hourly Rate: <strong>%s %s</strong> ', APP_TD), $desired_salary, $currency);
                        endif;

                        $desired_position = wp_get_post_terms($post->ID, 'resume_job_type');
                        if ($desired_position) :
                            $desired_position = current($desired_position);
                            echo '<br/>' . sprintf(__('Desired Position Type: <strong>%s</strong><br /> ', APP_TD), $desired_position->name);
                        else :
                            echo '<br/>' . __('Desired Position Type: <strong>Any</strong><br /> ', APP_TD);
                        endif;
                        ?>

                        <?php
                        $contact_details = array();
                        $contact_details['mobile'] = get_post_meta($post->ID, '_mobile', true);
                        $contact_details['tel'] = get_post_meta($post->ID, '_tel', true);
                        $contact_details['email_address'] = get_post_meta($post->ID, '_email_address', true);
                        $contact_details['skype'] = get_post_meta($post->ID, 'skype', true);
                        if ($show_contact_form && $post->post_author != get_current_user_id()):
                            echo '<p class="button"><a class="contact_button inline noscroll" href="#contact">' . sprintf(__('Contact %s', APP_TD), wptexturize(get_the_author_meta('display_name'))) . '</a></p>';
                        else:
                            if ($contact_details && is_array($contact_details) && sizeof($contact_details) > 0) :

                                //echo '<dl>';
                                if ($contact_details['email_address'])
                                //echo '<dt class="email">' . __('Email', APP_TD) . ':</dt><dd><a href="mailto:' . $contact_details['email_address'] . '?subject=' . __('Your Resume on', APP_TD) . ' ' . get_bloginfo('name') . '">' . $contact_details['email_address'] . '</a></dd>';
                                    echo __('Email', APP_TD) . ": " . '<strong><a href="mailto:' . $contact_details['email_address'] . '?subject=' . __('Your Resume on', APP_TD) . ' ' . get_bloginfo('name') . '">' . $contact_details['email_address'] . '</a></strong><br />';
                                if ($contact_details['tel'])
                                //echo '<dt class="tel">' . __('Tel', APP_TD) . ':</dt><dd>' . $contact_details['tel'] . '</dd>';
                                    echo __('Tel', APP_TD) . ': <strong>' . $contact_details['tel'] . '</strong><br />';
                                if ($contact_details['mobile'])
                                //echo '<dt class="mobile">' . __('Mobile', APP_TD) . ':</dt><dd>' . $contact_details['mobile'] . '</dd>';
                                    echo __('Mobile', APP_TD) . ': <strong>' . $contact_details['mobile'] . '</strong><br />';
                                if ($contact_details['skype'])
                                //echo '<dt class="skype">' . __('Skype', APP_TD) . ':</dt><dd>'
                                    echo __('Skype', APP_TD) . ': ';
                                ?>											
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <a href="<?php echo 'skype:<strong>' . $contact_details['skype'] . '</strong>'; ?>"><!--img height="20" weight="20" src="<?php bloginfo('template_url') ?>/images/social-skype-button-blue-icon.png" /--><?php echo $contact_details['skype']; ?></a>


                                <!--script type="text/javascript" src="http://www.skypeassets.com/i/scom/js/skype-uri.js"></script-->
                                <?php if ($contact_details['skype'])  ?>
                            <div id="SkypeButton_Call">    	

                                <!--
                                <script type="text/javascript">
                                                                  Skype.ui({
                                                                  "name": "call",
                                                                  "element": "SkypeButton_Call",
                                                                  "participants": ["<?php echo $contact_details['skype']; ?>"],
                                                                  "imageSize": 24
                                                                  });
                                                          </script>
                                -->	
                            </div>
                            <!--</dd>-->   
                            <?php
                        //echo '</dl>';
                        endif;
                    endif;

                    $websites = get_post_meta($post->ID, '_resume_websites', true);

                    if ($websites && is_array($websites)) :
                        $loop = 0;
                        echo '<dl>';
                        foreach ($websites as $website) :
                            echo '<dt class="email">' . strip_tags($website['name']) . ':</dt><dd><a href="' . esc_url($website['url']) . '" target="_blank" rel="nofollow">' . strip_tags($website['url']) . '</a>';
                            if (get_the_author_meta('ID') == get_current_user_id())
                                echo ' <a class="delete" href="?delete_website=' . $loop . '">[&times;]</a>';
                            echo '</dd>';
                            $loop++;
                        endforeach;
                        echo '</dl>';
                    endif;
                    if (get_the_author_meta('ID') == get_current_user_id())
                        echo '<p class="edit_button button"><a class="inline noscroll" href="#websites">' . __('+ Add Website', APP_TD) . '</a></p>';
                    ?>
                    </p>


                    <?php appthemes_after_post_title(); ?>

                </div><!-- end section_header -->

                <div class="section_content">

                    <?php do_action('resume_main_section', $post); ?>

                    <?php appthemes_before_post_content(); ?>
                    <!--
                    <h2 class="resume_section_heading"><span><?php _e('Objective', APP_TD); ?></span></h2>
                    <div class="resume_section summary">
                    <?php the_content(); ?>
                    </div>
                    <div class="clear"></div>
                    -->
                    <?php appthemes_after_post_content(); ?>

                    <?php
                    $display_sections = array(
                        'resume_specialities' => __('Skills', APP_TD),
                        //'skills' => __('Skills', APP_TD),
                        //'resume_languages' => __('Spoken Languages', APP_TD),
                        'education' => __('Education', APP_TD),
                        'experience' => __('Career Map', APP_TD),
                            /* 'resume_groups' => __('Groups &amp; Associations', APP_TD) */
                    );

                    foreach ($display_sections as $term => $section) :

                        switch ($term) :

                            case "experience" :
                                ?>
                                <!--				
                        <h2 class="resume_section_heading"><span><?php echo $section; ?></span></h2>
                                                                <div class="resume_section">
                                <?php echo wpautop(wptexturize(get_post_meta($post->ID, '_experience', true))); ?>
                                                                </div>
                                                                <div class="clear"></div>
                                -->				
                                <div class="experience">

                                    <h2 class="career-map-heading"><span><?php echo $section; ?></span></h2>


                                    <table class="career-map-table"> 
                                        <tr>
                                            <td class="carreer-heading">&nbsp;</td>
                                            <td class="carreer-heading">Most Recent Job</td>
                                            <td class="carreer-heading">2nd Last</td>
                                            <td class="carreer-heading">3rd Last</td>
                                        </tr>
                                        <tr>
                                            <td nowrap><strong>Position/Title</strong></td>
                                            <?php if (get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		
                                                <td><strong><?php echo wptexturize(get_post_meta($post->ID, 'company_1_position', true)); ?></strong></td>
                                            <?php } ?>
                                            <?php if (get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	
                                                <td><strong><?php echo wptexturize(get_post_meta($post->ID, 'company_2_position', true)); ?></strong></td>
                                            <?php } ?>
                                            <?php if (get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	
                                                <td><strong><?php echo wptexturize(get_post_meta($post->ID, 'company_3_position', true)); ?></strong></td>
                                            <?php } ?>
                                        </tr>

                                        <tr>
                                            <td nowrap><strong>Start Date</strong></td>
                                            <?php if (get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		
                                                <td><?php echo date_format(date_create(get_post_meta($post->ID, 'company_1_start_date', true)), "M d Y"); ?></td>
                                            <?php } ?>
                                            <?php if (get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	
                                                <td><?php echo date_format(date_create(get_post_meta($post->ID, 'company_2_start_date', true)), "M d Y"); ?></td>
                                            <?php } ?>     
                                            <?php if (get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  	                   
                                                <td><?php echo date_format(date_create(get_post_meta($post->ID, 'company_3_start_date', true)), "M d Y"); ?></td>
                                            <?php } ?>
                                        </tr>
                                        <tr>
                                            <td nowrap><strong>End Date</strong></td>
                                            <?php if (get_post_meta($post->ID, 'company_1_position', true) != "") { ?>			
                                                <td><?php echo date_format(date_create(get_post_meta($post->ID, 'company_1_end_date', true)), "M d Y"); ?></td>
                                            <?php } ?>
                                            <?php if (get_post_meta($post->ID, 'company_2_position', true) != "") { ?>		
                                                <td><?php echo date_format(date_create(get_post_meta($post->ID, 'company_2_end_date', true)), "M d Y"); ?></td>
                                            <?php } ?>     
                                            <?php if (get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  	                   
                                                <td><?php echo date_format(date_create(get_post_meta($post->ID, 'company_3_end_date', true)), "M d Y"); ?></td>
                                            <?php } ?>     
                                        </tr>

                                        <tr>
                                            <td nowrap><strong>Job Type</strong></td>
                                            <?php if (get_post_meta($post->ID, 'company_1_position', true) != "") { ?>			
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'company_1_job_type', true)); ?></td>
                                            <?php } ?>
                                            <?php if (get_post_meta($post->ID, 'company_2_position', true) != "") { ?>		
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'company_2_job_type', true)); ?></td>
                                            <?php } ?>     
                                            <?php if (get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  	
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'company_3_job_type', true)); ?></td>
                                            <?php } ?>
                                        </tr>

                                        <tr>
                                            <td nowrap><strong>Company</strong></td>
                                            <?php if (get_post_meta($post->ID, 'company_1_position', true) != "") { ?>			
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'company_1_company', true)); ?></td>
                                            <?php } ?>     
                                            <?php if (get_post_meta($post->ID, 'company_2_position', true) != "") { ?>		
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'company_2_company', true)); ?></td>
                                            <?php } ?>     
                                            <?php if (get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  	                   
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'company_3_company', true)); ?></td>
                                            <?php } ?>     
                                        </tr>
                                        <tr>
                                            <td nowrap><strong>City</strong></td>
                                            <?php if (get_post_meta($post->ID, 'company_1_position', true) != "") { ?>			
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'company_1_city', true)); ?></td>
                                            <?php } ?>
                                            <?php if (get_post_meta($post->ID, 'company_2_position', true) != "") { ?>		
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'company_2_city', true)); ?></td>
                                            <?php } ?>
                                            <?php if (get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  	                   
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'company_3_city', true)); ?></td>
                                            <?php } ?>     
                                        </tr>
                                        <tr>
                                            <td nowrap><strong>Country</strong></td>
                                            <?php if (get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		  
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'company_1_country', true)); ?></td>
                                            <?php } ?>
                                            <?php if (get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	  
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'company_2_country', true)); ?></td>
                                            <?php } ?>       
                                            <?php if (get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	                     
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'company_3_country', true)); ?></td>
                                            <?php } ?>       
                                        </tr>

                                        <tr>
                                            <td nowrap><strong>Reason for Leaving</strong></td>
                                            <?php if (get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		  
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'company_1_reason_for_leaving', true)); ?></td>
                                            <?php } ?>
                                            <?php if (get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	  
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'company_2_reason_for_leaving', true)); ?></td>
                                            <?php } ?>
                                            <?php if (get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	                     
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'company_3_reason_for_leaving', true)); ?></td>
                                            <?php } ?>
                                        </tr>

                                        <tr class="divider">
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr>
                                            <td nowrap><strong>Starting Salary</strong></td>
                                            <?php if (get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		  
                                                <td class="user_salary"><strong><?php echo wptexturize(get_post_meta($post->ID, 'company_1_starting_salary', true)); ?></strong>&nbsp;<?php echo $currency; ?></td>
                                            <?php } ?>
                                            <?php if (get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	  
                                                <td class="user_salary"><strong><?php echo wptexturize(get_post_meta($post->ID, 'company_2_starting_salary', true)); ?></strong>&nbsp;<?php echo $currency; ?></td>
                                            <?php } ?>
                                            <?php if (get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	         
                                                <td class="user_salary"><strong><?php echo wptexturize(get_post_meta($post->ID, 'company_3_starting_salary', true)); ?></strong>&nbsp;<?php echo $currency; ?></td>
                                            <?php } ?>

                                        </tr>

                                        <tr>
                                            <td nowrap><strong>Final Salary</strong></td>
                                            <?php if (get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		  
                                                <td class="user_salary"><strong><?php echo wptexturize(get_post_meta($post->ID, 'company_1_final_salary', true)); ?></strong>&nbsp;<?php echo $currency; ?></td>
                                            <?php } ?>
                                            <?php if (get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	  
                                                <td class="user_salary"><strong><?php echo wptexturize(get_post_meta($post->ID, 'company_2_final_salary', true)); ?></strong>&nbsp;<?php echo $currency; ?></td>
                                            <?php } ?>         
                                            <?php if (get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  
                                                <td class="user_salary"><strong><?php echo wptexturize(get_post_meta($post->ID, 'company_3_final_salary', true)); ?></strong>&nbsp;<?php echo $currency; ?></td>
                                            <?php } ?>
                                        </tr>

                                        <tr>
                                            <td nowrap><strong>Salary Type</strong></td>
                                            <?php if (get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		  
                                                <td class="user_salary"><?php echo wptexturize(get_post_meta($post->ID, 'company_1_salary_type', true)); ?></td>
                                            <?php } ?>
                                            <?php if (get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	  
                                                <td class="user_salary"><?php echo wptexturize(get_post_meta($post->ID, 'company_2_salary_type', true)); ?></td>
                                            <?php } ?>
                                            <?php if (get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  
                                                <td class="user_salary"><?php echo wptexturize(get_post_meta($post->ID, 'company_3_salary_type', true)); ?></td>
                                            <?php } ?> 
                                        </tr>
                                        <tr class="divider">
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>	
                                        <?php if (current_user_can('can_submit_job')) { ?>
                                            <tr>
                                                <td nowrap><strong>Reference Name</strong></td>
                                                <?php if (get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		 
                                                    <td class="reference_name"><strong><?php echo wptexturize(get_post_meta($post->ID, 'reference_name_1', true)); ?></strong></td>
                                                <?php } ?>
                                                <?php if (get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	 
                                                    <td class="reference_name"><strong><?php echo wptexturize(get_post_meta($post->ID, 'reference_name_2', true)); ?></strong></td>  
                                                <?php } ?>
                                                <?php if (get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  
                                                    <td class="reference_name"><strong><?php echo wptexturize(get_post_meta($post->ID, 'reference_name_3', true)); ?></strong></td>
                                                <?php } ?>
                                            </tr>

                                            <tr>
                                                <td nowrap><strong>Position/Title</strong></td>
                                                <?php if (get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		  
                                                    <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_position_1', true)); ?></td>
                                                <?php } ?>
                                                <?php if (get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	  
                                                    <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_position_2', true)); ?></td>
                                                <?php } ?>
                                                <?php if (get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  
                                                    <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_position_3', true)); ?></td>
                                                <?php } ?>
                                            </tr>

                                            <tr>
                                                <td nowrap><strong>Reference Email</strong></td>
                                                <?php if (get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		  
                                                    <td>
                                                        <a class="noscroll reference_email" href="#"><?php echo wptexturize(get_post_meta($post->ID, 'reference_email_1', true)); ?></a>
                                                    </td>
                                                <?php } ?>
                                                <?php if (get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	  
                                                    <td>
                                                        <a class="noscroll reference_email" href="#"><?php echo wptexturize(get_post_meta($post->ID, 'reference_email_2', true)); ?></a>
                                                    </td>
                                                <?php } ?>
                                                <?php if (get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  
                                                    <td>
                                                        <a class="noscroll reference_email" href="#"><?php echo wptexturize(get_post_meta($post->ID, 'reference_email_3', true)); ?></a>
                                                    </td>
                                                <?php } ?>
                                            </tr>

                                            <tr>
                                                <td nowrap><strong>Reference Phone</strong></td>
                                                <?php if (get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		  
                                                    <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_phone_number_1', true)); ?></td>
                                                <?php } ?>
                                                <?php if (get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	  
                                                    <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_phone_number_2', true)); ?></td>
                                                <?php } ?>
                                                <?php if (get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	    
                                                    <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_phone_number_3', true)); ?></td>
                                                <?php } ?>
                                            </tr>

                                            <tr>
                                                <td nowrap><strong>Notes</strong></td>
                                                <?php if (get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		  
                                                    <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_additional_info_1', true)); ?></td>
                                                <?php } ?>
                                                <?php if (get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	  
                                                    <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_additional_info_2', true)); ?></td>
                                                <?php } ?>
                                                <?php if (get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  
                                                    <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_additional_info_3', true)); ?></td>
                                                <?php } ?>
                                            </tr>

                                            <tr class="request_reference_button_holder">
                                                <td nowrap style="border: 0px;background: transparent;">&nbsp;</td>
                                                <?php if (get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		  
                                                    <td style="border: 0px;"><input type="button" class="email_reference" value="Request Reference" /></td>
                                                <?php } ?>
                                                <?php if (get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	  
                                                    <td style="border: 0px;"><input type="button" class="email_reference" value="Request Reference" /></td>
                                                <?php } ?>
                                                <?php if (get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  
                                                    <td style="border: 0px;"><input type="button" class="email_reference" value="Request Reference" /></td>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
                                    </table> 
                                    <div class="reference-dialog">
                                        <div>
                                            <label for="email-address">To:</label>
                                            <input type="text" name="email-address" class="email-address" value="" />
                                            <br />
                                            <label for="email-from">From:</label>
                                            <input type="text" name="email-from" class="email-from" value="Vidhire Human Resources ref@vidhire.net" />
                                            <br />
                                            <label for="email-subject">Subject:</label>
                                            <input type="text" name="email-subject" class="email-subject" value="Regarding Evaluation of <?php the_title(); ?>" />
                                            <br />
                                            <br />
                                            <div class="mail-content" contenteditable="true">
                                                Dear <text class='reference-name'></text>,<br />
                                                We are in process of assessing your past employee <?php echo the_title(); ?> for a position with company.
                                                On a scale of 1 – 5, how would you rate past performance? <br /><br />

                                                <strong>Rating: (1) poor (2) fair (3) good (4) very good (5) excellent</strong>
                                                <br /><br />

                                                <form action='<?php echo get_template_directory_uri() . "/process.php"; ?>' method='post' target='_blank'>

                                                    <table>
                                                        <tr>
                                                            <td><strong>Productivity</strong></td>
                                                            <td><select name='performance'><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option></select></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Attitude</strong></td>
                                                            <td><select name='attitude'><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option></select></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Dependability</strong></td>
                                                            <td><select name='depend'><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option></select></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Team Player</strong></td>
                                                            <td><select name='team_player'><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option></select></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Learning Speed</strong></td>
                                                            <td><select name='learning_speed'><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option></select></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Flexibility</strong></td>
                                                            <td><select name='flexibility'><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option></select></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Creativity</strong></td>
                                                            <td><select name='creativity'><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option></select></td>
                                                        </tr>
                                                    </table>
                                                    <input name='resume_id'  value='<?php echo $post->ID; ?>' type='hidden' />
                                                    <input class='reference-name' name='reference_name' value='' type='hidden' />
                                                    <br />
                                                    <input type='submit' value='Submit Review' /></form><br />
                                                Note: Your assessment is confidential.  If you cannot see the pull down menu, please use this link.<br /> Vidhire.net is a free hiring system. <br />
                                                <br />Thank you.
                                            </div>
                                            <br />
                                            <input class="reference-resume-id" name="resume_id" type="hidden" value="<?php echo $post->ID; ?>" />
                                            <input class="reference-name" name="reference_name" type="hidden" value="" />
                                            <input class="reference-send-button" type="button" value="Send" />
                                            <input class="reference-close-button" type="button" value="Close" />

                                        </div>
                                    </div>
                                </div>

                                <br />

                                <?php
                                /* Days of Employment Chart
                                 */
                                $company_1_get_start_date = date_format(date_create(get_post_meta($post->ID, 'company_1_start_date', true)), "M d Y");
                                $company_1_get_end_date = date_format(date_create(get_post_meta($post->ID, 'company_1_end_date', true)), "M d Y");

                                $company_2_get_start_date = date_format(date_create(get_post_meta($post->ID, 'company_2_start_date', true)), "M d Y");
                                $company_2_get_end_date = date_format(date_create(get_post_meta($post->ID, 'company_2_end_date', true)), "M d Y");

                                $company_3_get_start_date = date_format(date_create(get_post_meta($post->ID, 'company_3_start_date', true)), "M d Y");
                                $company_3_get_end_date = date_format(date_create(get_post_meta($post->ID, 'company_3_end_date', true)), "M d Y");


                                $company_1_start_date = new DateTime($company_1_get_start_date);
                                $company_1_end_date = new DateTime($company_1_get_end_date);

                                $company_2_start_date = new DateTime($company_2_get_start_date);
                                $company_2_end_date = new DateTime($company_2_get_end_date);

                                $company_3_start_date = new DateTime($company_3_get_start_date);
                                $company_3_end_date = new DateTime($company_3_get_end_date);

                                $company_1_date_diff = $company_1_start_date->diff($company_1_end_date);
                                $company_2_date_diff = $company_2_start_date->diff($company_2_end_date);
                                $company_3_date_diff = $company_3_start_date->diff($company_3_end_date);

                                $company_1_unemployment = $company_1_end_date->diff(new DateTime());
                                $company_2_unemployment = $company_2_end_date->diff($company_1_start_date);
                                $company_3_unemployment = $company_3_end_date->diff($company_2_start_date);

                                $days_worked = $company_3_date_diff->days . ',' . $company_2_date_diff->days . ',' . $company_1_date_diff->days;
                                $unemployment_days = $company_3_unemployment->days . ',' . $company_2_unemployment->days . ',' . $company_1_unemployment->days;

                                /* Wage Chart */

                                $company_1_start_wage = get_post_meta($post->ID, 'company_1_starting_salary', true);
                                $company_1_end_wage = get_post_meta($post->ID, 'company_1_final_salary', true);

                                $company_2_start_wage = get_post_meta($post->ID, 'company_2_starting_salary', true);
                                $company_2_end_wage = get_post_meta($post->ID, 'company_2_final_salary', true);

                                $company_3_start_wage = get_post_meta($post->ID, 'company_3_starting_salary', true);
                                $company_3_end_wage = get_post_meta($post->ID, 'company_3_final_salary', true);

                                $start_wage = $company_3_start_wage . ',' . $company_2_start_wage . ',' . $company_1_start_wage;
                                $end_wage = $company_3_end_wage . ',' . $company_2_end_wage . ',' . $company_1_end_wage;

                                $company_names = "3rd Last Employment,2nd Last Employment,Most Recent Employment";

                                $company_1_days_worked = $company_1_date_diff->days / 100;
                                $company_2_days_worked = $company_2_date_diff->days / 100;
                                $company_3_days_worked = $company_3_date_diff->days / 100;

                                $company_1_days_before_next_job = $company_1_unemployment->days / 100;
                                $company_2_days_before_next_job = $company_2_unemployment->days / 100;
                                $company_3_days_before_next_job = $company_3_unemployment->days / 100;



                                /* Display the Charts */
                                //Employment Period
                                echo '<div class="experience">';
                                echo do_shortcode('[easychart type="vertbarstack" height="400" width="650" title=""  groupcolors="288000,B50404" groupnames="Days Worked,Days Before Next Job" valuenames="' . $company_names . '" group1values="' . $days_worked . '" group2values="' . $unemployment_days . '" ]');


                                /* Shortcode Chart for Unemployment Period */
                                echo do_shortcode('[su_shadow style="simple"][su_box title="Employment Period Evaluation" style="glass" box_color="#000"]
                                <h2>Days Worked</h2>
                                [su_progress_bar style="fancy" percent="' . $company_1_days_worked . '" text="Most Recent Employment" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [su_progress_bar style="fancy" percent="' . $company_2_days_worked . '" text="2nd Last Employment" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [su_progress_bar style="fancy" percent="' . $company_3_days_worked . '" text="3rd Last Employment" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                <h2>Days Before Next Job</h2>
                                [su_progress_bar style="fancy" percent="' . $company_1_days_before_next_job . '" text="Most Recent Employment" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [su_progress_bar style="fancy" percent="' . $company_2_days_before_next_job . '" text="2nd Last Employment" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [su_progress_bar style="fancy" percent="' . $company_3_days_before_next_job . '" text="3rd Last Employment" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [/su_box][/su_shadow]');


                                //Start and End Wage
                                echo do_shortcode('[easychart type="vertbar" height="400" width="650" title=""  groupcolors="0070C0,006AE3" groupnames="Start Wage,End Wage" valuenames="' . $company_names . '" group1values="' . $start_wage . '" group2values="' . $end_wage . '" ]');
                                echo '</div>';

                                /* Shortcode Chart for Wage */
                                echo do_shortcode('[su_shadow style="simple"][su_box title="Wage Evaluation" style="glass" box_color="#000"]
                                <h2>Start Wage</h2>
                                [su_progress_bar style="fancy" percent="' . $company_1_start_wage . '" text="Most Recent Employment" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [su_progress_bar style="fancy" percent="' . $company_2_start_wage . '" text="2nd Last Employment" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [su_progress_bar style="fancy" percent="' . $company_3_start_wage . '" text="3rd Last Employment" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                <h2>End Wage</h2>
                                [su_progress_bar style="fancy" percent="' . $company_1_end_wage . '" text="Most Recent Employment" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [su_progress_bar style="fancy" percent="' . $company_2_end_wage . '" text="2nd Last Employment" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [su_progress_bar style="fancy" percent="' . $company_3_end_wage . '" text="3rd Last Employment" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [/su_box][/su_shadow]');
                                ?>


                                <?php
                                break;
                            case "education" :
                                ?>
                                <!--
                                <h2 class="resume_section_heading"><span><?php echo $section; ?></span></h2>
                                <div class="resume_section">
                                <?php echo wpautop(wptexturize(get_post_meta($post->ID, '_education', true))); ?>
                                </div>
                                -->
                                <div class="attached_documents">
                                    <h2 class="attached_documents_header"><span>Resume &AMP; Documents</span></h2>
                                    <?php
                                    $resume = get_children(array(
                                        'post_parent' => $post->ID,
                                        'post_type' => 'attachment',
                                        'numberposts' => 1, // show all -1
                                        'post_status' => null,
                                        'post_mime_type' => array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword'),
                                        'order' => 'DESC'
                                    ));
                                    ?>


                                    <?php
                                    foreach ($resume as $attachment_id => $attachment) {
                                        if (get_the_author_meta('ID') == get_current_user_id() || current_user_can('manage_options')) {
                                            //echo '<tr><td>'.wp_get_attachment_link($attachment_id) . '</td><td><a href="' . get_delete_post_link($attachment_id, '', true) . '">Delete</a></td></tr>';
                                            echo do_shortcode('[embeddoc url="' . wp_get_attachment_url($attachment_id) . '" viewer="microsoft"]');
                                        } else {
                                            echo wp_get_attachment_link($attachment_id);
                                        }
                                    }
                                    ?>


                                    <table>
                                        <?php
                                        $attachments = get_children(array(
                                            'post_parent' => $post->ID,
                                            'post_type' => 'attachment',
                                            'numberposts' => -1, // show all -1
                                            'post_status' => null,
                                            'post_mime_type' => array('image/png', 'image/jpg', 'image/gif'),
                                            'order' => 'DESC'
                                        ));
                                        ?>


                                        <?php
                                        foreach ($attachments as $attachment_id => $attachment) {
                                            if (get_the_author_meta('ID') == get_current_user_id() || current_user_can('manage_options')) {
                                                echo '<tr><td>' . wp_get_attachment_link($attachment_id) . '</td></tr>';
                                                //echo do_shortcode('[embeddoc url="'.wp_get_attachment_url($attachment_id).'" viewer="microsoft"]'); 
                                            } else {
                                                echo wp_get_attachment_link($attachment_id);
                                            }
                                        }
                                        ?>
                                    </table>
                                </div>
                                <br />

                                <?php
                                break;
                            case "skills" :
                                $skills = array_map('trim', explode("\n", get_post_meta($post->ID, '_skills', true)));
                                if ($skills) :
                                    ?>

                                    <h2 class="resume_section_heading"><span><?php echo $section; ?></span></h2>
                                    <div class="resume_section">
                                        <?php
                                        echo '<ul>';
                                        foreach ($skills as $skill) :
                                            if ($skill)
                                                echo '<li class="tag">' . wptexturize($skill) . '</li>';
                                        endforeach;
                                        echo '</ul>';
                                        ?>
                                    </div>
                                    <div class="clear"></div>

                                    <?php
                                endif;
                                break;
                            default :
                                $terms = wp_get_post_terms($post->ID, $term);

                                if ($term) :
                                    ?>
                                    <div class="skills-container">
                                        <h2 class="skills-heading"><span><?php echo $section; ?></span></h2>
                                        <div class="skills-text">
                                            <?php
                                            $terms_array = array();
                                            foreach ($terms as $t) :
                                                if (sizeof($terms_array) != (sizeof($terms) - 1)) :
                                                    $terms_array[] = $t->name . ', ';
                                                else :
                                                    $terms_array[] = $t->name;
                                                endif;
                                            endforeach;
                                            echo '<ul class="terms"><li class="tag">' . implode('</li><li>', $terms_array) . '</li></ul>';
                                            //echo implode('', $terms_array);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                    <br />
                                    <div class="degree-section">
                                        <h2 class="degree-section-heading"><span>Certificate, Diploma or Degree</span></h2>
                                        <div class="degree-section-text-section">
                                            <ul>
                                                <li><?php echo wptexturize(get_post_meta($post->ID, 'degree', true)) ?>,</li>
                                                <li><?php echo wptexturize(get_post_meta($post->ID, 'institution', true)); ?>,</li>
                                                <li><?php echo wptexturize(get_post_meta($post->ID, 'degree_date_issued', true)); ?></li>
                                            </ul>
                                            <ul>
                                                <li>
                                                    <label>Last year's overall average: </label><?php echo wptexturize(get_post_meta($post->ID, 'overall_average', true)); ?>
                                                </li>
                                                <li>
                                                    <label>Transcripts:</label>
                                                    <?php echo wptexturize(get_post_meta($post->ID, 'transcripts', true)); ?>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                    <br />
                                    <?php
                                endif;
                                break;

                        endswitch;

                    endforeach;
                    ?>
                <?php endif; ?>

                <div style="display:none" class="test-results" style="height: 300px;">

                    <?php
                    $display_tests = array(
                        'typing_test' => __('Typing Test', APP_TD),
                        'math_test' => __('Math Test', APP_TD),
                        'english_test' => __('English Test', APP_TD),
                        'memory_test' => __('Memory Test', APP_TD),
                        'internet_speed' => __('Internet Speed Test', APP_TD)
                    );

                    foreach ($display_tests as $tests => $test_title) :

                        switch ($tests) :

                            case 'typing_test' :
                                ?>  
                                <h2 class="resume_section_heading"><span><?php echo $test_title; ?></span></h2>
                                <div class="resume_section">
                                    <?php echo wptexturize(get_post_meta($post->ID, 'typing_test', true)); ?>
                                </div>
                                <div class="clear"></div>
                                <?php
                                break;
                            case 'math_test' :
                                ?>
                                <h2 class="resume_section_heading"><span><?php echo $test_title; ?></span></h2>
                                <div class="resume_section">
                                    <?php echo wpautop(wptexturize(get_post_meta($post->ID, 'math_test', true))); ?> 
                                </div>
                                <div class="clear"></div>  
                                <?php
                                break;
                            case 'english_test' :
                                ?>
                                <h2 class="resume_section_heading"><span><?php echo $test_title; ?></span></h2>
                                <div class="resume_section">
                                    <?php echo wpautop(wptexturize(get_post_meta($post->ID, 'english_test', true))); ?>         
                                </div>
                                <div class="clear"></div>   
                                <?php
                                break;
                            case 'memory_test' :
                                ?>
                                <h2 class="resume_section_heading"><span><?php echo $test_title; ?></span></h2>
                                <div class="resume_section">
                                    <?php echo wpautop(wptexturize(get_post_meta($post->ID, 'memory_test', true))); ?>            
                                </div>
                                <div class="clear"></div>            
                                <?php
                                break;
                            case 'internet_speed' :
                                ?>
                                <h2 class="resume_section_heading"><span><?php echo $test_title; ?></span></h2>
                                <div class="resume_section">
                                    <?php echo wpautop(wptexturize(get_post_meta($post->ID, 'internet_speed', true))); ?>           
                                </div>
                                <div class="clear"></div>  
                                <?php
                                break;
                        endswitch;
                    endforeach;
                    ?>

                </div>



                <div class="related-media">
                    <?php
                    $display_media = array(
                        'chart' => __('Chart', APP_TD),
                        'interview_video' => __('Interview Video', APP_TD)
                    );

                    foreach ($display_media as $media => $media_title) :

                        switch ($media) :

                            case 'interview_video' :
                                ?>  
                                <!--
                    <h2 class="resume_section_heading"><span><?php echo $media_title; ?></span></h2>
                                -->
                                <div class="interview-video-container" >
                                    <!--
                    <video height="355" width="500" controls>
                            <source src="<?php echo wptexturize(get_post_meta($post->ID, 'interview_video', true)); ?>" type="video/mp4">
                            Your browser does not support HTML5 video.
                     </video>	
                                    -->
                                    <h2 class="video-interview-header">Video Interview</h2>
                                    <iframe height="355" width="650" src="<?php echo wptexturize(get_post_meta($post->ID, 'interview_video', true)); ?>"></iframe>
                                </div>
                                <?php if (current_user_can('can_submit_job')) { ?>
                                    <form class="video-evaluation-form">

                                        <h2 class="video-evaluation-heading" style="text-align:left;"><span>Video Evaluation of <?php the_title(); ?></span></h2>
                                        <?php
                                        global $wpdb, $post;

                                        $employer_id = wp_get_current_user();

                                        $get_video_evaluation = $wpdb->get_row("SELECT * FROM wp_video_evaluation WHERE employer_id in ('" . $employer_id->ID . "') AND resume_id in ('" . $post->ID . "')");
                                        ?>      
                                        <table class="video-evaluation">
                                            <thead>
                                            <th>&nbsp;</th>
                                            <th>Evaluation Notes</th>
                                            <th>Evaluator</th>
                                            <th>Score</th>
                                            </thead>      
                                            <tbody>
                                                <tr>
                                                    <td><strong>Confidence</strong></td>
                                                    <td>
                                                        <textarea name="confidence_notes"><?php echo trim($get_video_evaluation->confidence_notes); ?></textarea>
                                                    </td>
                                                    <td><input type="text" name="confidence_evaluator" value="<?php echo $get_video_evaluation->confidence_evaluator; ?>"/></td>
                                                    <td>
                                                        <select name="confidence_score">
                                                            <?php
                                                            for ($i = 1; $i < 6; $i++) {
                                                                if ($i == $get_video_evaluation->confidence_score) {
                                                                    ?>
                                                                    <option selected="selected"><?php echo $i; ?></option>
                                                                <?php } else { ?>
                                                                    <option><?php echo $i; ?></option>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Communication</strong></td>
                                                    <td>
                                                        <textarea name="communication_notes"><?php echo $get_video_evaluation->communication_notes; ?></textarea>
                                                    </td>
                                                    <td><input type="text" name="communication_evaluator" value="<?php echo $get_video_evaluation->communication_evaluator; ?>"/></td>
                                                    <td>
                                                        <select name="communication_score">
                                                            <?php
                                                            for ($i = 1; $i < 6; $i++) {
                                                                if ($i == $get_video_evaluation->communication_score) {
                                                                    ?>
                                                                    <option selected="selected"><?php echo $i; ?></option>
                                                                <?php } else { ?>
                                                                    <option><?php echo $i; ?></option>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Enthusiasm</strong></td>
                                                    <td>
                                                        <textarea name="fun_factor_notes"><?php echo $get_video_evaluation->fun_factor_notes; ?></textarea>
                                                    </td>
                                                    <td><input type="text" name="fun_factor_evaluator" value="<?php echo $get_video_evaluation->fun_factor_evaluator; ?>"/></td>
                                                    <td>
                                                        <select name="fun_factor_score">
                                                            <?php
                                                            for ($i = 1; $i < 6; $i++) {
                                                                if ($i == $get_video_evaluation->fun_factor_score) {
                                                                    ?>
                                                                    <option selected="selected"><?php echo $i; ?></option>
                                                                <?php } else { ?>
                                                                    <option><?php echo $i; ?></option>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Connection</strong></td>
                                                    <td>
                                                        <textarea name="connection_notes"><?php echo $get_video_evaluation->connection_notes; ?></textarea></td>
                                                    <td><input type="text" name="connection_evaluator" value="<?php echo $get_video_evaluation->connection_evaluator; ?>"/></td>
                                                    <td>
                                                        <select name="connection_score">
                                                            <?php
                                                            for ($i = 1; $i < 6; $i++) {
                                                                if ($i == $get_video_evaluation->connection_score) {
                                                                    ?>
                                                                    <option selected="selected"><?php echo $i; ?></option>
                                                                <?php } else { ?>
                                                                    <option><?php echo $i; ?></option>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Understanding</strong></td>
                                                    <td><textarea name="understanding_notes"><?php echo $get_video_evaluation->understanding_notes; ?></textarea></td>
                                                    <td><input type="text" name="understanding_evaluator" value="<?php echo $get_video_evaluation->understanding_evaluator; ?>"/></td>
                                                    <td>
                                                        <select name="understanding_score">
                                                            <?php
                                                            for ($i = 1; $i < 6; $i++) {
                                                                if ($i == $get_video_evaluation->understanding_score) {
                                                                    ?>
                                                                    <option selected="selected"><?php echo $i; ?></option>
                                                                <?php } else { ?>
                                                                    <option><?php echo $i; ?></option>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Optional Boost</strong></td>
                                                    <td><textarea name="bonus_notes"><?php echo $get_video_evaluation->bonus_notes; ?></textarea></td>
                                                    <td><input type="text" name="bonus_evaluator" value="<?php echo $get_video_evaluation->bonus_evaluator; ?>"/></td>
                                                    <td>
                                                        <select name="bonus_score">
                                                            <?php
                                                            for ($i = 1; $i < 6; $i++) {
                                                                if ($i == $get_video_evaluation->bonus_score) {
                                                                    ?>
                                                                    <option selected="selected"><?php echo $i; ?></option>
                                                                <?php } else { ?>
                                                                    <option><?php echo $i; ?></option>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                    <td><strong style="float:right;font-weight:bolder;font-size:20px;color:#a9a9a9;">Total</strong></td>
                                                    <td><input id="video_evaluation_score" name="video_evaluation_score" type="text" value="<?php echo $get_video_evaluation->video_evaluation_score ?>" /></td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                    <td>
                                                        <div class="evaluation-action-buttons">                      
                                                            <input type="button" id="save_video_score" name="save_video_score" class="save_video_score" value="Save And Calculate"/>
                                                            <br />
                                                            <br />
                                                            <a target="_blank" class="evaluation-instructions" href="http://vidhire.net/?p=329">Evaluation Instructions</a>
                                                            <input name="resume_id" type="hidden" value="<?php echo $post->ID; ?>">	  
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>  
                                        </table>
                                    </form>

                                    <?php
                                    $video_evaluation_values = $get_video_evaluation->confidence_score . ',' . $get_video_evaluation->communication_score . ',' . $get_video_evaluation->fun_factor_score . ',' . $get_video_evaluation->connection_score . ',' . $get_video_evaluation->understanding_score . ',' . $get_video_evaluation->bonus_score;

                                    /* echo "<div class='video-evaluation-form'>";
                                      echo do_shortcode('[easychart type="vertbar" height="400" width="650" title="" groupcolors="333399" groupnames="Score" valuenames="Confidence,Communication,Enthusiasm,Connection,Understanding,Boost" group1values="' . $video_evaluation_values . '"]');
                                      echo "</div>"; */


                                    $confidence_score = $get_video_evaluation->confidence_score * 20;
                                    $communication_score = $get_video_evaluation->communication_score * 20;
                                    $enthusiasm_score = $get_video_evaluation->fun_factor_score * 20;
                                    $connection_score = $get_video_evaluation->connection_score * 20;
                                    $understanding_score = $get_video_evaluation->understanding_score * 20;
                                    $optional_boost_score = $get_video_evaluation->bonus_score * 20;
                                    $video_evaluation_total = round(($get_video_evaluation->video_evaluation_score / 6 ) * 20);

                                    
                                    //EE
                                    /* Shortcode Chart for Video Evaluation */
                                    echo do_shortcode('[su_shadow style="simple"][su_box title="Video Evaluation" style="glass" box_color="#000000"][su_note note_color="#000000" radius="5"]
                                [su_progress_bar style="fancy" percent="' . $confidence_score . '" text="<strong>Confidence</strong>" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [su_progress_bar style="fancy" percent="' . $communication_score . '" text="<strong>Communication</strong>" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [su_progress_bar style="fancy" percent="' . $enthusiasm_score . '" text="<strong>Enthusiasm</strong>" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [su_progress_bar style="fancy" percent="' . $connection_score . '" text="<strong>Connection</strong>" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [su_progress_bar style="fancy" percent="' . $understanding_score . '" text="<strong>Understanding</strong>" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [su_progress_bar style="fancy" percent="' . $optional_boost_score . '" text="<strong>Optional Boost</strong>" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [su_progress_pie percent="' . $video_evaluation_total . '" before="<strong>" after="%</strong>" size="60" pie_width="40" text_size="16" pie_color="#f1efb6" fill_color="#f29b00" text_color="#cd8803"]
                                [/su_note][/su_box][/su_shadow]');
                                    ?>
                                <?php } ?>

                                <?php
                                if ($address = get_post_meta($post->ID, 'geo_short_address', true)) :
                                    echo '<br/><h2 class="location-map-header">' . __('Location: ', APP_TD);
                                    echo '<strong>' . wptexturize($address) . ' ';
                                    echo wptexturize(get_post_meta($post->ID, 'geo_short_address_country', true)) . '</strong></h2><br />';
                                endif;
                                ?>

                                <?php
                                jr_geolocation_scripts();
                                ?>

                                <div id="geolocation_box">
                                    <p>
                                        <label>

                                            <input id="geolocation-load" type="hidden" class="button geolocationadd submit" value="<?php esc_attr_e('Find Address/Location', APP_TD); ?>" />
                                        </label>

                                        <input type="hidden" class="hidden" name="jr_address" id="geolocation-address" value="<?php if (isset($posted['jr_address'])) echo esc_attr($posted['jr_address']); ?>" />
                                        <input type="hidden" class="text" name="jr_geo_latitude" id="geolocation-latitude" value="<?php if (isset($posted['jr_geo_latitude'])) echo esc_attr($posted['jr_geo_latitude']); ?>" />
                                        <input type="hidden" class="text" name="jr_geo_longitude" id="geolocation-longitude" value="<?php if (isset($posted['jr_geo_longitude'])) echo esc_attr($posted['jr_geo_longitude']); ?>" />
                                    </p>

                                    <div id="map_wrap" style="width:100%;height:250px;"><div id="geolocation-map" style="width:100%;height:250px;"></div></div>
                                </div>
                                <?php if (current_user_can('can_submit_job')) { ?>

                                    <!--
                                    <iframe height="355" width="658" src="https://docs.google.com/spreadsheets/d/1CRIj5PYzwEnQySpBnMNw6sIqRTA2NiWQb0ieXm0nxDk/edit#gid=0"></iframe>
                                    
                                    -->

                                    <form class="final-evaluation-form">       
                                        <h2 class="final-evaluation-heading" style="text-align:left;"><span>Final Evaluation of <?php the_title(); ?></span></h2>
                                        <?php
                                        global $wpdb, $post;

                                        $employer_id = wp_get_current_user();

                                        $get_evaluation = $wpdb->get_row("SELECT * FROM wp_final_evaluation WHERE employer_id in ('" . $employer_id->ID . "') AND resume_id in ('" . $post->ID . "')");
                                        ?>      
                                        <table class="final-evaluation">
                                            <thead>
                                            <th>&nbsp;</th>
                                            <th>Evaluation Notes</th>
                                            <th>Evaluator</th>
                                            <th>Score</th>
                                            </thead>      
                                            <tbody>
                                                <tr>
                                                    <td><strong>Skills</strong></td>
                                                    <td>
                                                        <textarea name="skills_notes"><?php echo trim($get_evaluation->skills_notes); ?></textarea>
                                                    </td>
                                                    <td><input type="text" name="skills_evaluator" value="<?php echo $get_evaluation->skills_evaluator; ?>"/></td>
                                                    <td>
                                                        <select name="skills_score">
                                                            <?php
                                                            for ($i = 1; $i < 6; $i++) {
                                                                if ($i == $get_evaluation->skills_score) {
                                                                    ?>
                                                                    <option selected="selected"><?php echo $i; ?></option>
                                                                <?php } else { ?>
                                                                    <option><?php echo $i; ?></option>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Education</strong></td>
                                                    <td>
                                                        <textarea name="education_notes"><?php echo $get_evaluation->education_notes; ?></textarea>
                                                    </td>
                                                    <td><input type="text" name="education_evaluator" value="<?php echo $get_evaluation->education_evaluator; ?>"/></td>
                                                    <td>
                                                        <select name="education_score">
                                                            <?php
                                                            for ($i = 1; $i < 6; $i++) {
                                                                if ($i == $get_evaluation->education_score) {
                                                                    ?>
                                                                    <option selected="selected"><?php echo $i; ?></option>
                                                                <?php } else { ?>
                                                                    <option><?php echo $i; ?></option>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Career Map</strong></td>
                                                    <td>
                                                        <textarea name="career_map_notes"><?php echo $get_evaluation->career_map_notes; ?></textarea>
                                                    </td>
                                                    <td><input type="text" name="career_map_evaluator" value="<?php echo $get_evaluation->career_map_evaluator; ?>"/></td>
                                                    <td>
                                                        <select name="career_map_score">
                                                            <?php
                                                            for ($i = 1; $i < 6; $i++) {
                                                                if ($i == $get_evaluation->career_map_score) {
                                                                    ?>
                                                                    <option selected="selected"><?php echo $i; ?></option>
                                                                <?php } else { ?>
                                                                    <option><?php echo $i; ?></option>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><strong>References</strong></td>
                                                    <td>
                                                        <textarea name="references_notes"><?php echo $get_evaluation->references_notes; ?></textarea>				 </td>
                                                    <td><input type="text" name="references_evaluator" value="<?php echo $get_evaluation->references_evaluator; ?>"/></td>
                                                    <td>
                                                        <select name="references_score">
                                                            <?php
                                                            for ($i = 1; $i < 6; $i++) {
                                                                if ($i == $get_evaluation->references_score) {
                                                                    ?>
                                                                    <option selected="selected"><?php echo $i; ?></option>
                                                                <?php } else { ?>
                                                                    <option><?php echo $i; ?></option>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Video Interview</strong></td>
                                                    <td><textarea name="video_interview_notes"><?php echo $get_evaluation->video_interview_notes; ?></textarea></td>
                                                    <td><input type="text" name="video_interview_evaluator" value="<?php echo $get_evaluation->video_interview_evaluator; ?>"/></td>
                                                    <td>
                                                        <select name="video_interview_score">
                                                            <?php
                                                            for ($i = 1; $i < 6; $i++) {
                                                                if ($i == $get_evaluation->video_interview_score) {
                                                                    ?>
                                                                    <option selected="selected"><?php echo $i; ?></option>
                                                                <?php } else { ?>
                                                                    <option><?php echo $i; ?></option>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Tests</strong></td>
                                                    <td><textarea name="tests_notes"><?php echo $get_evaluation->tests_notes; ?></textarea></td>
                                                    <td><input type="text" name="tests_evaluator" value="<?php echo $get_evaluation->tests_evaluator; ?>"/></td>
                                                    <td>
                                                        <select name="tests_score">
                                                            <?php
                                                            for ($i = 1; $i < 6; $i++) {
                                                                if ($i == $get_evaluation->tests_score) {
                                                                    ?>
                                                                    <option selected="selected"><?php echo $i; ?></option>
                                                                <?php } else { ?>
                                                                    <option><?php echo $i; ?></option>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>	

                                                <tr>
                                                    <td><strong>Optional Boost</strong></td>
                                                    <td><textarea name="positive_adjustments_notes"><?php echo $get_evaluation->positive_adjustments_notes; ?></textarea></td>
                                                    <td><input type="text" name="positive_adjustments_evaluator" value="<?php echo $get_evaluation->positive_adjustments_evaluator; ?>"/></td>
                                                    <td>
                                                        <select name="positive_adjustments_score">
                                                            <?php
                                                            for ($i = 1; $i < 6; $i++) {
                                                                if ($i == $get_evaluation->positive_adjustments_score) {
                                                                    ?>
                                                                    <option selected="selected"><?php echo $i; ?></option>
                                                                <?php } else { ?>
                                                                    <option><?php echo $i; ?></option>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Admin Notes</strong></td>
                                                    <td><textarea name="internal-notes-text-area" class="internal-notes-text-area"><?php echo wptexturize(get_post_meta($post->ID, 'internal_notes', true)); ?></textarea></td>
                                                    <td><strong style="float:right;font-weight:bolder;font-size:20px;color:#1F5802;">Total</strong></td>
                                                    <td><input id="final_evaluation_score" name="final_evaluation_score" type="text" value="<?php echo $get_evaluation->final_evaluation_score; ?>" /></td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                    <td>
                                                        <div class="evaluation-action-buttons">                      
                                                            <input type="button" id="save_score" name="save_score" class="save_score" value="Save And Calculate"/>
                                                            <br />
                                                            <br />
                                                            <a target="_blank" class="evaluation-instructions" href="http://vidhire.net/?p=329">Evaluation Instructions</a>
                                                            <input name="resume_id" type="hidden" value="<?php echo $post->ID; ?>">	  
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>  
                                        </table>    
                                    </form>         

                                    <?php
                                    //Evaluation 
                                    $evaluation_values = $get_evaluation->skills_score . ',' . $get_evaluation->education_score . ',' . $get_evaluation->career_map_score . ',' . $get_evaluation->references_score . ',' . $get_evaluation->video_interview_score . ',' . $get_evaluation->tests_score . ',' . $get_evaluation->positive_adjustments_score;

                                    echo "<div class='final-evaluation-form'>";
                                    echo do_shortcode('[easychart type="vertbar" height="400" width="650" title="" groupcolors="1F5802" groupnames="Score" valuenames="Skills,Education,Career Map,References,V. Interview,Tests,Boost" group1values="' . $evaluation_values . '"]');
                                    echo "</div>";


                                    $skills_score = $get_evaluation->skills_score * 20;
                                    $education_score = $get_evaluation->education_score * 20;
                                    $career_map_score = $get_evaluation->career_map_score * 20;
                                    $references_score = $get_evaluation->references_score * 20;
                                    $video_interview_score = $get_evaluation->video_interview_score * 20;
                                    $tests_score = $get_evaluation->tests_score * 20;
                                    $optional_boost_score = $get_evaluation->positive_adjustments_score * 20;
                                    $final_evaluation_total = round(($get_evaluation->final_evaluation_score / 7) * 20);

                                    /* Shortcode Chart for Final Evaluation */

                                    echo do_shortcode('[su_shadow style="simple"][su_box title="Final Evaluation" style="glass" box_color="#000"]
                                [su_progress_bar style="fancy" percent="' . $skills_score . '" text="Skills" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [su_progress_bar style="fancy" percent="' . $education_score . '" text="Education" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [su_progress_bar style="fancy" percent="' . $career_map_score . '" text="Career Map" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [su_progress_bar style="fancy" percent="' . $references_score . '" text="References" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [su_progress_bar style="fancy" percent="' . $video_interview_score . '" text="Video Interview" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [su_progress_bar style="fancy" percent="' . $tests_score . '" text="Tests" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [su_progress_bar style="fancy" percent="' . $optional_boost_score . '" text="Optional Boost" bar_color="#f0f0f0" fill_color="#97daed" text_color="#555555" class=""]
                                [su_progress_pie percent="' . $final_evaluation_total . '" before="<strong>" after="%</strong>" size="60" pie_width="40" text_size="16" pie_color="#f1efb6" fill_color="#f29b00" text_color="#cd8803"]
                                [/su_box][/su_shadow]');
                                    ?>
                                <?php } ?>
                                <div class="clear"></div>
                                <?php
                                break;
                            case 'chart' :
                                ?>		
                                <!--
                    <h2 class="resume_section_heading"><span><?php echo $media_title ?></span></h2>
                               
                                <div class="reference-request-responses-container" style="border-bottom: 0px solid rgb(204, 204, 204); padding: 0px 0px 0px 0px;">						
                                    
                                    <iframe height="400" width="658" src="<?php echo wptexturize(get_post_meta($post->ID, 'chart', true)); ?>"></iframe>
                                    
                                    <h2 class="reference-request-responses-heading"><span>Reference Request Responses</span></h2>
                                </div> 
                                -->

                                <?php
                                $reference_1 = get_post_meta($post->ID, 'reference_name_1', true);
                                $reference_2 = get_post_meta($post->ID, 'reference_name_2', true);
                                $reference_3 = get_post_meta($post->ID, 'reference_name_3', true);

                                $get_chart_data = $wpdb->get_results($wpdb->prepare("SELECT reference_name,performance,attitude,dependability,team_player,learning_speed,flexibility,creativity FROM wp_references_responses WHERE resume_id in (%d) and reference_name in (%s,%s,%s)", $post->ID, $reference_1, $reference_2, $reference_3), ARRAY_A);

                                foreach ($get_chart_data as $chart_data) {
                                    if ($chart_data['reference_name'] == $reference_1) {
                                        $performance .= $chart_data['performance'];
                                        $attitude .= $chart_data['attitude'];
                                        $dependability .= $chart_data['dependability'];
                                        $team_player .= $chart_data['team_player'];
                                        $learning_speed .= $chart_data['learning_speed'];
                                        $flexibility .= $chart_data['flexibility'];
                                        $creativity .= $chart_data['creativity'];

                                        /* For Getting Text Rating */
                                        $productivity_rating_1 = get_rating($chart_data['performance']);
                                        $attitude_rating_1 = get_rating($chart_data['attitude']);
                                        $dependability_rating_1 = get_rating($chart_data['dependability']);
                                        $team_player_rating_1 = get_rating($chart_data['team_player']);
                                        $learning_speed_rating_1 = get_rating($chart_data['learning_speed']);
                                        $flexibility_rating_1 = get_rating($chart_data['flexibility']);
                                        $creativity_rating_1 = get_rating($chart_data['creativity']);

                                        $productivity_reference_1 = $chart_data['performance'] * 20;
                                        $attitude_reference_1 = $chart_data['attitude'] * 20;
                                        $dependability_reference_1 = $chart_data['dependability'] * 20;
                                        $team_player_reference_1 = $chart_data['team_player'] * 20;
                                        $learning_speed_reference_1 = $chart_data['learning_speed'] * 20;
                                        $flexibility_reference_1 = $chart_data['flexibility'] * 20;
                                        $creativity_reference_1 = $chart_data['creativity'] * 20;

                                        $total_productivity += $chart_data['performance'];
                                        $total_attitude += $chart_data['attitude'];
                                        $total_dependability += $chart_data['dependability'];
                                        $total_team_player += $chart_data['team_player'];
                                        $total_learning_speed += $chart_data['learning_speed'];
                                        $total_flexibility += $chart_data['flexibility'];
                                        $total_creativity += $chart_data['creativity'];
                                        $total_divider += 1;
                                    }


                                    if ($chart_data['reference_name'] == $reference_2) {
                                        $performance .= ',' . $chart_data['performance'];
                                        $attitude .= ',' . $chart_data['attitude'];
                                        $dependability .= ',' . $chart_data['dependability'];
                                        $team_player .= ',' . $chart_data['team_player'];
                                        $learning_speed .= ',' . $chart_data['learning_speed'];
                                        $flexibility .= ',' . $chart_data['flexibility'];
                                        $creativity .= ',' . $chart_data['creativity'];

                                        /* For Getting Text Rating */
                                       /* For Getting Text Rating */
                                        $productivity_rating_2 = get_rating($chart_data['performance']);
                                        $attitude_rating_2 = get_rating($chart_data['attitude']);
                                        $dependability_rating_2 = get_rating($chart_data['dependability']);
                                        $team_player_rating_2 = get_rating($chart_data['team_player']);
                                        $learning_speed_rating_2 = get_rating($chart_data['learning_speed']);
                                        $flexibility_rating_2 = get_rating($chart_data['flexibility']);
                                        $creativity_rating_2 = get_rating($chart_data['creativity']);
                                        
                                        $productivity_reference_2 = $chart_data['performance'] * 20;
                                        $attitude_reference_2 = $chart_data['attitude'] * 20;
                                        $dependability_reference_2 = $chart_data['dependability'] * 20;
                                        $team_player_reference_2 = $chart_data['team_player'] * 20;
                                        $learning_speed_reference_2 = $chart_data['learning_speed'] * 20;
                                        $flexibility_reference_2 = $chart_data['flexibility'] * 20;
                                        $creativity_reference_2 = $chart_data['creativity'] * 20;

                                        $total_productivity += $chart_data['performance'];
                                        $total_attitude += $chart_data['attitude'];
                                        $total_dependability += $chart_data['dependability'];
                                        $total_team_player += $chart_data['team_player'];
                                        $total_learning_speed += $chart_data['learning_speed'];
                                        $total_flexibility += $chart_data['flexibility'];
                                        $total_creativity += $chart_data['creativity'];
                                        $total_divider += 1;
                                    }

                                    if ($chart_data['reference_name'] == $reference_3) {
                                        $performance .= ',' . $chart_data['performance'];
                                        $attitude .= ',' . $chart_data['attitude'];
                                        $dependability .= ',' . $chart_data['dependability'];
                                        $team_player .= ',' . $chart_data['team_player'];
                                        $learning_speed .= ',' . $chart_data['learning_speed'];
                                        $flexibility .= ',' . $chart_data['flexibility'];
                                        $creativity .= ',' . $chart_data['creativity'];
                                        
                                        /* For Getting Text Rating */
                                        $productivity_rating_3 = get_rating($chart_data['performance']);
                                        $attitude_rating_3 = get_rating($chart_data['attitude']);
                                        $dependability_rating_3 = get_rating($chart_data['dependability']);
                                        $team_player_rating_3 = get_rating($chart_data['team_player']);
                                        $learning_speed_rating_3 = get_rating($chart_data['learning_speed']);
                                        $flexibility_rating_3 = get_rating($chart_data['flexibility']);
                                        $creativity_rating_3 = get_rating($chart_data['creativity']);

                                        $productivity_reference_3 = $chart_data['performance'] * 20;
                                        $attitude_reference_3 = $chart_data['attitude'] * 20;
                                        $dependability_reference_3 = $chart_data['dependability'] * 20;
                                        $team_player_reference_3 = $chart_data['team_player'] * 20;
                                        $learning_speed_reference_3 = $chart_data['learning_speed'] * 20;
                                        $flexibility_reference_3 = $chart_data['flexibility'] * 20;
                                        $creativity_reference_3 = $chart_data['creativity'] * 20;

                                        $total_productivity += $chart_data['performance'];
                                        $total_attitude += $chart_data['attitude'];
                                        $total_dependability += $chart_data['dependability'];
                                        $total_team_player += $chart_data['team_player'];
                                        $total_learning_speed += $chart_data['learning_speed'];
                                        $total_flexibility += $chart_data['flexibility'];
                                        $total_creativity += $chart_data['creativity'];
                                        $total_divider += 1;
                                    }
                                }
                                //echo do_shortcode('[wp_charts title="barchart" type="bar" align="alignleft" margin="5px 20px" width="658px" height="400px" datasets="'.$performance.' next '.$attitude.' next '.$dependability.' next '.$team_player .' next '.$learning_speed.' next '.$flexibility.' next '.$creativity.'" labels="' . $reference_1 . ',' . $reference_2 . ',' . $reference_3 . '"]');
                                //echo do_shortcode('[chart barwidth="a,5,15" yaxisrange="0,0,10" yaxislabel="y" legendpos="l" legend="Performance|Attitude|Dependability|Team+Player|Learning+Speed|Flexibility|Creativity" data="'.$performance.'|'.$attitude.'|'.$dependability.'|'.$team_player .'|'.$learning_speed.'|'.$flexibility.'|'.$creativity.'" bg="" labels="' . $reference_1 . '|' . $reference_2 . '|' . $reference_3 . '" colors="058DC7,81feb6,ff8080,1601d1,50B432,ff0f0f,800040,ED561B,EDEF00" size="658x400" title="" type="bar"]');
                                //echo do_shortcode('[barchart]{ label: "Performance", data:   [[1, 10],[2, 10],[3, 10]] },{ label: "Attitude",data: [[5, 10],[3, 10],[3, 10]] }[/barchart]');
                                //echo do_shortcode('[easychart type="vertbar" height="400" width="650" title="" grid="false" groupnames="Productivity,Attitude,Dependability,Team Player,Learning Speed,Flexibility,Creativity" valuenames="' . $reference_1 . ',' . $reference_2 . ',' . $reference_3 . '" group1values="' . $performance . '" group2values="' . $attitude . '" group3values="' . $dependability . '" group4values="' . $team_player . '" group5values="' . $learning_speed . '" group6values="' . $flexibility . '" group7values="' . $creativity . '"]');

                                /* Per Criteria Average */
                                $average_productivity = round($total_productivity / $total_divider,1,PHP_ROUND_HALF_ODD) * 20;
                                $average_attitude = round($total_attitude / $total_divider,1,PHP_ROUND_HALF_ODD) * 20;
                                $average_dependability = round($total_dependability / $total_divider,1,PHP_ROUND_HALF_ODD) * 20;
                                $average_team_player = round($total_team_player / $total_divider,1,PHP_ROUND_HALF_ODD) * 20;
                                $average_learning_speed = round($total_learning_speed / $total_divider,1,PHP_ROUND_HALF_ODD) * 20;
                                $average_flexibility = round($total_flexibility / $total_divider,1,PHP_ROUND_HALF_ODD) * 20;
                                $average_creativity = round($total_creativity / $total_divider,1,PHP_ROUND_HALF_ODD) * 20;
                                $average_final = round(($total_productivity + 
                                                 $total_attitude + 
                                                 $total_dependability + 
                                                 $total_team_player +
                                                 $total_learning_speed +
                                                 $total_flexibility +
                                                 $total_creativity) / (7 * $total_divider),1,PHP_ROUND_HALF_ODD)  * 20;

                                //RRR
                                /* Shortcode Chart for Reference Request Responses */
                                echo do_shortcode('[su_shadow style="simple"][su_box title="Reference Request Responses" style="glass" box_color="#993300"][su_note note_color="#FFF4EA" text_color="#000000" radius="0"]
                                    [su_row][su_column size="3/4"]
                                    <h2>Productivity</h2>
                                    [su_progress_bar style="thin" percent="' . $productivity_reference_1 . '" text="<strong>' . $reference_1 . '</strong>  (' . $productivity_rating_1 . ')" bar_color="#f0dbc9" fill_color="#820063" text_color="#000000"]
                                    [su_progress_bar style="thin" percent="' . $productivity_reference_2 . '" text="<strong>' . $reference_2 . '</strong>  (' . $productivity_rating_2 . ')" bar_color="#f0dbc9" fill_color="#040082" text_color="#000000"]
                                    [su_progress_bar style="thin" percent="' . $productivity_reference_3 . '" text="<strong>' . $reference_3 . '</strong>  (' . $productivity_rating_3 . ')" bar_color="#f0dbc9" fill_color="#66bf04" text_color="#000000"]
                                    [/su_column]
                                    [su_column size="1/4"]
                                    <h3 style="text-align: center;"><span style="color: #919182;">Average</span></h3>
                                    [su_progress_pie percent="' . $average_productivity . '" before="<strong>" after="%</strong>" size="80" pie_width="40" text_size="20" pie_color="#f0dbc9" fill_color="#E68A8A" text_color="#666652"][/su_column]
                                    [/su_row]
                                    [su_heading style="line-light" size="25"][/su_heading]
                                    [su_row][su_column size="3/4"]
                                    <h2>Attitude</h2>
                                    [su_progress_bar style="thin" percent="' . $attitude_reference_1 . '" text="<strong>' . $reference_1 . '</strong>  ('.$attitude_rating_1.')" bar_color="#f0dbc9" fill_color="#820063" text_color="#000000"]
                                    [su_progress_bar style="thin" percent="' . $attitude_reference_2 . '" text="<strong>' . $reference_2 . '</strong>  ('.$attitude_rating_2.')" bar_color="#f0dbc9" fill_color="#040082" text_color="#000000"]
                                    [su_progress_bar style="thin" percent="' . $attitude_reference_3 . '" text="<strong>' . $reference_3 . '</strong>  ('.$attitude_rating_3.')" bar_color="#f0dbc9" fill_color="#66bf04" text_color="#000000"]
                                    [/su_column]
                                    [su_column size="1/4"]
                                    <h3 style="text-align: center;"><span style="color: #919182;">Average</span></h3>
                                    [su_progress_pie percent="' . $average_attitude . '" before="<strong>" after="%</strong>" size="80" pie_width="40" text_size="20" pie_color="#f0dbc9" fill_color="#E68A8A" text_color="#666652"][/su_column]
                                    [/su_row]
                                    [su_heading style="line-light" size="25"][/su_heading]
                                    [su_row][su_column size="3/4"]
                                    <h2>Dependability</h2>
                                    [su_progress_bar style="thin" percent="' . $dependability_reference_1 . '" text="<strong>' . $reference_1 . '</strong>  ('.$dependability_rating_1.')" bar_color="#f0dbc9" fill_color="#820063" text_color="#000000"]
                                    [su_progress_bar style="thin" percent="' . $dependability_reference_2 . '" text="<strong>' . $reference_2 . '</strong>  ('.$dependability_rating_2.')" bar_color="#f0dbc9" fill_color="#040082" text_color="#000000"]
                                    [su_progress_bar style="thin" percent="' . $dependability_reference_3 . '" text="<strong>' . $reference_3 . '</strong>  ('.$dependability_rating_3.')" bar_color="#f0dbc9" fill_color="#66bf04" text_color="#000000"]
                                    [/su_column]
                                    [su_column size="1/4"]
                                    <h3 style="text-align: center;"><span style="color: #919182;">Average</span></h3>
                                    [su_progress_pie percent="' . $average_dependability . '" before="<strong>" after="%</strong>" size="80" pie_width="40" text_size="20" pie_color="#f0dbc9" fill_color="#E68A8A" text_color="#666652"][/su_column]
                                    [/su_row]
                                    [su_heading style="line-light" size="25"][/su_heading]
                                    [su_row][su_column size="3/4"]
                                    <h2>Team Player</h2>
                                    [su_progress_bar style="thin" percent="' . $team_player_reference_1 . '" text="<strong>' . $reference_1 . '</strong>  ('.$team_player_rating_1.')" bar_color="#f0dbc9" fill_color="#820063" text_color="#000000"]
                                    [su_progress_bar style="thin" percent="' . $team_player_reference_2 . '" text="<strong>' . $reference_2 . '</strong>  ('.$team_player_rating_2.')" bar_color="#f0dbc9" fill_color="#040082" text_color="#000000"]
                                    [su_progress_bar style="thin" percent="' . $team_player_reference_3 . '" text="<strong>' . $reference_3 . '</strong>  ('.$team_player_rating_3.')" bar_color="#f0dbc9" fill_color="#66bf04" text_color="#000000"]
                                    [/su_column]
                                    [su_column size="1/4"]
                                    <h3 style="text-align: center;"><span style="color: #919182;">Average</span></h3>
                                    [su_progress_pie percent="' . $average_team_player . '" before="<strong>" after="%</strong>" size="80" pie_width="40" text_size="20" pie_color="#f0dbc9" fill_color="#E68A8A" text_color="#666652"][/su_column]
                                    [/su_row]
                                    [su_heading style="line-light" size="25"][/su_heading]
                                    [su_row][su_column size="3/4"]
                                    <h2>Learning Speed</h2>
                                    [su_progress_bar style="thin" percent="' . $learning_speed_reference_1 . '" text="<strong>' . $reference_1 . '</strong>  ('.$learning_speed_rating_1.')" bar_color="#f0dbc9" fill_color="#820063" text_color="#000000"]
                                    [su_progress_bar style="thin" percent="' . $learning_speed_reference_2 . '" text="<strong>' . $reference_2 . '</strong>  ('.$learning_speed_rating_2.')" bar_color="#f0dbc9" fill_color="#040082" text_color="#000000"]
                                    [su_progress_bar style="thin" percent="' . $learning_speed_reference_3 . '" text="<strong>' . $reference_3 . '</strong>  ('.$learning_speed_rating_3.')" bar_color="#f0dbc9" fill_color="#66bf04" text_color="#000000"]
                                    [/su_column]
                                    [su_column size="1/4"]
                                    <h3 style="text-align: center;"><span style="color: #919182;">Average</span></h3>
                                    [su_progress_pie percent="' . $average_learning_speed . '" before="<strong>" after="%</strong>" size="80" pie_width="40" text_size="20" pie_color="#f0dbc9" fill_color="#E68A8A" text_color="#666652"]
                                    [/su_column]
                                    [/su_row]
                                    [su_heading style="line-light" size="25"][/su_heading]
                                    [su_row][su_column size="3/4"]
                                    <h2>Flexibility</h2>
                                    [su_progress_bar style="thin" percent="' . $flexibility_reference_1 . '" text="<strong>' . $reference_1 . '</strong>  ('.$flexibility_rating_1.')" bar_color="#f0dbc9" fill_color="#820063" text_color="#000000"]
                                    [su_progress_bar style="thin" percent="' . $flexibility_reference_2 . '" text="<strong>' . $reference_2 . '</strong>  ('.$flexibility_rating_2.')" bar_color="#f0dbc9" fill_color="#040082" text_color="#000000"]
                                    [su_progress_bar style="thin" percent="' . $flexibility_reference_3 . '" text="<strong>' . $reference_3 . '</strong>  ('.$flexibility_rating_3.')" bar_color="#f0dbc9" fill_color="#66bf04" text_color="#000000"]
                                    [/su_column]
                                    [su_column size="1/4"]
                                    <h3 style="text-align: center;"><span style="color: #919182;">Average</span></h3>
                                    [su_progress_pie percent="' . $average_flexibility . '" before="<strong>" after="%</strong>" size="80" pie_width="40" text_size="20" pie_color="#f0dbc9" fill_color="#E68A8A" text_color="#666652"][/su_column]
                                    [/su_row]
                                    [su_heading style="line-light" size="25"][/su_heading]
                                    [su_row][su_column size="3/4"]
                                    <h2>Creativity</h2>
                                    [su_progress_bar style="thin" percent="' . $creativity_reference_1 . '" text="<strong>' . $reference_1 . '</strong>  ('.$creativity_rating_1.')" bar_color="#f0dbc9" fill_color="#820063" text_color="#000000"]
                                    [su_progress_bar style="thin" percent="' . $creativity_reference_2 . '" text="<strong>' . $reference_2 . '</strong>  ('.$creativity_rating_2.')" bar_color="#f0dbc9" fill_color="#040082" text_color="#000000"]
                                    [su_progress_bar style="thin" percent="' . $creativity_reference_3 . '" text="<strong>' . $reference_3 . '</strong>  ('.$creativity_rating_3.')" bar_color="#f0dbc9" fill_color="#66bf04" text_color="#000000"]
                                    [/su_column]
                                    [su_column size="1/4"]
                                    <h3 style="text-align: center;"><span style="color: #919182;">Average</span></h3>
                                    [su_progress_pie percent="' . $average_creativity . '" before="<strong>" after="%</strong>" size="80" pie_width="40" text_size="20" pie_color="#f0dbc9" fill_color="#E68A8A" text_color="#666652"]
                                    [/su_column]
                                    [/su_row]
                                    [su_heading style="line-light" size="25"][/su_heading]
                                    [su_row]
                                    [su_column size="2/4"]
                                    <h2 style="text-align:center">References Evaluation</h2>
                                    [/su_column]
                                    [su_column size="1/4"]
                                    &nbsp;
                                    [/su_column]
                                    [su_column size="1/4"]
                                    [su_progress_pie percent="'.$average_final.'" before="<strong>" after="%</strong>" size="80" pie_width="40" text_size="20" pie_color="#f1efb6" fill_color="#cd8803" text_color="#cd8803"]
                                    [/su_column]
                                    [/su_row][/su_note][/su_box][/su_shadow]');
                                ?>


                                <div style="display:none" class="references">
                                    <!--
                                    <h2 class="resume_section_heading"><?php _e('References', APP_TD); ?></h2>
                                    -->

                                    <table class="references-table">
                                        <thead>
                                        <th class="resume_section_heading">Name</th>
                                        <th class="resume_section_heading">Email</th>
                                        <th class="resume_section_heading">Phone</th>
                                        <th class="resume_section_heading">Additional Info</th>
                                        </thead> 	
                                        <tbody>
                                            <tr class="resume_section">
                                                <td ><?php echo wptexturize(get_post_meta($post->ID, 'reference_name_1', true)); ?></td>
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_email_1', true)); ?></td>
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_phone_number_1', true)); ?></td>
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_position_1', true)); ?></td>	
                                                <td nowrap><?php echo wptexturize(get_post_meta($post->ID, 'reference_additional_info_1', true)); ?></td>
                                            </tr>
                                            <tr class="resume_section">
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_name_2', true)); ?></td>
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_email_2', true)); ?></td>
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_phone_number_2', true)); ?></td>
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_position_2', true)); ?></td>
                                                <td nowrap><?php echo wptexturize(get_post_meta($post->ID, 'reference_additional_info_2', true)); ?></td>
                                            </tr>
                                            <tr class="resume_section">
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_name_3', true)); ?></td>
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_email_3', true)); ?></td>
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_phone_number_3', true)); ?></td>
                                                <td nowrap><?php echo wptexturize(get_post_meta($post->ID, 'reference_additional_info_3', true)); ?></td>
                                            </tr>
                                            <tr class="resume_section">
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_name_4', true)); ?></td>
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_email_4', true)); ?></td>
                                                <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_phone_number_4', true)); ?></td>
                                                <td nowrap><?php echo wptexturize(get_post_meta($post->ID, 'reference_additional_info_4', true)); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>              
                                </div>

                                <div class="clear"></div>  
                                <?php
                                break;
                        endswitch;
                    endforeach;
                    ?>


                    <?php if (current_user_can('manage_options') || current_user_can('can_submit_job')) : ?>  
                        <!--                 
                         <div class="internal-notes">
                             <h2 class="resume_section_heading"><span style="padding-left: 4px;"><?php echo __('Admin Notes', APP_TD) ?></span></h2>
                             <div class="resume_section" style="border-bottom: 0px solid rgb(204, 204, 204);">						
                                 <p>
                        <?php echo wptexturize(get_post_meta($post->ID, 'internal_notes', true)); ?>
                                 </p>
                             </div>  
                         </div>
                        
                        <div class="processing-status">
                            <h2 class="resume_section_heading"><span style="padding-left: 4px;"><?php echo __('Processing Status', APP_TD) ?></span></h2>    
                            <div class="resume_section">
                        <?php
                        $terms = wp_get_post_terms($post->ID, 'resume_groups');
                        if ($terms) :
                            ?>
                            <?php
                            $terms_array = array();
                            foreach ($terms as $t) :
                                if (sizeof($terms_array) != (sizeof($terms) - 1)) :
                                    $terms_array[] = $t->name . ', ';
                                else :
                                    $terms_array[] = $t->name;
                                endif;
                            endforeach;
                            echo '<ul class="terms"><li>' . implode('</li><li>', $terms_array) . '</li></ul>';
                            ?>

                                                                                                                                                                                        <div class="clear"></div>

                        <?php endif; ?>   
                            </div>
                        </div>
                        -->

                    <?php else : ?>    	                      

                    <?php endif; ?>    

                    <!--           
                    <?php if (get_option('jr_ad_stats_all') == 'yes' && current_theme_supports('app-stats')) { ?><p class="stats"><?php appthemes_stats_counter($post->ID); ?></p> <?php } ?>
                                                         
                                                         <div class="clear"></div>                      
                    -->  
                </div>  

                <div style="display:none">
                    <?php
                    /* For Status Tags */

                    $thetags = array($resume_options['fast_tracked'], $resume_options['reference_checked'], $resume_options['video_interview'], $resume_options['red_flagged'], $resume_options['completed_evaluation'], $resume_options['starred']);
                    $thetags = array_map('trim', $thetags);

                    if (sizeof($thetags) > 0) {

                        wp_set_object_terms($post->ID, $thetags, 'resume_groups');
                    }
                    ?>
                </div>


                <?php if (get_the_author_meta('ID') == get_current_user_id() || current_user_can('manage_options')) : ?>
                    <!--
                    <p class="button edit_resume"><a href="<?php echo add_query_arg('edit', $post->ID, get_permalink(JR_Resume_Edit_Page::get_id())); ?>"><?php _e('Edit Resume&nbsp;&rarr;', APP_TD); ?></a></p>
                    -->
                    <p class="button edit_resume">
                        <input class="button_edit_resume" type="button" value="Edit Resume" />
                        <input class="edit_link" type="hidden" value="<?php echo add_query_arg('edit', $post->ID, get_permalink(JR_Resume_Edit_Page::get_id())); ?>">
                    </p>


                <?php endif; ?>


            </div><!-- end section_content -->

            <?php appthemes_after_post(); ?>

            <?php jr_resume_footer($post); ?>

        <?php endwhile; ?>

        <?php appthemes_after_endwhile(); ?>

    <?php else: ?>

        <?php jr_no_access_permission(__('Sorry, you do not have permission to View Resumes.', APP_TD)); ?>

        <?php appthemes_loop_else(); ?>

    <?php endif; ?>	

    <?php appthemes_after_loop(); ?>

    <div id="comment-tab">
        <!--?php comments_template('/theme-comments.php'); ?-->
        <?php comments_template(); ?>
    </div>

</div><!-- end section -->	

<div class="clear"></div>

<?php $wpdb->flush(); ?>

</div><!-- end main content -->


<?php if ($show_contact_form) : ?>
    <script type="text/javascript">
        /* <![CDATA[ */

        jQuery('a.contact_button').fancybox({
            'speedIn': 600,
            'speedOut': 200,
            'overlayShow': true,
            'centerOnScroll': true,
            'overlayColor': '#555',
            'hideOnOverlayClick': false
        });
        /* ]]> */
    </script>
    <?php
endif;
?>	

<?php if (get_the_author_meta('ID') == get_current_user_id()) : ?>
    <script type="text/javascript">
        /* <![CDATA[ */

        jQuery('p.edit_button a, a.edit_button').fancybox({
            'speedIn': 600,
            'speedOut': 200,
            'overlayShow': true,
            'centerOnScroll': true,
            'overlayColor': '#555',
            'hideOnOverlayClick': false
        });

        jQuery('a.delete').click(function () {
            var answer = confirm("<?php _e('Are you sure you want to delete this? This action cannot be undone...', APP_TD); ?>")
            if (answer)
                return true;
            return false;
        });

        /* ]]> */
    </script>
    <?php
    if (get_option('jr_show_sidebar') !== 'no') : get_sidebar('user');
    endif;
else :
    if (get_option('jr_show_sidebar') !== 'no') : get_sidebar('resume');
    endif; 
endif; 

