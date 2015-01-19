<?php

add_action('admin_menu', 'jr_remove_menus');

// include all the core admin files
require_once ('admin-values.php');
require_once ('admin-subscriptions.php');
require_once ('admin-alerts-subscribers.php');


// remove the admin jobs menu if editing is disabled
function jr_remove_menus () {
	if ( 'no' == get_option('jr_allow_editing') && !current_user_can('edit_jobs') )
		remove_menu_page( 'edit.php?post_type=' . APP_POST_TYPE );
}

################################################################################
// Set up menus within the wordpress admin sections
################################################################################

function appthemes_admin_menu() {
	add_submenu_page( 'app-dashboard', __( 'General Settings', APP_TD ),  __( 'Settings', APP_TD ), 'manage_options', 'settings', 'jr_settings' );
	add_submenu_page( 'app-dashboard', __( 'Emails', APP_TD ), __( 'Emails', APP_TD ), 'manage_options', 'emails', 'jr_emails' );
	add_submenu_page( 'app-dashboard', __( 'Alerts', APP_TD ), __( 'Alerts', APP_TD ), 'manage_options', 'alerts', 'jr_alerts' );
	add_submenu_page( 'app-dashboard', __( 'Integration', APP_TD ), __( 'Integration', APP_TD ), 'manage_options', 'integration', 'jr_integration' );
	add_submenu_page( 'app-dashboard', __( 'Subscriptions', APP_TD ), __( 'Subscriptions', APP_TD ), 'manage_options', 'subscriptions', 'jr_subscriptions' );
	add_submenu_page( 'app-dashboard', __( 'Alerts Subscribers', APP_TD ), __( 'Alerts Subscribers', APP_TD ), 'manage_options', 'alerts_subscribers', 'jr_alerts_subscribers' );

	do_action( 'appthemes_add_submenu_page' );
}

add_action('admin_menu', 'appthemes_admin_menu');


// update all the admin options on save
function appthemes_update_options($options) {

    if(isset($_POST['submitted']) && $_POST['submitted'] == 'yes') {

        foreach ($options as $value) {

            if(isset($value['id']) && isset($_POST[$value['id']])) {
                // echo $value['id'] . '<-- value ID | ' . $_POST[$value['id']] . '<-- $_POST value ID <br/><br/>'; // FOR DEBUGGING
                update_option($value['id'], appthemes_clean($_POST[$value['id']]));
            } else {
                @delete_option($value['id']);
            }
        }

        echo '<div id="message" class="updated fade"><p><strong>'.__('Your settings have been saved.',APP_TD).'</strong></p></div>';

    }

}


