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
 * Core class, set as singleton instance
 *
 * @package    KWP_UserList
 */
final class KWP_UserList {

    public $version;
    public $plugin_name;
    public $plugin_path;
    public $plugin_url;

    // Store the instance
    protected static $_instance = null;
    
    /**
     * Main plugin instance, onle one
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
     * __construct
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
        $this->run();

        // Some hooks
        add_action('init', array( $this, 'on_init' ));
        add_action('plugins_loaded', array( $this, 'on_plugins_loaded' ));

        // Add shortcode for the table
        add_shortcode('kwp_userlist', array($this, 'render_table')); 

        // AJAX hooks
        add_action('wp_ajax_reload_table', array($this, 'table_reload'));
        add_action('wp_ajax_nopriv_reload_table', array($this, 'table_no_data'));
    }


    
    /**
     * run
     *
     * @return void
     */
    public function run() {

        $this->load_classes();
        $this->enqueue_assets();
        $this->set_locale();
    }


    /**
     * run
     *
     * @return void
     */
    public function load_classes() {

        // Define names
        $class_names = array(
            'kwp-userlist-user',
            'kwp-userlist-list',
            'kwp-userlist-table',
            'kwp-userlist-import',
        );

        // Iterate and include all files
        foreach ($class_names as $class_name) {
            require_once $this->plugin_path . 'includes/class-' . $class_name . '.php';
        }

        // Additional public file (TODO: is it needed here?)
        require_once $this->plugin_path . 'public/class-kwp-userlist-public.php';


    }

    /**
     * Enqueue css/js
     *
     * @return void
     */
    public function enqueue_assets()
    {
        wp_enqueue_style($this->plugin_name, $this->plugin_url . 'assets/kwp-userlist-style.css', array(), $this->version, 'all');
        wp_enqueue_script($this->plugin_name, $this->plugin_url . 'assets/kwp-userlist-script.js', array('jquery'), $this->version, false);
    }
    
    /**
     * set_locale
     *
     * @return void
     */
    public function set_locale() {

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
     * on_init
     *
     * @return void
     */
    public function on_init() {



        // Handle import call
        if (isset($_GET['kwp-userlist-import'])) {

            $import_type = $_GET['kwp-userlist-import'];

            if (in_array($import_type, array('real', 'random'))) {

                $KWP_UserList_Import = new KWP_UserList_Import($import_type);
                $KWP_UserList_Import->launch();

                unset($KWP_UserList_Import);
                unset($_GET['kwp-userlist-import']);
            }
        }


    }
    
    /**
     * on_plugins_loaded
     *
     * @return void
     */
    public function on_plugins_loaded() {


    }



    /**
     * Render the table
     *
     * @return void
     */
    public function render_table() {
        
        // Set the table
        $KWP_UserList_Table = new KWP_UserList_Table();

        // Pass the variables
        $labels =  $KWP_UserList_Table->table_labels;
        $users_data =  $KWP_UserList_Table->table_data;

        // Get template and render the table with data
        $KWP_UserList_Table->get_template('main');
    }

    public function table_reload() {
        $this->render_table();

    }

    public function table_no_data() {
        $this->render_table();

    }



    
    /**
     * is_allowed
     *
     * @return void
     */
    public static function is_allowed_to_view() {
        return current_user_can(self::allowed_capability());
    }
    
    /**
     * allowed_capability
     *
     * @return void
     */
    public static function allowed_capability() {
        return 'list_users';
    }

}
}