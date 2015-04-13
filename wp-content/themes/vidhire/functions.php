<?php
/**
 * Theme functions file
 *
 * DO NOT MODIFY THIS FILE. Make a child theme instead: http://codex.wordpress.org/Child_Themes
 *
 * @package Vidhire
 * @author AppThemes
 */
// Define vars and globals
global $app_version, $app_form_results, $jr_log, $app_abbr;

// current version
$app_theme = 'JobRoller';
$app_abbr = 'jr';
$app_version = '1.7.4';

define('APP_TD', 'jobroller');
define('JR_VERSION', $app_version);
define('JR_FIELD_PREFIX', '_' . $app_abbr . '_');

// Framework
require( dirname(__FILE__) . '/framework/load.php' );

// Payments Framework
require dirname(__FILE__) . '/includes/payments/load.php';

scb_register_table('app_pop_daily', $app_abbr . '_counter_daily');
scb_register_table('app_pop_total', $app_abbr . '_counter_total');

require( dirname(__FILE__) . '/framework/includes/stats.php' );

// Custom forms
require dirname(__FILE__) . '/includes/custom-forms/form-builder.php';

// Theme-specific files
require( dirname(__FILE__) . '/includes/theme-functions.php' );

/*
  Add comments to the custom post type resume
 */
add_post_type_support('resume', array('comments'));

/*
  Remove the admin bar for non-admin accounts
 */

show_admin_bar(false);


/*
  Tell WP to support svg
 */

function cc_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}

add_filter('upload_mimes', 'cc_mime_types');

/*
 * Add All Custom Ajax functions to be called
 * */
add_action('wp_ajax_save_evaluation', 'save_evaluation');
add_action('wp_ajax_save_video_evaluation', 'save_video_evaluation');
add_action('wp_ajax_job_end', 'job_end');
add_action('wp_ajax_job_delete', 'job_delete');
add_action('wp_ajax_resume_delete', 'resume_delete');
add_action('wp_ajax_job_relisting', 'job_relisting');
add_action('wp_ajax_change_resume_statuses', 'change_resume_statuses');
add_action('wp_ajax_post_comment_ajax', 'post_comment_ajax');
add_action('wp_ajax_send_email_ajax', 'send_email_ajax');
add_action('wp_ajax_apply_for_job', 'apply_for_job');

function apply_for_job() {

    global $wpdb;

    if ($_POST) {
        $resume_id = $_POST['resume_id'];
        $resume = $_POST['resume_name'];
        $job = $_POST['job'];

        $data = array(
            'ID' => $resume_id,
            'post_title' => $resume
        );

        wp_set_object_terms($resume_id, array($job), 'resume_category');

        wp_update_post($data);

        $job_terms = wp_get_post_terms($resume_id, 'resume_category');

        $get_job_owner = $wpdb->get_row("SELECT distinct(post_author) as job_owner, ID as job_id FROM wp_posts WHERE post_name in ('" . $job_terms[0]->slug . "')");

        $job_owner = $get_job_owner->job_owner;

        $job_id = $get_job_owner->job_id;

        //Check Resume Statuses if Resume ID exists
        //If they do not, insert them

        if ($wpdb->update(
                        'wp_resume_statuses', array(
                    'employer_id' => $job_owner,
                    'job_applied_to' => $job_terms[0]->name,
                    'job_slug' => $job_terms[0]->slug,
                    'job_owner' => $job_owner,
                    'job_id' => $job_id
                        ), array(
                    //'employer_id' => $job_owner,
                    'resume_id' => $resume_id
                        //'job_id' => $job_id
                        ), array(
                    '%s'
                        ), array('%s')
                ) == false) {
            echo "Could not update wp_resume_statuses table";
        } else {
            echo "Updated wp_resume_statuses table";
        }
    }
}

