<?php
/**
 * Plugin Name: WP Cache Buster
 * Plugin URI: wp-cache-buster
 * Description: This plugin prevent page and browser caching by adding random version numbers to CSS & JS & IMAGES assets
 * Version: 1.0.0
 * Author: rfmasters
 * Author URI:
 * Text Domain: wp-cache-buster
 */

/**
 * define constants
 */

if (!defined('WP_CACHE_BUSTER_VERSION'))
    define('WP_CACHE_BUSTER_VERSION', '1.0.0');

if (!defined('WP_CACHE_BUSTER_PATH'))
    define('WP_CACHE_BUSTER_PATH', plugin_dir_path(__FILE__));

if (!defined('WP_CACHE_BUSTER_URL'))
    define('WP_CACHE_BUSTER_URL', plugin_dir_url(__FILE__));

/**
 * Include files.
 */

include WP_CACHE_BUSTER_PATH.'/includes/index.php';