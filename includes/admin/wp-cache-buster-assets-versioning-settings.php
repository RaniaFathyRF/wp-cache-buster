<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('WP_Cache_Buster_Assets_Version_Settings')) {

    class WP_Cache_Buster_Assets_Version_Settings
    {

        private $all_options = null;
        /**
         * @var WP_Cache_Buster_Assets_Version_Settings
         */
        public static $instance;


        const MENU_SLUG = 'wp-cache-buster-assets-version-settings';


        const WP_CACHE_BUSTER_ASSETS_VERSION = 'wp_cache_buster_assets_version';

        const WP_CACHE_BUSTER_ASSETS_VERSION_TIME = 'wp_cache_buster_assets_version_time';
        const WP_CACHE_BUSTER_ASSETS_VERSION_CSS_ENABLED = 'wp_cache_buster_assets_version_css_enabled';

        const WP_CACHE_BUSTER_ASSETS_VERSION_JS_ENABLED = 'wp_cache_buster_assets_version_js_enabled';

        const WP_CACHE_BUSTER_ASSETS_VERSION_IMAGE_ENABLED = 'wp_cache_buster_assets_version_image_enabled';

        private function __construct()
        {
            add_action('admin_menu', array($this, 'wp_cache_buster_assets_versioning_settings_menu'), 100);
            add_action('admin_init', array($this, 'wp_cache_buster_assets_versioning_settings_fields'));
            $this->define_constants();
            add_filter('wp_cache_buster_images_asset_version', array($this, 'wp_cache_buster_add_version_to_image'), 10, 2);
        }

        /**
         * add cache buster menu page
         * @return void
         */
        public function wp_cache_buster_assets_versioning_settings_menu()
        {
            add_menu_page(
                __('Assets Version Settings', 'wp-cache-buster'),
                __('Assets Version Settings', 'wp-cache-buster'),
                'manage_options',
                self::MENU_SLUG,
                array($this, 'wp_cache_buster_assets_version_settings_page')
            );
        }

        /**
         * cache buster menu page setting
         * @return void
         */
        public function wp_cache_buster_assets_version_settings_page()
        {

            ?>

            <div class="wp-cache-buster-assets-versioning-options-form-wrapper">
                <form method="POST" action="options.php"
                      class="wp-cache-buster-admin-assets-versioning wp-cache-buster-settings-form-wrapper">
                    <?php settings_fields('wp_cache_buster_assets_versioning_settings'); ?>
                    <div class="wp-cache-buster-assets-versioning-text-messages-wrap wp-cache-buster-settings-fields-wrapper">
                        <?php
                        do_settings_sections(self::MENU_SLUG);
                        ?>
                    </div>
                    <?php
                    submit_button();
                    ?>
                </form>
            </div>

            <?php
        }

        /**
         * cache buster menu settings
         * @return void
         */
        public function wp_cache_buster_assets_versioning_settings_fields()
        {
            add_settings_section(
                'wp_cache_buster_assets_versioning_general_settings',
                __('Assets Versioning General Configuration', 'wp-cache-buster'),
                null,
                self::MENU_SLUG
            );

            add_settings_field(
                'wp_cache_buster_assets_version',
                __('WP Cache Buster Assets Version', 'wp-cache-buster'),
                array($this, 'wp_cache_buster_display_assets_version'),
                self::MENU_SLUG,
                'wp_cache_buster_assets_versioning_general_settings'
            );
            add_settings_field(
                'wp_cache_buster_css_version_enabled',
                __('Enable CSS', 'wp-cache-buster'),
                array($this, 'wp_cache_buster_display_enable_css'),
                self::MENU_SLUG,
                'wp_cache_buster_assets_versioning_general_settings'
            );
            add_settings_field(
                'wp_cache_buster_js_version_enabled',
                __('Enable JS', 'wp-cache-buster'),
                array($this, 'wp_cache_buster_display_enable_js'),
                self::MENU_SLUG,
                'wp_cache_buster_assets_versioning_general_settings'
            );
            add_settings_field(
                'wp_cache_buster_image_version_enabled',
                __('Enable Images', 'wp-cache-buster'),
                array($this, 'wp_cache_buster_display_enable_images'),
                self::MENU_SLUG,
                'wp_cache_buster_assets_versioning_general_settings'
            );

            register_setting('wp_cache_buster_assets_versioning_settings', self::WP_CACHE_BUSTER_ASSETS_VERSION,array(
                'sanitize_callback' => array($this, 'wp_cache_buster_sanitize_settings')
            ));
        }

        /**
         * cache buster display assest version
         */
        public function wp_cache_buster_display_assets_version()
        {
            $assets_version = $this->get_option_by_key(self::WP_CACHE_BUSTER_ASSETS_VERSION_TIME) ?? time() ;
            ?>
            <input type="text" name="<?php echo self::WP_CACHE_BUSTER_ASSETS_VERSION_TIME ?>"
                   value="<?php echo $assets_version; ?>">
            <?php
        }

        /**
         * cache buster display enable css field
         * @return void
         */
        public function wp_cache_buster_display_enable_css()
        {
            $css_enabled = $this->get_option_by_key(self::WP_CACHE_BUSTER_ASSETS_VERSION_CSS_ENABLED);
            ?>
            <input type="checkbox"
                   name="<?php echo self::WP_CACHE_BUSTER_ASSETS_VERSION_CSS_ENABLED ?>" <?php checked($css_enabled); ?>
                   value="1">
            <?php
        }

        /**
         * cache buster display enable js field
         * @return void
         */
        public function wp_cache_buster_display_enable_js()
        {
            $js_enabled = $this->get_option_by_key(self::WP_CACHE_BUSTER_ASSETS_VERSION_JS_ENABLED);
            ?>
            <input type="checkbox"
                   name="<?php echo self::WP_CACHE_BUSTER_ASSETS_VERSION_JS_ENABLED ?>" <?php checked($js_enabled); ?>
                   value="1">
            <?php
        }

        /**
         * cache buster display enable image field
         * @return void
         */
        public function wp_cache_buster_display_enable_images()
        {
            $image_enabled = $this->get_option_by_key(self::WP_CACHE_BUSTER_ASSETS_VERSION_IMAGE_ENABLED);
            ?>
            <input type="checkbox"
                   name="<?php echo self::WP_CACHE_BUSTER_ASSETS_VERSION_IMAGE_ENABLED ?>" <?php checked($image_enabled); ?>
                   value="1">
            <?php
        }

        /**
         * santitize settings
         * @param $submitted_options
         * @return array
         */
        public function wp_cache_buster_sanitize_settings($submitted_options)
        {
            $assets_version = $_POST[self::WP_CACHE_BUSTER_ASSETS_VERSION_TIME] ?? time();
            $enable_css = $_POST[self::WP_CACHE_BUSTER_ASSETS_VERSION_CSS_ENABLED] ?? '';
            $enable_js = $_POST[self::WP_CACHE_BUSTER_ASSETS_VERSION_JS_ENABLED] ?? '';
            $enable_image = $_POST[self::WP_CACHE_BUSTER_ASSETS_VERSION_IMAGE_ENABLED] ?? '';

            return array(
                self::WP_CACHE_BUSTER_ASSETS_VERSION_TIME => $assets_version,
                self::WP_CACHE_BUSTER_ASSETS_VERSION_CSS_ENABLED => $enable_css,
                self::WP_CACHE_BUSTER_ASSETS_VERSION_JS_ENABLED => $enable_js,
                self::WP_CACHE_BUSTER_ASSETS_VERSION_IMAGE_ENABLED => $enable_image,
            );
        }

        /**
         * define assets version const
         */
        public function define_constants()
        {
            if (!defined('WP_CACHE_BUSTER_ASSETS_VERSION'))
                define('WP_CACHE_BUSTER_ASSETS_VERSION', $this->get_option_by_key(self::WP_CACHE_BUSTER_ASSETS_VERSION_TIME));

        }

        /**
         * add version to image
         * @param $image_src
         * @param bool $is_url
         * @return string
         */
        public function wp_cache_buster_add_version_to_image($image_src, $is_url = false)
        {
            if ($is_url)
                return add_query_arg('ver', WP_CACHE_BUSTER_ASSETS_VERSION, $image_src);

            return $image_src . '?ver=' . WP_CACHE_BUSTER_ASSETS_VERSION;
        }
        /**
         * get setting field by key
         * @return mixed
         */
        public function get_option_by_key($key)
        {
            return $this->get_all_options()[$key];
        }

        /**
         * gett all settings
         * @return array
         */
        public function get_all_options()
        {
            if (is_null($this->all_options))
                $this->all_options = get_option(self::WP_CACHE_BUSTER_ASSETS_VERSION, false);

            return $this->normalize_all_options();
        }

        /**
         * normalize all fields options
         * @return array|string[]
         */
        public function normalize_all_options()
        {
            if (!is_array($this->all_options))
                $this->all_options = array();

            $this->all_options = array(
                self::WP_CACHE_BUSTER_ASSETS_VERSION_TIME => $this->all_options[self::WP_CACHE_BUSTER_ASSETS_VERSION_TIME] ?? time(),
                self::WP_CACHE_BUSTER_ASSETS_VERSION_CSS_ENABLED => $this->all_options[self::WP_CACHE_BUSTER_ASSETS_VERSION_CSS_ENABLED] ?? array(),
                self::WP_CACHE_BUSTER_ASSETS_VERSION_JS_ENABLED => $this->all_options[self::WP_CACHE_BUSTER_ASSETS_VERSION_JS_ENABLED] ?? array(),
                self::WP_CACHE_BUSTER_ASSETS_VERSION_IMAGE_ENABLED => $this->all_options[self::WP_CACHE_BUSTER_ASSETS_VERSION_IMAGE_ENABLED] ?? '',
                );

            return $this->all_options;
        }
        /**
         * @return WP_Cache_Buster_Assets_Version_Settings
         */
        public static function get_instance()
        {
            if (!isset(self::$instance) || is_null(self::$instance))
                self::$instance = new self();

            return self::$instance;
        }

    }

    WP_Cache_Buster_Assets_Version_Settings::get_instance();

}

