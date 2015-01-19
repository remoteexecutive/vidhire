<?php
/*
Template Name: Edit Resume Template
*/
?>
<?php
	### Prevent Caching
	nocache_headers();
	
	appthemes_auth_redirect_login();

	$resume_id = 0;
	
	if (isset($_GET['edit'])) $resume_id = (int) $_GET['edit'];

	if ( ! current_user_can('can_submit_resume') || ( isset($_GET['edit']) && ! $resume_id ) ) :
		wp_redirect(home_url());
		exit();
	endif;
	
	if (isset($_REQUEST['edit'])) $editing = true; else $editing = false;
	
	$message = '';
	
	global $post, $job_details, $posted;

	$posted = array();
	$errors = new WP_Error();
	
	### Edit?

	if ($resume_id>0) :
		
		// Get job details
		$resume_details = get_post($resume_id);
			
		if (!isset($_POST['save_resume'])) :
			// Set post data
			$posted['resume_name'] = $resume_details->post_title;
			$posted['summary'] = $resume_details->post_content;
			$posted['skills'] = get_post_meta($resume_id, '_skills', true);
			$posted['desired_salary'] = get_post_meta($resume_id, '_desired_salary', true);
			$posted['desired_position'] = get_post_meta($resume_id, '_desired_position', true);
			
			$posted['mobile'] = get_post_meta($resume_id, '_mobile', true);
			$posted['tel'] = get_post_meta($resume_id, '_tel', true);
			$posted['email_address'] = get_post_meta($resume_id, '_email_address', true);
			
			$posted['education'] = get_post_meta($resume_id, '_education', true);
			$posted['overall_average'] = get_post_meta($resume_id,'overall_average', true);
			$posted['degree'] = get_post_meta($resume_id,'degree',true);
			$posted['institution'] = get_post_meta($resume_id,'institution',true);
			$posted['degree_date_issued'] = get_post_meta($resume_id,'degree_date_issued',true);
			$posted['transcripts'] = get_post_meta($resume_id,'transcripts',true);		

			$posted['experience'] = get_post_meta($resume_id, '_experience', true);
			$posted['skype'] = get_post_meta($resume_id,'skype',true);			
	
			$posted['typing_test'] = get_post_meta($resume_id,'typing_test',true);
			$posted['math_test'] = get_post_meta($resume_id,'math_test',true);
			$posted['english_test'] = get_post_meta($resume_id,'english_test',true);
			$posted['memory_test'] = get_post_meta($resume_id,'memory_test',true);
			$posted['internet_speed'] = get_post_meta($resume_id,'internet_speed',true);


			$posted['company_1_position'] = get_post_meta($resume_id,'company_1_position',true);
			$posted['company_1_company'] = get_post_meta($resume_id,'company_1_company',true);
			$posted['company_1_city'] = get_post_meta($resume_id,'company_1_city',true);
			$posted['company_1_country'] = get_post_meta($resume_id,'company_1_country',true);
			$posted['company_1_reason_for_leaving'] = get_post_meta($resume_id,'company_1_reason_for_leaving',true);
			$posted['company_1_starting_salary'] = get_post_meta($resume_id,'company_1_starting_salary',true);
			$posted['company_1_final_salary'] = get_post_meta($resume_id,'company_1_final_salary',true);
			$posted['company_1_job_type'] = get_post_meta($resume_id,'company_1_job_type',true);
			
			$posted['company_1_start_date'] = get_post_meta($resume_id,'company_1_start_date',true);
			$posted['company_1_end_date'] = get_post_meta($resume_id,'company_1_end_date',true);
			$posted['company_1_salary_type'] = get_post_meta($resume_id,'company_1_salary_type',true);


			$posted['company_2_position'] = get_post_meta($resume_id,'company_2_position',true);
			$posted['company_2_company'] = get_post_meta($resume_id,'company_2_company',true);
			$posted['company_2_city'] = get_post_meta($resume_id,'company_2_city',true);
			$posted['company_2_country'] = get_post_meta($resume_id,'company_2_country',true);
			$posted['company_2_reason_for_leaving'] = get_post_meta($resume_id,'company_2_reason_for_leaving',true);
			$posted['company_2_starting_salary'] = get_post_meta($resume_id,'company_2_starting_salary',true);
			$posted['company_2_final_salary'] = get_post_meta($resume_id,'company_2_final_salary',true);
			$posted['company_2_job_type'] = get_post_meta($resume_id,'company_2_job_type',true);	
			$posted['company_2_start_date'] = get_post_meta($resume_id,'company_2_start_date',true);
			$posted['company_2_end_date'] = get_post_meta($resume_id,'company_2_end_date',true);
			$posted['company_2_salary_type'] = get_post_meta($resume_id,'company_2_salary_type',true);


			$posted['company_3_position'] = get_post_meta($resume_id,'company_3_position',true);
			$posted['company_3_company'] = get_post_meta($resume_id,'company_3_company',true);
			$posted['company_3_city'] = get_post_meta($resume_id,'company_3_city',true);
			$posted['company_3_country'] = get_post_meta($resume_id,'company_3_country',true);	
			$posted['company_3_reason_for_leaving'] = get_post_meta($resume_id,'company_3_reason_for_leaving',true);
			$posted['company_3_starting_salary'] = get_post_meta($resume_id,'company_3_starting_salary',true);
			$posted['company_3_final_salary'] = get_post_meta($resume_id,'company_3_final_salary',true);
			$posted['company_3_job_type'] = get_post_meta($resume_id,'company_3_job_type',true);
			$posted['company_3_start_date'] = get_post_meta($resume_id,'company_3_start_date',true);
			$posted['company_3_end_date'] = get_post_meta($resume_id,'company_3_end_date',true);
			$posted['company_3_salary_type'] = get_post_meta($resume_id,'company_3_salary_type',true);		


			$posted['other_employments'] = get_post_meta($resume_id,'other_employments',true);

			$posted['reference_name_1'] = get_post_meta($resume_id,'reference_name_1',true);
			$posted['reference_email_1'] = get_post_meta($resume_id,'reference_email_1',true);
			$posted['reference_phone_number_1'] = get_post_meta($resume_id,'reference_phone_number_1',true);
		$posted['reference_position_1'] = get_post_meta($resume_id,'reference_position_1',true);		
		$posted['reference_additional_info_1'] = get_post_meta($resume_id,'reference_additional_info_1',true);

						$posted['reference_name_2'] = get_post_meta($resume_id,'reference_name_2',true);
			$posted['reference_email_2'] = get_post_meta($resume_id,'reference_email_2',true);
			$posted['reference_phone_number_2'] = get_post_meta($resume_id,'reference_phone_number_2',true);
			$posted['reference_position_2'] = get_post_meta($resume_id,'reference_position_2',true);
			$posted['reference_additional_info_2'] = get_post_meta($resume_id,'reference_additional_info_2',true);

						$posted['reference_name_3'] = get_post_meta($resume_id,'reference_name_3',true);
			$posted['reference_email_3'] = get_post_meta($resume_id,'reference_email_3',true);
			$posted['reference_phone_number_3'] = get_post_meta($resume_id,'reference_phone_number_3',true);
			$posted['reference_position_3'] = get_post_meta($resume_id,'reference_position_3',true);
			$posted['reference_additional_info_3'] = get_post_meta($resume_id,'reference_additional_info_3',true);
			
			$posted['interview_video'] = get_post_meta($resume_id,'interview_video',true);
			
			$posted['currency'] = get_post_meta($resume_id,'currency',true);	


			$terms = wp_get_post_terms($resume_id, 'resume_category');
			$terms_array = array();
			foreach ($terms as $t) $terms_array[] = $t->term_id;
			if (isset($terms_array[0])) $posted['resume_cat'] = $terms_array[0];
			
			$terms = wp_get_post_terms($resume_id, 'resume_specialities');
			$terms_array = array();
			foreach ($terms as $t) $terms_array[] = $t->name;
			$posted['specialities'] = implode(', ', $terms_array);
			
			$terms = wp_get_post_terms($resume_id, 'resume_groups');
			$terms_array = array();
			foreach ($terms as $t) $terms_array[] = $t->name;
			$posted['groups'] = implode(', ', $terms_array);
			
			$terms = wp_get_post_terms($resume_id, 'resume_languages');
			$terms_array = array();
			foreach ($terms as $t) $terms_array[] = $t->name;
			$posted['languages'] = implode(', ', $terms_array);
			
			$terms = wp_get_post_terms($resume_id, 'resume_job_type');
			if ($terms) : 
				$terms = current($terms);
				$posted['desired_position'] = $terms->slug;
			else :
				$posted['desired_position'] = '';
			endif;
			
			$posted['jr_geo_latitude'] = get_post_meta($resume_id, '_jr_geo_latitude', true);
			$posted['jr_geo_longitude'] = get_post_meta($resume_id, '_jr_geo_longitude', true);
			$posted['jr_address'] = get_post_meta($resume_id, 'geo_address', true);
			
		endif;
	
		// Permission?
		$current_user = wp_get_current_user();

		if ($current_user->ID == $resume_details->post_author) :
		
			// We have permission to edit this!
		
		else : redirect_myjobs(); endif;
	
	endif;
	
	### Process Forms
	
	$result = jr_process_submit_resume_form( $resume_id );
		
	$errors = $result['errors'];
	$posted = $result['posted'];
?>

	<div class="section">
	
		<div class="section_content">
		
			<h1><?php if ($editing) _e('Edit Resume', APP_TD); else _e('Add Resume', APP_TD); ?></h1>

			<?php do_action( 'appthemes_notices' ); ?>

			<?php jr_submit_resume_form( $resume_id ); ?>

		</div><!-- end section_content -->

	</div><!-- end section -->

	<div class="clear"></div>

</div><!-- end main content -->

<?php if (get_option('jr_show_sidebar')!=='no') get_sidebar('resume'); ?>