// generates admin fields based on array params passed in
function appthemes_admin_fields($options) {
    global $shortname;
?>

<script type="text/javascript">
jQuery(function() {
    jQuery("#tabs-wrap").tabs({
        fx: {
            opacity: 'toggle',
            duration: 200
        }
    });
});
</script>

<div id="tabs-wrap">


<?php

    // first generate the page tabs
    $counter = 1;

    echo '<ul>'. "\n";
    foreach ($options as $value) {

        if (in_array('tab', $value)) :
            echo '<li><a href="#'.$value['type'].$counter.'">'.$value['tabname'].'</a></li>'. "\n";
            $counter = $counter + 1;
        endif;

    }
    echo '</ul>'. "\n\n";


     // now loop through all the options
    $counter = 1;
    foreach ($options as $value) {

        switch($value['type']) {

            case 'tab':

                echo '<div id="'.esc_attr($value['type'].$counter).'">'. "\n\n";
                echo '<table class="widefat fixed" style="width:850px; margin-bottom:20px;">'. "\n\n";

            break;
            
            case 'title':
            ?>

                <thead><tr><th scope="col" width="200px"><?php echo $value['name'] ?></th><th scope="col"><?php if (isset($value['desc'])) echo $value['desc'] ?>&nbsp;</th></tr></thead>

            <?php
            break;

            case 'text':
            ?>

                <tr <?php if ($value['vis'] == '0') { ?>id="<?php if ($value['visid']) { echo esc_attr($value['visid']); } else { echo 'drop-down'; } ?>" style="display:none;"<?php } ?>>
                    <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo esc_attr($value['tip']); ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
                    <td class="forminp"><input name="<?php echo esc_attr($value['id']); ?>" id="<?php echo esc_attr($value['id']) ?>" type="<?php echo esc_attr($value['type']); ?>" style="<?php echo @$value['css'] ?>" value="<?php if (get_option( $value['id'])) echo esc_attr(get_option( $value['id'] )); else echo esc_attr($value['std']) ?>"<?php if (!empty($value['req'])) { ?> class="required" <?php } ?> <?php if ($value['min']) { ?> minlength="<?php echo esc_attr($value['min']); ?>"<?php } ?> /><?php echo isset($value['extra']) && $value['extra'] ? $value['extra'] : ''; ?><br /><small><?php echo $value['desc'] ?></small></td>
                </tr>


            <?php
            break;

            case 'select':
            ?>

                <tr>
                    <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo esc_attr($value['tip']); ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
                    <td class="forminp"><select <?php if (isset($value['js']) && $value['js']) echo esc_js($value['js']); ?> name="<?php echo esc_attr($value['id']); ?>" id="<?php echo esc_attr($value['id']); ?>" style="<?php if ( isset($value['css']) ) echo esc_attr($value['css']); ?>"<?php if (!empty($value['req'])) { ?> class="required"<?php } ?>>

                        <?php
                        foreach ($value['options'] as $key => $val) {
                        ?>

                            <option value="<?php echo esc_attr($key); ?>" <?php selected(get_option($value['id']),$key); ?>><?php echo ucfirst($val) ?></option>

                        <?php
                        }
                        ?>

                       </select><?php isset($value['extra']) && $value['extra'] ? $value['extra'] : ''; ?><br /><small><?php echo $value['desc'] ?></small>
                    </td>
                </tr>

            <?php
            break;

            case 'checkbox':
            ?>

                <tr>
                    <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo esc_attr($value['tip']); ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
                    <td class="forminp"><input type="checkbox" name="<?php echo esc_attr($value['id']); ?>" id="<?php echo esc_attr($value['id']); ?>" value="true" style="<?php echo esc_attr($value['css']);?>" <?php if(get_option($value['id'])) { ?>checked="checked"<?php } ?> />
                        <?php isset($value['extra']) && $value['extra'] ? $value['extra'] : ''; ?><br /><small><?php echo $value['desc'] ?></small>
                    </td>
                </tr>

            <?php
            break;

            case 'textarea':
            ?>
                <tr>
                    <td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo esc_attr($value['tip']); ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
                    <td class="forminp">
                        <textarea name="<?php echo esc_attr($value['id']); ?>" id="<?php echo esc_attr($value['id']); ?>" style="<?php echo esc_attr($value['css']); ?>" <?php if (!empty($value['req'])) { ?> class="required" <?php } ?><?php if ($value['min']) { ?> minlength="<?php echo esc_attr($value['min']); ?>"<?php } ?>><?php if (get_option($value['id'])) echo stripslashes(get_option($value['id'])); else echo $value['std']; ?></textarea>
                        <?php isset($value['extra']) && $value['extra'] ? $value['extra'] : ''; ?><br /><small><?php echo $value['desc'] ?></small>
                    </td>
                </tr>

            <?php
            break;

			case 'upload':
			?>

				<tr>
					<td class="titledesc"><?php if ($value['tip']) { ?><a href="#" tip="<?php echo esc_attr($value['tip']); ?>" tabindex="99"><div class="helpico"></div></a><?php } ?><?php echo $value['name'] ?>:</td>
					<td class="forminp">
						<input id="<?php echo esc_attr($value['id']); ?>" class="upload_image_url" type="text" style="<?php echo esc_attr($value['css']); ?>" name="<?php echo esc_attr($value['id']); ?>" value="<?php if (get_option( $value['id'])) echo esc_attr(get_option( $value['id'] )); else echo esc_attr($value['std']) ?>" />
						<input id="upload_image_button" class="upload_button button" rel="<?php echo esc_attr($value['id']); ?>" type="button" value="<?php esc_attr_e('Upload Image', APP_TD) ?>" />
						<br /><small><?php echo $value['desc'] ?></small>
						<div id="<?php echo esc_attr($value['id']); ?>_image" class="<?php echo esc_attr($value['id']); ?>_image upload_image_preview"><?php if (get_option( $value['id'])) echo '<img src="' .esc_attr(get_option( $value['id'] )) . '" />'; ?></div>
					</td>
                </tr>

			<?php
			break;

            case 'logo':
            ?>
                <tr>
                    <td class="titledesc"><?php echo $value['name'] ?></td>
                    <td class="forminp">&nbsp;</td>
                </tr>

            <?php
            break;

            case 'tabend':

                echo '</table>'. "\n\n";
                echo '</div> <!-- #tab'.$counter.' -->'. "\n\n";
                $counter = $counter + 1;

            break;
			
			case 'html':
				
				echo $value['html'];
			
			break;

        } // end switch

    } // end foreach
?>

</div> <!-- #tabs-wrap -->

<?php
}


