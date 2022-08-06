<?php

/**
 * The file defines the core plugin class
 *
 * @package    KW\UserDisplay
 * @subpackage KW\UserDisplay\Inc
 */

namespace KW\UserDisplay\Inc;

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Main plguin class
 *
 * @class KW_UserDisplay
 */
final class Init {
    
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

        // Set the properties
        $this->version = KW_USERDISPLAY_PLUGIN_VERSION;
        $this->plugin_name = KW_USERDISPLAY_PLUGIN_NAME;
        $this->plugin_path = KW_USERDISPLAY_PLUGIN_PATH;
        $this->plugin_url = KW_USERDISPLAY_PLUGIN_URL;

        // Run the setup
        $this->load_classes();
        $this->set_locale();

        // Init hook
        add_action('init', array( $this, 'on_init'));

        // Add shortcode for the table
        add_shortcode('kw_userdisplay', array($this, 'content_load'));

        // AJAX hooks
        if (wp_doing_ajax()) {

            // admin AJAX handler
            add_action('wp_ajax_reload_table', array($this, 'content_reload'));

            // public AJAX handler, disabled for now
            // add_action('wp_ajax_nopriv_reload_table', array($this, 'content_reload')); 
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
            'user',
            'table',
            'import',
            'admin-page',
        );

        // Iterate and include all files
        foreach ($class_names as $class_name) {
            require_once $this->plugin_path . 'includes/class-kw-userdisplay-' . $class_name . '.php';
        }
    }


    /**
     * Enqueue CSS and JS
     *
     * @return void
     */
    public function enqueue_assets() {

        wp_enqueue_style('kw-userdisplay', $this->plugin_url . 'assets/kw-userdisplay-style.css', array(), $this->version, 'all');
        wp_enqueue_script('kw-userdisplay', $this->plugin_url . 'assets/kw-userdisplay-script.js', array('jquery'), $this->version, false);

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
        $locale = apply_filters('plugin_locale', $locale, $this->plugin_name);

        load_textdomain(
            'kw-userdisplay',
            WP_LANG_DIR . '/kw-userdisplay/kw-userdisplay-' . $locale . '.mo'
        );

        load_plugin_textdomain(
            'kw-userdisplay',
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
        if (isset($_GET['kw-userdisplay-import'])) {

            // Extract the type
            $import_type = $_GET['kw-userdisplay-import'];

            // Check what's passed
            if (in_array($import_type, array('real', 'random'))) {

                // Initialize import
                $KW_UserDisplay_Import = new \KW\UserDisplay\Inc\UsersImport($import_type);

                // Perform it on the database
                $KW_UserDisplay_Import->launch();

                // Remove it
                unset($KW_UserDisplay_Import);
                unset($_GET['kw-userdisplay-import']);
            }
        }

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
    public static function is_allowed_to_view() {
        return current_user_can(self::allowed_capability());
    }


    /**
     * Render the content (default view)
     *
     * @return void
     */
    public function content_load() {

        // Start the buffering
        ob_start();

        // Get template and render the table with data
        // TODO - allow options
        $this->get_template('table', 'main');

        // Pass the buffer
        return ob_get_clean();   
    }


    /**
     * Render the part of the content - AJAX reload
     *
     * @return void
     */
    public function content_reload() {

        // Get the posted data: array $kw_state('sortby', 'sorting', 'filter', 'page')
        if (isset($_POST['kw_state'])) {
            $kw_state = $_POST['kw_state'];
        }

        // TODO - only table is handled now, need to change for more general usage
        $content_type = 'table';

        if ($content_type = 'table') {
            // Call the table to reload
            \KW\UserDisplay\Inc\Table::table_reload($kw_state);
        }
    }


    /**
     * Include the templates
     *
     * @param string $template_name
     * @param string $template_type
     * @return void
     */
    public function get_template($template_name = 'table', $template_type = 'main') {

        if ($template_name == 'table') {
            
            // Initialize the table and prepare params
            $Table = new \KW\UserDisplay\Inc\Table();

            $params = array(
                'object'     => $Table,
                'labels'     => $Table->table_labels,
                'users_data' => $Table->table_data,
            );
        }

        // Check if allowed to display
        $allowed = !empty($params['users_data']) && self::is_allowed_to_view();

        // Just an outline
        // TODO - improve this
        $templates = array(
            'list'        => array('files' => 1),
            'single-user' => array('files' => 1),
            'table'       => array('files' => 3),
        );

        // Set the files amount
        $template_files = $templates[$template_name]['files'];

        // Start building the template
        $include_stack = array('templates/container-header.php');

        // Set the path
        $predefined_template_path = 'templates/' . $template_name . '-' . $template_type . '/' . $template_name . '-' . $template_type;

        // Include the template if allowed
        if ($allowed === true) {

            if ($template_files == 1) {
                $include_stack[] =  $predefined_template_path . '.php';
            }

            else if ($template_files == 3) {
                $include_stack[] = $predefined_template_path . '-header.php';
                $include_stack[] = $predefined_template_path . '-body.php';
                $include_stack[] = $predefined_template_path . '-footer.php';
            }
        }

        // Wrap the container
        $include_stack[] = 'templates/container-footer.php';

        // Check the files and include
        foreach ($include_stack as $path) {

            $filename = KW_USERDISPLAY_PLUGIN_PATH . $path;

            if (file_exists($filename)) {
                include $filename;
            }
        }

    }


}
