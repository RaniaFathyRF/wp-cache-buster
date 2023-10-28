<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('WP_Cache_Buster_Assets_Version_Process')) {

    class WP_Cache_Buster_Assets_Version_Process
    {

        /**
         * @var WP_Cache_Buster_Assets_Version_Process
         */
        public static $instance;
        /**
         * @var WP_Cache_Buster_Assets_Version_Settings
         */
        private $assets_version_settings;

        private function __construct()
        {
            // add version to css files
            add_filter('style_loader_src', array($this, 'wp_cache_buster_set_custom_ver_css'), 9999);
            // add version to js files
            add_filter('script_loader_src', array($this, 'wp_cache_buster_set_custom_ver_js'), 9999);
            // add version to images
            add_filter('wp_get_attachment_image_src', array($this, 'wp_cache_buster_set_custom_ver_images'), 9999);

            $this->assets_version_settings = WP_Cache_Buster_Assets_Version_Settings::get_instance();

        }

        /**
         * add version to css files
         * @param $src
         * @return false|string
         */
        public function wp_cache_buster_set_custom_ver_css($src)
        {
            if (!$this->assets_version_settings->get_option_by_key(WP_Cache_Buster_Assets_Version_Settings::WP_CACHE_BUSTER_ASSETS_VERSION_CSS_ENABLED))
                return $src;
            if (strpos($src, 'ver'))
                return $src;
            return esc_url(add_query_arg('ver', WP_CACHE_BUSTER_ASSETS_VERSION, $src));

        }

        /**
         * add version to js files
         * @param $src
         * @return false|string
         */
        public function wp_cache_buster_set_custom_ver_js($src)
        {
            if (!$this->assets_version_settings->get_option_by_key(WP_Cache_Buster_Assets_Version_Settings::WP_CACHE_BUSTER_ASSETS_VERSION_JS_ENABLED))
                return $src;
            if (strpos($src, 'ver'))
                return $src;
            return esc_url(add_query_arg('ver', WP_CACHE_BUSTER_ASSETS_VERSION, $src));

        }

        /**
         * add version to images
         * @param $image
         * @return false|string
         */
        public function wp_cache_buster_set_custom_ver_images($image)
        {
            if (!$this->assets_version_settings->get_option_by_key(WP_Cache_Buster_Assets_Version_Settings::WP_CACHE_BUSTER_ASSETS_VERSION_IMAGE_ENABLED))
                return $image;

            if (empty($image[0]))
                return $image;

            if (strpos($image[0], 'ver'))
                return $image;

            $src = $image[0];

            $image[0] = apply_filters('wp_cache_buster_images_asset_version', $image[0], true);
            return $image;

        }

        /**
         * @return WP_Cache_Buster_Assets_Version_Process
         */
        public static function get_instance()
        {
            if (!isset(self::$instance) || is_null(self::$instance))
                self::$instance = new self();

            return self::$instance;
        }

    }

    WP_Cache_Buster_Assets_Version_Process::get_instance();

}

