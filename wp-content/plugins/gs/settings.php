<?php 
 

function gs_options_page() {
	add_options_page('GelSheet Settings', 'GelSheet Settings', 'manage_options', 'gs_options', 'gs_options_do_page');
}

function gs_options_init() {
	register_setting( 'gs_options', 'gs_language' );

}

function gs_options_do_page() {
	?>
		<div class="wrap">
		<h2>GelSheet Settings</h2>

		<form method="post" action="options.php">
		
		<?php settings_fields('gs_options'); ?>

		<table class="form-table">

			<tr valign="top">
				<th scope="row">Language
				<td><input type="text" name="gs_language" value="<?php echo get_option('gs_language'); ?>" size="8" /></td>
			</tr>


		</table>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>

		</form>
	</div>	
	<?php
}