do_action( 'appthemes_add_submenu_page_content' );

// general settings admin page
function jr_settings() {
    global $options_settings;

    appthemes_update_options($options_settings);
    ?>

	<div class="wrap jobroller">
            <div class="icon32" id="icon-tools"><br/></div>
		<h2><?php _e('General Settings',APP_TD); ?></h2>

                <form method="post" id="mainform" action="">

                        <?php appthemes_admin_fields($options_settings); ?>

                    <p class="submit bbot"><input class="button-primary" name="save" type="submit" value="<?php _e('Save changes',APP_TD) ?>" /></p>
                    <input name="submitted" type="hidden" value="yes" />
		</form>
	</div>
	<?php
}

// theme styles
// populates the theme dropdown with the default styles and adds any custom .css styles found on the styles path
// styles must be placed under the child folder \styles\ (fallback to the parent /styles folder if directory does not exist)
// the resulting styles array is filterable to allow adding custom theme styles
function jr_settings_theme_styles() {

	$styles_path = get_stylesheet_directory() . '/styles/';
	if ( ! file_exists( $styles_path ) ) $styles_path = get_template_directory(). '/styles/';

	$styles_pattern = $styles_path . 'style*.css';

	$styles = array (
			'style-default.css'    => __('Default Theme', APP_TD),
			'style-pro-blue.css'   => __('Blue Pro Theme', APP_TD),
			'style-pro-green.css'  => __('Green Pro Theme', APP_TD),
			'style-pro-orange.css' => __('Orange Pro Theme', APP_TD),
			'style-pro-gray.css'   => __('Gray Pro Theme', APP_TD),
			'style-pro-red.css'    => __('Red Pro Theme', APP_TD),
			'style-basic.css'      => __('Basic Plain Theme', APP_TD)
	);

	// get all the available theme styles and append them to the defaults
	foreach (glob($styles_pattern) as $filename)
		if ( !array_key_exists (basename($filename), $styles) )
			$styles[basename($filename)] = __('Custom Theme',APP_TD) . ' (' . basename($filename) . ')';

	return apply_filters('jr_theme_styles', $styles);
}

// feed settings admin page
function jr_integration() {
    global $options_integration;

    appthemes_update_options($options_integration);
    ?>

	<div class="wrap jobroller">
            <div class="icon32" id="icon-tools"><br/></div>
		<h2><?php _e('3rd Party Integration',APP_TD); ?></h2>

                <form method="post" id="mainform" action="">

                        <?php appthemes_admin_fields($options_integration); ?>

                    <p class="submit bbot"><input class="button-primary" name="save" type="submit" value="<?php _e('Save changes',APP_TD) ?>" /></p>
                    <input name="submitted" type="hidden" value="yes" />
		</form>
	</div>
	<?php
}


