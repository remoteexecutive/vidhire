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

define( 'APP_TD', 'jobroller' );
define( 'JR_VERSION' , $app_version );
define( 'JR_FIELD_PREFIX', '_' . $app_abbr . '_' );

// Framework
require( dirname(__FILE__) . '/framework/load.php' );

// Payments Framework
require dirname( __FILE__ ) . '/includes/payments/load.php';

scb_register_table( 'app_pop_daily', $app_abbr . '_counter_daily' );
scb_register_table( 'app_pop_total', $app_abbr . '_counter_total' );

require( dirname(__FILE__) . '/framework/includes/stats.php' );

// Custom forms
require dirname( __FILE__ ) . '/includes/custom-forms/form-builder.php';

// Theme-specific files
require( dirname(__FILE__) . '/includes/theme-functions.php' );

/*
Add comments to the custom post type resume
*/
add_post_type_support( 'resume', array( 'comments' ) );

/*
Remove the admin bar for non-admin accounts
*/

show_admin_bar(false);


/*
Tell WP to support svg
*/
function cc_mime_types( $mimes ){
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );

add_action(  'wp_ajax_save_evaluation', 'save_evaluation' );

function save_evaluation() {
 
  /*
		Ajax Save Evaluation
	*/
		global $wpdb,$post;


	$employer_id = wp_get_current_user();
	$resume_id = intval($_POST['resume_id']);
	

	$skills_score = intval($_POST['skills_score']);
	$education_score = intval($_POST['education_score']);
  $career_map_score = intval($_POST['career_map_score']);
  $references_score = intval($_POST['references_score']);
  $video_interview_score = intval($_POST['video_interview_score']);
  $tests_score = intval($_POST['tests_score']);
  $positive_adjustments_score = intval($_POST['positive_adjustments_score']);
  $final_evaluation_score = $skills_score+$education_score+$career_map_score+$references_score+$video_interview_score+$tests_score+$positive_adjustments_score;
  
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
  

	$employer_id_count = $wpdb->get_row( "SELECT count(employer_id) as count FROM wp_final_evaluation WHERE employer_id in ('".$employer_id->ID."') AND resume_id in ('".$resume_id."')" );
	
	if ($employer_id_count->count == 0) {
      	//For Resume Statuses
      	$wpdb->insert( 'wp_final_evaluation', 
                      array(
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
								'wp_final_evaluation', 
									array( 
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
									), 
									array( 
                    'employer_id' => $employer_id->ID,
                    'resume_id' => $resume_id 
                  ));
			
  				}  
	
  return true;
}


