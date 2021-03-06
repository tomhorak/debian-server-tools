<?php

// https://codex.wordpress.org/Editing_wp-config.php

/** Shared hosting */

// User home directory, absolute path without trailing slash
if ( empty( $_SERVER['HOME'] ) ) {
    define( '_HOME_DIR', realpath( getenv( 'HOME' ) ) );
} else {
    define( '_HOME_DIR', realpath( $_SERVER['HOME'] ) );
}

// Different FTP/PHP UID
define( 'FS_CHMOD_DIR', ( 0775 & ~ umask() ) );
define( 'FS_CHMOD_FILE', ( 0664 & ~ umask() ) );

// Upload temp and session directory
ini_set( 'upload_tmp_dir', _HOME_DIR . '/tmp' );
ini_set( 'session.save_path', _HOME_DIR . '/session' );

// Create dirs - Comment out after first use!
mkdir( _HOME_DIR . '/tmp', 0700 );
mkdir( _HOME_DIR . '/session', 0700 );

// See: shared-hosting-aid/enable-logging.php
ini_set( 'error_log', _HOME_DIR . '/log/error.log' );
ini_set( 'log_errors', 1 );

/** Security */

// WordPress Fail2ban
//define( 'O1_WP_FAIL2BAN_ALLOW_REDIRECT', true ); // Polylang + separate domains
//define( 'O1_BAD_REQUEST_ALLOW_CONNECTION_EMPTY', true ); // HTTP2
//define( 'O1_BAD_REQUEST_CDN_HEADERS', 'HTTP_X_AMZ_CF_ID:HTTP_VIA:HTTP_X_FORWARDED_FOR' );
//require_once __DIR__ . '/wp-miniban-htaccess.inc.php';
require_once __DIR__ . '/wp-fail2ban-bad-request-instant.inc.php';
new \O1\Bad_Request();
// wget https://github.com/szepeviktor/wordpress-fail2ban/raw/master/block-bad-requests/wp-fail2ban-bad-request-instant.inc.php
// wget https://github.com/szepeviktor/wordpress-fail2ban/raw/master/mu-plugin/wp-fail2ban-mu-instant.php

/** Composer */

//require_once __DIR__ . '/vendor/autoload.php';

/** Core */

// See: wp-config-live-debugger/
define( 'WP_DEBUG', false );
// Don't allow any other write method
define( 'FS_METHOD', 'direct' );

// "wp-content" location
// EDIT wp-content directory
define( 'WP_CONTENT_DIR', '/HOME/USER/SITE/DOC-ROOT/wp-content' );
define( 'WP_CONTENT_URL', 'https://DOMAIN.TLD/wp-content' );

// Moving to subdirs
//     siteurl += /site
//     wp search-repalce: /wp-includes/ -> /site/wp-includes/ and /wp-content/ -> /static/
//     wp option set home
//     wp option set siteurl

//define( 'WP_MEMORY_LIMIT', '96M' );
//define( 'WP_MAX_MEMORY_LIMIT', '384M' );
define( 'DISALLOW_FILE_EDIT', true );
define( 'WP_USE_EXT_MYSQL', false );
define( 'WP_POST_REVISIONS', 20 );

//define( 'WP_CACHE', true );

// CLI cron job: debian-server-tools:/webserver/wp-cron-cli.sh
// HTTP cron job: wordpress-plugin-construction:/shared-hosting-aid/wp-cron-http.sh
// Simple CLI cron job: /usr/bin/php ABSPATH/wp-cron.php # stdout and stderr to cron email
define( 'DISABLE_WP_CRON', true );
define( 'AUTOMATIC_UPDATER_DISABLED', true );

/** Multisite */

/*
define( 'WP_ALLOW_MULTISITE', true );
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', true );
$base = '/';
define( 'DOMAIN_CURRENT_SITE', 'example.com' );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );
//define( 'WP_DEFAULT_THEME', 'theme-slug' );
*/

/** Plugins */

define( 'WP_CACHE_KEY_SALT', 'SITE-SHORT_' );
//define( 'WP_APCU_KEY_SALT', 'SITE-SHORT_' );
//define( 'MEMCACHED_SERVERS', '127.0.0.1:11211:0' );
// https://polylang.wordpress.com/documentation/documentation-for-developers/list-of-options-which-can-be-set-in-wp-config-php/
// Tiny CDN - No trailing slash!
define( 'TINY_CDN_INCLUDES_URL', 'https://d2aaaaaaaaaaae.cloudfront.net/wp-includes' );
define( 'TINY_CDN_CONTENT_URL', 'https://d2aaaaaaaaaaae.cloudfront.net/wp-content' );
//define( 'PLL_LINGOTEK_AD', false );
//define( 'PLL_WPML_COMPAT', false );
//define( 'PODS_LIGHT', true );
//define( 'PODS_SESSION_AUTO_START', false );
//define( 'WPCF7_LOAD_CSS', false );
//define( 'WPCF7_LOAD_JS', false );
//define( 'ACF_LITE', true ); // -> Use the 'acf/settings/show_admin' filter
//define( 'AUTOPTIMIZE_WP_CONTENT_NAME', '/static' );
define( 'ENABLE_FORCE_CHECK_UPDATE', true );
//define( 'YIKES_MC_API_KEY', '00000000-us3' );
// Non-free
//define( 'GF_LICENSE_KEY', '' ); // Gravity Forms - rg_gforms_key
//define( 'OTGS_DISABLE_AUTO_UPDATES', true ); // WPML

/** DB */

// See: /mysql/wp-createdb.sh

define( 'DB_NAME', 'database_name_here' );
define( 'DB_USER', 'username_here' );
define( 'DB_PASSWORD', 'password_here' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );
$table_prefix = 'wp_';

/** Salts */

// wget -qO- https://api.wordpress.org/secret-key/1.1/salt/

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
    error_log( 'Please use wp-load.php to load WordPress.' );
    exit;
    //define( 'ABSPATH', __DIR__ . '/site/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
