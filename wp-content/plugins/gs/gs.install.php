<?php



function gs_install () {

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   global $wpdb;


   $table_name = $wpdb->prefix . "gs_books";
   if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	   $sql = "
	   DROP TABLE IF EXISTS $table_name;
	   CREATE TABLE " . $table_name . " (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `json` longtext,
		  `html` longtext,
		  `created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
		  `modified` timestamp NOT NULL default '0000-00-00 00:00:00',
		  `author` int(10) unsigned default NULL,
		  PRIMARY KEY  (`id`)
		)DEFAULT CHARSET=utf8 ";

		$delta = dbDelta($sql);
		

	}
   

	return true ;


}