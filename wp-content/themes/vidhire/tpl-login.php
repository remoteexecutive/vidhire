<?php
// Template Name: Login

	$redirect = $action = $role = '';

	// set a redirect for after logging in
	if ( isset( $_REQUEST['redirect_to'] ) ) {
		$redirect = $_REQUEST['redirect_to'];
	}

	if ( 'yes' == get_option( 'jr_allow_recruiters' ) )
		$employer_recruiter = __( 'Employer/Recruiter', APP_TD );
	else
		$employer_recruiter = __( 'Employer', APP_TD );
?>

	<div class="section">

    	<div class="section_content">

			<h1><?php _e('Login', APP_TD); ?></h1>

			<?php do_action( 'appthemes_notices' ); ?>

			<?php if (get_option('jr_allow_job_seekers')=='yes') { ?>

				<!--?php echo sprintf( __( 'As a <strong>%s</strong> you\'ll be able to submit your profile, post your resume, and be found by employers.', APP_TD ), __( 'Job Seeker', APP_TD ) ); ?-->
        <!--
        <p>  
          To apply for a job, create an account first, create your resume, open the job              and click "Apply". <br />
            Link your resume on the pulldown menu called "Submit Resume"
            and click the "Submit Button".
        </p> 
				-->

			<?php } else { ?>

				<p><?php _e('You must login or create an account in order to post a job &ndash; this will enable you to view, remove, or relist your listing in the future.', APP_TD); ?></p>

			<?php } ?>

		    <div class="col-1">

		        <?php 
						
						jr_register_form( $redirect, $role ); 
						
						//jr_register_form( $redirect='http://singularityteam.com/11/register', $role='job_seeker' ); 

						?>

		    </div>

		    <div class="col-2">

				<?php jr_login_form( $action, $redirect ); ?>

		    </div>

			<div class="clear"></div>

    	</div><!-- end section_content -->

		<div class="clear"></div>

	</div><!-- end section -->

    <div class="clear"></div>

</div><!-- end main content -->

<?php if (get_option('jr_show_sidebar')!=='no') get_sidebar('page'); ?>
