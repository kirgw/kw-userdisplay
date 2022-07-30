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
            add_action('wp_ajax_reload_table', array($this, 'content_reload'));
            // public AJAX handler isn't needed yet
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
            'kw-userdisplay-user',
            'kw-userdisplay-table',
            'kw-userdisplay-import',
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
     * Render the content (default view)
     *
     * @return void
     */
    public function content_load() {

        ob_start();

        // Get template and render the table with data
        $this->get_template('table', 'main');

        return ob_get_clean();   
    }


    /**
     * Render the part of the content - AJAX reload
     *
     * @return void
     */
    public function content_reload() {

        // TODO - only table is handled now, rearrange for more general usage

        // kw_state:
        // - sortby
        // - sorting
        // - filter
        // - page

        // Get the data passed
        if (isset($_POST['kw_state'])) {
            $kw_state = $_POST['kw_state'];
        }

        // Set the params
        $sort_by = ($kw_state['sortby'] === 'email') ? 'user_email' : 'user_login';
        $sort_type = $kw_state['sorting'];

        // Initialize the table
        $KW_UserDisplay_Table = new \KW\UserDisplay\Inc\Table($sort_type, $sort_by, $kw_state['filter'], $kw_state['page']);

        // Get data
        $table_body_html = $KW_UserDisplay_Table->get_table_body_html($KW_UserDisplay_Table->table_data);

        $result = json_encode(array(
            'table_html' => $table_body_html,   
        ));

        echo $result;
        wp_die();
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

            // Initialize the table and set the data
            $Table = new \KW\UserDisplay\Inc\Table();
            $labels = $Table->table_labels;
            $users_data = $Table->table_data;
            $table_body_html = $Table->get_table_body_html($users_data);
            
            $data = $table_body_html;
        }

        // Check if allowed to display
        $allowed = !empty($data) && self::is_allowed_to_view();

        // Just an outline
        // TODO - improve this with settings
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
