<?php
/**
 * Main loop for displaying resumes
 *
 * @package JobRoller
 * @author AppThemes
 *
 */
 
 global $app_abbr;
?>

<?php appthemes_before_loop( 'resume' ); ?>

<?php if (have_posts()) : $alt = 1; ?>

    <ol class="resumes">

        <?php while (have_posts()) : the_post(); ?>
		
			<?php appthemes_before_post( 'resume' ); ?>

      <?php 
					global $wpdb,$post;					

					$employer_id = get_current_user_id();

					$employer_resumes = $wpdb->get_results("SELECT DISTINCT(resume_id) from wp_resume_statuses WHERE job_owner = $employer_id",ARRAY_A);
							

					foreach ($employer_resumes as $resumes) {		

							if($post->ID == $resumes['resume_id']) { 
			?>
      
            <li class="resume" title="<?php echo htmlspecialchars(jr_seeker_prefs( get_the_author_meta('ID') ), ENT_QUOTES); ?>">

              
              <!--
                <dl>

					<?php appthemes_before_post_title( 'resume' ); ?>
					
                    <dt><?php _e('Resume title', APP_TD); ?></dt>
					
                    <dd class="title">
					
						<strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong>
						
						<?php 
						if ( get_option($app_abbr.'_resume_listing_visibility') != 'public' )
							echo __('Applying for: ',APP_TD); //. wptexturize(get_the_author_meta('display_name'));
						
						$terms = wp_get_post_terms($post->ID, 'resume_category');
						if ($terms) :
							_e('',APP_TD);
							echo '<a href="'.get_term_link($terms[0]->slug, 'resume_category').'">' . $terms[0]->name .'</a>';  
						endif;
						?>
						
                    </dd>
					
					<?php appthemes_after_post_title( 'resume' ); ?>

					<dt><?php _e('Photo',APP_TD); ?></dt>
                    <dd class="photo"><a href="<?php the_permalink(); ?>"><?php if (has_post_thumbnail()) the_post_thumbnail('listing-thumbnail'); ?></a></dd>
                    
                    <dt><?php _e('Location', APP_TD); ?></dt>
					<dd class="location"><?php jr_location(); ?></dd>
					
                    <dt><?php _e('Date Posted', APP_TD); ?></dt>
                    <dd class="date"><strong><?php echo date_i18n('j M', strtotime($post->post_date)); ?></strong> <span class="year"><?php echo date_i18n('Y', strtotime($post->post_date)); ?></span></dd>

                </dl>
							-->
              
              <div class="resume_container">
                <div class="photo">
                <a href="<?php the_permalink(); ?>"><?php if (has_post_thumbnail()) the_post_thumbnail('thumbnail'); ?></a>
                </div>
             <div class="final-evaluation-rating">
             		  <?php 
									global $wpdb,$post;                                                   	
          
          $employer_id = wp_get_current_user();                                                    
                                                              
					$get_evaluation = $wpdb->get_row( "SELECT final_evaluation_score FROM wp_final_evaluation WHERE employer_id in ('".$employer_id->ID."') AND resume_id in ('".$post->ID."')" );  
					        
                		if ($get_evaluation->final_evaluation_score == NULL) {
                    	
                      echo "<span class='final-evaluation-no-rating'>No Rating</span>"; 
                      
                    } else {
                    	
									?>
               		<img class="final-evaluation-with-rating-img" height="70" width="60" src="<?php bloginfo('template_url')?>/images/rating-medal.jpg" />
               		<span class="final-evaluation-with-rating"><?php echo $get_evaluation->final_evaluation_score ?></span>
               	<?php } ?>
             </div>
                
                
								<div class="title">
                		<strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong>             
                
                  
                <div class="location">
                  <?php jr_location(); ?>
                </div>
              
                  
                <div class="applying-for">  
                <?php appthemes_before_post_title( 'resume' ); ?>
                <?php 
						if ( get_option($app_abbr.'_resume_listing_visibility') != 'public' )
							echo __('Applying for: ',APP_TD); //. wptexturize(get_the_author_meta('display_name'));
						
						$terms = wp_get_post_terms($post->ID, 'resume_category');
						if ($terms) :
							_e('',APP_TD);
							/*echo '<a href="'.get_term_link($terms[0]->slug, 'resume_category').'">' . $terms[0]->name .'</a>';*/
								echo '<a class="job_applying_for_link" href="/jobs/'.$terms[0]->slug.'">'.$terms[0]->name.'</a>';
						endif;
						?>
            on <?php echo date_i18n('j M', strtotime($post->post_date)); ?>,&nbsp;<span class="resume_year"><?php echo date_i18n('Y', strtotime($post->post_date)); ?></span>
                  
						<!--div class="resume_date">
                    <div class="resume_date"><?php echo date_i18n('j M', strtotime($post->post_date)); ?>,&nbsp;<span class="resume_year"><?php echo date_i18n('Y', strtotime($post->post_date)); ?></span></div>
                </div>                  
            </div-->      
             <?php appthemes_after_post_title( 'resume' ); ?>       
                
                  <br />
                  <table class="toggle-processing-status" style="font-size: 9px;">
    					
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
            <tr>        
            <td class="fast-track"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/orange-check-mark.png" /><a href="<?php echo add_query_arg( 'fast-track', 'true', '' ).'&resume_id='.$post->ID; ?>" class="fast-track"><?php echo $resume_options['fast_tracked']; ?></a></td>
            
            <?php } elseif(is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['fast_tracked'] == 'Fast Tracked') { ?>
            	<tr>
              <td class="fast-track"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/green-check-mark.png" /><a href="<?php echo add_query_arg( 'fast-track', 'insufficient', '' ).'&resume_id='.$post->ID; ?>" class="fast-track"><?php echo $resume_options['fast_tracked']; ?></a></td>
            
            <?php } elseif(is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['fast_tracked'] == 'Insufficient Skills') { ?>
            
            <td class="fast-track"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/red-flag-check.gif" /><a href="<?php echo add_query_arg( 'fast-track', 'false', '' ).'&resume_id='.$post->ID; ?>" class="fast-track"><?php echo $resume_options['fast_tracked']; ?></a></td>    
            
            
            <?php } 

						/*Reference Checked HTML*/
						if (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['reference_checked'] == 'Check Reference') {
						?>
    				
							<td class="reference-checked"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/orange-check-mark.png" /><a href="<?php echo add_query_arg( 'reference-checked', 'true', '' ).'&resume_id='.$post->ID; ?>" class="reference-checked"><?php echo $resume_options['reference_checked']; ?></a></td>
            
						<?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['reference_checked'] == 'References Checked') { ?>
            
            
							<td class="reference-checked"><a href="<?php echo add_query_arg( 'reference-checked', 'false', '' ).'&resume_id='.$post->ID; ?>" class="reference-checked"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/green-check-mark.png" /><?php echo $resume_options['reference_checked']; ?></a></td>
					
        <?php }
					/*Highest Rated HTML*/
					if (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['starred'] == 'Unrated'){
					?>  
    		
							<td class="highest-rated"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/orange-check-mark.png" /><a href="<?php echo add_query_arg( 'star-resume', 'second', '' ).'&resume_id='.$post->ID; ?>" class="highest-rated"><?php echo $resume_options['starred']; ?></a></td>
						
            <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['starred'] == '2nd Highest Rated') { ?>
            
            <td class="highest-rated"><a href="<?php echo add_query_arg( 'star-resume', 'first', '' ).'&resume_id='.$post->ID; ?>" class="highest-rated"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/green-check-mark.png" /><?php echo $resume_options['starred']; ?></a></td>
            
            <?php } elseif(is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['starred'] == 'Highest Rated') { ?>
							<td class="highest-rated"><a href="<?php echo add_query_arg( 'star-resume', 'unrated', '' ).'&resume_id='.$post->ID; ?>" class="highest-rated"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/green-check-mark.png" /><?php echo $resume_options['starred']; ?></a></td>						


						

					<?php } 
					
					/*Video Interview Evaluated HTML*/
						if (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['video_interview'] == 'No Video') {
					?>
					</tr>		
          <tr>          
					<td class="video-interview-evaluated"><a href="<?php echo add_query_arg( 'video-interview-evaluated', 'submitted', '' ).'&resume_id='.$post->ID; ?>" class="video-interview-evaluated"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/red-flag-check.gif" /><?php echo $resume_options['video_interview']; ?></a></td>
						
            <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['video_interview'] == 'Video Submitted') { ?>
           </tr>		
          <tr> 
            <td class="video-interview-evaluated"><a href="<?php echo add_query_arg( 'video-interview-evaluated', 'evaluated', '' ).'&resume_id='.$post->ID; ?>" class="video-interview-evaluated"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/orange-check-mark.png" /><?php echo $resume_options['video_interview']; ?></a></td>
            
            <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['video_interview'] == 'Video Evaluated') { ?>
						</tr>		
          <tr>
            <td class="video-interview-evaluated"><a href="<?php echo add_query_arg( 'video-interview-evaluated', 'false', '' ).'&resume_id='.$post->ID; ?>" class="video-interview-evaluated"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/green-check-mark.png" /><?php echo $resume_options['video_interview']; ?></a></td>
					<?php } 
					
					/*No Red Flags HTML*/	
					if (is_user_logged_in() && current_user_can('can_submit_job') && trim($resume_options['red_flagged']) == 'Check For Red Flags') {

					?>
							<td class="no-red-flags"><a href="<?php echo add_query_arg( 'no-red-flags', 'false', '' ).'&resume_id='.$post->ID; ?>" class="no-red-flags"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/orange-check-mark.png" /><?php echo $resume_options['red_flagged']; ?></a></td>
						<?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && trim($resume_options['red_flagged']) == 'Red Flagged') { ?>
            	<td class="no-red-flags"><a href="<?php echo add_query_arg( 'no-red-flags', 'true', '' ).'&resume_id='.$post->ID; ?>" class="no-red-flags"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/red-flag-check.gif" /><?php echo $resume_options['red_flagged']; ?></a></td>
            <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && trim($resume_options['red_flagged']) == 'No Red Flags') { ?>
							<td class="no-red-flags"><a href="<?php echo add_query_arg( 'no-red-flags', 'checking', '').'&resume_id='.$post->ID; ?>" class="no-red-flags"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/green-check-mark.png" /><?php echo $resume_options['red_flagged'];?></a></td>
						
					<?php } 
					
					/*Completed Evaluation HTML*/
					if (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['completed_evaluation'] == 'Evaluate') {	
					?>  
            	<td class="completed-evaluation"><a href="<?php echo add_query_arg( 'completed-evaluation', 'true', '' ).'&resume_id='.$post->ID; ?>" class="completed-evaluation"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/orange-check-mark.png" /><?php echo $resume_options['completed_evaluation']; ?></a></td>
						<?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['completed_evaluation'] == 'Completed Evaluation'){ ?>
							<td class="completed-evaluation"><a href="<?php echo add_query_arg( 'completed-evaluation', 'false', '' ).'&resume_id='.$post->ID; ?>" class="completed-evaluation"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/green-check-mark.png" /><?php echo $resume_options['completed_evaluation']; ?></a></td>
						
         <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['completed_evaluation'] == 'First'){ ?>   
            
            <td class="completed-evaluation"><a href="<?php echo add_query_arg( 'completed-evaluation', 'second', '' ).'&resume_id='.$post->ID; ?>" class="completed-evaluation"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/green-check-mark.png" /><?php echo $resume_options['completed_evaluation']; ?></a></td>
            
         <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['completed_evaluation'] == 'Second'){ ?>
            
            <td class="completed-evaluation"><a href="<?php echo add_query_arg( 'completed-evaluation', 'third', '' ).'&resume_id='.$post->ID; ?>" class="completed-evaluation"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/green-check-mark.png" /><?php echo $resume_options['completed_evaluation']; ?></a></td>
            
         <?php } elseif (is_user_logged_in() && current_user_can('can_submit_job') && $resume_options['completed_evaluation'] == 'Third'){ ?>   
            
            <td class="completed-evaluation"><a href="<?php echo add_query_arg( 'completed-evaluation', 'true', '' ).'&resume_id='.$post->ID; ?>" class="completed-evaluation"><img class="green-checked" height="16" width="16" src="<?php bloginfo('template_url')?>/images/green-check-mark.png" /><?php echo $resume_options['completed_evaluation']; ?></a></td>	
						
					<?php } ?>
           </tr> 
    		</table><!--toggle-processing-status-->
                </div>
              </div>
            </li>
			
   <?php } //end resume_id if?>            
<?php } //end resume_id foreach?>
                
      <div style="display:none">
            	<?php
				/*For Status Tags*/

				$thetags = array($resume_options['fast_tracked'],$resume_options['reference_checked'],$resume_options['video_interview'],$resume_options['red_flagged'],$resume_options['completed_evaluation'],$resume_options['starred']);

								$thetags = array_map('trim', $thetags);
					
								if (sizeof($thetags)>0) {
                  
                  wp_set_object_terms($post->ID, $thetags, 'resume_groups',false);
                  
                 }
							?>
            </div>          
			<?php appthemes_after_post( 'resume' ); ?>

        <?php endwhile; ?>
		
		<?php appthemes_after_endwhile( 'resume' ); ?>

        

    </ol>

<?php else: ?>

	<?php appthemes_loop_else( 'resume' ); ?>
	
<?php endif; ?>

<?php appthemes_after_loop( 'resume' ); ?>
