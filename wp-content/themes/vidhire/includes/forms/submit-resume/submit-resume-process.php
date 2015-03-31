<?php

/**
 * JobRoller Submit Resume Process
 * Processes a job submission.
 *
 *
 * @version 1.4
 * @author AppThemes
 * @package JobRoller
 * @copyright 2010 all rights reserved
 *
 */
function jr_process_submit_resume_form($resume_id = 0) {

    global $post, $posted;

    $errors = new WP_Error();
    if (isset($_POST['save_resume']) && $_POST['save_resume']) :

        // Get (and clean) data
        $fields = array(
            'resume_name',
            'summary',
            /* 'skills', */
            'specialities',
            /* 'groups', */
            'languages',
            'desired_salary',
            'desired_position',
            'resume_cat',
            'mobile',
            'tel',
            'email_address',
            'education',
            'overall_average',
            'degree',
            'institution',
            'degree_date_issued',
            'transcripts',
            'experience',
            'jr_geo_latitude',
            'jr_geo_longitude',
            'jr_address',
            'skype',
            'typing_test',
            'math_test',
            'english_test',
            'memory_test',
            'internet_speed',
            'company_1_position',
            'company_1_company',
            'company_1_city',
            'company_1_country',
            'company_1_reason_for_leaving',
            'company_1_starting_salary',
            'company_1_final_salary',
            'company_1_job_type',
            'company_1_start_date',
            'company_1_end_date',
            'company_1_salary_type',
            'company_2_position',
            'company_2_company',
            'company_2_city',
            'company_2_country',
            'company_2_reason_for_leaving',
            'company_2_starting_salary',
            'company_2_final_salary',
            'company_2_job_type',
            'company_2_start_date',
            'company_2_end_date',
            'company_2_salary_type',
            'company_3_position',
            'company_3_company',
            'company_3_city',
            'company_3_country',
            'company_3_reason_for_leaving',
            'company_3_starting_salary',
            'company_3_final_salary',
            'company_3_job_type',
            'company_3_start_date',
            'company_3_end_date',
            'company_3_salary_type',
            'other_employments',
            'reference_name_1',
            'reference_email_1',
            'reference_phone_number_1',
            'reference_additional_info_1',
            'reference_position_1',
            'reference_name_2',
            'reference_email_2',
            'reference_phone_number_2',
            'reference_additional_info_2',
            'reference_position_2',
            'reference_name_3',
            'reference_email_3',
            'reference_phone_number_3',
            'reference_additional_info_3',
            'reference_position_3',
            'interview_video',
            'currency'
        );

        $posted = stripslashes_deep(wp_array_slice_assoc($_POST, $fields));

        $sanitizer = ( get_option('jr_html_allowed') == 'no' ) ? 'strip_tags' : 'wp_kses_post';

        foreach ($posted as $key => &$value) {
            if (in_array($key, array('summary', 'education', 'experience'))) {
                $value = $sanitizer($value);
            } else {
                $value = strip_tags($value);
            }
        }

        // Check required fields
        $required = array(
            'resume_name' => __('Title', APP_TD),
            //'summary' => __('Summary', APP_TD),
            'jr_geo_latitude' => __('Location', APP_TD),
        );

        foreach ($required as $field => $name) {
            if (empty($posted[$field])) {
                $errors->add('submit_error_' . $field, __('<strong>ERROR</strong>: &ldquo;', APP_TD) . $name . __('&rdquo; is a required field.', APP_TD));
            }
        }

        if (!empty($posted['desired_salary']) && !intval($posted['desired_salary'])) {
            $errors->add('submit_error_salary', __('Salary must be numeric.', APP_TD));
        }

        if ($errors && sizeof($errors) > 0 && $errors->get_error_code()) {
            
        } else {

            // TODO: use uploads.php function library for resumes uploading

            if (isset($_FILES['your-photo']) && !empty($_FILES['your-photo']['name'])) {

                $posted['your-photo-name'] = $_FILES['your-photo']['name'];

                // Check valid extension
                $allowed = array(
                    'png',
                    'gif',
                    'jpg',
                    'jpeg'
                );

                $extension = strtolower(pathinfo($_FILES['your-photo']['name'], PATHINFO_EXTENSION));

                if (!in_array($extension, $allowed)) {
                    $errors->add('submit_error', __('<strong>ERROR</strong>: Only jpg, gif, and png images are allowed.', APP_TD));
                } else {

                    /** WordPress Administration File API */
                    include_once(ABSPATH . 'wp-admin/includes/file.php');
                    /** WordPress Media Administration API */
                    include_once(ABSPATH . 'wp-admin/includes/media.php');

                    function resume_photo_upload_dir($pathdata) {
                        $subdir = '/resume_photos' . $pathdata['subdir'];
                        $pathdata['path'] = str_replace($pathdata['subdir'], $subdir, $pathdata['path']);
                        $pathdata['url'] = str_replace($pathdata['subdir'], $subdir, $pathdata['url']);
                        $pathdata['subdir'] = str_replace($pathdata['subdir'], $subdir, $pathdata['subdir']);
                        return $pathdata;
                    }

                    add_filter('upload_dir', 'resume_photo_upload_dir');

                    $time = current_time('mysql');
                    $overrides = array('test_form' => false);

                    $file = wp_handle_upload($_FILES['your-photo'], $overrides, $time);

                    $file_size = jr_get_file_size('resumes');

                    if ($_FILES['your-photo']['size'] > ( $file_size['size'] * $file_size['unit_size'] )) {
                        $errors->add('upload_size_warning', sprintf(__('File exceeds %d%s size limit.', APP_TD), ($file_size['size'] * $file_size['unit_size']) / $file_size['unit_size'], $file_size['unit']));
                    } else {

                        remove_filter('upload_dir', 'resume_photo_upload_dir');

                        if (!isset($file['error'])) {
                            $posted['your-photo'] = $file['url'];
                            $posted['your-photo-type'] = $file['type'];
                            $posted['your-photo-file'] = $file['file'];
                        } else {
                            $errors->add('submit_error', __('<strong>ERROR</strong>: ', APP_TD) . $file['error'] . '');
                        }
                    }
                }
            }


            if (isset($_FILES['your-resume']) && !empty($_FILES['your-resume']['name'])) {

                $posted['your-resume-name'] = $_FILES['your-resume']['name'];

                // Check valid extension
                $allowed = array(
                    'doc',
                    'pdf',
                    'docx'
                );

                $extension = strtolower(pathinfo($_FILES['your-resume']['name'], PATHINFO_EXTENSION));

                if (!in_array($extension, $allowed)) {
                    $errors->add('submit_error', __('<strong>ERROR</strong>: Only .doc and .pdf files are allowed.', APP_TD));
                } else {

                    include_once(ABSPATH . 'wp-admin/includes/file.php');
                    include_once(ABSPATH . 'wp-admin/includes/media.php');

                    function resume_files_upload_dir($pathdata) {
                        $subdir = '/resume_files' . $pathdata['subdir'];
                        $pathdata['path'] = str_replace($pathdata['subdir'], $subdir, $pathdata['path']);
                        $pathdata['url'] = str_replace($pathdata['subdir'], $subdir, $pathdata['url']);
                        $pathdata['subdir'] = str_replace($pathdata['subdir'], $subdir, $pathdata['subdir']);
                        return $pathdata;
                    }

                    add_filter('upload_dir', 'resume_files_upload_dir');

                    $time = current_time('mysql');
                    $overrides = array('test_form' => false);

                    $file = wp_handle_upload($_FILES['your-resume'], $overrides, $time);

                    $file_size = jr_get_file_size('resumes');

                    if ($_FILES['your-resume']['size'] > ( $file_size['size'] * $file_size['unit_size'] )) {
                        $errors->add('upload_size_warning', sprintf(__('File exceeds %d%s size limit.', APP_TD), ($file_size['size'] * $file_size['unit_size']) / $file_size['unit_size'], $file_size['unit']));
                    } else {

                        remove_filter('upload_dir', 'resume_files_upload_dir');

                        if (!isset($file['error'])) {
                            $posted['your-resume'] = $file['url'];
                            $posted['your-resume-type'] = $file['type'];
                            $posted['your-resume-file'] = $file['file'];
                        } else {
                            $errors->add('submit_error', __('<strong>ERROR</strong>: ', APP_TD) . $file['error'] . '');
                        }
                    }
                }
            } /* Upload Resume */

            if (isset($_FILES['misc-documents']) && !empty($_FILES['misc-documents']['name'])) {

                $posted['misc-documents-name'] = $_FILES['misc-documents']['name'];

                // Check valid extension
                $allowed = array(
                    'png',
                    'gif',
                    'jpg',
                    'jpeg'
                );

                $extension = strtolower(pathinfo($_FILES['misc-documents']['name'], PATHINFO_EXTENSION));

                if (!in_array($extension, $allowed)) {
                    $errors->add('submit_error', __('<strong>ERROR</strong>: Only jpg, gif, and png images are allowed.', APP_TD));
                } else {

                    /** WordPress Administration File API */
                    include_once(ABSPATH . 'wp-admin/includes/file.php');
                    /** WordPress Media Administration API */
                    include_once(ABSPATH . 'wp-admin/includes/media.php');

                    function misc_files_upload_dir($pathdata) {
                        $subdir = '/misc_documents' . $pathdata['subdir'];
                        $pathdata['path'] = str_replace($pathdata['subdir'], $subdir, $pathdata['path']);
                        $pathdata['url'] = str_replace($pathdata['subdir'], $subdir, $pathdata['url']);
                        $pathdata['subdir'] = str_replace($pathdata['subdir'], $subdir, $pathdata['subdir']);
                        return $pathdata;
                    }

                    add_filter('upload_dir', 'misc_files_upload_dir');

                    $time = current_time('mysql');
                    $overrides = array('test_form' => false);

                    $file = wp_handle_upload($_FILES['misc-documents'], $overrides, $time);

                    $file_size = jr_get_file_size('resumes');

                    if ($_FILES['misc-documents']['size'] > ( $file_size['size'] * $file_size['unit_size'] )) {
                        $errors->add('upload_size_warning', sprintf(__('File exceeds %d%s size limit.', APP_TD), ($file_size['size'] * $file_size['unit_size']) / $file_size['unit_size'], $file_size['unit']));
                    } else {

                        remove_filter('upload_dir', 'misc_files_upload_dir');

                        if (!isset($file['error'])) {
                            $posted['misc-documents'] = $file['url'];
                            $posted['misc-documents-type'] = $file['type'];
                            $posted['misc-documents-file'] = $file['file'];
                        } else {
                            $errors->add('submit_error', __('<strong>ERROR</strong>: ', APP_TD) . $file['error'] . '');
                        }
                    }
                }
            }
        }

        if ($errors && sizeof($errors) > 0 && $errors->get_error_code()) {
            $error_messages = $errors->get_error_messages();

            foreach ($error_messages as $error) {
                echo "- " . $error . "<br />";
            }
        } else {

            // No errors? Create the resume post
            global $wpdb;

            ### Category

            $post_into_cats = array();

            if ($posted['resume_cat'] > 0) {
                $post_into_cats[] = get_term_by('id', $posted['resume_cat'], 'resume_category')->slug;
            }
            if (sizeof($post_into_cats) > 0) {
                wp_set_object_terms($resume_id, $post_into_cats, 'resume_category');
            }

            if ($resume_id > 0) {

                $data = array(
                    'ID' => $resume_id,
                    'post_content' => $wpdb->escape($posted['summary']),
                    'post_title' => $wpdb->escape($posted['resume_name'])
                );

                wp_update_post($data);

                $job_terms = wp_get_post_terms($resume_id, 'resume_category');

                $get_job_owner = $wpdb->get_row("SELECT distinct(post_author) as job_owner, ID as job_id FROM wp_posts WHERE post_name in ('" . $job_terms[0]->slug . "')");

                $job_owner = $get_job_owner->job_owner;

                $job_id = $get_job_owner->job_id;

                //Check Resume Statuses if Resume ID exists
                //If they do not, insert them
                $resume_statuses_exist = $wpdb->get_row("SELECT count(resume_id) as count FROM wp_resume_statuses WHERE resume_id in ('" . $resume_id . "')");

                if ($resume_statuses_exist->count == 0) {
                    //For Resume Statuses

                    $wpdb->insert('wp_resume_statuses', array(
                        'resume_id' => $resume_id,
                        'employer_id' => $job_owner,
                        'job_id' => $job_id,
                        'job_applied_to' => $job_terms[0]->name,
                        'job_slug' => $job_terms[0]->slug,
                        'job_owner' => $job_owner
                    ));
                } else {

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
            } else {

                $data = array(
                    'post_content' => $wpdb->escape($posted['summary'])
                    , 'post_title' => $wpdb->escape($posted['resume_name'])
                    , 'post_status' => 'private'
                    , 'post_author' => get_current_user_id()
                    , 'post_type' => 'resume'
                    , 'post_name' => get_current_user_id() . uniqid(rand(10, 1000), false)
                );

                $resume_id = wp_insert_post($data);

                //For Resume Statuses
                /* $wpdb->insert('wp_resume_statuses', array(
                  'resume_id' => $resume_id
                  )); */

                update_post_meta($resume_id, 'resume_cat', $posted['resume_cat']);

                $job_terms = wp_get_post_terms($resume_id, 'resume_category');

                $get_job_owner = $wpdb->get_row("SELECT distinct(post_author) as job_owner, ID as job_id FROM wp_posts WHERE post_name in ('" . $job_terms[0]->slug . "')");

                $job_owner = $get_job_owner->job_owner;

                $job_id = $get_job_owner->job_id;


                $resume_statuses_exist = $wpdb->get_row("SELECT count(resume_id) as count FROM wp_resume_statuses WHERE resume_id in ('" . $resume_id . "')");

                if ($resume_statuses_exist->count == 0) {
                    //For Resume Statuses

                    $wpdb->insert('wp_resume_statuses', array(
                        'resume_id' => $resume_id,
                        'employer_id' => $job_owner,
                        'job_id' => $job_id,
                        'job_applied_to' => $job_terms[0]->name,
                        'job_slug' => $job_terms[0]->slug,
                        'job_owner' => $job_owner
                    ));
                } else {

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

                if ($resume_id == 0 || is_wp_error($resume_id)) {
                    $errors = $resume_id->get_error_messages();
                    foreach ($errors as $error) {
                        echo "- " . $error . "<br />";
                    }
                    //wp_die(__('Error: Unable to create entry.', APP_TD));
                }
            }

            ### Add meta data

            update_post_meta($resume_id, '_skills', $posted['skills']);
            update_post_meta($resume_id, '_desired_salary', preg_replace('/[^0-9]/', '', $posted['desired_salary']));

            update_post_meta($resume_id, '_mobile', $posted['mobile']);
            update_post_meta($resume_id, '_tel', $posted['tel']);
            update_post_meta($resume_id, '_email_address', $posted['email_address']);

            update_post_meta($resume_id, '_education', $posted['education']);
            update_post_meta($resume_id, 'overall_average', $posted['overall_average']);
            update_post_meta($resume_id, 'degree', $posted['degree']);
            update_post_meta($resume_id, 'institution', $posted['institution']);
            update_post_meta($resume_id, 'degree_date_issued', $posted['degree_date_issued']);
            update_post_meta($resume_id, 'transcripts', $posted['transcripts']);

            update_post_meta($resume_id, '_experience', $posted['experience']);
            update_post_meta($resume_id, 'skype', $posted['skype']);

            update_post_meta($resume_id, 'typing_test', $posted['typing_test']);
            update_post_meta($resume_id, 'math_test', $posted['math_test']);
            update_post_meta($resume_id, 'english_test', $posted['english_test']);
            update_post_meta($resume_id, 'memory_test', $posted['memory_test']);
            update_post_meta($resume_id, 'internet_speed', $posted['internet_speed']);

            update_post_meta($resume_id, 'reference_name_1', $posted['reference_name_1']);
            update_post_meta($resume_id, 'reference_email_1', $posted['reference_email_1']);
            update_post_meta($resume_id, 'reference_phone_number_1', $posted['reference_phone_number_1']);
            update_post_meta($resume_id, 'reference_position_1', $posted['reference_position_1']);
            update_post_meta($resume_id, 'reference_additional_info_1', $posted['reference_additional_info_1']);

            update_post_meta($resume_id, 'reference_name_2', $posted['reference_name_2']);
            update_post_meta($resume_id, 'reference_email_2', $posted['reference_email_2']);
            update_post_meta($resume_id, 'reference_phone_number_2', $posted['reference_phone_number_2']);
            update_post_meta($resume_id, 'reference_position_2', $posted['reference_position_2']);
            update_post_meta($resume_id, 'reference_additional_info_2', $posted['reference_additional_info_2']);

            update_post_meta($resume_id, 'reference_name_3', $posted['reference_name_3']);
            update_post_meta($resume_id, 'reference_email_3', $posted['reference_email_3']);
            update_post_meta($resume_id, 'reference_phone_number_3', $posted['reference_phone_number_3']);
            update_post_meta($resume_id, 'reference_position_3', $posted['reference_position_3']);
            update_post_meta($resume_id, 'reference_additional_info_3', $posted['reference_additional_info_3']);

            update_post_meta($resume_id, 'interview_video', $posted['interview_video']);

            //Company 1     

            update_post_meta($resume_id, 'company_1_position', $posted['company_1_position']);
            update_post_meta($resume_id, 'company_1_company', $posted['company_1_company']);
            update_post_meta($resume_id, 'company_1_city', $posted['company_1_city']);
            update_post_meta($resume_id, 'company_1_country', $posted['company_1_country']);
            update_post_meta($resume_id, 'company_1_reason_for_leaving', $posted['company_1_reason_for_leaving']);
            update_post_meta($resume_id, 'company_1_starting_salary', $posted['company_1_starting_salary']);

            update_post_meta($resume_id, 'company_1_final_salary', $posted['company_1_final_salary']);

            update_post_meta($resume_id, 'company_1_job_type', $posted['company_1_job_type']);

            update_post_meta($resume_id, 'company_1_start_date', $posted['company_1_start_date']);
            update_post_meta($resume_id, 'company_1_end_date', $posted['company_1_end_date']);
            update_post_meta($resume_id, 'company_1_salary_type', $posted['company_1_salary_type']);

            //Company 2     

            update_post_meta($resume_id, 'company_2_position', $posted['company_2_position']);
            update_post_meta($resume_id, 'company_2_company', $posted['company_2_company']);
            update_post_meta($resume_id, 'company_2_city', $posted['company_2_city']);
            update_post_meta($resume_id, 'company_2_country', $posted['company_2_country']);

            update_post_meta($resume_id, 'company_2_reason_for_leaving', $posted['company_2_reason_for_leaving']);
            update_post_meta($resume_id, 'company_2_starting_salary', $posted['company_2_starting_salary']);

            update_post_meta($resume_id, 'company_2_final_salary', $posted['company_2_final_salary']);

            update_post_meta($resume_id, 'company_2_job_type', $posted['company_2_job_type']);
            update_post_meta($resume_id, 'company_2_start_date', $posted['company_2_start_date']);
            update_post_meta($resume_id, 'company_2_end_date', $posted['company_2_end_date']);
            update_post_meta($resume_id, 'company_2_salary_type', $posted['company_2_salary_type']);

            //Company 3     

            update_post_meta($resume_id, 'company_3_position', $posted['company_3_position']);
            update_post_meta($resume_id, 'company_3_company', $posted['company_3_company']);
            update_post_meta($resume_id, 'company_3_city', $posted['company_3_city']);
            update_post_meta($resume_id, 'company_3_country', $posted['company_3_country']);

            update_post_meta($resume_id, 'company_3_reason_for_leaving', $posted['company_3_reason_for_leaving']);
            update_post_meta($resume_id, 'company_3_starting_salary', $posted['company_3_starting_salary']);

            update_post_meta($resume_id, 'company_3_final_salary', $posted['company_3_final_salary']);

            update_post_meta($resume_id, 'company_3_job_type', $posted['company_3_job_type']);

            update_post_meta($resume_id, 'company_3_start_date', $posted['company_3_start_date']);
            update_post_meta($resume_id, 'company_3_end_date', $posted['company_3_end_date']);
            update_post_meta($resume_id, 'company_3_salary_type', $posted['company_3_salary_type']);


            //Other Employments

            update_post_meta($resume_id, 'other_employments', $posted['other_employments']);

            update_post_meta($resume_id, 'currency', $posted['currency']);

            ## Desired position

            $post_into_types[] = get_term_by('slug', sanitize_title($posted['desired_position']), 'resume_job_type')->slug;

            if (sizeof($post_into_types) > 0)
                wp_set_object_terms($resume_id, $post_into_types, 'resume_job_type');

            ### Category

            $post_into_cats = array();

            if ($posted['resume_cat'] > 0) {
                $post_into_cats[] = get_term_by('id', $posted['resume_cat'], 'resume_category')->slug;
            }
            if (sizeof($post_into_cats) > 0) {
                wp_set_object_terms($resume_id, $post_into_cats, 'resume_category');
            }

            ### Tags

            if ($posted['specialities']) :

                $thetags = explode(',', $posted['specialities']);
                $thetags = array_map('trim', $thetags);

                if (sizeof($thetags) > 0)
                    wp_set_object_terms($resume_id, $thetags, 'resume_specialities');

            endif;

            if ($posted['groups']) :

                $thetags = explode(',', $posted['groups']);
                $thetags = array_map('trim', $thetags);

                if (sizeof($thetags) > 0)
                    wp_set_object_terms($resume_id, $thetags, 'resume_groups');

            endif;

            if ($posted['languages']) :

                $thetags = explode(',', $posted['languages']);
                $thetags = array_map('trim', $thetags);

                if (sizeof($thetags) > 0)
                    wp_set_object_terms($resume_id, $thetags, 'resume_languages');

            endif;

            ### GEO

            if (!empty($posted['jr_address'])) :

                $latitude = jr_clean_coordinate($posted['jr_geo_latitude']);
                $longitude = jr_clean_coordinate($posted['jr_geo_longitude']);

                update_post_meta($resume_id, '_jr_geo_latitude', $posted['jr_geo_latitude']);
                update_post_meta($resume_id, '_jr_geo_longitude', $posted['jr_geo_longitude']);

                if ($latitude && $longitude) :
                    $address = jr_reverse_geocode($latitude, $longitude);

                    update_post_meta($resume_id, 'geo_address', $address['address']);
                    update_post_meta($resume_id, 'geo_country', $address['country']);
                    update_post_meta($resume_id, 'geo_short_address', $address['short_address']);
                    update_post_meta($resume_id, 'geo_short_address_country', $address['short_address_country']);

                endif;

            endif;

            ## Load APIs and Link to photo

            include_once(ABSPATH . 'wp-admin/includes/file.php');
            include_once(ABSPATH . 'wp-admin/includes/image.php');
            include_once(ABSPATH . 'wp-admin/includes/media.php');

            $name_parts = pathinfo($posted['your-photo-name']);
            $name = trim(substr($name, 0, -(1 + strlen($name_parts['extension']))));

            $url = $posted['your-photo'];
            $type = $posted['your-photo-type'];
            $file = $posted['your-photo-file'];
            $title = $posted['your-photo-name'];
            $content = '';

            $resume_file_name_parts = pathinfo($posted['your-resume-name']);
            $resume_file_name = trim(substr($name, 0, -(1 + strlen($resume_file_name_parts['extension']))));

            $resume_url = $posted['your-resume'];
            $resume_type = $posted['your-resume-type'];
            $resume_file = $posted['your-resume-file'];
            $resume_title = $posted['your-resume-name'];
            $resume_content = '';

            $misc_doc_url = $posted['misc-documents'];
            $misc_doc_type = $posted['misc-documents-type'];
            $misc_doc_file = $posted['misc-documents-file'];
            $misc_doc_title = $posted['misc-documents-name'];


            if ($misc_doc_file) :

                // Construct the attachment array
                $misc_doc_attachment = array_merge(array(
                    'post_mime_type' => $misc_doc_type,
                    'guid' => $misc_doc_url,
                    'post_parent' => $resume_id,
                    'post_title' => $misc_doc_title,
                    'post_content' => '',
                        ), array());

                // Save the data
                $id = wp_insert_attachment($misc_doc_attachment, $misc_doc_file, $resume_id);
                if (!is_wp_error($id)) {
                    wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $misc_doc_file));
                }


            endif;

            if ($resume_file) :

                // Construct the attachment array
                $resume_attachment = array_merge(array(
                    'post_mime_type' => $resume_type,
                    'guid' => $resume_url,
                    'post_parent' => $resume_id,
                    'post_title' => $resume_title,
                    'post_content' => $resume_content,
                        ), array());

                // Save the data
                $id = wp_insert_attachment($resume_attachment, $resume_file, $resume_id);
                if (!is_wp_error($id)) {
                    wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $resume_file));
                }

            endif;


            if ($file) :

                // use image exif/iptc data for title and caption defaults if possible
                if ($image_meta = @wp_read_image_metadata($file)) {
                    if (trim($image_meta['title']))
                        $title = $image_meta['title'];
                    if (trim($image_meta['caption']))
                        $content = $image_meta['caption'];
                }

                // Construct the attachment array
                $attachment = array_merge(array(
                    'post_mime_type' => $type,
                    'guid' => $url,
                    'post_parent' => $resume_id,
                    'post_title' => $title,
                    'post_content' => $content,
                        ), array());

                // Save the data
                $id = wp_insert_attachment($attachment, $file, $resume_id);
                if (!is_wp_error($id)) {
                    wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $file));
                }

                /*
                  Queries for toggling resume statuses
                 */
                /* global $wpdb, $post;


                  Check if current user that is an employer
                  is mapped to the resume
                 */

                /* $job_terms = wp_get_post_terms($resume_id, 'resume_category');

                  $get_job_owner = $wpdb->get_row("SELECT distinct(post_author) as job_owner, ID as job_id FROM wp_posts WHERE post_name in ('" . $job_terms[0]->slug . "')");

                  $job_owner = $get_job_owner->job_owner;

                  $job_id = $get_job_owner->job_id;

                  $get_past_job_id = $wpdb->get_row("SELECT DISTINCT(job_id) FROM wp_resume_statuses WHERE resume_id in ('" . $resume_id . "')");

                  if ($get_past_job_id->job_id == $job_id) {

                  $wpdb->update(
                  'wp_resume_statuses', array(
                  'employer_id' => $job_owner,
                  'job_applied_to' => $job_terms[0]->name,
                  'job_slug' => $job_terms[0]->slug,
                  'job_owner' => $job_owner
                  ), array(
                  //'employer_id' => $job_owner,
                  'resume_id' => $resume_id
                  //'job_id' => $job_id
                  ), array(
                  '%s'
                  ), array('%s')
                  );
                  } else {

                  //For Resume Statuses
                  $wpdb->insert('wp_resume_statuses', array(
                  'resume_id' => $resume_id,
                  'employer_id' => $job_owner,
                  'job_id' => $job_id,
                  'job_applied_to' => $job_terms[0]->name,
                  'job_slug' => $job_terms[0]->slug,
                  'job_owner' => $job_owner
                  ));
                  } */


                update_post_meta($resume_id, '_thumbnail_id', $id);

            endif;

            // Redirect to Resume
            $url = get_permalink($resume_id);
            if (!$url) {
                $url = get_permalink(JR_User_Profile_Page::get_id());
            }
            wp_redirect($url);
            exit();
        }

    endif;

    $submit_form_results = array(
        'errors' => $errors,
        'posted' => $posted
    );

    return $submit_form_results;
}