function resume_delete() {

    global $wpdb;

    if ($_POST) {
        $resume_id = $_POST['resume_id'];

        $posts = $wpdb->prepare("DELETE FROM wp_posts WHERE ID = '$resume_id' and post_type = 'resume'");

        $statuses = $wpdb->prepare("DELETE FROM wp_resume_statuses WHERE resume_id = '$resume_id'");

        $video_evaluation = $wpdb->prepare("DELETE FROM wp_video_evaluation WHERE resume_id = '$resume_id'");

        $final_evaluation = $wpdb->prepare("DELETE FROM wp_final_evaluation WHERE resume_id = '$resume_id'");

        $reference_responses = $wpdb->prepare("DELETE FROM wp_references_responses WHERE resume_id = '$resume_id'");

        $wpdb->query($posts);
        $wpdb->query($statuses);
        $wpdb->query($video_evaluation);
        $wpdb->query($final_evaluation);
        $wpdb->query($reference_responses);
    }
}

function save_video_evaluation() {

    /*
      Ajax Save Evaluation
     */
    global $wpdb, $post;

    if ($_POST) {
        $employer_id = wp_get_current_user();
        $resume_id = intval($_POST['resume_id']);


        $confidence_score = intval($_POST['confidence_score']);
        $communication_score = intval($_POST['communication_score']);
        $fun_factor_score = intval($_POST['fun_factor_score']);
        $connection_score = intval($_POST['connection_score']);
        $understanding_score = intval($_POST['understanding_score']);
        $bonus_score = intval($_POST['bonus_score']);
        $video_evaluation_score = $confidence_score + $communication_score + $fun_factor_score + $connection_score + $understanding_score + $bonus_score;

        $confidence_notes = $_POST['confidence_notes'];
        $communication_notes = $_POST['communication_notes'];
        $fun_factor_notes = $_POST['fun_factor_notes'];
        $connection_notes = $_POST['connection_notes'];
        $understanding_notes = $_POST['understanding_notes'];
        $bonus_notes = $_POST['bonus_notes'];

        $confidence_evaluator = $_POST['confidence_evaluator'];
        $communication_evaluator = $_POST['communication_evaluator'];
        $fun_factor_evaluator = $_POST['fun_factor_evaluator'];
        $connection_evaluator = $_POST['connection_evaluator'];
        $understanding_evaluator = $_POST['understanding_evaluator'];
        $bonus_evaluator = $_POST['bonus_evaluator'];


        $employer_id_count = $wpdb->get_row("SELECT count(employer_id) as count FROM wp_video_evaluation WHERE employer_id in ('" . $employer_id->ID . "') AND resume_id in ('" . $resume_id . "')");

        if ($employer_id_count->count == 0) {
            //For Resume Statuses
            $wpdb->insert('wp_video_evaluation', array(
                'resume_id' => $resume_id,
                'employer_id' => $employer_id->ID,
                'confidence_score' => $confidence_score,
                'communication_score' => $communication_score,
                'fun_factor_score' => $fun_factor_score,
                'connection_score' => $connection_score,
                'understanding_score' => $understanding_score,
                'bonus_score' => $bonus_score,
                'video_evaluation_score' => $video_evaluation_score,
                'confidence_notes' => $confidence_notes,
                'communication_notes' => $communication_notes,
                'fun_factor_notes' => $fun_factor_notes,
                'connection_notes' => $connection_notes,
                'understanding_notes' => $understanding_notes,
                'bonus_notes' => $bonus_notes,
                'confidence_evaluator' => $confidence_evaluator,
                'communication_evaluator' => $communication_evaluator,
                'fun_factor_evaluator' => $fun_factor_evaluator,
                'connection_evaluator' => $connection_evaluator,
                'understanding_evaluator' => $understanding_evaluator,
                'bonus_evaluator' => $bonus_evaluator
            ));
        } else {

            $wpdb->update(
                    'wp_video_evaluation', array(
                'resume_id' => $resume_id,
                'employer_id' => $employer_id->ID,
                'confidence_score' => $confidence_score,
                'communication_score' => $communication_score,
                'fun_factor_score' => $fun_factor_score,
                'connection_score' => $connection_score,
                'understanding_score' => $understanding_score,
                'bonus_score' => $bonus_score,
                'video_evaluation_score' => $video_evaluation_score,
                'confidence_notes' => $confidence_notes,
                'communication_notes' => $communication_notes,
                'fun_factor_notes' => $fun_factor_notes,
                'connection_notes' => $connection_notes,
                'understanding_notes' => $understanding_notes,
                'bonus_notes' => $bonus_notes,
                'confidence_evaluator' => $confidence_evaluator,
                'communication_evaluator' => $communication_evaluator,
                'fun_factor_evaluator' => $fun_factor_evaluator,
                'connection_evaluator' => $connection_evaluator,
                'understanding_evaluator' => $understanding_evaluator,
                'bonus_evaluator' => $bonus_evaluator
                    ), array(
                'employer_id' => $employer_id->ID,
                'resume_id' => $resume_id
            ));
        }
    }
    return true;
}

