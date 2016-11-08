<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'stori5i7_wordpress');

/** MySQL database username */
define('DB_USER', 'stori5i7_wp');

/** MySQL database password */
define('DB_PASSWORD', '93CywOrskt3b');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         '3j|MQF!?Zeg~jjIRnF+Sn<slfM9o:UA|]:O|#Nr`|TF-(allBh04Rjo|}Fo9]BTw');
define('SECURE_AUTH_KEY',  ']C]_-kSDQEwacg N=q!h+1Yz@z|oQ%(1u;Q8xCC5ytljvSICr>};TPN^eftu:Jn-');
define('LOGGED_IN_KEY',    '6>kOf)y+UlEmlW|>5n{kgYG!mcj_!8;|y#rVRZ|g6u_>[p-O+{[=)m6>+o 02~|o');
define('NONCE_KEY',        '^#KA5AMd)?0#_uE+-E|pm$(;WX||zmuBkx5i%W|4a<zw+dYPb<TThj~@WuES?E#(');
define('AUTH_SALT',        'i&pxhs`)AkMc6d+[sCZhtOMHt;][Y&85sxH)vXj9T:9r/n0c!$K$O8hOR.lK!D2 ');
define('SECURE_AUTH_SALT', '.j7J4~sJ1z3<nKcM]~]|@+zy1 (3tAO9L_JFn]h7M@&+!}a:<xV4E(w z|6|u4O0');
define('LOGGED_IN_SALT',   'h<|j]4xCc*AEnOgA]p7;Xo)i%T!t:i@~E-5p&KjQ^1 :cU(@hhgnIfG{f?oN{--}');
define('NONCE_SALT',       '&. ,q8rxrIOz7BrIg$*C2Q[$k|E|-V]DM++XURUlW~0v1ViPMCbQ:MDJ=+#bqi;!');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
