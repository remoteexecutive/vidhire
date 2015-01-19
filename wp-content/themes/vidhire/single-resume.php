<?php 
	jr_resume_page_auth(); 

	$errors = new WP_Error();
	$resume_access_level = 'all';

	### Visibility check
	if ( !jr_resume_is_visible('single') && $post->post_author!=get_current_user_id() ) :

		$errors->add('resume_error', __('Sorry, you do not have permission to view individual resumes.', APP_TD) );

		if ( jr_current_user_can_subscribe_for_resumes() )
			$resume_access_level = 'subscribe';
		else
			$resume_access_level = 'none';

	endif;

	### Publish
	
	if (isset($_GET['publish']) && $_GET['publish'] && $post->post_author==get_current_user_id()) :
		
		$post_id = $post->ID;
		$post_to_edit = get_post($post_id);

		global $user_ID;

		if ($post_to_edit->ID==$post_id && $post_to_edit->post_author==$user_ID) :
			$update_resume = array();
			$update_resume['ID'] = $post_to_edit->ID;
			if ($post_to_edit->post_status=='private') :
				$update_resume['post_status'] = 'publish';
			else :
				$update_resume['post_status'] = 'private';
			endif;
			wp_update_post( $update_resume );
			wp_safe_redirect(get_permalink($post_to_edit->ID));
		endif;
		
	endif;
	
	$show_contact_form = (get_option('jr_resume_show_contact_form') == 'yes');		
