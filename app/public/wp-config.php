<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'PLKA#_1?7 ez{3P/[*bgI8-jU9JEq*1-X@vwUsUAplg_am9PeKkhH!q##[5#%.*p' );
define( 'SECURE_AUTH_KEY',   '}wShr&=7w5%2<W%QMX9]-Wo&`SR_ N4=-n-axN}0l6F_3JGv b[[=VE::R}Lj++r' );
define( 'LOGGED_IN_KEY',     '=S^i:B(fYP|k%Q2+:/X< R&5_8Q`A[kV/42Wlosg1uWBCk1K=,^roP}%e5yp<e*9' );
define( 'NONCE_KEY',         'pi=ON1(k[+*L,n<I+Smae0!f,(yU7ZmX,dZ<~~<Lmo.3x9yEF|MYp+.L6u<oKk`7' );
define( 'AUTH_SALT',         'ir.xdAR7Nz;?>60W6Xz!z$/ ;2:$rE7Lxsd]Ubd@b/*z]uZ.,O9&sB#EO<h_3pej' );
define( 'SECURE_AUTH_SALT',  'yKr$E/GHuR]re$&menqy*IF<+81j(Ba.},Np]>FP`0gMUyKcQ$)4/CC%#r_]P:y.' );
define( 'LOGGED_IN_SALT',    '@}mUwH|[TWRuS,V-DdZmeb<~SZvc/[:-:*o;XNO[+hOwLS@tEUw&Lr(p.U5ac|]}' );
define( 'NONCE_SALT',        '5&13Pw/N2g?U8Vv[wDlC-Lwecj8chVJsZ+d?nsyTGp3O6%IQ<dV*Yg>$UXR_wh)i' );
define( 'WP_CACHE_KEY_SALT', '%LQV=z,0=pU{)3[G8U6kgFRy]cgkhy7r|HZqF!fX{9OOG#5guv*]kp_@zFjGp1qQ' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
define( 'SURECART_ENCRYPTION_KEY', '=S^i:B(fYP|k%Q2+:/X< R&5_8Q`A[kV/42Wlosg1uWBCk1K=,^roP}%e5yp<e*9' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
