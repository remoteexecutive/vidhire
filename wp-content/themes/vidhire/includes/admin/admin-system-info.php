<?php

class JR_Theme_System_Info extends APP_System_Info {


	function __construct( $args = array(), $options = null ) {

		parent::__construct( $args, $options );

		add_action( 'admin_notices', array( $this, 'admin_tools' ) );
	}


	public function admin_tools() {

		if ( ! empty( $_POST['jr_tools']['delete_tables'] ) ) {
			jr_delete_db_tables();
		}

		if ( ! empty( $_POST['jr_tools']['delete_options'] ) ) {
			jr_delete_all_options();
		}

	}


	function form_handler() {
		if ( empty( $_POST['action'] ) || ! $this->tabs->contains( $_POST['action'] ) )
			return;

		check_admin_referer( $this->nonce );

		if ( ! empty( $_POST['jr_tools'] ) )
			return;
		else
			parent::form_handler();
	}


	protected function init_tabs() {
		parent::init_tabs();

		$this->tabs->add( 'jr_tools', __( 'Advanced', APP_TD ) );

		$this->tab_sections['jr_tools']['uninstall'] = array(
			'title' => __( 'Uninstall Theme', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Delete Database Tables', APP_TD ),
					'type' => 'submit',
					'name' => array( 'jr_tools', 'delete_tables' ),
					'extra' => array(
						'class' => 'button-secondary',
						'onclick' => 'return jr_confirmBeforeDeleteTables();',
					),
					'value' => __( 'Delete JobRoller Database Tables', APP_TD ),
					'desc' =>
						'<br />' . __( 'Do you wish to completely delete all JobRoller database tables?', APP_TD ) .
						'<br />' . __( 'Once you do this you will lose any transaction data you have stored.', APP_TD ),
				),
				array(
					'title' => __( 'Delete Config Options', APP_TD ),
					'type' => 'submit',
					'name' => array( 'jr_tools', 'delete_options' ),
					'extra' => array(
						'class' => 'button-secondary',
						'onclick' => 'return jr_confirmBeforeDeleteOptions();',
					),
					'value' => __( 'Delete JobRoller Config Options', APP_TD ),
					'desc' =>
						'<br />' . __( 'Do you wish to completely delete all JobRoller configuration options?', APP_TD ) .
						'<br />' . __( 'This will delete all values saved on the settings, pricing, gateways, etc admin pages from the wp_options database table.', APP_TD ),
				),
			),
		);


		$this->tab_sections['info']['log'] = array(
			'title' => __( 'Theme Log', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Log File Check', APP_TD ),
					'type' => 'text',
					'name' => array( 'system_info', 'jr_log' ),
					'extra' => array(
						'style' => 'display: none;'
					),
					'desc' => $this->log_file_check(),
				),
			),
		);


	}


	function page_footer() {
		parent::page_footer();
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	if ( $("form input[name^='jr_tools']").length ) {
		$('form p.submit').html('');
	}
});
</script>
<?php
	}


	private function log_file_check() {
		$logging_enabled = get_option( 'jr_enable_log' );
		if ( $logging_enabled != 'yes' )
			return sprintf( __( 'Logging is disabled - %s', APP_TD ), html( 'a', array( 'href' => 'admin.php?page=settings' ), __( '(change this)', APP_TD ) ) );

		$fp = fopen( TEMPLATEPATH . '/log/jobroller_log.txt', 'a' );
		if ( $fp )
			$message = __( 'Log file is writable.', APP_TD );
		else
			$message = __( 'Log file is not writable. Edit file permissions (jobroller/log/jobroller_log.txt)', APP_TD );

		fclose( $fp );

		return $message;		
	}


}

