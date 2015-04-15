<div style="float:right;">
<?php 
    echo do_shortcode('[su_button url="javascript:window.print();" style="glass" background="#003de6" color="#ffffff" size="1" radius="round" icon="icon: file-powerpoint-o" icon_color="#ffffff"]<strong>Print</strong>[/su_button]');
    echo "&nbsp;";
    echo "&nbsp;";
    echo do_shortcode('[su_button url="jrchange me)" style="glass" background="#003de6" color="#ffffff" size="1" radius="round" icon="icon: envelope-o" icon_color="#ffffff"]<strong>Email</strong>[/su_button]');
?>
   
</div>    
<div class="section single">
    <?php do_action('appthemes_notices'); ?>

    <?php appthemes_before_loop(); ?>

    <?php if (have_posts()) : ?>

        <?php while (have_posts()) : the_post(); ?>

            <?php appthemes_before_post(); ?>

            <?php appthemes_stats_update($post->ID); //records the page hit  ?>

            <div class="section_header">

                <?php appthemes_before_post_title(); ?>

                <?php if (has_post_thumbnail()) { ?>
                    <div class="job_company_logo">
                        <?php the_post_thumbnail(array(250, 180)); ?>
                    </div>
                <?php } ?>
                <br />
                <br />
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
                            <h2 style="height: 0px;"><strong><?php echo $company_name; ?> @ <a href="<?php echo 'http://'.$company_url; ?>" rel="nofollow"><?php echo $company_url; ?></a></strong></h2>
                            
                            <div class="location">
                                <strong><?php jr_location(); ?></strong>
                                <a class="toggleMap" href="#"><img src="http://vidhire.net/wp-content/uploads/2015/04/map-icon2-40.jpg" /></a>
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

                <?php
                $author = get_user_by('id', $post->post_author);
                if ($author && $link = get_author_posts_url($author->ID, $author->user_nicename))
                    echo sprintf($format, $link, $author->display_name);

                // load up theme-actions.php and display the apply form
                //do_action('job_footer');
                ?>

                <?php appthemes_after_post_content(); ?>

                <div class="clear"></div>

            </div><!--section content-->
            <br />
            <br />

            <!--?php
            jr_geolocation_scripts();
            
            ?-->

            <!--div id="geolocation_box">
                <p>
                    <label>

                        <input id="geolocation-load" type="hidden" class="button geolocationadd submit" value="<?php esc_attr_e('Find Address/Location', APP_TD); ?>" />
                    </label>

                    <input type="hidden" class="hidden" name="jr_address" id="geolocation-address" value="<?php echo esc_attr($post->jr_address); ?>" />
                    <input type="hidden" class="text" name="jr_geo_latitude" id="geolocation-latitude" value="<?php echo esc_attr($post->jr_geo_latitude); ?>" />
                    <input type="hidden" class="text" name="jr_geo_longitude" id="geolocation-longitude" value="<?php echo esc_attr($post->jr_geo_longitude); ?>" />
                </p>

                <div id="map_wrap" style="width:100%;height:250px;"><div id="geolocation-map" style="width:100%;height:250px;"></div></div>
            </div-->

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

<!--?php
if (get_option('jr_show_sidebar') !== 'no') :
    get_sidebar('job');
endif;
?-->

<div class="clear"></div>
