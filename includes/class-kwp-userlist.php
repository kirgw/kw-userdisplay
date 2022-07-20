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
        $this->set_locale();

        // Init hook
        add_action('init', array( $this, 'on_init'));

        // Add shortcode for the table
        add_shortcode('kwp_userlist', array($this, 'render_table')); 

        // AJAX hooks
        if (wp_doing_ajax()) {
            add_action('wp_ajax_reload_table', array($this, 'table_reload'));
            // public AJAX handler isn't needed yet
            // add_action('wp_ajax_nopriv_reload_table', array($this, 'table_reload')); 
        }
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

        wp_enqueue_style('kwp-userlist', $this->plugin_url . 'assets/kwp-userlist-style.css', array(), $this->version, 'all');
        wp_enqueue_script('kwp-userlist', $this->plugin_url . 'assets/kwp-userlist-script.js', array('jquery'), $this->version, false);

        $ajaxdata = array(
            'url'   => admin_url('admin-ajax.php'),
            //'nonce' => wp_create_nonce('kwp-userlist'), // not used atm
        );

        wp_localize_script('kwp-userlist', 'ajaxdata', $ajaxdata);
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
            'kwp-userlist',
            WP_LANG_DIR . '/kwp-userlist/kwp-userlist-' . $locale . '.mo'
        );

        load_plugin_textdomain(
            'kwp-userlist',
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

        // Load assets
        $this->enqueue_assets();
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


    /**
     * Render the table (default view)
     *
     * @return void
     */
    public function render_table() {

        // Initialize the table
        $KWP_UserList_Table = new KWP_UserList_Table();

        // Get template and render the table with data
        $KWP_UserList_Table->get_template('main');
    }


    /**
     * Table data (tbody) AJAX reload
     *
     * @return void
     */
    public function table_reload() {

        // kwp_state:
        // - sortby
        // - sorting
        // - filter
        // - page

        // Get the data passed
        if (isset($_POST['kwp_state'])) {
            $kwp_state = $_POST['kwp_state'];
        }

        // Set the params
        $sort_by = ($kwp_state['sortby'] === 'email') ? 'user_email' : 'user_login';
        $sort_type = $kwp_state['sorting'];

        // Initialize the table
        $KWP_UserList_Table = new KWP_UserList_Table($sort_type, $sort_by, $kwp_state['filter'], $kwp_state['page']);

        // Get data
        $table_body_html = $KWP_UserList_Table->get_table_body_html($KWP_UserList_Table->table_data);

        $result = json_encode(array(
            'table_html' => $table_body_html,   
        ));

        echo $result;
        wp_die();
    }


}
}