function jr_emails() {
    global $options_emails;

    appthemes_update_options($options_emails);
    ?>

    <div class="wrap jobroller">
        <div class="icon32" id="icon-tools"><br/></div>
        <h2><?php _e('Email Settings',APP_TD) ?></h2>

        <form method="post" id="mainform" action="">

            <?php appthemes_admin_fields($options_emails); ?>

            <p class="submit bbot"><input class="button-primary" name="save" type="submit" value="<?php _e('Save changes',APP_TD) ?>" /></p>
            <input name="submitted" type="hidden" value="yes" />
        </form>
    </div>

<?php

}

function jr_alerts() {
    global $options_alerts, $error_email, $user_ID;

    appthemes_update_options($options_alerts);
    
    // validate test emails 
	if ( isset($_POST['testalerts']) ):		

		$args = array(
			'post_type'				=> APP_POST_TYPE,
			'post_status' 			=> 'publish',
			'ignore_sticky_posts' 	=> 1,
			'posts_per_page' 		=> 5,
		);
		$jobs = query_posts($args);		
		
		$errors = 0;
		$result = jr_job_alerts_send_email( $user_ID, $jobs );
		if (!$result) $errors++; 
					
		if ( $errors > 0 ) $notice = 'error|' . __('There were errors sending the test email. Please check your log file for more details.',APP_TD);
		else $notice = 'updated|' . __('Test email sent succesfully!',APP_TD);	
		
		$notice = explode('|',$notice);
    	echo '<div class="'.$notice[0].'">
    	   			<p>'.$notice[1].'</p>
    		 </div>';
    	    	
    endif;	
	
    ?>

    <div class="wrap jobroller">
        <div class="icon32" id="icon-tools"><br/></div>
        <h2><?php _e('Alert Settings',APP_TD) ?></h2>

        <form method="post" id="mainform" action="">

            <?php appthemes_admin_fields($options_alerts); ?>

            <p class="submit bbot"><input class="button-primary" name="save" type="submit" value="<?php _e('Save changes',APP_TD) ?>" /></p>
            <input name="submitted" type="hidden" value="yes" />
        </form>

		<table class="widefat fixed" style="width:850px;">

             <thead>
                <tr>
                    <th scope="col" width="200px"><?php _e('Test Job Alerts',APP_TD)?></th>
                    <th scope="col">&nbsp;</th>
                </tr>
            </thead>
					               	
           <form method="post" id="mainform" action="">
                <tr>
                    <td class="titledesc"><?php _e('Test Email',APP_TD); ?></td>
                    <td class="forminp">
                        <input class="button"  style="float: none" name="save" type="submit" value="<?php _e('Send Test Email',APP_TD); ?>"/>
						<p><?php _e('Use this button to test your job alert emails and make any necessary tweaks or template changes. 
						The last 5 jobs will be sent to your email.',APP_TD)?></p>							
                        <input name="testalerts" type="hidden" value="yes" />
                    </td>
                </tr>
           </form>	        
		</table>   
		
    </div>

<?php
}



// pricing options admin page
function jr_pricing() {
    global $options_pricing;

    appthemes_update_options($options_pricing);
    ?>

    <div class="wrap jobroller">
        <div class="icon32" id="icon-options-general"><br/></div>
        <h2><?php _e('Pricing &amp; Payment Settings',APP_TD) ?></h2>

        <?php // jr_admin_info_box(); ?>

        <form method="post" id="mainform" action="">

            <?php appthemes_admin_fields($options_pricing); ?>

            <p class="submit bbot"><input class="button-primary" name="save" type="submit" value="<?php _e('Save changes',APP_TD) ?>" /></p>
            <input name="submitted" type="hidden" value="yes" />
        </form>
    </div>

<?php
}


/**
 * Was generating admin system info page.
 *
 * @deprecated 1.7.2
 */
function jr_system_info() {
	_deprecated_function( __FUNCTION__, '1.7.2' );
}
