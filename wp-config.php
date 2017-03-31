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
define('DB_NAME', 'hierisdatfeestje');

/** MySQL database username */
define('DB_USER', 'hierisdatfeestje');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         '2Aatq2`=-yT`sd_7Wm5^1K!&O%d(0)m(?B>XJ}M_g2k^*1MVWOAxh;zO_J!v-~0;');
define('SECURE_AUTH_KEY',  ']1w&?0ew!0liJai](u)i@))n|%~,xMG_},/v@_e}>s!;B5d!. (+tn/H[b7#6~wR');
define('LOGGED_IN_KEY',    ']vwqZGEQz>biD1o6X+dbz4;n&{!0G`P658nSduZqCfW F#bg@.1;Fvz@$gO{jZvK');
define('NONCE_KEY',        '%e5PX{Z,q$r([G|1%J`Isjw>+S%mWB~ePLAxcWhkyg>^90eEW1nc#&(xt]u6=>vr');
define('AUTH_SALT',        '&{hI?HP$Z,17#Ug|HG3q|KaX6RFi6VEhjJxBEHy[1j{BF}EnYvZW|7)53Y.6{;yn');
define('SECURE_AUTH_SALT', 's*l8phqyVhAlGmUwL9=Y`Sb`*;YJ_KVHDbzXmqP5$SR~}}pttKvoZ9u]I5WwL|{>');
define('LOGGED_IN_SALT',   'o16rF~`N/WAGlX1><s8^ 9dmkIM@ nB!!EsjTtWzy 4!C/oHCx4R<8S9/?r$^+IR');
define('NONCE_SALT',       'MjZ5r;WRMiwj*`LeD&T=TRt4<2esF1OC>j;*2HU8jHUzj.<X3D&qm39)yk(trdx6');

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
