
<?php if (is_user_logged_in()) { ?>
<div id="sidebar">
<?php } else { ?>
<div id="sidebar" style="visibility: hidden;">
<?php }?>
    
	<ul class="widgets">

		<?php appthemes_before_sidebar_widgets( 'job_listing' ); ?>

		<?php get_template_part('includes/sidebar-nav'); ?>

		<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar_job')) : else : ?>

			<!-- no dynamic sidebar so don't do anything -->

		<?php endif; ?>

		<?php appthemes_after_sidebar_widgets( 'job_listing' ); ?>

	</ul>

</div><!-- end sidebar -->
