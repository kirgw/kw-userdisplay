<?php

/**
 * The file defines the core plugin class
 *
 * @package    KW\UserDisplay
 * @subpackage KW\UserDisplay\Inc
 */

namespace KW\UserDisplay\Inc;
use KW\UserDisplay\Inc\TemplateHandler as TemplateHandler;
use KW\UserDisplay\Inc\UserData as UserData;

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

final class Init {

    /**
     * Instance of the class
     *
     * @var \KW\UserDisplay\Inc\Init
     */
    protected static $_instance = null;


    /**
     * Store the main instance (singleton)
     *
     * @return \KW\UserDisplay\Inc\Init
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

        // Run the setup
        // $this->load_classes();
        $this->set_locale();

        // Init hook
        add_action('init', array( $this, 'on_init'));

        // Register shortcodes:
        // Since 2.0 done in ShortcodeHandler class

    }


    /**
     * Load all the needed classes
     *
     * @return void
     */
    public function load_classes() {

        // Define names
        $class_names = array(
            'shortcode-handler',
            'userdata',
            'template-handler',
            'admin-page',
        );

        // Iterate and include all files
        foreach ($class_names as $class_name) {
            require_once KW_USERDISPLAY_PLUGIN_PATH . 'includes/class-kw-userdisplay-' . $class_name . '.php';
        }
    }


    /**
     * Enqueue CSS and JS
     *
     * @return void
     */
    public function enqueue_assets() {

        wp_enqueue_style('kw-userdisplay', KW_USERDISPLAY_PLUGIN_URL . 'assets/kw-userdisplay-frontend-style.css', array(), KW_USERDISPLAY_PLUGIN_VERSION, 'all');
        wp_enqueue_script('kw-userdisplay', KW_USERDISPLAY_PLUGIN_URL . 'assets/kw-userdisplay-script.js', array('jquery'), KW_USERDISPLAY_PLUGIN_VERSION, false);

        $ajaxdata = array(
            'url'   => admin_url('admin-ajax.php'),
        );

        wp_localize_script('kw-userdisplay', 'ajaxdata', $ajaxdata);
    }


    /**
     * Set locale and allow i18n of the plugin
     *
     * @return void
     */
    public function set_locale() {

        // Set the locale, use plugin name as domain
        $locale = determine_locale();
        $locale = apply_filters('plugin_locale', $locale, KW_USERDISPLAY_PLUGIN_NAME);

        load_textdomain(
            'kw-userdisplay',
            WP_LANG_DIR . '/kw-userdisplay/kw-userdisplay-' . $locale . '.mo'
        );

        load_plugin_textdomain(
            'kw-userdisplay',
            false,
            KW_USERDISPLAY_PLUGIN_NAME . '/languages/'
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