?>

	<div class="section single">

	<?php do_action( 'appthemes_notices' ); ?>

	<?php appthemes_before_loop(); ?>
		
		<?php if ($resume_access_level != 'none' && have_posts()): ?>

			<?php while (have_posts()) : the_post(); ?>
			
				<?php appthemes_before_post(); ?>
				
				<?php jr_resume_header($post); ?>

				<?php appthemes_stats_update($post->ID); //records the page hit ?>

				<div class="section_header resume_header">

				<?php appthemes_before_post_title(); ?>

				<?php

					if ( $resume_access_level == 'subscribe' ):

						if ($notice = get_option('jr_resume_subscription_notice')) echo '<p>'.wptexturize($notice).'</p>';

						the_resume_purchase_plan_link();

						echo '<div class="clear"></div>';

					else: ?>

						
    <?php if (has_post_thumbnail()) the_post_thumbnail('thumbnail'); ?>
		
						<h1 class="title resume-title"><span><?php the_title(); ?></span></h1>
					
    			<ul class="toggle-processing-status">
    				<?php 
									/*
								Queries for toggling resume statuses
							*/
																global $wpdb,$post;
							
							/*
								Check if current user that is an employer
								is mapped to the resume
						*/
				
					$job_terms = wp_get_post_terms($post->ID, 'resume_category');

					$employer_id = get_current_user_id();

					$get_job_owner = $wpdb->get_row("SELECT distinct(post_author) as job_owner FROM wp_posts WHERE post_name in ('".$job_terms[0]->slug."')");
						
					$job_owner = $get_job_owner->job_owner;
	
					$employer_id_count = $wpdb->get_row("SELECT distinct(employer_id) as count FROM wp_resume_statuses WHERE resume_id in ('".$post->ID."') AND employer_id in ('".$employer_id."')");

					$resume_id_count = $wpdb->get_row("SELECT distinct(resume_id) as count FROM wp_resume_statuses WHERE resume_id in ('".$post->ID."') AND employer_id in ('".$employer_id."')");

					$job_applied_to = $wpdb->get_row("SELECT distinct(job_applied_to) as count FROM wp_resume_statuses WHERE resume_id in ('".$post->ID."') AND employer_id in ('".$employer_id."') AND job_applied_to in ('".$job_terms[0]->name."')");
       
					$job_slug = $wpdb->get_row("SELECT distinct(job_applied_to_slug) as count FROM wp_resume_statuses WHERE resume_id in ('".$post->ID."') AND employer_id in ('".$employer_id."') AND job_slug in ('".$job_terms[0]->slug."')");


			if ($employer_id_count == NULL) {
      	//For Resume Statuses
      	$wpdb->insert( 'wp_resume_statuses', 
                      array(
                       'resume_id' => $post->ID,
                       'employer_id' => $employer_id,
                       'job_applied_to' => $job_terms[0]->name,
                       'job_slug' => $job_terms[0]->slug,
                       'job_owner' => $job_owner 
                      ));
      } else {
      	
        $wpdb->update( 
								'wp_resume_statuses', 
									array( 
										'employer_id' => $employer_id,
                    'job_applied_to' => $job_terms[0]->name,
                    'job_slug' => $job_terms[0]->slug,
                    'job_owner' => $job_owner
									), 
									array( 
                    'employer_id' => $employer_id,
                    'resume_id' => $post->ID 
                  ), 
									array( 
										'%s'
									), 
									array( '%s' ) 
								);
        
      }

							/*
								Queries for toggling resume statuses
							*/
				
							/*$resume_options = $wpdb->get_row( "SELECT fast_tracked,skills_required,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM $wpdb->posts where ID = $post->ID",ARRAY_A);*/

						$resume_options = $wpdb->get_row("SELECT fast_tracked,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM wp_resume_statuses where resume_id = $post->ID AND employer_id = $employer_id",ARRAY_A);

						/*For Fast Tracked Update*/	
						
						if (isset($_GET['fast-track']) && $_GET['fast-track'] == 'true' && $_GET['resume_id'] ==  $post->ID) {
								
              	/*$sql = $wpdb->prepare("UPDATE $wpdb->posts 
														 SET fast_tracked = %s
														 WHERE ID = %d",'Fast Tracked',$post->ID);*/
               
              $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
														 SET fast_tracked = %s
														 WHERE resume_id = %d 
														 AND employer_id = %d",'Fast Tracked',$post->ID,$employer_id);
              
      
      					$wpdb->query($sql);
              
              	$resume_options = $wpdb->get_row("SELECT fast_tracked,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM wp_resume_statuses where resume_id = $post->ID AND employer_id = $employer_id",ARRAY_A);
              
              	/*$resume_options = $wpdb->get_row( "SELECT fast_tracked,skills_required,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM $wpdb->posts where ID = $post->ID",ARRAY_A);*/	
  
            
            	} elseif (isset($_GET['fast-track']) && $_GET['fast-track'] == 'false' && $_GET['resume_id'] ==  $post->ID) {
            		
              	/*$sql = $wpdb->prepare("UPDATE $wpdb->posts 
														 SET fast_tracked = %s
														 WHERE ID = %d",'Standard Tracked',$post->ID);*/
              
              $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
														 SET fast_tracked = %s
														 WHERE resume_id = %d 
														 AND employer_id = %d",'Standard Tracked',$post->ID,$employer_id);
              
      
      					$wpdb->query($sql);
              
              /*$resume_options = $wpdb->get_row( "SELECT fast_tracked,skills_required,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM $wpdb->posts where ID = $post->ID",ARRAY_A);*/	
							
              $resume_options = $wpdb->get_row("SELECT fast_tracked,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM wp_resume_statuses where resume_id = $post->ID AND employer_id = $employer_id",ARRAY_A);
              
                    	} elseif (isset($_GET['fast-track']) && $_GET['fast-track'] == 'insufficient' && $_GET['resume_id'] ==  $post->ID) {
              
              /*$sql = $wpdb->prepare("UPDATE $wpdb->posts 
														 SET fast_tracked = %s
														 WHERE ID = %d",'Insufficient Skills',$post->ID);*/
      
              $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
														 SET fast_tracked = %s
														 WHERE resume_id = %d 
														 AND employer_id = %d",'Insufficient Skills',$post->ID,$employer_id);
              
              
      					$wpdb->query($sql);
              
              /*$resume_options = $wpdb->get_row( "SELECT fast_tracked,skills_required,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM $wpdb->posts where ID = $post->ID",ARRAY_A);*/
              
              $resume_options = $wpdb->get_row("SELECT fast_tracked,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM wp_resume_statuses where resume_id = $post->ID AND employer_id = $employer_id",ARRAY_A);
              
              
            	}//end fast-tracked update
						
							/*For Reference Checked Update*/
								if (isset($_GET['reference-checked']) && $_GET['reference-checked'] == 'true' && $_GET['resume_id'] ==  $post->ID) {
								
              	/*$sql = $wpdb->prepare("UPDATE $wpdb->posts 
														 SET reference_checked = %s
														 WHERE ID = %d",'References Checked',$post->ID);*/
      
                $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
														 SET reference_checked = %s
														 WHERE resume_id = %d 
														 AND employer_id = %d",'References Checked',$post->ID,$employer_id);
                
                  
      					$wpdb->query($sql);
              
              	/*$resume_options = $wpdb->get_row( "SELECT fast_tracked,skills_required,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM $wpdb->posts where ID = $post->ID",ARRAY_A);*/
                  
                $resume_options = $wpdb->get_row("SELECT fast_tracked,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM wp_resume_statuses where resume_id = $post->ID AND employer_id = $employer_id",ARRAY_A);  

            
            	} elseif (isset($_GET['reference-checked']) && $_GET['reference-checked'] == 'false' && $_GET['resume_id'] ==  $post->ID) {
            		
              	/*$sql = $wpdb->prepare("UPDATE $wpdb->posts 
														 SET reference_checked = %s
														 WHERE ID = %d",'Check Reference',$post->ID);*/
      
                  $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
														 SET reference_checked = %s
														 WHERE resume_id = %d 
														 AND employer_id = %d",'Check Reference',$post->ID,$employer_id);
                  
      					$wpdb->query($sql);
              
              /*$resume_options = $wpdb->get_row( "SELECT fast_tracked,skills_required,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM $wpdb->posts where ID = $post->ID",ARRAY_A);*/
                  
                $resume_options = $wpdb->get_row("SELECT fast_tracked,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM wp_resume_statuses where resume_id = $post->ID AND employer_id = $employer_id",ARRAY_A);  

              	
            	} //end reference check update


							/*For Video Interview Update*/
								if (isset($_GET['video-interview-evaluated']) && $_GET['video-interview-evaluated'] == 'evaluated' && $_GET['resume_id'] ==  $post->ID) {
								
              	/*$sql = $wpdb->prepare("UPDATE $wpdb->posts 
														 SET video_interview = %s
														 WHERE ID = %d",'Video Evaluated',$post->ID);*/
                  
                  $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
														 SET video_interview = %s
														 WHERE resume_id = %d 
														 AND employer_id = %d",'Video Evaluated',$post->ID,$employer_id);
      
      					$wpdb->query($sql);
              
              	/*$resume_options = $wpdb->get_row( "SELECT fast_tracked,skills_required,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM $wpdb->posts where ID = $post->ID",ARRAY_A);*/
                  
                  $resume_options = $wpdb->get_row("SELECT fast_tracked,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM wp_resume_statuses where resume_id = $post->ID AND employer_id = $employer_id",ARRAY_A);

            
            	} elseif (isset($_GET['video-interview-evaluated']) && $_GET['video-interview-evaluated'] == 'submitted' && $_GET['resume_id'] ==  $post->ID) {
            		
              	/*$sql = $wpdb->prepare("UPDATE $wpdb->posts 
														 SET video_interview = %s
														 WHERE ID = %d",'Video Submitted',$post->ID);*/
                  
                  $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
														 SET video_interview = %s
														 WHERE resume_id = %d 
														 AND employer_id = %d",'Video Submitted',$post->ID,$employer_id);
      
      					$wpdb->query($sql);
              
              /*$resume_options = $wpdb->get_row( "SELECT fast_tracked,skills_required,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM $wpdb->posts where ID = $post->ID",ARRAY_A);*/
                  
                $resume_options = $wpdb->get_row("SELECT fast_tracked,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM wp_resume_statuses where resume_id = $post->ID AND employer_id = $employer_id",ARRAY_A);  

              	
            	} elseif (isset($_GET['video-interview-evaluated']) && $_GET['video-interview-evaluated'] == 'false' && $_GET['resume_id'] ==  $post->ID) {
            		
              	/*$sql = $wpdb->prepare("UPDATE $wpdb->posts 
														 SET video_interview = %s
														 WHERE ID = %d",'No Video',$post->ID);*/
                  
                  $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
														 SET video_interview = %s
														 WHERE resume_id = %d 
														 AND employer_id = %d",'No Video',$post->ID,$employer_id);
                  
      
      					$wpdb->query($sql);
              
              /*$resume_options = $wpdb->get_row( "SELECT fast_tracked,skills_required,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM $wpdb->posts where ID = $post->ID",ARRAY_A);*/
                  
                $resume_options = $wpdb->get_row("SELECT fast_tracked,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM wp_resume_statuses where resume_id = $post->ID AND employer_id = $employer_id",ARRAY_A);  
                
                }//end video interview evaluated update

							/*Red Flag Update*/	
			
								if (isset($_GET['no-red-flags']) && $_GET['no-red-flags'] == 'checking' && $_GET['resume_id'] ==  $post->ID) {
								
              	/*$sql = $wpdb->prepare("UPDATE $wpdb->posts 
														 SET red_flagged = %s
														 WHERE ID = %d",'Check For Red Flags',$post->ID);*/
                  
                  $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
														 SET red_flagged = %s
														 WHERE resume_id = %d 
														 AND employer_id = %d",'Check For Red Flags',$post->ID,$employer_id);
      
      					$wpdb->query($sql);
              
              	/*$resume_options = $wpdb->get_row( "SELECT fast_tracked,skills_required,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM $wpdb->posts where ID = $post->ID",ARRAY_A);*/
                  
               $resume_options = $wpdb->get_row("SELECT fast_tracked,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM wp_resume_statuses where resume_id = $post->ID AND employer_id = $employer_id",ARRAY_A);   

            
            	} elseif (isset($_GET['no-red-flags']) && $_GET['no-red-flags'] == 'true' && $_GET['resume_id'] ==  $post->ID) {
            		
              	/*$sql = $wpdb->prepare("UPDATE $wpdb->posts 
														 SET red_flagged = %s
														 WHERE ID = %d",'No Red Flags',$post->ID);*/
      
                  $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
														 SET red_flagged = %s
														 WHERE resume_id = %d 
														 AND employer_id = %d",'No Red Flags',$post->ID,$employer_id);
                  
      					$wpdb->query($sql);
              
              /*$resume_options = $wpdb->get_row( "SELECT fast_tracked,skills_required,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM $wpdb->posts where ID = $post->ID",ARRAY_A);*/	

               $resume_options = $wpdb->get_row("SELECT fast_tracked,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM wp_resume_statuses where resume_id = $post->ID AND employer_id = $employer_id",ARRAY_A);   
              	
            	} elseif (isset($_GET['no-red-flags']) && $_GET['no-red-flags'] == 'false' && $_GET['resume_id'] ==  $post->ID) {
            		
              	/*$sql = $wpdb->prepare("UPDATE $wpdb->posts 
														 SET red_flagged = %s
														 WHERE ID = %d",'Red Flagged',$post->ID);*/
                  
                  $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
														 SET red_flagged = %s
														 WHERE resume_id = %d 
														 AND employer_id = %d",'Red Flagged',$post->ID,$employer_id);
      
      					$wpdb->query($sql);
              
              /*$resume_options = $wpdb->get_row( "SELECT fast_tracked,skills_required,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM $wpdb->posts where ID = $post->ID",ARRAY_A);*/
                  
                $resume_options = $wpdb->get_row("SELECT fast_tracked,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM wp_resume_statuses where resume_id = $post->ID AND employer_id = $employer_id",ARRAY_A);  
                
                }//end red flagged update


							/*Completed Evaluated Update*/	
			
								if (isset($_GET['completed-evaluation']) && $_GET['completed-evaluation'] == 'true' && $_GET['resume_id'] ==  $post->ID) {
								
              	/*$sql = $wpdb->prepare("UPDATE $wpdb->posts 
														 SET completed_evaluation = %s
														 WHERE ID = %d",'Completed Evaluation',$post->ID);*/
                  
                  $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
														 SET completed_evaluation = %s
														 WHERE resume_id = %d 
														 AND employer_id = %d",'Completed Evaluation',$post->ID,$employer_id);
      
      					$wpdb->query($sql);
              
              	/*$resume_options = $wpdb->get_row( "SELECT fast_tracked,skills_required,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM $wpdb->posts where ID = $post->ID",ARRAY_A);*/
                  
                  $resume_options = $wpdb->get_row("SELECT fast_tracked,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM wp_resume_statuses where resume_id = $post->ID AND employer_id = $employer_id",ARRAY_A);

            	} elseif (isset($_GET['completed-evaluation']) && $_GET['completed-evaluation'] == 'false' && $_GET['resume_id'] ==  $post->ID) {
            		
              	/*$sql = $wpdb->prepare("UPDATE $wpdb->posts 
														 SET completed_evaluation = %s
														 WHERE ID = %d",'Evaluate',$post->ID);*/
                  
                  $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
														 SET completed_evaluation = %s
														 WHERE resume_id = %d 
														 AND employer_id = %d",'Evaluate',$post->ID,$employer_id);
      
      					$wpdb->query($sql);
              
              /*$resume_options = $wpdb->get_row( "SELECT fast_tracked,skills_required,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM $wpdb->posts where ID = $post->ID",ARRAY_A);	*/

                $resume_options = $wpdb->get_row("SELECT fast_tracked,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM wp_resume_statuses where resume_id = $post->ID AND employer_id = $employer_id",ARRAY_A);  
                  
                  
             } elseif (isset($_GET['completed-evaluation']) && $_GET['completed-evaluation'] == 'first' && $_GET['resume_id'] ==  $post->ID) {     
                  
                  /*$sql = $wpdb->prepare("UPDATE $wpdb->posts 
														 SET completed_evaluation = %s
														 WHERE ID = %d",'First',$post->ID);*/
                  
                  $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
														 SET completed_evaluation = %s
														 WHERE resume_id = %d 
														 AND employer_id = %d",'First',$post->ID,$employer_id);
                  
      
      					$wpdb->query($sql);
              
              	/*$resume_options = $wpdb->get_row( "SELECT fast_tracked,skills_required,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM $wpdb->posts where ID = $post->ID",ARRAY_A);	*/
                  
                  $resume_options = $wpdb->get_row("SELECT fast_tracked,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM wp_resume_statuses where resume_id = $post->ID AND employer_id = $employer_id",ARRAY_A);
                  
              	
             } elseif (isset($_GET['completed-evaluation']) && $_GET['completed-evaluation'] == 'second' && $_GET['resume_id'] ==  $post->ID) {   
                  
                 /*$sql = $wpdb->prepare("UPDATE $wpdb->posts 
														 SET completed_evaluation = %s
														 WHERE ID = %d",'Second',$post->ID);*/
      
                  $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
														 SET completed_evaluation = %s
														 WHERE resume_id = %d 
														 AND employer_id = %d",'Second',$post->ID,$employer_id);
                  
                  
      					$wpdb->query($sql);
              
              	/*$resume_options = $wpdb->get_row( "SELECT fast_tracked,skills_required,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM $wpdb->posts where ID = $post->ID",ARRAY_A);*/
                  
                  $resume_options = $wpdb->get_row("SELECT fast_tracked,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM wp_resume_statuses where resume_id = $post->ID AND employer_id = $employer_id",ARRAY_A);
                  
             } elseif (isset($_GET['completed-evaluation']) && $_GET['completed-evaluation'] == 'third' && $_GET['resume_id'] ==  $post->ID) {     
                  
                  /*$sql = $wpdb->prepare("UPDATE $wpdb->posts 
														 SET completed_evaluation = %s
														 WHERE ID = %d",'Third',$post->ID);*/
                  
                  $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
														 SET completed_evaluation = %s
														 WHERE resume_id = %d 
														 AND employer_id = %d",'Third',$post->ID,$employer_id);
                  
      
      					$wpdb->query($sql);
              
              	/*$resume_options = $wpdb->get_row( "SELECT fast_tracked,skills_required,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM $wpdb->posts where ID = $post->ID",ARRAY_A);	*/
                  
                  $resume_options = $wpdb->get_row("SELECT fast_tracked,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM wp_resume_statuses where resume_id = $post->ID AND employer_id = $employer_id",ARRAY_A);
                  
            	} //end completed evaluation update
				
								/*Starred Resume Update*/	
			
								if (isset($_GET['star-resume']) && $_GET['star-resume'] == 'unrated' && $_GET['resume_id'] ==  $post->ID) {
								
              	/*$sql = $wpdb->prepare("UPDATE $wpdb->posts 
														 SET starred = %s
														 WHERE ID = %d",'Unrated',$post->ID);*/
      
                $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
														 SET starred = %s
														 WHERE resume_id = %d 
														 AND employer_id = %d",'Unrated',$post->ID,$employer_id);
                    
                  
      					$wpdb->query($sql);
              
              	/*$resume_options = $wpdb->get_row( "SELECT fast_tracked,skills_required,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM $wpdb->posts where ID = $post->ID",ARRAY_A);	*/

                  $resume_options = $wpdb->get_row("SELECT fast_tracked,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM wp_resume_statuses where resume_id = $post->ID AND employer_id = $employer_id",ARRAY_A);
            
            	} elseif (isset($_GET['star-resume']) && $_GET['star-resume'] == 'second' && $_GET['resume_id'] ==  $post->ID) {
            		
              	/*$sql = $wpdb->prepare("UPDATE $wpdb->posts 
														 SET starred = %s
														 WHERE ID = %d",'2nd Highest Rated',$post->ID);*/
      
                $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
														 SET starred = %s
														 WHERE resume_id = %d 
														 AND employer_id = %d",'2nd Highest Rated',$post->ID,$employer_id);
                  
                  
      					$wpdb->query($sql);
              
              /*$resume_options = $wpdb->get_row( "SELECT fast_tracked,skills_required,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM $wpdb->posts where ID = $post->ID",ARRAY_A);	*/
                
                $resume_options = $wpdb->get_row("SELECT fast_tracked,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM wp_resume_statuses where resume_id = $post->ID AND employer_id = $employer_id",ARRAY_A);  

              	
            	} elseif (isset($_GET['star-resume']) && $_GET['star-resume'] == 'first' && $_GET['resume_id'] ==  $post->ID) {
            		
              	/*$sql = $wpdb->prepare("UPDATE $wpdb->posts 
														 SET starred = %s
														 WHERE ID = %d",'Highest Rated',$post->ID);*/
      
                $sql = $wpdb->prepare("UPDATE wp_resume_statuses 
														 SET starred = %s
														 WHERE resume_id = %d 
														 AND employer_id = %d",'Highest Rated',$post->ID,$employer_id);  
                  
                  
      					$wpdb->query($sql);
              
              /*$resume_options = $wpdb->get_row( "SELECT fast_tracked,skills_required,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM $wpdb->posts where ID = $post->ID",ARRAY_A);*/	

                $resume_options = $wpdb->get_row("SELECT fast_tracked,reference_checked,video_interview,red_flagged,completed_evaluation,starred FROM wp_resume_statuses where resume_id = $post->ID AND employer_id = $employer_id",ARRAY_A);  
              	
            	} //end starred Resume			
						
							/*Fast Tracked HTML*/

							if (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['fast_tracked'] == 'Standard Tracked') {
               
							?>
            <li class="fast-track"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/orange-check-mark.png" /><a href="<?php echo add_query_arg( 'fast-track', 'true', '' ).'&resume_id='.$post->ID; ?>" class="fast-track"><?php echo $resume_options['fast_tracked']; ?></a></li>
            
            <?php } elseif(is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['fast_tracked'] == 'Fast Tracked') { ?>
            
            <li class="fast-track"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/green-check-mark.png" /><a href="<?php echo add_query_arg( 'fast-track', 'insufficient', '' ).'&resume_id='.$post->ID; ?>" class="fast-track"><?php echo $resume_options['fast_tracked']; ?></a></li>
            
            <?php } elseif(is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['fast_tracked'] == 'Insufficient Skills') { ?>
            
            <li class="fast-track"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/red-flag-check.gif" /><a href="<?php echo add_query_arg( 'fast-track', 'false', '' ).'&resume_id='.$post->ID; ?>" class="fast-track"><?php echo $resume_options['fast_tracked']; ?></a></li>
            
            <?php } 

						/*Reference Checked HTML*/
						if (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['reference_checked'] == 'Check Reference') {
						?>
    				
							<li class="reference-checked"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/orange-check-mark.png" /><a href="<?php echo add_query_arg( 'reference-checked', 'true', '' ).'&resume_id='.$post->ID; ?>" class="reference-checked"><?php echo $resume_options['reference_checked']; ?></a></li>
            
						<?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['reference_checked'] == 'References Checked') { ?>
            
            
							<li class="reference-checked"><a href="<?php echo add_query_arg( 'reference-checked', 'false', '' ).'&resume_id='.$post->ID; ?>" class="reference-checked"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/green-check-mark.png" /><?php echo $resume_options['reference_checked']; ?></a></li>
						
					<?php } 
					
					/*Video Interview Evaluated HTML*/
						if (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['video_interview'] == 'No Video') {
					?>
							<li class="video-interview-evaluated"><a href="<?php echo add_query_arg( 'video-interview-evaluated', 'submitted', '' ).'&resume_id='.$post->ID; ?>" class="video-interview-evaluated"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/red-flag-check.gif" /><?php echo $resume_options['video_interview']; ?></a></li>
						
            <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['video_interview'] == 'Video Submitted') { ?>
            
            <li class="video-interview-evaluated"><a href="<?php echo add_query_arg( 'video-interview-evaluated', 'evaluated', '' ).'&resume_id='.$post->ID; ?>" class="video-interview-evaluated"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/orange-check-mark.png" /><?php echo $resume_options['video_interview']; ?></a></li>
            
            <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['video_interview'] == 'Video Evaluated') { ?>
						
            <li class="video-interview-evaluated"><a href="<?php echo add_query_arg( 'video-interview-evaluated', 'false', '' ).'&resume_id='.$post->ID; ?>" class="video-interview-evaluated"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/green-check-mark.png" /><?php echo $resume_options['video_interview']; ?></a></li>
					<?php } 
					
					/*No Red Flags HTML*/	
					if (is_user_logged_in() && current_user_can('can_submit_job') && trim($resume_options['red_flagged']) == 'Check For Red Flags') {

					?>
							<li class="no-red-flags"><a href="<?php echo add_query_arg( 'no-red-flags', 'false', '' ).'&resume_id='.$post->ID; ?>" class="no-red-flags"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/orange-check-mark.png" /><?php echo $resume_options['red_flagged']; ?></a></li>
						<?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && trim($resume_options['red_flagged']) == 'Red Flagged') { ?>
            	<li class="no-red-flags"><a href="<?php echo add_query_arg( 'no-red-flags', 'true', '' ).'&resume_id='.$post->ID; ?>" class="no-red-flags"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/red-flag-check.gif" /><?php echo $resume_options['red_flagged']; ?></a></li>
            <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && trim($resume_options['red_flagged']) == 'No Red Flags') { ?>
							<li class="no-red-flags"><a href="<?php echo add_query_arg( 'no-red-flags', 'checking', '').'&resume_id='.$post->ID; ?>" class="no-red-flags"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/green-check-mark.png" /><?php echo $resume_options['red_flagged'];?></a></li>
						
					<?php } 
				
					/*Completed Evaluation HTML*/
					if (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['completed_evaluation'] == 'Evaluate') {	
					?>  
            	<li class="completed-evaluation"><a href="<?php echo add_query_arg( 'completed-evaluation', 'true', '' ).'&resume_id='.$post->ID; ?>" class="completed-evaluation"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/orange-check-mark.png" /><?php echo $resume_options['completed_evaluation']; ?></a></li>
						<?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['completed_evaluation'] == 'Completed Evaluation'){ ?>
							<li class="completed-evaluation"><a href="<?php echo add_query_arg( 'completed-evaluation', 'false', '' ).'&resume_id='.$post->ID; ?>" class="completed-evaluation"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/green-check-mark.png" /><?php echo $resume_options['completed_evaluation']; ?></a></li>
						
         <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['completed_evaluation'] == 'First'){ ?>   
            
            <li class="completed-evaluation"><a href="<?php echo add_query_arg( 'completed-evaluation', 'second', '' ).'&resume_id='.$post->ID; ?>" class="completed-evaluation"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/green-check-mark.png" /><?php echo $resume_options['completed_evaluation']; ?></a></li>
            
         <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['completed_evaluation'] == 'Second'){ ?>
            
            <li class="completed-evaluation"><a href="<?php echo add_query_arg( 'completed-evaluation', 'third', '' ).'&resume_id='.$post->ID; ?>" class="completed-evaluation"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/green-check-mark.png" /><?php echo $resume_options['completed_evaluation']; ?></a></li>
            
         <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['completed_evaluation'] == 'Third'){ ?>   
            
            <li class="completed-evaluation"><a href="<?php echo add_query_arg( 'completed-evaluation', 'false', '' ).'&resume_id='.$post->ID; ?>" class="completed-evaluation"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/green-check-mark.png" /><?php echo $resume_options['completed_evaluation']; ?></a></li>
            
            
					<?php } 
					/*Highest Rated HTML*/
					if (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['starred'] == 'Unrated'){
					?>  
    		
							<li class="highest-rated"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/orange-check-mark.png" /><a href="<?php echo add_query_arg( 'star-resume', 'second', '' ).'&resume_id='.$post->ID; ?>" class="highest-rated"><?php echo $resume_options['starred']; ?></a></li>
						
            <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['starred'] == '2nd Highest Rated') { ?>
            
            <li class="highest-rated"><a href="<?php echo add_query_arg( 'star-resume', 'first', '' ).'&resume_id='.$post->ID; ?>" class="highest-rated"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/green-check-mark.png" /><?php echo $resume_options['starred']; ?></a></li>
            
            <?php } elseif(is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['starred'] == 'Highest Rated') { ?>
							<li class="highest-rated"><a href="<?php echo add_query_arg( 'star-resume', 'unrated', '' ).'&resume_id='.$post->ID; ?>" class="highest-rated"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/green-check-mark.png" /><?php echo $resume_options['starred']; ?></a></li>
						
					<?php } ?>
            
    		</ul>
    			
    
						<div class="user_prefs_wrap" style="display: none"><?php echo jr_seeker_prefs( get_the_author_meta('ID') ); ?></div>

						<?php

						if ($post->post_status=='private' && $post->post_author==get_current_user_id())
							appthemes_display_notice( 'success', sprintf(__('Your resume is currently hidden &mdash; <a href="%s">click here to publish it</a>.', APP_TD), add_query_arg('publish', 'true')) );

						?>

						<p class="meta"><?php 
							
							/*echo __('Resume of ',APP_TD) . '<strong>' .wptexturize(get_the_author_meta('display_name')) . '</strong>';*/
							
							$terms = wp_get_post_terms($post->ID, 'resume_category');
							$currency = get_post_meta($post->ID,'currency', true);							

							if ($terms) :
								//echo '<br />';
								_e(' Applying For: ',APP_TD);
								echo '<strong>'.$terms[0]->name.'</strong> ';
							endif;

							if ($desired_salary = get_post_meta($post->ID, '_desired_salary', true)) :
								echo sprintf( __('<br/>Minimum Hourly Rate: <strong>%s %s</strong> ', APP_TD), 		 $desired_salary, $currency );
							endif;

							$desired_position = wp_get_post_terms($post->ID, 'resume_job_type');
							if ($desired_position) :
								$desired_position = current($desired_position);
								echo '<br/>'.sprintf( __('Desired Position Type: <strong>%s</strong> ', APP_TD), $desired_position->name );
							else :
								echo '<br/>'.__('Desired Position Type: <strong>Any</strong> ', APP_TD);
							endif;

							if ($address = get_post_meta($post->ID, 'geo_short_address', true)) :
								echo '<br/>'.__('Location: ', APP_TD);
								echo '<strong>'.wptexturize($address). ' ';
								echo wptexturize(get_post_meta($post->ID, 'geo_short_address_country', true)).'</strong>';
							endif;

						if($posted_by = get_the_author_meta( 'display_name')):					
						
						echo '<br />'.__('Posted by: ', APP_TD);
						echo '<strong>'.wptexturize($posted_by).'</strong>';
						
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
              
              
    				</p>
						<?php
							$contact_details = array();
							$contact_details['mobile'] = get_post_meta($post->ID, '_mobile', true);
							$contact_details['tel'] = get_post_meta($post->ID, '_tel', true);
							$contact_details['email_address'] = get_post_meta($post->ID, '_email_address', true);
							$contact_details['skype'] = get_post_meta($post->ID,'skype',true);							
							if ($show_contact_form && $post->post_author!=get_current_user_id()):
								echo '<p class="button"><a class="contact_button inline noscroll" href="#contact">'.sprintf(__('Contact %s', APP_TD),wptexturize(get_the_author_meta('display_name'))).'</a></p>';
							else:
								if ($contact_details && is_array($contact_details) && sizeof($contact_details)>0) :

									echo '<dl>';
									if ($contact_details['email_address']) echo '<dt class="email">'.__('Email',APP_TD).':</dt><dd><a href="mailto:'.$contact_details['email_address'].'?subject='.__('Your Resume on',APP_TD).' '.get_bloginfo('name').'">'.$contact_details['email_address'].'</a></dd>';
									if ($contact_details['tel']) echo '<dt class="tel">'.__('Tel',APP_TD).':</dt><dd>'.$contact_details['tel'].'</dd>';
									if ($contact_details['mobile']) echo '<dt class="mobile">'.__('Mobile',APP_TD).':</dt><dd>'.$contact_details['mobile'].'</dd>';
									
									if ($contact_details['skype']) 
                    echo '<dt class="skype">'.__('Skype',APP_TD).':</dt><dd>'
?>											

<script type="text/javascript" src="http://www.skypeassets.com/i/scom/js/skype-uri.js"></script>
  <?php if ($contact_details['mobile'])?>
    <div id="SkypeButton_Call">    	
    <a href="<?php echo 'skype:'.$contact_details['skype']; ?>"><!--img height="20" weight="20" src="<?php bloginfo('template_url')?>/images/social-skype-button-blue-icon.png" /--><?php echo $contact_details['skype']; ?></a>        
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
        </dd>   
<?php 		

echo '</dl>';
								endif;
							endif;

							$websites = get_post_meta($post->ID, '_resume_websites', true);

							if ($websites && is_array($websites)) :
								$loop = 0;
								echo '<dl>';
								foreach ($websites as $website) :
								echo '<dt class="email">'.strip_tags($website['name']).':</dt><dd><a href="'.esc_url($website['url']).'" target="_blank" rel="nofollow">'.strip_tags($website['url']).'</a>';
								if (get_the_author_meta('ID')==get_current_user_id()) echo ' <a class="delete" href="?delete_website='.$loop.'">[&times;]</a>';
								echo '</dd>';
								$loop++;
								endforeach;
								echo '</dl>';
							endif;
							if (get_the_author_meta('ID')==get_current_user_id()) echo '<p class="edit_button button"><a class="inline noscroll" href="#websites">'.__('+ Add Website', APP_TD).'</a></p>';
						?>

						<?php appthemes_after_post_title(); ?>

					</div><!-- end section_header -->
	
					<div class="section_content">
	
						<?php do_action('resume_main_section', $post); ?>
	
						<?php appthemes_before_post_content(); ?>

						<h2 class="resume_section_heading"><span><?php _e('Objective', APP_TD); ?></span></h2>
						<div class="resume_section summary">
							<?php the_content(); ?>
						</div>
						<div class="clear"></div>

						<?php appthemes_after_post_content(); ?>

						<?php

							$display_sections = array(
								'resume_specialities' => __('Skills &amp; Specialties', APP_TD),
								//'skills' => __('Skills', APP_TD),
								//'resume_languages' => __('Spoken Languages', APP_TD),
								'education' => __('Education', APP_TD),
								'experience' => __('Career Map', APP_TD),
								/*'resume_groups' => __('Groups &amp; Associations', APP_TD)*/
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
              
              <h2 class="" style="text-align:center;"><span><?php echo $section; ?></span></h2>
            	
							
              <table class="carreer-map"> 
                						<tr>
                								<td class="carreer-heading">&nbsp;</td>
                              	<td class="carreer-heading">Most Recent Job</td>
                              	<td class="carreer-heading">2nd Last</td>
                              	<td class="carreer-heading">3rd Last</td>
                						</tr>
                				<tr>
                						<td nowrap><strong>Position</strong></td>
                          <?php if(get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		
                          <td><strong><?php echo wptexturize(get_post_meta($post->ID, 'company_1_position', true)); ?></strong></td>
                          <?php } ?>
                          <?php if(get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	
                          <td><strong><?php echo wptexturize(get_post_meta($post->ID, 'company_2_position', true)); ?></strong></td>
                          <?php } ?>
                          <?php if(get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	
                          <td><strong><?php echo wptexturize(get_post_meta($post->ID, 'company_3_position', true)); ?></strong></td>
                          <?php } ?>
                				</tr>
                
                				<tr>
                						<td nowrap><strong>Start Date</strong></td>
                          <?php if(get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		
                          <td><?php echo date_format(date_create(get_post_meta($post->ID, 'company_1_start_date', true)),"M d Y"); ?></td>
                          <?php } ?>
                     <?php if(get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	
                          	<td><?php echo date_format(date_create(get_post_meta($post->ID, 'company_2_start_date', true)),"M d Y"); ?></td>
                      <?php } ?>     
                     <?php if(get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  	                   
                          <td><?php echo date_format(date_create(get_post_meta($post->ID, 'company_3_start_date', true)),"M d Y"); ?></td>
                		<?php } ?>
                				</tr>
                				<tr>
                						<td nowrap><strong>End Date</strong></td>
                          <?php if(get_post_meta($post->ID, 'company_1_position', true) != "") { ?>			
                          <td><?php echo date_format(date_create(get_post_meta($post->ID, 'company_1_end_date', true)),"M d Y"); ?></td>
                          <?php } ?>
                     <?php if(get_post_meta($post->ID, 'company_2_position', true) != "") { ?>		
                          <td><?php echo date_format(date_create(get_post_meta($post->ID, 'company_2_end_date', true)),"M d Y"); ?></td>
                      <?php } ?>     
                     <?php if(get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  	                   
                          <td><?php echo date_format(date_create(get_post_meta($post->ID, 'company_3_end_date', true)),"M d Y"); ?></td>
                     <?php } ?>     
                				</tr>
                
                				<tr>
                						<td nowrap><strong>Job Type</strong></td>
                          <?php if(get_post_meta($post->ID, 'company_1_position', true) != "") { ?>			
                          <td><?php echo wptexturize(get_post_meta($post->ID, 'company_1_job_type', true)); ?></td>
                          <?php } ?>
                     <?php if(get_post_meta($post->ID, 'company_2_position', true) != "") { ?>		
                          <td><?php echo wptexturize(get_post_meta($post->ID, 'company_2_job_type', true)); ?></td>
                     <?php } ?>     
                     <?php if(get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  	
                          <td><?php echo wptexturize(get_post_meta($post->ID, 'company_3_job_type', true)); ?></td>
                				<?php } ?>
                				</tr>
                
                    		<tr>
                						<td nowrap><strong>Company</strong></td>
                          <?php if(get_post_meta($post->ID, 'company_1_position', true) != "") { ?>			
                          <td><?php echo wptexturize(get_post_meta($post->ID, 'company_1_company', true)); ?></td>
                     <?php } ?>     
                     <?php if(get_post_meta($post->ID, 'company_2_position', true) != "") { ?>		
                          <td><?php echo wptexturize(get_post_meta($post->ID, 'company_2_company', true)); ?></td>
                     <?php } ?>     
                     <?php if(get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  	                   
                          <td><?php echo wptexturize(get_post_meta($post->ID, 'company_3_company', true)); ?></td>
                     <?php } ?>     
                				</tr>
                    		<tr>
                						<td nowrap><strong>City</strong></td>
                          <?php if(get_post_meta($post->ID, 'company_1_position', true) != "") { ?>			
                          <td><?php echo wptexturize(get_post_meta($post->ID, 'company_1_city', true)); ?></td>
                          <?php } ?>
                     <?php if(get_post_meta($post->ID, 'company_2_position', true) != "") { ?>		
                          <td><?php echo wptexturize(get_post_meta($post->ID, 'company_2_city', true)); ?></td>
                          <?php } ?>
                     <?php if(get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  	                   
                          <td><?php echo wptexturize(get_post_meta($post->ID, 'company_3_city', true)); ?></td>
                     <?php } ?>     
                				</tr>
                    			<tr>
                    					<td nowrap><strong>Country</strong></td>
                            <?php if(get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		  
                            <td><?php echo wptexturize(get_post_meta($post->ID, 'company_1_country', true)); ?></td>
                            <?php } ?>
                     <?php if(get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	  
                            <td><?php echo wptexturize(get_post_meta($post->ID, 'company_2_country', true)); ?></td>
                     <?php } ?>       
                     <?php if(get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	                     
                            <td><?php echo wptexturize(get_post_meta($post->ID, 'company_3_country', true)); ?></td>
                     <?php } ?>       
                    			</tr>
                
                					<tr>
                    					<td nowrap><strong>Reason for Leaving</strong></td>
                            <?php if(get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		  
                            <td><?php echo wptexturize(get_post_meta($post->ID, 'company_1_reason_for_leaving', true)); ?></td>
                            <?php } ?>
                            <?php if(get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	  
                            <td><?php echo wptexturize(get_post_meta($post->ID, 'company_2_reason_for_leaving', true)); ?></td>
                            <?php } ?>
                            <?php if(get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	                     
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
                            <?php if(get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		  
                            <td class="user_salary"><strong><?php echo wptexturize(get_post_meta($post->ID, 'company_1_starting_salary', true)); ?></strong>&nbsp;<?php echo $currency; ?></td>
                            <?php } ?>
                     <?php if(get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	  
                            <td class="user_salary"><strong><?php echo wptexturize(get_post_meta($post->ID, 'company_2_starting_salary', true)); ?></strong>&nbsp;<?php echo $currency; ?></td>
                           <?php } ?>
                     <?php if(get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	         
                            <td class="user_salary"><strong><?php echo wptexturize(get_post_meta($post->ID, 'company_3_starting_salary', true)); ?></strong>&nbsp;<?php echo $currency; ?></td>
                    				<?php } ?>
                
                					</tr>
                
                					<tr>
                    					<td nowrap><strong>Final Salary</strong></td>
                            <?php if(get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		  
                            <td class="user_salary"><strong><?php echo wptexturize(get_post_meta($post->ID, 'company_1_final_salary', true)); ?></strong>&nbsp;<?php echo $currency; ?></td>
                            <?php } ?>
                            <?php if(get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	  
                            <td class="user_salary"><strong><?php echo wptexturize(get_post_meta($post->ID, 'company_2_final_salary', true)); ?></strong>&nbsp;<?php echo $currency; ?></td>
                     <?php } ?>         
                            <?php if(get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  
                            <td class="user_salary"><strong><?php echo wptexturize(get_post_meta($post->ID, 'company_3_final_salary', true)); ?></strong>&nbsp;<?php echo $currency; ?></td>
                    			<?php } ?>
                					</tr>
                					
                					<tr>
                    					<td nowrap><strong>Salary Type</strong></td>
                            <?php if(get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		  
                            <td class="user_salary"><?php echo wptexturize(get_post_meta($post->ID, 'company_1_salary_type', true)); ?></td>
                            <?php } ?>
                            <?php if(get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	  
                            <td class="user_salary"><?php echo wptexturize(get_post_meta($post->ID, 'company_2_salary_type', true)); ?></td>
                            <?php } ?>
                            <?php if(get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  
                            <td class="user_salary"><?php echo wptexturize(get_post_meta($post->ID, 'company_3_salary_type', true)); ?></td>
                           <?php } ?> 
                    			</tr>
                					<tr class="divider">
                						<td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                					</tr>	
                					 <?php if ( current_user_can( 'can_submit_job' ) ) { ?>
                						<tr>
                    					<td nowrap><strong>Reference Name</strong></td>
                             <?php if(get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		 
                             <td><strong><?php echo wptexturize(get_post_meta($post->ID, 'reference_name_1', true)); ?></strong></td>
                             <?php } ?>
                             <?php if(get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	 
                             <td><strong><?php echo wptexturize(get_post_meta($post->ID, 'reference_name_2', true)); ?></strong></td>  
                             <?php } ?>
                             <?php if(get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  
                             <td><strong><?php echo wptexturize(get_post_meta($post->ID, 'reference_name_3', true)); ?></strong></td>
                             <?php } ?>
                    			</tr>
                
                					<tr>
                    					<td nowrap><strong>Position</strong></td>
                            <?php if(get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		  
                            <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_position_1', true)); ?></td>
                            <?php } ?>
                            <?php if(get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	  
                            <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_position_2', true)); ?></td>
                            <?php } ?>
                            <?php if(get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  
                            <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_position_3', true)); ?></td>
                            <?php } ?>
                    			</tr>
                    
                    			<tr>
                    					<td nowrap><strong>Reference Email</strong></td>
                            <?php if(get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		  
                            <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_email_1', true)); ?></td>
                            <?php } ?>
                            <?php if(get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	  
                            <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_email_2', true)); ?></td>
                            <?php } ?>
           									<?php if(get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  
                            <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_email_3', true)); ?></td>
                            <?php } ?>
                    			</tr>
                    
                    			<tr>
                    					<td nowrap><strong>Reference Phone</strong></td>
                            <?php if(get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		  
                            <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_phone_number_1', true)); ?></td>
                            <?php } ?>
                            <?php if(get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	  
                            <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_phone_number_2', true)); ?></td>
                            <?php } ?>
                            <?php if(get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	    
                            <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_phone_number_3', true)); ?></td>
                            <?php } ?>
                    			</tr>
                
                				<tr>
                    					<td nowrap><strong>Notes</strong></td>
                            <?php if(get_post_meta($post->ID, 'company_1_position', true) != "") { ?>		  
                            <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_additional_info_1', true)); ?></td>
                            <?php } ?>
                            <?php if(get_post_meta($post->ID, 'company_2_position', true) != "") { ?>	  
                            <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_additional_info_2', true)); ?></td>
                            <?php } ?>
                            <?php if(get_post_meta($post->ID, 'company_3_position', true) != "") { ?>	  
                            <td><?php echo wptexturize(get_post_meta($post->ID, 'reference_additional_info_3', true)); ?></td>
                            <?php } ?>
                    			</tr>	
                    		<?php } ?>
                </table> 
                      
            </div>
           
            <h2 class="" style="text-align:center;"><span><?php echo "Other Employments and Career Info"; ?></span></h2>
										<div class="resume_section">
											<?php echo wpautop(wptexturize(get_post_meta($post->ID, 'other_employments', true))); ?>
										</div>
										<div class="clear"></div>
            
										<?php
									break;
									case "education" : 
										?>
										<h2 class="resume_section_heading"><span><?php echo $section; ?></span></h2>
										<div class="resume_section">
											<?php echo wpautop(wptexturize(get_post_meta($post->ID, '_education', true))); ?>
										</div>
            				
            				<div class="degree-section">
            				<div class="clear"></div>
                    <h2 class="resume_section_heading"><span><?php echo "Degree"; ?></span></h2>
            
            				<div class="resume_section">
											<span><?php echo wptexturize(get_post_meta($post->ID, 'degree', true)) ?></span>,
                      <span><?php echo wptexturize(get_post_meta($post->ID, 'institution', true));?></span>,
                      <span><?php echo wptexturize(get_post_meta($post->ID, 'degree_date_issued', true)); ?></span>
                      <br />
                      <label>Last year's overall average: </label><?php echo wptexturize(get_post_meta($post->ID, 'overall_average', true)); ?>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label>Transcripts:</label>
											<?php echo wptexturize(get_post_meta($post->ID, 'transcripts', true));?>		
            	
            			</div>	
								</div>		
										<div class="clear"></div>
            				
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
														if ($skill) echo '<li>'.wptexturize($skill).'</li>';
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
            
            <h2 class="resume_section_heading"><span><?php echo $section; ?></span></h2>
											<div class="resume_section">
									<?php 
												$terms_array = array();
												foreach ($terms as $t) :
													if (sizeof($terms_array) != (sizeof($terms) -1)) :
														$terms_array[] = $t->name . ', ';
													else :
														$terms_array[] = $t->name;
													endif;
												endforeach;
											echo '<ul class="terms"><li>'.implode('</li><li>', $terms_array).'</li></ul>'; 
							
												?>
										</div>
            				<div class="clear"></div>
            
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
							
						  switch($tests) :	

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
									$display_media = array (
                  'chart'   => __('Chart',APP_TD),
                  'interview_video' => __('Interview Video',APP_TD)
                  );
								
								foreach ($display_media as $media => $media_title) :

								switch($media) :

								case 'interview_video' :
              ?>  
							<!--
              <h2 class="resume_section_heading"><span><?php echo $media_title; ?></span></h2>
							-->
										<div class="resume_section" style="border-bottom: 0px solid rgb(204, 204, 204); padding: 0px 0px 0px 0px;">
											<!--
                      <video height="355" width="500" controls>
  										<source src="<?php echo wptexturize(get_post_meta($post->ID, 'interview_video', true)); ?>" type="video/mp4">
  										Your browser does not support HTML5 video.
									 </video>	
              				-->
                      
                      <iframe height="355" width="658" src="<?php echo wptexturize(get_post_meta($post->ID, 'interview_video', true)); ?>"></iframe>
                      </div>
              				
              			
              <?php if(current_user_can( 'can_submit_job' )) { ?>
              
              <!--
              <iframe height="355" width="658" src="https://docs.google.com/spreadsheets/d/1CRIj5PYzwEnQySpBnMNw6sIqRTA2NiWQb0ieXm0nxDk/edit#gid=0"></iframe>
              
							-->

       <form class="final-evaluation-form">       
         <h2 class="final-evaluation-heading" style="text-align:left;"><span>Evaluations of <?php the_title(); ?></span></h2>
         <?php 

           global $wpdb,$post;                                                   	
          
          $employer_id = wp_get_current_user();                                                    
                                                              
					$get_evaluation = $wpdb->get_row( "SELECT * FROM wp_final_evaluation WHERE employer_id in ('".$employer_id->ID."') AND resume_id in ('".$post->ID."')" );  
					                                                    
    		//var_dump($get_evaluation);                                                   

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
									for ($i = 1;$i < 6;$i++) {
             			if($i == $get_evaluation->skills_score) {
							?>
              <option selected="selected"><?php echo $i;?></option>
              <?php } else { ?>
              <option><?php echo $i;?></option>
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
									for ($i = 1;$i < 6;$i++) {
             			if($i == $get_evaluation->education_score) {
							?>
              <option selected="selected"><?php echo $i;?></option>
              <?php } else { ?>
              <option><?php echo $i;?></option>
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
									for ($i = 1;$i < 6;$i++) {
             			if($i == $get_evaluation->career_map_score) {
							?>
              <option selected="selected"><?php echo $i;?></option>
              <?php } else { ?>
              <option><?php echo $i;?></option>
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
									for ($i = 1;$i < 6;$i++) {
             			if($i == $get_evaluation->references_score) {
							?>
              <option selected="selected"><?php echo $i;?></option>
              <?php } else { ?>
              <option><?php echo $i;?></option>
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
									for ($i = 1;$i < 6;$i++) {
             			if($i == $get_evaluation->video_interview_score) {
							?>
              <option selected="selected"><?php echo $i;?></option>
              <?php } else { ?>
              <option><?php echo $i;?></option>
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
									for ($i = 1;$i < 6;$i++) {
             			if($i == $get_evaluation->tests_score) {
							?>
              <option selected="selected"><?php echo $i;?></option>
              <?php } else { ?>
              <option><?php echo $i;?></option>
              <?php } ?>
            	<?php } ?>
            </select>
            </td>
        	</tr>	
        	
        	<tr>
         		<td><strong>Positive Adjustments</strong></td>
            <td><textarea name="positive_adjustments_notes"><?php echo $get_evaluation->positive_adjustments_notes; ?></textarea></td>
            <td><input type="text" name="positive_adjustments_evaluator" value="<?php echo $get_evaluation->positive_adjustments_evaluator; ?>"/></td>
            <td>
            	<select name="positive_adjustments_score">
            	<?php 
									for ($i = 1;$i < 6;$i++) {
             			if($i == $get_evaluation->positive_adjustments_score) {
							?>
              <option selected="selected"><?php echo $i;?></option>
              <?php } else { ?>
              <option><?php echo $i;?></option>
              <?php } ?>
            	<?php } ?>
            </select>
            </td>
        	</tr>
        
        	<tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><strong style="float:right;font-weight:bolder;font-size:16px;color:#1F5802;">Total</strong></td>
            <td>
            	<input id="final_evaluation_score" name="final_evaluation_score" type="text" value="<?php echo $get_evaluation->final_evaluation_score;?>" />
            </td>
        	</tr>
      </tbody>  
    			<tfoot>
           	<tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td><a target="_blank" class="evaluation-instructions" href="http://vidhire.net/?p=329">Evaluation Instructions</a></td>
              <td>
            		<input type="button" id="save_score" name="save_score" class="save_score" value="Save And Calculate"/>
            	<input name="resume_id" type="hidden" value="<?php echo $post->ID; ?>">	  
              </td>
            </tr>
        	</tfoot>
            </tr>
         </table>
         
         </form>          
              <?php } ?>
										<div class="clear"></div>
							<?php
              break;
						case 'chart' :
              ?>		
						 <!--
                      <h2 class="resume_section_heading"><span><?php echo $media_title ?></span></h2>
-->
										<div class="resume_section" style="border-bottom: 0px solid rgb(204, 204, 204); padding: 0px 0px 0px 0px;">						
              			<iframe height="400" width="658" src="<?php echo wptexturize(get_post_meta($post->ID, 'chart', true)); ?>"></iframe>
              			</div>
              
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
            
            
            <?php if (current_user_can( 'manage_options' ) || current_user_can( 'can_submit_job' )) :?>  
              
            <div class="internal-notes">
              <h2 class="resume_section_heading"><span style="padding-left: 4px;"><?php echo __('Admin Notes',APP_TD) ?></span></h2>
								  <div class="resume_section" style="border-bottom: 0px solid rgb(204, 204, 204);">						
              		<p>
                  <?php echo wptexturize(get_post_meta($post->ID, 'internal_notes', true)); ?>
                  </p>
                  </div>  
            </div>
            
						<div class="processing-status">
              <h2 class="resume_section_heading"><span style="padding-left: 4px;"><?php echo __('Processing Status',APP_TD) ?></span></h2>    
              <div class="resume_section">
              <?php $terms = wp_get_post_terms($post->ID, 'resume_groups');
										if ($terms) :
											?>
												<?php 
												$terms_array = array();
												foreach ($terms as $t) :
													if (sizeof($terms_array) != (sizeof($terms) -1)) :
														$terms_array[] = $t->name . ', ';
													else :
														$terms_array[] = $t->name;
													endif;
												endforeach;
												echo '<ul class="terms"><li>'.implode('</li><li>', $terms_array).'</li></ul>'; 
												?>

            				<div class="clear"></div>
							
              		<?php endif;?>   
							</div>
					</div>
					
                      
					  <?php else : ?>    	                      
                      
            <?php endif;?>    
              
           <!--           
					<?php if ( get_option('jr_ad_stats_all') == 'yes' && current_theme_supports( 'app-stats' ) ) { ?><p class="stats"><?php appthemes_stats_counter($post->ID); ?></p> <?php } ?>
						
						<div class="clear"></div>                      
          -->  
					</div>  
            
            <div style="display:none">
            	<?php
								
								/*For Status Tags*/

				$thetags = array($resume_options['fast_tracked'],$resume_options['reference_checked'],$resume_options['video_interview'],$resume_options['red_flagged'],$resume_options['completed_evaluation'],$resume_options['starred']);
								$thetags = array_map('trim', $thetags);
					
								if (sizeof($thetags)>0) {
                  
                  wp_set_object_terms($post->ID, $thetags, 'resume_groups');
                  
                 }
							?>
            </div>
            
              
            <?php if (get_the_author_meta('ID')==get_current_user_id()) : ?>
							<p class="button edit_resume"><a href="<?php echo add_query_arg( 'edit', $post->ID, get_permalink( JR_Resume_Edit_Page::get_id() ) ); ?>"><?php _e('Edit Resume&nbsp;&rarr;',APP_TD); ?></a></p>
						<?php endif; ?>
            
            
				</div><!-- end section_content -->
				
				<?php appthemes_after_post(); ?>
				
				<?php jr_resume_footer($post); ?>

			<?php endwhile; ?>

				<?php appthemes_after_endwhile(); ?>

		<?php else: ?>

			<?php jr_no_access_permission( __('Sorry, you do not have permission to View Resumes.', APP_TD ) ); ?>

			<?php appthemes_loop_else(); ?>

		<?php endif; ?>	

		<?php appthemes_after_loop(); ?>

	<?php
edit_post_link(__('Edit'), '');
?>



<div id="comment-tab">
            <?php comments_template('/theme-comments.php'); ?>
</div>

	</div><!-- end section -->	

	<div class="clear"></div>



</div><!-- end main content -->


<?php if ($show_contact_form) : ?>
	<script type="text/javascript">
	/* <![CDATA[ */
		
		jQuery('a.contact_button').fancybox({
			'speedIn'		:	600, 
			'speedOut'		:	200, 
			'overlayShow'	:	true,
			'centerOnScroll':	true,
			'overlayColor'	:	'#555',
			'hideOnOverlayClick' : false
		});	
	/* ]]> */
	</script>
<?php
	endif;
?>	

<?php if (get_the_author_meta('ID')==get_current_user_id()) : ?>
	<script type="text/javascript">
	/* <![CDATA[ */
		
		jQuery('p.edit_button a, a.edit_button').fancybox({
			'speedIn'		:	600, 
			'speedOut'		:	200, 
			'overlayShow'	:	true,
			'centerOnScroll':	true,
			'overlayColor'	:	'#555',
			'hideOnOverlayClick' : false
		});	
		
		jQuery('a.delete').click(function(){
    		var answer = confirm ("<?php _e('Are you sure you want to delete this? This action cannot be undone...', APP_TD); ?>")
			if (answer)
				return true;
			return false;
    	});
		
	/* ]]> */
	</script>
	<?php 
	if (get_option('jr_show_sidebar')!=='no') : get_sidebar('user'); endif; 
else :	
	if (get_option('jr_show_sidebar')!=='no') : get_sidebar('resume'); endif; 
endif; 

