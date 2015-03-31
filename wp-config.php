<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'WPCACHEHOME', '/home/vidhire/public_html/wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
define('WP_CACHE', true); //Added by WP-Cache Manager
define('DB_NAME', 'vidhire_wrdp1');
/*define('DB_NAME', 'vidhire_wp');*/

/** MySQL database username */
define('DB_USER', 'vidhire_wrdp1');
/*define('DB_USER', 'vidhire_wp');*/

/** MySQL database password */
define('DB_PASSWORD', 'O0hOt9g2yyCivgbo');
/*define('DB_PASSWORD', '(radio5)');*/

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Q!*nRCQrn;8)La!4bE@bg?Cw/S4n73~^9O#R#:9k)913^(xM6T)Y-~#fm<cr-)!@_(p)A7t\`;7');
define('SECURE_AUTH_KEY',  'I/~fkM:7|k/\`R8q7c4mo/uh8y|6RIl@acr6U9tmUU<-0_KZPb:b<aiI!(y^Q=?Db4ra?');
define('LOGGED_IN_KEY',    'YQAz##Ma-eN9k83OefVaWjuYJvvMOdyNq6QhpUmCgt:vbFMMgnJxv8N;N@o3^r/T@pz');
define('NONCE_KEY',        '9h-BFhuroCLTyXKJG1ayZ<4#-lN2!-i8Wa)Rx3~vd(w3oHfH^5=ve?@Y~GAr@0p');
define('AUTH_SALT',        'w4h/!*/5Ha)g33Ly5n>W-Syhs_lU(YLztdXi:eVZ/Yv^Pk-=*WJZ=VV@ke@d\`9*CmA_7El5>*#9LB');
define('SECURE_AUTH_SALT', 'O^VCtli<z*@uiXsA-yaijUUL0|jo1~I?8V^1W9=~wJdBK<#pd#nL-1cZCuMupdWTWVQ');
define('LOGGED_IN_SALT',   ';sJxI;>NYQ?;uALly1HxD-gPin_x<Z/v0:Pyn6FlSE10a8CA;265m~^)M^i_T8CFzcQXR(2jlGefmAb');
define('NONCE_SALT',       'PZD|VId-c-kt35Dcl3Ik@VHWgZX>P<_|$_eTvOSj6?C6Ds$8x^g71hzs<*Gk*NB|E>uf|k6g=C|aJMV-IT');

/**#@-*/
define('AUTOSAVE_INTERVAL', 600 );
define('WP_POST_REVISIONS', 1);
define( 'WP_CRON_LOCK_TIMEOUT', 120 );
define( 'WP_AUTO_UPDATE_CORE', true );
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);
define('CONCATENATE_SCRIPTS', false );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
add_filter( 'auto_update_plugin', '__return_true' );
add_filter( 'auto_update_theme', '__return_true' );