<?php
define('WP_AUTO_UPDATE_CORE', 'minor');// Esta opción es imprescindible para garantizar que las actualizaciones de WordPress pueden gestionarse correctamente en el paquete de herramientas de WordPress. Si este sitio web WordPress ya no está gestionado por el paquete de herramientas de WordPress, elimine esta línea.
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
define( 'DB_NAME', 'imediadorWP' );

/** MySQL database username */
define( 'DB_USER', 'imedDBU' );

/** MySQL database password */
define( 'DB_PASSWORD', '~2gim59O' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'Ys9`j]2`.h6QH#t>z~U`kcX&Z9&/JNeA]0Az`@/VVYYt{}7Sv8eG`I?sP8,,=s]g' );
define( 'SECURE_AUTH_KEY',  '5C{ 1a4y44%S(6C4-N8wS8@cSbpqgD /Fh8m<nhRZqyg`#Y!s3V#EVz!nd)H{ g(' );
define( 'LOGGED_IN_KEY',    ';Axe/wA#l`ZV$q>D)n|3ZC1NO]ASNBPE25h Q,0iO&^hd2~F/gy:`{]lCIQ[QR:F' );
define( 'NONCE_KEY',        '_T|Oj_Yo;FmEs{5NS!u.)ypP@j!>geJ@+I~8:mrCZU)F9TFV?q@n5Uc:U?so^Hl#' );
define( 'AUTH_SALT',        '5]K`|ebw1,#tj,&,~X-sO25GyM34Q8>$vQ5J5+b%>cweLOjQ;q6td3Dru_0z1x:-' );
define( 'SECURE_AUTH_SALT', 'UUzuIE5$w)&#>2YQl+Fp,CcDzP:>I~UNwJSwW@ lTM`zV!)9nIX1y4.o6NpQ&Z|_' );
define( 'LOGGED_IN_SALT',   'EMtUiN =ir`EGmn6rA[LR!>Tm9OcFN<G:Kz!PxyQE6Uc0NqG3>#g*/$t6b+%A%6~' );
define( 'NONCE_SALT',       'V8Z3wC()Qg.(0T.*.+Jn1S08wDj2rxwFLwOZ6AXW3,i_dn:{N%/CD0Cd,SOw1k.c' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
