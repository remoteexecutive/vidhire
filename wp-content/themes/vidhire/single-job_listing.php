<div class="section single">

    <?php do_action('appthemes_notices'); ?>

    <?php appthemes_before_loop(); ?>

    <?php if (have_posts()) : ?>

        <?php while (have_posts()) : the_post(); ?>

            <?php appthemes_before_post(); ?>

            <?php appthemes_stats_update($post->ID); //records the page hit ?>

            <div class="section_header">

                <?php appthemes_before_post_title(); ?>

                <?php if (has_post_thumbnail()) { ?>
                    <div class="job_company_logo">
                        <?php the_post_thumbnail(array(650, 1535)); ?>
                    </div>
                <?php } ?>

                <div class="job-details-title">
                    <div class="title">
                        <strong><a class="job-title-color" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong>&nbsp;&nbsp;<?php jr_get_custom_taxonomy($post->ID, 'job_type', 'jtype') ?>
                    </div>      
                </div><!--job-details-title-->

                <?php appthemes_after_post_title(); ?>

                <div class="job-details">

                    <?php
                    global $post;

                    $company_name = wptexturize(strip_tags(get_post_meta($post->ID, '_Company', true)));

                    if ($company_name) {
                        //if ( $company_url = esc_url( get_post_meta( $post->ID, '_CompanyURL', true ) ) ) {
                        if ($company_url = get_post_meta($post->ID, '_CompanyURL', true)) {
                            ?>  
                            <h2 style="height: 0px;"><strong><?php echo $company_name; ?></strong></h2>
                            <a href="<?php echo $company_url; ?>" rel="nofollow"><?php echo $company_url; ?></a>
                            <div class="location">
                                <strong><?php jr_location(); ?></strong>
                            </div>        
                            <?php
                        } else {
                            echo $company_name;
                        }
                        $format = __('<div class="posted-by">Posted by: <a style="font-weight: normal;" href="%s">%s</a>
 on ' . date_i18n('M j', strtotime($post->post_date)) . ',&nbsp;' . date_i18n('Y', strtotime($post->post_date)) . '
</div><br />', APP_TD);

                        /*
                          $format .= __('<div class="jobs_date"> on : '.date_i18n('j M', strtotime($post->post_date))',&nbsp;<span class="resume_year">'.date_i18n('Y', strtotime($post->post_date)).'</span></div>', APP_TD);


                          $format .= __('<div class="jobs_date"> on : '.date_i18n('j M', strtotime($post->post_date))',&nbsp;<span class="resume_year">'.date_i18n('Y', strtotime($post->post_date)).'</span></div>', APP_TD);
                         */
                    } else {
                        $format = '<a href="%s">%s</a>';
                    }

                    $author = get_user_by('id', $post->post_author);
                    if ($author && $link = get_author_posts_url($author->ID, $author->user_nicename))
                        echo sprintf($format, $link, $author->display_name);
                    ?>

                </div><!--job-details-->

                <div class="clear"></div>

            </div><!--end section header-->

            <div class="section_content">

                <?php do_action('job_main_section', $post); ?>

                <?php appthemes_before_post_content(); ?>

                <?php the_content(); ?>

                <?php the_job_listing_fields(); ?>

                <?php the_listing_files(); ?>

                <div class="job_listing_video">
                    <?php echo do_shortcode('[video height="355" width="658" src="' . wptexturize(get_post_meta($post->ID, 'job_listing_video', true)) . '" ]'); ?>
                </div>

                <?php appthemes_after_post_content(); ?>

                <div class="clear"></div>



            </div><!--section content-->

            <?php
            // load up theme-actions.php and display the apply form
            //do_action('job_footer');
            ?>

            <ul class="section_footer" >

                <?php if ($url = get_post_meta($post->ID, 'job_url', true)) : ?>
                    <li class="apply"><a href="<?php
                        echo $url;
                        echo 'unregistered'
                        ?>" <?php
                                         if ($onmousedown = get_post_meta($post->ID, 'onmousedown', true)) :
                                             echo 'onmousedown="' . $onmousedown . '"';
                                         endif;
                                         ?> target="_blank" rel="nofollow"><?php _e('View &amp; Apply Online', APP_TD); ?></a></li>
                    <?php else : ?>
                        <?php
                        if (is_user_logged_in()) {
                            ?>	
                        <li class="apply"><a href="#" class="noscroll apply_for_job" ><?php _e('Apply for Job', APP_TD); ?></a></li>
                    <?php } else { ?>
                        <li class="apply"><a href="#" class="noscroll apply_for_job" ><?php _e('Apply for Job', APP_TD); ?></a></li>
                    <?php } ?>
                <?php endif; ?>

                <?php if (is_user_logged_in() && current_user_can('can_submit_resume')) : $starred = (array) get_user_meta(get_current_user_id(), '_starred_jobs', true); ?>
                    <?php if (!in_array($post->ID, $starred)) : ?>
                        <li class="star"><a href="<?php echo add_query_arg('star', 'true', get_permalink()); ?>" class="star"><?php _e('Star Job', APP_TD); ?></a></li>
                    <?php else : ?>
                        <li class="star"><a href="<?php echo add_query_arg('star', 'false', get_permalink()); ?>" class="star"><?php _e('Un-star Job', APP_TD); ?></a></li>
                    <?php endif; ?>
                <?php endif; ?>

                <li class="print"><a href="javascript:window.print();"><?php _e('Print Job', APP_TD); ?></a></li>

                <!--?php if (get_post_meta($post->ID, '_jr_geo_longitude', true) && get_post_meta($post->ID, '_jr_geo_latitude', true)) : ?-->
                <!--li class="map">
                    <a href="#map" class="toggle_map"><!--?php _e('View Company Location', APP_TD); ?></a-->
                <!--/li-->
                <!--?php endif; ?-->

                <?php if (function_exists('selfserv_sexy')) { ?><li class="sexy share"><a href="#share_form" class="share"><?php _e('Share Job', APP_TD); ?></a></li><?php } ?>

                <?php if (get_the_author_meta('ID') == get_current_user_id() || current_user_can('manage_options')) : ?>
                    <li class="edit-job"><?php
                        //the_job_edit_link(); 

                        if (!jr_allow_editing())
                            return;

                        $job_id = $job_id ? $job_id : get_the_ID();

                        if (!jr_is_job_author($job_id))
                            return;

                        if (empty($text))
                            $text = __('Edit Job', APP_TD);

                        echo html('a', array(
                            'class' => 'job-edit-link',
                            'href' => jr_get_job_edit_url($job_id),
                                ), $text);
                        ?></li>
                <?php endif; ?>
            </ul>
            <br />
            <br />
            <div class="apply_for_job_div">
                <?php if (is_user_logged_in() && current_user_can('can_submit_resume') || is_user_logged_in() && current_user_can('manage_options')) { ?>
                    <form class="apply_for_job_form">    
                        <input class="job_title" type="hidden" value="<?php the_title(); ?>" />
                        <label>Resume: </label>
                        <select class="apply_for_job_dropdown">
                            <?php
                            global $wpdb;

                            if (current_user_can('manage_options')) {
                                $get_resumes = $wpdb->get_results('SELECT * FROM wp_posts WHERE post_type in ("resume")', ARRAY_A);
                            } else {
                                $get_resumes = $wpdb->get_results('SELECT * FROM wp_posts WHERE post_author in (' . get_current_user_id() . ') AND post_type in ("resume")', ARRAY_A);
                            }
                            
                            foreach ($get_resumes as $resumes) {
                                ?>
                                <option value="<?php echo $resumes['ID']; ?>"><?php echo $resumes['post_title']; ?></option>
                            <?php } ?>
                        </select>    
                        <br />
                        <br />
                        <input type="button" class="apply_for_job_submit" value="Submit Application"/>
                    </form>
                    <?php
                } else {

                    $redirect = $action = $role = '';

// set a redirect for after logging in
                    if (isset($_REQUEST['redirect_to'])) {
                        $redirect = $_REQUEST['redirect_to'];
                    }

                    jr_register_form($redirect, $role);
                }
                ?>
            </div>

            <?php
            jr_geolocation_scripts();
            ?>

            <div id="job_map" style="display:inline">

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

        </div>

        <?php comments_template(); ?>

        <?php appthemes_after_post(); ?>

    <?php endwhile; ?>

    <?php appthemes_after_endwhile(); ?>

<?php else: ?>

    <?php appthemes_loop_else(); ?>

<?php endif; ?>	

<?php appthemes_after_loop(); ?>



</div><!--end section-->

<?php
if (get_option('jr_show_sidebar') !== 'no') :
    get_sidebar('job');
endif;
?>

<div class="clear"></div>




</div>




