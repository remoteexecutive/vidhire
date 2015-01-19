<?php
/*
Template Name: My Jobs Template
*/
?>

<?php
### Prevent Caching
nocache_headers();

appthemes_auth_redirect_login();
if ( !current_user_can('can_submit_job') ) 
	redirect_profile();

global $userdata, $user_ID, $message, $jr_options;

$myjobsID = JR_Dashboard_Page::get_id();

$pending_payment_jobs = _jr_pending_payment_jobs_for_user( $user_ID );

$can_subscribe = jr_current_user_can_subscribe_for_resumes();
?>
	<div class="section myjobs">

		<div class="section_content">

		<h1><?php printf(__("Employer Dashboard - %s", APP_TD), ucwords($userdata->user_login)); ?></h1>

		<?php do_action( 'appthemes_notices' ); ?>

		<ul class="myjobs_display_section">

			<?php do_action( 'jr_dashboard_tab_before', 'job_lister' ); ?>

			<li id="myjobs_tab_resumes"><a href="#employer_resumes" class="noscroll"><?php _e('Top Picks', APP_TD); ?></a></li>
      <li id="myjobs_tab_recent_resumes"><a href="#employer_recent_resumes" class="noscroll"><?php _e('Recent Resumes', APP_TD); ?></a></li>
      <li id="myjobs_tab_evaluation"><a href="#employer_evaluation" class="noscroll"><?php _e('Evaluations', APP_TD); ?></a></li>
      <li id="myjobs_tab_jobs"><a href="#employer_jobs" class="noscroll"><?php _e('Jobs', APP_TD); ?></a></li>
			<?php if ( 'pack' == $jr_options->plan_type && jr_charge_job_listings() ) : ?><li><a href="#packs" class="noscroll"><?php _e('Job Packs', APP_TD); ?></a></li><?php endif; ?>
			<?php if ( $can_subscribe ) : ?><li><a href="#subscriptions" class="noscroll"><?php _e('Subscriptions', APP_TD); ?></a></li><?php endif; ?>
			<?php if ( jr_charge_job_listings() || $can_subscribe || jr_get_user_orders_count() > 0 ) : ?><li><a href="#orders" class="noscroll"><?php _e('Orders', APP_TD); ?></a></li><?php endif; ?>

			<?php do_action( 'jr_dashboard_tab_after', 'job_lister' ); ?>

		</ul>

    <div id="employer_recent_resumes" class="myjobs_tab_section">
      <?php 
		$args = array(
    'post_type' => 'resume',
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
			
    // Using the date_query to filter posts from last week
    'date_query' => array(
        array(
            'after' => '5 days ago'
        )
    )
); 
				
				query_posts($args);
				get_template_part( 'loop', 'resume' );
			  wp_reset_query();

			?>
    </div>  
      
    <div id="employer_evaluation" class="myjobs_tab_section"> 
      <?php 
					global $wpdb;
					
					$get_employer_jobs = $wpdb->get_results("SELECT distinct(job_applied_to) from wp_resume_statuses where job_owner = $user_ID",ARRAY_N);
			
						foreach ($get_employer_jobs as $jobs) {
             		$employer_data[] = strval($jobs[0]); 
            }  

		  ?> 
						<div id="jobs_dropdown_div">
      			<select class="jobs_dropdown">
      				<option>All Recent Evaluations</option>
              <?php 
							foreach ($employer_data as $data) {
							?>	
              <option><?php echo $data; ?></option>
      				<?php } ?>
      		 </select>
      		 </div>
			<?php
					
	$evaluated_resumes = $wpdb->get_results("SELECT a.ID as resume_id, b.final_evaluation_score 
			  FROM $wpdb->posts a,wp_final_evaluation b 
				WHERE 
					b.employer_id = $user_ID
				AND 
					b.resume_id = a.ID
				AND 
					a.post_type = 'resume'
				AND
					a.post_status = 'publish'
					ORDER BY b.final_evaluation_score ASC",ARRAY_A);
		
			foreach ($evaluated_resumes as $key => $row) {      			
        
        		$evaluated_resume_id[$key] = intval($row['resume_id']);
          	$evaluated_final_evaluation[$key] = intval($row['final_evaluation_score']);
        		
      }					

			array_multisort($evaluated_resume_id, SORT_ASC, $evaluated_final_evaluation, SORT_DESC, $evaluated_resumes);

					if (is_array($evaluated_resume_id) && sizeof($evaluated_resume_id) > 0) :
						$args = array(
							'post_type'	=> 'resume',
							'post_status' => 'publish',
							'ignore_sticky_posts' => 1,
							'post__in' => $evaluated_resume_id,
							'posts_per_page' => -1
						);
						query_posts($args);
						get_template_part( 'loop', 'resume' );
						wp_reset_query();
					else :
								echo '<p>'.__('There are no Evaluated Resumes yet. Go to a Resume and save an evaluation score for the resume', APP_TD).'</p>';
					endif;

			?>
    </div>  
      
      
    <div id="employer_resumes" class="myjobs_tab_section">  
		
			
      <h3><?php _e('First Rated', APP_TD); ?></h3>
				<?php
					global $wpdb;
					
					/*$first = $wpdb->get_results("SELECT distinct(ID) FROM $wpdb->posts  where starred in ('Highest Rated')",ARRAY_N);*/	
					
					$first = $wpdb->get_results("SELECT distinct(resume_id) FROM wp_resume_statuses  where starred in ('Highest Rated') AND employer_id = $user_ID",ARRAY_N);

					foreach ($first as $first_data) {
          	$first_results[] = intval($first_data[0]);
          }					
	
					if (is_array($first_results) && sizeof($first_results) > 0) :
						$args = array(
							'post_type'	=> 'resume',
							'post_status' => 'publish',
							'ignore_sticky_posts' => 1,
							'post__in' => $first_results,
							'posts_per_page' => -1
						);
						query_posts($args);
						get_template_part( 'loop', 'resume' );
						wp_reset_query();
					else :
								echo '<p>'.__('You have not marked any resumes yet as the Highest Rated Resume. You can rate resumes from the individual resume pages.', APP_TD).'</p>';
					endif;
				?>
      
      <br />
      
       <h3><?php _e('Second Rated', APP_TD); ?></h3>
				<?php
					global $wpdb;
					
					/*$second = $wpdb->get_results( "SELECT distinct(ID) FROM $wpdb->posts where starred in ('2nd Highest Rated')",ARRAY_N);*/	

					$second = $wpdb->get_results("SELECT distinct(resume_id) FROM wp_resume_statuses  where starred in ('2nd Highest Rated') AND employer_id = $user_ID",ARRAY_N);
					
				
					foreach ($second as $second_data) {
          	$second_results[] = intval($second_data[0]);
          }					
	
					if (is_array($second_results) && sizeof($second_results) > 0) :
						$args = array(
							'post_type'	=> 'resume',
							'post_status' => 'publish',
							'ignore_sticky_posts' => 1,
							'post__in' => $second_results,
							'posts_per_page' => -1
						);
						query_posts($args);
						get_template_part( 'loop', 'resume' );
						wp_reset_query();
					else :
						echo '<p>'.__('You have not marked any resumes yet as the 2nd Highest Rated Resume. You can rate resumes from the individual resume pages.', APP_TD).'</p>';
					endif;
				?>
      
      <br />
      
       <h3><?php _e('Completed Resumes', APP_TD); ?></h3>
				<?php
					global $wpdb;
					
					/*$completed = $wpdb->get_results( "SELECT distinct(ID) FROM $wpdb->posts where completed_evaluation in ('Completed Evaluation')",ARRAY_N);*/	
					
					$completed = $wpdb->get_results("SELECT distinct(resume_id) FROM wp_resume_statuses  where completed_evaluation in ('Completed Evaluation') AND employer_id = $user_ID",ARRAY_N);
				
					foreach ($completed as $completed_data) {
          	$completed_results[] = intval($completed_data[0]);
          }					
	
					if (is_array($completed_results) && sizeof($completed_results) > 0) :
						$args = array(
							'post_type'	=> 'resume',
							'post_status' => 'publish',
							'ignore_sticky_posts' => 1,
							'post__in' => $completed_results,
							'posts_per_page' => -1
						);
						query_posts($args);
						get_template_part( 'loop', 'resume' );
						wp_reset_query();
					else :
						echo '<p>'.__('You have not marked any resumes yet as Completed yet. You can mark resumes complete from the individual resume pages.', APP_TD).'</p>';
					endif;
				?>
      </div>
      <div id="employer_jobs" class="myjobs_tab_section">
      <div id="live" class="myjobs_section">
      <!--
			<h2><?php _e('Jobs', APP_TD); ?></h2>
			-->

			<?php
				global $user_ID;

				if ( get_query_var('tab') && 'live' == get_query_var('tab') ) {
					$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
				} else {
					$paged = 1;
				}

				$args = array(
						'ignore_sticky_posts'	=> true,
						'author' 				=> $user_ID,
						'post_type' 			=> APP_POST_TYPE,
						'post_status' 			=> 'publish',
						'posts_per_page' 		=> jr_get_jobs_per_page(),
						'paged' 				=> $paged,
				);
				$my_query = new WP_Query($args);
			?>
			<?php if ($my_query->have_posts()) : ?>
			
      <!--
			<p><?php _e('Below you will find a list of jobs you have previously posted which are visible on the site.', APP_TD); ?></p>
			-->

			<!--
			<table cellpadding="0" cellspacing="0" class="data_list footable myjobs_active_jobs_table">
				<thead>
					<tr>
						<th data-class="expand"><?php _e('Active Jobs',APP_TD); ?></th>
						<th class="center" data-hide="phone"><?php _e('Posted',APP_TD); ?></th>
						<th class="center" data-hide="phone"><?php _e('Remaining',APP_TD); ?></th>
						<th class="center" data-hide="phone"><?php _e('Views',APP_TD); ?></th>
						<th class="right" data-hide="phone"><?php _e('Actions',APP_TD); ?></th>
					</tr>
				</thead>
				<tbody>

				<?php while ($my_query->have_posts()) : ?>

					<?php $my_query->the_post(); ?>

					<?php if (get_post_meta($my_query->post->ID, 'jr_total_count', true)) $job_views = number_format(get_post_meta($my_query->post->ID, 'jr_total_count', true)); else $job_views = '-'; ?>

					<?php if (jr_check_expired($post)) continue; ?>

					<tr>
						<td><strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						<?php the_job_addons(); ?>
						</strong></td>
						<td class="date"><strong><?php the_time(__('j M',APP_TD)); ?></strong> <span class="year"><?php the_time(__('Y',APP_TD)); ?></span></td>
						<td class="center days"><?php echo jr_remaining_days($my_query->post); ?></td>
						<td class="center"><?php echo $job_views; ?></td>
						<td class="actions">
							<?php the_job_edit_link( $my_query->post->ID ); ?>
							<?php the_job_end_link( $my_query->post->ID ); ?>
						</td>
					</tr>
				<?php endwhile; ?>
				</tbody>
			</table>
			-->
			
      <ol class="jobs">

        		<?php while ($my_query->have_posts()) : ?>
            
        		<?php $my_query->the_post(); ?>

					<?php if (get_post_meta($my_query->post->ID, 'jr_total_count', true)) $job_views = number_format(get_post_meta($my_query->post->ID, 'jr_total_count', true)); else $job_views = '-'; ?>

					<?php if (jr_check_expired($post)) continue; ?>
        
        		<li class="job">

           <div class="job-details-title">
              <div class="title">
                    <strong>
                      <a class="job-title-color" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                		</strong>
                		<?php jr_get_custom_taxonomy($post->ID, 'job_type', 'jtype'); ?>              
           		</div><!--title-->  
              
           </div> <!--job-details-title-->
           
					<div class="job-details">
            <div>
                			<a href="" rel="nofollow"></a>
			<div class="location">
                    <strong></strong>
			</div>        
			<div class="posted-by">
  			<?php jr_job_author(); ?>
			</div>               
                   
              </div><!--job-details-->
              
            <div class="actions">
							<?php the_job_edit_link( $my_query->post->ID ); ?>
							<?php the_job_end_link( $my_query->post->ID ); ?>
						</div>
            </div></li>
			
        <?php endwhile; ?>
    </ol>        
	<?php jr_paging( $my_query, 'paged', array ( 'add_args' => array( 'tab' => 'live' ) ) ); ?>

       
			<?php else: ?>
				<p><?php _e('No live jobs found.',APP_TD); ?></p>
			<?php endif; ?>

		</div>

		<?php
		if ( 'pack' == $jr_options->plan_type && jr_charge_job_listings() ) :
			get_template_part( '/includes/dashboard-packs' );
		endif; 
		?>

      <!--?php
				
				echo do_shortcode('[wpbusinessintelligence id="2" type="chart" iframe="n"  ]Any text (chart name?)[/wpbusinessintelligence]');
			?--> 
      
<!--        
      <?php 

				/*
				Get total number of resumes and total number of jobs
				*/

				$date_yesterday = date( 'Y-m-d', strtotime('-1 days') );
				
				$resume_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type='resume' AND post_status='publish'" );
				
				$job_count = $wpdb->get_var(" SELECT COUNT(*) FROM $wpdb->posts WHERE post_type='job_listing' AND post_status='publish'");
				
				$recent_resume_count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type='resume' AND post_status='publish' AND post_date > '".$date_yesterday."'");
				
				
				echo do_shortcode('[json id="d25307"]
{
"results": {
"stats": [
{
 "Name": "Live Resumes",
 "Value": "'.$resume_count.'"
},
{
 "Name": "Live Jobs",
 "Value": "'.$job_count.'"
}
]
}
}
[/json]');


echo do_shortcode('[chart id="c25307" json="#d25307" adapter="return data.results.stats;" type="bar.horizontal" category="Name" value="Value" format="s,n" color="auto" style="width:100%;height:400px;" title="Site Statistics" description="" sort="false" interpolate="none" img="" debug="true"]');


			?>  
        -->
      
      
      
		<div id="pending" class="myjobs_section">

			<h2><?php _e('Pending Jobs', APP_TD); ?></h2>

			<?php
				global $user_ID;

				if ( get_query_var('tab') && 'pending' == get_query_var('tab') ) {
					$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
				} else {
					$paged = 1;
				}

				$args = array(
					'ignore_sticky_posts'	=> true,
					'author' 				=> $user_ID,
					'post_type' 			=> APP_POST_TYPE,
					'post_status' 			=> array( 'pending', 'draft' ),
					'posts_per_page' 		=> jr_get_jobs_per_page(),
					'paged' 				=> $paged,
				);
				$my_query = new WP_Query($args);
			?>
			<?php if ($my_query->have_posts()) : ?>

			<p><?php _e('The following jobs are pending and are not visible to users.', APP_TD); ?></p>
			<!--
			<table cellpadding="0" cellspacing="0" class="data_list footable myjobs_active_jobs_table">
				<thead>
					<tr>
						<th data-class="expand"><?php _e('Job Title',APP_TD); ?></th>
						<th class="center" data-hide="phone"><?php _e('Date Posted',APP_TD); ?></th>
						<th class="center" data-hide="phone"><?php _e('Status',APP_TD); ?></th>
						<th class="right" data-hide="phone"><?php _e('Actions',APP_TD); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
						<tr>
							<td>
							<?php
								// only users with 'edit_jobs' capability can preview pending jobs
								if ( current_user_can( 'edit_jobs', $post->ID ) ) { ?>
									<strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong>
							<?php } else { ?>
									<strong><?php the_title(); ?></strong>
							<?php } ?>
							</td>
							<td class="date"><strong><?php the_time(__('j M',APP_TD)); ?></strong> <span class="year"><?php the_time(__('Y',APP_TD)); ?></span></td>
							<td class="center"><?php

								$can_edit = jr_allow_editing();

								$job_status = jr_get_job_status( $my_query->post, $pending_payment_jobs );

								if ( $order_status = jr_get_job_order_status( $my_query->post, $pending_payment_jobs ) )
									echo sprintf( ' %s', $order_status  );
								else
									echo sprintf( ' %s', $job_status );

							?></td>
							<td class="actions"><?php the_job_actions( $my_query->post, $pending_payment_jobs ); ?>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
			-->			
			
      <ol class="jobs">

        		<?php while ($my_query->have_posts()) : ?>
            
        		<?php $my_query->the_post(); ?>

					<?php if (get_post_meta($my_query->post->ID, 'jr_total_count', true)) $job_views = number_format(get_post_meta($my_query->post->ID, 'jr_total_count', true)); else $job_views = '-'; ?>

					<?php if (jr_check_expired($post)) continue; ?>
        
        		<li class="job">

           <div class="job-details-title">
              <div class="title">
                    <strong>
                      <a class="job-title-color" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                		</strong>
                			<?php jr_get_custom_taxonomy($post->ID, 'job_type', 'jtype'); ?>
           		</div><!--title-->  
              
           </div> <!--job-details-title-->
           
					<div class="job-details">
            <div>
                			<a href="" rel="nofollow"></a>
			<div class="location">
                    <strong></strong>
			</div>        
			<div class="posted-by">
  			<?php jr_job_author(); ?>
			</div>               
                   
              </div><!--job-details-->
              
            <div class="actions">
							<?php the_job_edit_link( $my_query->post->ID ); ?>
							<?php the_job_end_link( $my_query->post->ID ); ?>
						</div>
            </div></li>
			
        <?php endwhile; ?>
    </ol>
<?php jr_paging( $my_query, 'paged', array ( 'add_args' => array( 'tab' => 'pending' ) ) ); ?>
		  
      
			<?php else : ?>
				<p><?php _e('No pending jobs found.', APP_TD); ?></p>
			<?php endif; ?>

		</div>
			
		<div id="ended" class="myjobs_section">

			<h2><?php _e('Ended/Expired Jobs', APP_TD); ?></h2>

			<?php
				global $user_ID;

				if ( get_query_var('tab') && 'ended' == get_query_var('tab') ) {
					$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
				} else {
					$paged = 1;
				}

				$args = array(
					'ignore_sticky_posts'	=> true,
					'author' 				=> $user_ID,
					'post_type' 			=> APP_POST_TYPE,
					'post_status' 			=> 'expired',
					'posts_per_page' 		=> jr_get_jobs_per_page(),
					'paged' 				=> $paged,
				);

				$my_query = new WP_Query($args);
			?>

			<?php if ( $my_query->have_posts() ): ?>

			<p><?php _e('The following jobs have expired or have been ended and are not visible to users.', APP_TD); ?></p>
			
      
      <!--
			<table cellpadding="0" cellspacing="0" class="data_list footable myjobs_active_jobs_table">
				<thead>
					<tr>
						<th data-class="expand"><?php _e('Job Title',APP_TD); ?></th>
						<th class="center" data-hide="phone"><?php _e('Date Posted',APP_TD); ?></th>
						<th class="center" data-hide="phone"><?php _e('Status',APP_TD); ?></th>
						<th class="center" data-hide="phone"><?php _e('Views',APP_TD); ?></th>
						<th class="right" data-hide="phone"><?php _e('Actions',APP_TD); ?></th>
					</tr>
				</thead>
				<tbody>

				<?php while ( $my_query->have_posts() ) : $my_query->the_post(); ?>

					<?php if (get_post_meta($my_query->post->ID, 'jr_total_count', true)) $job_views = number_format(get_post_meta($my_query->post->ID, 'jr_total_count', true)); else $job_views = '-'; ?>

					<tr>
						<td><strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong></td>
						<td class="date"><strong><?php the_time(__('j M',APP_TD)); ?></strong> <span class="year"><?php the_time(__('Y',APP_TD)); ?></span></td>
						<td class="center"><?php
					
							$job_status = jr_get_job_status( $my_query->post );

							if ( $order_status = jr_get_job_order_status( $my_query->post, $pending_payment_jobs ) )
								echo sprintf( ' %s', $order_status  );
							else
								echo sprintf( ' %s', $job_status );

						?>
						<td class="center"><?php echo $job_views; ?></td>
						<td class="actions"><?php the_job_actions( $my_query->post, $pending_payment_jobs ); ?></td>
					</tr>

				<?php endwhile; ?>

				</tbody>
			</table>
-->
			
      <ol class="jobs">

        		<?php while ($my_query->have_posts()) : ?>
            
        		<?php $my_query->the_post(); ?>

					<?php if (get_post_meta($my_query->post->ID, 'jr_total_count', true)) $job_views = number_format(get_post_meta($my_query->post->ID, 'jr_total_count', true)); else $job_views = '-'; ?>

					<?php if (jr_check_expired($post)) continue; ?>
        
        		<li class="job">

           <div class="job-details-title">
              <div class="title">
                    <strong>
                      <a class="job-title-color" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                		</strong>
                		<?php jr_get_custom_taxonomy($post->ID, 'job_type', 'jtype'); ?>
           		</div><!--title-->  
              
           </div> <!--job-details-title-->
           
					<div class="job-details">
            <div>
                			<a href="" rel="nofollow"></a>
			<div class="location">
                    <strong></strong>
			</div>        
			<div class="posted-by">
  			<?php jr_job_author(); ?>
			</div>               
                   
              </div><!--job-details-->
              
            <div class="actions">
							<?php the_job_edit_link( $my_query->post->ID ); ?>
							<?php the_job_end_link( $my_query->post->ID ); ?>
						</div>
            </div></li>
			
        <?php endwhile; ?>
    </ol>

			<?php jr_paging( $my_query, 'paged', array ( 'add_args' => array( 'tab' => 'ended' ) ) ); ?>

			<?php else: ?>
				<p><?php _e('No expired jobs found.', APP_TD); ?></p>
			<?php endif; ?>

		</div>
    
<!--
		<div id="subscriptions" class="myjobs_section">
			<h2><?php _e('Resume Subscriptions ', APP_TD); ?></h2>
			<?php get_template_part( 'includes/dashboard-resumes' ); ?>
		</div>

		<div id="orders" class="myjobs_section">
			<h2><?php _e('Orders', APP_TD); ?></h2>
			<?php get_template_part( 'includes/dashboard-orders' ); ?>
		</div>
-->
   
    
      
    <?php do_action( 'jr_dashboard_tab_content', 'job_lister' ); ?>
		
		</div><!-- end section_content -->

	</div><!-- end section -->
  </div>  <!--Employee Jobs-->   
	
	<div class="clear"></div>

</div><!-- end main content -->

<?php if (get_option('jr_show_sidebar')!=='no') get_sidebar('user'); ?>

<script type="text/javascript">
	// <![CDATA[
		jQuery('ul.myjobs_display_section li a').click(function(){

			jQuery('div.myjobs_tab_section').hide();
			
			jQuery(jQuery(this).attr('href')).show();
			
			jQuery('ul.myjobs_display_section li').removeClass('active');
			
			jQuery(this).parent().addClass('active');
			
			return false;
		});
		jQuery('ul.myjobs_display_section li a:eq(<?php echo $activeTab; ?>)').click();
		
		jQuery('a.delete-resume').click(function(){
			var answer = confirm("<?php _e('Are you sure you want to delete this resume? This action cannot be undone.',APP_TD); ?>")
			if (answer){
				jQuery(this).attr('href', jQuery(this).attr('href') + '&confirm=true');
				return true;
			}
			else{
				return false;
			}
		});
	// ]]>
	</script>
