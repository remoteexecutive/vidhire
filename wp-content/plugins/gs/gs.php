<?php
/*
Plugin Name: gs 
Plugin URI: http://gelsheet.sourceforge.net
Description: Create spreadsheets in your posts
Version: 1.02 
Author: PHPepe, Palillo, Perico
Author URI: http://gelsheet.sourceforge.net
*/

include_once "gs.install.php" ;
include_once "settings.php" ;
include_once "functions.php" ;



 

add_action('plugins_loaded'	, 'gs_init'); //Init
add_action('media_buttons'	, 'gs_buttons' , 1000); // Editor buttons
add_action('admin_init', 'gs_options_init' );
add_action('admin_menu', 'gs_options_page' );
register_activation_hook( __FILE__, 'gs_activate' ); //Installator
