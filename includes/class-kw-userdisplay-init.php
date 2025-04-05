<?php

/**
 * The file defines the core plugin class
 *
 * @package    KGWP\UserDataDisplay
 * @subpackage KGWP\UserDataDisplay\Inc
 */

namespace KGWP\UserDataDisplay\Inc;
use KGWP\UserDataDisplay\Inc\TemplateHandler as TemplateHandler;
use KGWP\UserDataDisplay\Inc\UserData as UserData;

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

final class Init {

    /**
     * Instance of the class
     *
     * @var \KGWP\UserDataDisplay\Inc\Init
     */
    protected static $_instance = null;

    // Class instances
    public $admin_page;
    public $shortcode_handler;

    /**
     * Store the main instance (singleton)
     *
     * @return \KGWP\UserDataDisplay\Inc\Init
     */
    public static function instance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    /**
     * Constructor
     *
     * @return void
     */
    public function __construct() {

        // Load needed classes with autoloader
        $this->admin_page = new \KGWP\UserDataDisplay\Inc\AdminPages();
        $this->shortcode_handler = new \KGWP\UserDataDisplay\Inc\ShortcodeHandler();
        // UserData and TemplateHandler called later by ShortcodeHandler

        // Set locale
        $this->set_locale();

        // Init hook
        add_action('init', array( $this, 'on_init'));

        // Register shortcodes:
        // Since 2.0 done in ShortcodeHandler class
    }


    /**
     * Enqueue CSS and JS
     *
     * @return void
     */
    public function enqueue_assets() {

        wp_enqueue_style(
            'kgwp-user-data-display-styles',
            KGWP_USERDATADISPLAY_PLUGIN_URL . 'assets/kw-userdisplay-frontend-style.css',
            array(),
            KGWP_USERDATADISPLAY_PLUGIN_VERSION,
            'all'
        );

        wp_enqueue_script(
            'kgwp-user-data-display-scripts',
            KGWP_USERDATADISPLAY_PLUGIN_URL . 'assets/kw-userdisplay-script.js',
            array('jquery'),
            KGWP_USERDATADISPLAY_PLUGIN_VERSION,
            false
        );
    }


    /**
     * Set locale and allow i18n of the plugin
     *
     * @return void
     */
    public function set_locale() {

        // Set the locale, use plugin name as domain
        $locale = determine_locale();
        $locale = apply_filters('plugin_locale', $locale, KGWP_USERDATADISPLAY_PLUGIN_NAME);

        load_textdomain(
            'kgwp-user-data-display',
            WP_LANG_DIR . '/kgwp-user-data-display/kgwp-user-data-display-' . $locale . '.mo'
        );

        load_plugin_textdomain(
            'kgwp-user-data-display',
            false,
            KGWP_USERDATADISPLAY_PLUGIN_NAME . '/languages/'
        );
    }


    /**
     * Run some code on init
     *
     * @return void
     */
    public function on_init() {

        // Load assets
        $this->enqueue_assets();
    }


    /**
     * Allowed capability to view the table | TODO - allow to change + maybe multi-level
     *
     * @return string
     */
    public static function allowed_capability() {
        return 'list_users';
    }


    /**
     * User access control
     *
     * @return bool
     */
    public static function is_allowed_to_view($type = 'card') {

        if ($type !== 'table') {
            return true;
        }

        // Restriction only for table now
        return current_user_can(self::allowed_capability());
    }


}