function save_evaluation() {

    /*
      Ajax Save Evaluation
     */
    global $wpdb, $post;

    if ($_POST) {
        $employer_id = wp_get_current_user();
        $resume_id = intval($_POST['resume_id']);

        update_post_meta($resume_id, 'internal_notes', $_POST['internal-notes-text-area']);

        $skills_score = intval($_POST['skills_score']);
        $education_score = intval($_POST['education_score']);
        $career_map_score = intval($_POST['career_map_score']);
        $references_score = intval($_POST['references_score']);
        $video_interview_score = intval($_POST['video_interview_score']);
        $tests_score = intval($_POST['tests_score']);
        $positive_adjustments_score = intval($_POST['positive_adjustments_score']);
        $final_evaluation_score = $skills_score + $education_score + $career_map_score + $references_score + $video_interview_score + $tests_score + $positive_adjustments_score;

        $skills_notes = $_POST['skills_notes'];
        $education_notes = $_POST['education_notes'];
        $career_map_notes = $_POST['career_map_notes'];
        $references_notes = $_POST['references_notes'];
        $video_interview_notes = $_POST['video_interview_notes'];
        $tests_notes = $_POST['tests_notes'];
        $positive_adjustments_notes = $_POST['positive_adjustments_notes'];

        $skills_evaluator = $_POST['skills_evaluator'];
        $education_evaluator = $_POST['education_evaluator'];
        $career_map_evaluator = $_POST['career_map_evaluator'];
        $references_evaluator = $_POST['references_evaluator'];
        $video_interview_evaluator = $_POST['video_interview_evaluator'];
        $tests_evaluator = $_POST['tests_evaluator'];
        $positive_adjustments_evaluator = $_POST['positive_adjustments_evaluator'];


        $employer_id_count = $wpdb->get_row("SELECT count(employer_id) as count FROM wp_final_evaluation WHERE employer_id in ('" . $employer_id->ID . "') AND resume_id in ('" . $resume_id . "')");

        if ($employer_id_count->count == 0) {
            //For Resume Statuses
            $wpdb->insert('wp_final_evaluation', array(
                'resume_id' => $resume_id,
                'employer_id' => $employer_id->ID,
                'skills_score' => $skills_score,
                'education_score' => $education_score,
                'career_map_score' => $career_map_score,
                'references_score' => $references_score,
                'video_interview_score' => $video_interview_score,
                'tests_score' => $tests_score,
                'positive_adjustments_score' => $positive_adjustments_score,
                'final_evaluation_score' => $final_evaluation_score,
                'skills_notes' => $skills_notes,
                'education_notes' => $education_notes,
                'career_map_notes' => $career_map_notes,
                'references_notes' => $references_notes,
                'video_interview_notes' => $video_interview_notes,
                'tests_notes' => $tests_notes,
                'positive_adjustments_notes' => $positive_adjustments_notes,
                'skills_evaluator' => $skills_evaluator,
                'education_evaluator' => $education_evaluator,
                'career_map_evaluator' => $career_map_evaluator,
                'references_evaluator' => $references_evaluator,
                'video_interview_evaluator' => $video_interview_evaluator,
                'tests_evaluator' => $tests_evaluator,
                'positive_adjustments_evaluator' => $positive_adjustments_evaluator
            ));
        } else {

            $wpdb->update(
                    'wp_final_evaluation', array(
                'skills_score' => $skills_score,
                'education_score' => $education_score,
                'career_map_score' => $career_map_score,
                'references_score' => $references_score,
                'video_interview_score' => $video_interview_score,
                'tests_score' => $tests_score,
                'positive_adjustments_score' => $positive_adjustments_score,
                'final_evaluation_score' => $final_evaluation_score,
                'skills_notes' => $skills_notes,
                'education_notes' => $education_notes,
                'career_map_notes' => $career_map_notes,
                'references_notes' => $references_notes,
                'video_interview_notes' => $video_interview_notes,
                'tests_notes' => $tests_notes,
                'positive_adjustments_notes' => $positive_adjustments_notes,
                'skills_evaluator' => $skills_evaluator,
                'education_evaluator' => $education_evaluator,
                'career_map_evaluator' => $career_map_evaluator,
                'references_evaluator' => $references_evaluator,
                'video_interview_evaluator' => $video_interview_evaluator,
                'tests_evaluator' => $tests_evaluator,
                'positive_adjustments_evaluator' => $positive_adjustments_evaluator
                    ), array(
                'employer_id' => $employer_id->ID,
                'resume_id' => $resume_id
            ));
        }
    }
    return true;
}

