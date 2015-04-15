<div id="single-job-header">
    <?php if (is_singular('job_listing')) { ?>
        <div class="user-options" style="padding:30px;">
            <?php
            if (is_user_logged_in() || is_user_logged_in() && current_user_can('manage_options') || is_user_logged_in() && get_the_author_meta('ID') == get_current_user_id()) {

                echo do_shortcode('[su_button class="user-edit" url="' . jr_get_job_edit_url($post->ID) . '" style="glass" background="#003de6" color="#ffffff" size="1" radius="round" icon="icon: pencil-square-o" icon_color="#ffffff"]<strong>Edit</strong>[/su_button]');
            }
            if (current_user_can('manage_options')) {
                echo do_shortcode('[su_button class="admin-edit" url="http://' . $_SERVER['HTTP_HOST'] . '/wp-admin/post.php?post=' . $post->ID . '&action=edit" style="glass" background="#003de6" color="#ffffff" size="1" radius="round" icon="icon: wrench" icon_color="#ffffff"]<strong>Admin Edit</strong>[/su_button]');
            }

            echo do_shortcode('[su_button class="apply_for_job" url="#" style="glass" background="#16e600" color="#ffffff" size="4" radius="round" icon="icon: arrow-up" icon_color="#ffffff" text_shadow="1px 1px 3px #000000"]<strong>Apply Now</strong>[/su_button]');
            echo '<br />';
            echo '<br />';
            ?>
        <?php } else { ?>
            <div class="user-options" style="padding:30px;">
                <?php
                
                echo do_shortcode('[su_button class="job_save" url="#" style="glass" background="#003de6" color="#ffffff" size="1" radius="round" icon="icon: pencil-square-o" icon_color="#ffffff"]<strong>Save</strong>[/su_button]');
                echo do_shortcode('[su_button class="job_view" url="'.html_link( get_permalink( $job->ID ), get_the_title( $job->ID ) ).'" style="glass" background="#003de6" color="#ffffff" size="1" radius="round" icon="icon: pencil-square-o" icon_color="#ffffff"]<strong>View</strong>[/su_button]');
                echo do_shortcode('[su_button class="job_dashboard" url="http://'.$_SERVER['HTTP_HOST'].'" style="glass" background="#003de6" color="#ffffff" size="1" radius="round" icon="icon: pencil-square-o" icon_color="#ffffff"]<strong>Dashboard</strong>[/su_button]');
                echo '<br />';
                echo '<br />';
                ?>

            <?php } ?>    
        </div>

    </div>
