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



if($_SERVER['SERVER_NAME'] == 'localhost' || !isset($_SERVER['INSTANCE_NAME']) || strstr(strtolower($_SERVER['INSTANCE_NAME']), 'deployment')) {
      // ** SQL Azure settings  ** //
    /** The name of the database for WordPress. Please create database before starting WordPress configuration */
    define('DB_NAME', '$DB_NAME$');

    /** MySQL database username */
    define('DB_USER', '$DB_USER$');

    /** MySQL database password */
    define('DB_PASSWORD', '$DB_PASSWORD$');

    /** MySQL hostname */
    define('DB_HOST', '$DB_HOST$');

    /** Database Type. Do not change this database type for SQL Azure */
    define('DB_TYPE', '$DB_TYPE$');

    /** Database Charset to use in creating database tables. */
    define('DB_CHARSET', '$DB_CHARSET$');

    /** The Database Collate type. Don't change this if in doubt. */
    define('DB_COLLATE', '$DB_COLLATE$');

    /**#@+
     * Authentication Unique Keys and Salts.
     *
     * Change these to different unique phrases!
     * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
     * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
     *
     * @since 2.6.0
     */
    define('AUTH_KEY',         '$AUTH_KEY$');
    define('SECURE_AUTH_KEY',  '$SECURE_AUTH_KEY$');
    define('LOGGED_IN_KEY',    '$LOGGED_IN_KEY$');
    define('NONCE_KEY',        '$NONCE_KEY$');
    define('AUTH_SALT',        '$AUTH_SALT$');
    define('SECURE_AUTH_SALT', '$SECURE_AUTH_SALT$');
    define('LOGGED_IN_SALT',   '$LOGGED_IN_SALT$');
    define('NONCE_SALT',       '$NONCE_SALT$');

    /**#@-*/

    /**
     * WordPress Database Table prefix.
     *
     * You can have multiple installations in one database if you give each a unique
     * prefix. Only numbers, letters, and underscores please!
     */
    $table_prefix  = '$DB_TABLE_PREFIX$';

    /**
     * WordPress Localized Language, defaults to English.
     *
     * Change this to localize WordPress. A corresponding MO file for the chosen
     * language must be installed to wp-content/languages. For example, install
     * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
     * language support.
     */
    define('WPLANG', '$WPLANG$');

    /**
     * For developers: WordPress debugging mode.
     *
     * Change this to true to enable the display of notices during development.
     * It is strongly recommended that plugin and theme developers use WP_DEBUG
     * in their development environments.
     */
    define('WP_DEBUG', '$WP_DEBUG$');


    /** Query Logging Settings */
    define('SAVEQUERIES', '$SAVEQUERIES$');


    /** For Multisite WordPress */
    define('WP_ALLOW_MULTISITE', '$WP_ALLOW_MULTISITE$');
    define('MULTISITE', '$MULTISITE$');
    define('SUBDOMAIN_INSTALL', '$SUBDOMAIN_INSTALL$');


    $base = '/';

    $domain_current_site = '$DOMAIN_CURRENT_SITE$';
    define( 'DOMAIN_CURRENT_SITE',  $domain_current_site);


    define('PATH_CURRENT_SITE', '$PATH_CURRENT_SITE$');
    define('SITE_ID_CURRENT_SITE', $SITE_ID_CURRENT_SITE$);
    define('BLOG_ID_CURRENT_SITE', $BLOG_ID_CURRENT_SITE$);

    /** To relocate WordPress URL. Enable it only when needed and once done, switch it OFF */
    define('RELOCATE', $RELOCATE$);
 
} else {
    /********************************
     ********************************
     *** THESE SETTINGS ARE FOR DEPLOYMENT ONLY. PLEASE DO NOT CHANGE
     ********************************
     ******************************** 
     */
    
    // ** SQL Azure settings  ** //
    /** The name of the database for WordPress. Please create database before starting WordPress configuration */
    define('DB_NAME', azure_getconfig('DB_NAME'));

    /** MySQL database username */
    define('DB_USER', azure_getconfig('DB_USER'));

    /** MySQL database password */
    define('DB_PASSWORD', azure_getconfig('DB_PASSWORD'));

    /** MySQL hostname */
    define('DB_HOST', azure_getconfig('DB_HOST'));

    /** Database Type. Do not change this database type for SQL Azure */
    define('DB_TYPE', azure_getconfig('DB_TYPE'));

    /** Database Charset to use in creating database tables. */
    define('DB_CHARSET', azure_getconfig('DB_CHARSET'));

    /** The Database Collate type. Don't change this if in doubt. */
    define('DB_COLLATE', azure_getconfig('DB_COLLATE'));

    /**#@+
     * Authentication Unique Keys and Salts.
     *
     * Change these to different unique phrases!
     * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
     * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
     *
     * @since 2.6.0
     */
    define('AUTH_KEY',         azure_getconfig('AUTH_KEY'));
    define('SECURE_AUTH_KEY',  azure_getconfig('SECURE_AUTH_KEY'));
    define('LOGGED_IN_KEY',    azure_getconfig('LOGGED_IN_KEY'));
    define('NONCE_KEY',        azure_getconfig('NONCE_KEY'));
    define('AUTH_SALT',        azure_getconfig('AUTH_SALT'));
    define('SECURE_AUTH_SALT', azure_getconfig('SECURE_AUTH_SALT'));
    define('LOGGED_IN_SALT',   azure_getconfig('LOGGED_IN_SALT'));
    define('NONCE_SALT',       azure_getconfig('NONCE_SALT'));

    /**#@-*/

    /**
     * WordPress Database Table prefix.
     *
     * You can have multiple installations in one database if you give each a unique
     * prefix. Only numbers, letters, and underscores please!
     */
    $table_prefix  = azure_getconfig('DB_TABLE_PREFIX');

    /**
     * WordPress Localized Language, defaults to English.
     *
     * Change this to localize WordPress. A corresponding MO file for the chosen
     * language must be installed to wp-content/languages. For example, install
     * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
     * language support.
     */
    define('WPLANG', azure_getconfig('WPLANG'));

    /**
     * For developers: WordPress debugging mode.
     *
     * Change this to true to enable the display of notices during development.
     * It is strongly recommended that plugin and theme developers use WP_DEBUG
     * in their development environments.
     */
    if (strcasecmp(azure_getconfig('WP_DEBUG'), 'true') == 0) {
        define('WP_DEBUG', true);
    } else {
        define('WP_DEBUG', false);
    }

    /** Query Logging Settings */
    if (strcasecmp(azure_getconfig('SAVEQUERIES'), 'true') == 0) {
        define('SAVEQUERIES', true);
        if (isset($_SERVER["APPL_PHYSICAL_PATH"])) {
            define('QUERY_LOG', getenv('APPL_PHYSICAL_PATH') . '\wp-content\queries.log');
        }
    }
    else {
        define('SAVEQUERIES', false);
    }

    /** For Multisite WordPress */
    if (strcasecmp(azure_getconfig('WP_ALLOW_MULTISITE'), 'true') == 0) {
        define('WP_ALLOW_MULTISITE', true);
    } else {
        define('WP_ALLOW_MULTISITE', false);
    }

    if (strcasecmp(azure_getconfig('MULTISITE'), 'true') == 0) {
        define('MULTISITE', true);
    } else {
        define('MULTISITE', false);
    }

    if (strcasecmp(azure_getconfig('SUBDOMAIN_INSTALL'), 'true') == 0) {
        define('SUBDOMAIN_INSTALL', true);
    } else {
        define('SUBDOMAIN_INSTALL', false);
    }

    $base = azure_getconfig('base');

    $domain_current_site = azure_getconfig('DOMAIN_CURRENT_SITE');
    if (strlen($domain_current_site) > 0) {
        define( 'DOMAIN_CURRENT_SITE',  $domain_current_site);
    }

    define('PATH_CURRENT_SITE', azure_getconfig('PATH_CURRENT_SITE'));
    define('SITE_ID_CURRENT_SITE', azure_getconfig('SITE_ID_CURRENT_SITE'));
    define('BLOG_ID_CURRENT_SITE', azure_getconfig('BLOG_ID_CURRENT_SITE'));

    /** To relocate WordPress URL. Enable it only when needed and once done, switch it OFF */
    if (strcasecmp(azure_getconfig('RELOCATE'), 'true') == 0) {
        define('RELOCATE', true);
    } else {
        define('RELOCATE', false);
    }
}
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
