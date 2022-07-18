<?php

/**
 * The file defines the core plugin class
 *
 * @package    KWP_UserList
 * @subpackage KWP_UserList/includes
 */

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

if (!class_exists('KWP_UserList')) {

/**
 * Main plguin class
 *
 * @class KWP_UserList
 */
final class KWP_UserList {
    
    /**
     * Plugin version
     *
     * @var string
     */
    public $version;

    
    /**
     * Plugin name
     *
     * @var string
     */
    public $plugin_name;

        
    /**
     * Plugin path
     *
     * @var string
     */
    public $plugin_path;

    
    /**
     * Plugin URL
     *
     * @var string
     */
    public $plugin_url;

    /**
     * Instance of the class
     *
     * @var KWP_UserList
     */
    protected static $_instance = null;
    
    /**
     * Store the main instance (singleton)
     *
     * @return KWP_UserList
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

        // Set the properties
        $this->version = KWP_USERLIST_PLUGIN_VERSION;
        $this->plugin_name = KWP_USERLIST_PLUGIN_NAME;
        $this->plugin_path = KWP_USERLIST_PLUGIN_PATH;
        $this->plugin_url = KWP_USERLIST_PLUGIN_URL;

        // Run the setup
        $this->load_classes();
        $this->enqueue_assets();
        $this->set_locale();

        // Init hook
        add_action('init', array( $this, 'on_init'));

        // Plugins loaded hook (TODO: not used)
        add_action('plugins_loaded', array( $this, 'on_plugins_loaded'));

        // Add shortcode for the table
        add_shortcode('kwp_userlist', array($this, 'render_table')); 

        // AJAX hooks
        add_action('wp_ajax_reload_table', array($this, 'table_reload'));
        add_action('wp_ajax_nopriv_reload_table', array($this, 'table_no_data'));
    }


    /**
     * Load all the needed classes
     *
     * @return void
     */
    public function load_classes() {

        // Define names
        $class_names = array(
            'kwp-userlist-user',
            'kwp-userlist-table',
            'kwp-userlist-import',
        );

        // Iterate and include all files
        foreach ($class_names as $class_name) {
            require_once $this->plugin_path . 'includes/class-' . $class_name . '.php';
        }
    }


    /**
     * Enqueue CSS and JS
     *
     * @return void
     */
    public function enqueue_assets() {
        wp_enqueue_style($this->plugin_name, $this->plugin_url . 'assets/kwp-userlist-style.css', array(), $this->version, 'all');
        wp_enqueue_script($this->plugin_name, $this->plugin_url . 'assets/kwp-userlist-script.js', array('jquery'), $this->version, false);
    }
    

    /**
     * Set locale and allow i18n of the plugin 
     *
     * @return void
     */
    public function set_locale() {

        // Set the locale, use plugin name as domain
        $locale = determine_locale();
        $locale = apply_filters('plugin_locale', $locale, $this->plugin_name);

        load_textdomain(
            $this->plugin_name,
            WP_LANG_DIR . '/' . $this->plugin_name . '/' . $this->plugin_name . '-' . $locale . '.mo'
        );

        load_plugin_textdomain(
            $this->plugin_name,
            false,
            $this->plugin_name . '/languages/'
        );
    }


    /**
     * Run some code on init
     *
     * @return void
     */
    public function on_init() {

        // Handle import call
        if (isset($_GET['kwp-userlist-import'])) {

            // Extract the type
            $import_type = $_GET['kwp-userlist-import'];

            // Check what's passed
            if (in_array($import_type, array('real', 'random'))) {

                // Initialize import
                $KWP_UserList_Import = new KWP_UserList_Import($import_type);

                // Perform it on the database
                $KWP_UserList_Import->launch();

                // Remove it
                unset($KWP_UserList_Import);
                unset($_GET['kwp-userlist-import']);
            }
        }

    }
    

    /**
     * Run code after plugins loaded
     *
     * @return void
     */
    public function on_plugins_loaded() {
        // TODO: empty
    }


    /**
     * Render the table
     *
     * @return void
     */
    public function render_table() {

        // Initialize the table
        $KWP_UserList_Table = new KWP_UserList_Table();

        // Pass the variables
        $labels = $KWP_UserList_Table->table_labels;
        $users_data =  $KWP_UserList_Table->table_data;

        // Get template and render the table with data
        $KWP_UserList_Table->get_template('main');
    }


    // TODO
    public function table_reload() {
        $this->render_table();

    }

    // TODO
    public function table_no_data() {
        $this->render_table();

    }


    /**
     * Allowed capability to view the table
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
    public static function is_allowed_to_view() {
        return current_user_can(self::allowed_capability());
    }
    

}
}