/**
 * Functions for Job End
 *  
 */
function job_end() {

    global $wpdb;

    if ($_POST) {

        $job_id = $_POST['job_id'];

        $sql = $wpdb->prepare("UPDATE wp_posts 
                           SET post_status = 'expired'
                           WHERE ID = '$job_id' and post_type = 'job_listing'");

        $wpdb->query($sql);
    }
}

/**
 * Functions for Job Delete  
 *  
 */
function job_delete() {

    global $wpdb;

    if ($_POST) {
        $job_id = $_POST['job_id'];

        $sql = $wpdb->prepare("DELETE FROM wp_posts WHERE ID = '$job_id' and post_type = 'job_listing'");

        $wpdb->query($sql);
    }
}

/**
 * Functions for Job Relisting
 *  
 */
function job_relisting() {

    global $wpdb;

    if ($_POST) {


        $job_id = $_POST['job_id'];

        $sql = $wpdb->prepare("UPDATE wp_posts 
                           SET post_status = 'publish'
                           WHERE ID = '$job_id' and post_type = 'job_listing'");

        $wpdb->query($sql);
    }
}

/*
 * Functions for Resume Statuses
 * */

function change_resume_statuses() {
    global $wpdb;

    if ($_POST) {
        $resume_id = $_POST['resume_id'];
        $status_text = $_POST['status_text'];
        $resume_status = $_POST['resume_status'];
        $employer_id = $_POST['employer_id'];

        switch ($resume_status) {

            case "fast-track":
                if ($status_text == "Standard Tracked") {

                    $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
		SET fast_tracked = %s
		WHERE resume_id = %d 
		AND employer_id = %d", "Fast Tracked", $resume_id, $employer_id);

                    $wpdb->query($sql);
                    ?>
                    <img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/green-check-mark.png" /><a href="#" class="fast-track">Fast Tracked</a>
                    <?php
                } else {

                    $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
		SET fast_tracked = %s
		WHERE resume_id = %d 
		AND employer_id = %d", "Standard Tracked", $resume_id, $employer_id);

                    $wpdb->query($sql);
                    ?>
                    <img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/orange-check-mark.png" /><a href="#" class="fast-track">Standard Tracked</a>
                    <?php
                }

                break;

            case "reference-checked":

                if ($status_text == "Check Reference") {

                    $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
		SET reference_checked = %s
		WHERE resume_id = %d 
		AND employer_id = %d", "References Checked", $resume_id, $employer_id);

                    $wpdb->query($sql);
                    ?>
                    <img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/green-check-mark.png" /><a href="#" class="reference-checked">References Checked</a>
                    <?php
                } else {

                    $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
		SET reference_checked = %s
		WHERE resume_id = %d 
		AND employer_id = %d", "Check Reference", $resume_id, $employer_id);

                    $wpdb->query($sql);
                    ?>
                    <img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/orange-check-mark.png" /><a href="#" class="reference-checked">Check Reference</a>
                    <?php
                }

                break;

            case "highest-rated":

                if ($status_text == "Pick") {

                    $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
		SET starred = %s
		WHERE resume_id = %d 
		AND employer_id = %d", "2nd Highest Rated", $resume_id, $employer_id);

                    $wpdb->query($sql);
                    ?>
                    <img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/green-check-mark.png" /><a href="#" class="highest-rated">2nd Highest Rated</a>
                    <?php
                } else if ($status_text == "2nd Highest Rated") {

                    $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
		SET starred = %s
		WHERE resume_id = %d 
		AND employer_id = %d", "Highest Rated", $resume_id, $employer_id);

                    $wpdb->query($sql);
                    ?>
                    <img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/green-check-mark.png" /><a href="#" class="highest-rated">Highest Rated</a>
                    <?php
                } else {
                    $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
		SET starred = %s
		WHERE resume_id = %d 
		AND employer_id = %d", "Pick", $resume_id, $employer_id);

                    $wpdb->query($sql);
                    ?>
                    <img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/orange-check-mark.png" /><a href="#" class="highest-rated">Pick</a>
                    <?php
                }

                break;

            case "video-interview-evaluated":

                if ($status_text == "No Video") {

                    $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
		SET video_interview = %s
		WHERE resume_id = %d 
		AND employer_id = %d", "Video Submitted", $resume_id, $employer_id);

                    $wpdb->query($sql);
                    ?>
                    <img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/orange-check-mark.png" /><a href="#" class="video-interview-evaluated">Video Submitted</a>
                    <?php
                } else if ($status_text == "Video Submitted") {

                    $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
		SET video_interview = %s
		WHERE resume_id = %d 
		AND employer_id = %d", "Video Evaluated", $resume_id, $employer_id);

                    $wpdb->query($sql);
                    ?>
                    <img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/green-check-mark.png" /><a href="#" class="video-interview-evaluated">Video Evaluated</a>
                    <?php
                } else {
                    $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
		SET video_interview = %s
		WHERE resume_id = %d 
		AND employer_id = %d", "No Video", $resume_id, $employer_id);

                    $wpdb->query($sql);
                    ?>
                    <img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/red-flag-check.gif" /><a href="#" class="video-interview-evaluated">No Video</a>
                    <?php
                }

                break;

            case "no-red-flags":

                if ($status_text == "Check For Red Flags") {

                    $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
		SET red_flagged = %s
		WHERE resume_id = %d 
		AND employer_id = %d", "Red Flagged", $resume_id, $employer_id);

                    $wpdb->query($sql);
                    ?>
                    <img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/red-flag-check.gif" /><a href="#" class="no-red-flags">Red Flagged</a>
                    <?php
                } else if ($status_text == "Red Flagged") {

                    $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
		SET red_flagged = %s
		WHERE resume_id = %d 
		AND employer_id = %d", "No Red Flags", $resume_id, $employer_id);

                    $wpdb->query($sql);
                    ?>
                    <img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/green-check-mark.png" /><a href="#" class="no-red-flags">No Red Flags</a>
                    <?php
                } else {
                    $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
		SET red_flagged = %s
		WHERE resume_id = %d 
		AND employer_id = %d", "Check For Red Flags", $resume_id, $employer_id);

                    $wpdb->query($sql);
                    ?>
                    <img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/orange-check-mark.png" /><a href="#" class="no-red-flags">Check For Red Flags</a>
                    <?php
                }


                break;

            case "completed-evaluation":

                if ($status_text == "Evaluate") {

                    $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
		SET completed_evaluation = %s
		WHERE resume_id = %d 
		AND employer_id = %d", "Completed Evaluation", $resume_id, $employer_id);

                    $wpdb->query($sql);
                    ?>
                    <img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/green-check-mark.png" /><a href="#" class="completed-evaluation">Completed Evaluation</a>
                    <?php
                } else if ($status_text == "Completed Evaluation") {

                    $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
		SET completed_evaluation = %s
		WHERE resume_id = %d 
		AND employer_id = %d", "Hired", $resume_id, $employer_id);

                    $wpdb->query($sql);
                    ?>
                    <img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/green-check-mark.png" /><a href="#" class="completed-evaluation">Hired</a>
                    <?php
                } else {

                    $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
		SET completed_evaluation = %s
		WHERE resume_id = %d 
		AND employer_id = %d", "Evaluate", $resume_id, $employer_id);

                    $wpdb->query($sql);
                    ?>
                    <img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url') ?>/images/orange-check-mark.png" /><a href="#" class="completed-evaluation">Evaluate</a>
                    <?php
                }

                break;

            default:
        }
    }
}

/*
 * Function for comment posting(Resume and Job) using Ajax 
 */

function post_comment_ajax() {


    if ($_POST) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $content = $_POST['content'];
        $user_id = $_POST['user_id'];
        $post_id = $_POST['post_id'];
        $comment = get_comment($post_id);
        $time = current_time('mysql');
        $author_ip = get_comment_author_IP($post_id);
        $author_browser = $comment->comment_agent;

        $data = array(
            'comment_post_ID' => $post_id,
            'comment_author' => $name,
            'comment_author_email' => $email,
            'comment_author_url' => 'http://',
            'comment_content' => $content,
            'comment_type' => '',
            'comment_parent' => 0,
            'user_id' => $user_id,
            'comment_author_IP' => $author_ip,
            'comment_agent' => $author_browser,
            'comment_date' => $time,
            'comment_approved' => 1,
        );

        wp_insert_comment($data);
    }
}

function send_email_ajax() {

    if ($_POST) {
        $to = $_POST['to'];
        $subject = $_POST['subject'];
        $message = stripslashes($_POST['message']);
        $resume_id = $_POST['resume_id'];
        $reference_name = $_POST['reference_name'];
    }
    //wp_mail($to, $subject, $body);

    $headers = "From: ref@vidhire.net\r\n";
    //$headers .= "MIME-Version: 1.0\r\n";
    //$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    /*
      $message .= '<form action="' . get_template_directory_uri() . '/process.php" method="post" target="_blank">';
      //$message .= '<label>Rating for this Past Employee</label><br />';
      $message .= '<table style="position: relative;left: 200px;">';
      $message .= '<tr>';
      $message .= '<td>Productivity</td>';
      $message .= '<td><select name="performance"><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option></select></td>';
      $message .= '</tr>';
      $message .= '<tr>';
      $message .= '<td>Attitude</td>';
      $message .= '<td><select name="attitude"><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option></select></td>';
      $message .= '</tr>';
      $message .= '<tr>';
      $message .= '<td>Dependability</td>';
      $message .= '<td><select name="depend"><option>1<option>2</option><option>3</option><option>4</option><option>5</option></select></td>';
      $message .= '</tr>';
      $message .= '<tr>';
      $message .= '<td>Team Player</td>';
      $message .= '<td><select name="team_player"><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option></select></td>';
      $message .= '</tr>';
      $message .= '<tr>';
      $message .= '<td>Learning Speed</td>';
      $message .= '<td><select name="learning_speed"><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option></select></td>';
      $message .= '</tr>';
      $message .= '<tr>';
      $message .= '<td>Flexibility</td>';
      $message .= '<td><select name="flexibility"><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option></select></td>';
      $message .= '</tr>';
      $message .= '<tr>';
      $message .= '<td>Creativity</td>';
      $message .= '<td><select name="creativity"><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option></select></td>';
      $message .= '</tr>';
      $message .= '</table>';
      $message .= '<input name="resume_id"  value="' . $resume_id . '" type="hidden" />';
      $message .= '<input name="reference_name" value="' . $reference_name . '" type="hidden" />';
      //$message .= '<br />';
      //$message .= '<label for="commentText">Leave a quick review:</label><br />';
      //$message .= '<textarea cols="75" name="commentText" rows="5"></textarea><br />';
      $message .= '<br />';
      $message .= '<input type="submit" value="Submit your review" /></form><br />';
      $message .= 'Note: Your assessment is confidential.  If you cannot see the pull down menu, please use this link.<br /> Vidhire.net is a free hiring system. <br />';
      $message .= '<br />Thank you.'; */
    wp_mail($to, $subject, $message, $headers);
}

add_filter('wp_mail_from', 'my_mail_from');

function my_mail_from($email) {
    return "ref@vidhire.net";
}

add_filter('wp_mail_from_name', 'my_mail_from_name');

function my_mail_from_name($name) {
    return "Vidhire Human Resources ref@vidhire.net";
}

/*For wp_get_attachment_link to open in a new tab 
 * instead of on the current window
 */
function modify_attachment_link($markup) {
    return preg_replace('/^<a([^>]+)>(.*)$/', '<a\\1 target="_blank">\\2', $markup);
}
add_filter( 'wp_get_attachment_link', 'modify_attachment_link', 10, 6 );


/*Bug: the name "dependability" doesn't work and will be break when email is sent to user, used "depend" instead: Line 512*/