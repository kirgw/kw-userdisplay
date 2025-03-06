<?php

/**
 * The file defines the settings class
 *
 * @package    KW\UserDisplay
 * @subpackage KW\UserDisplay\Inc
 */

namespace KW\UserDisplay\Inc;

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Admin pages class
 *
 * @class KW\UserDisplay\Inc\AdminPages
 */

// includes/class-kw-userdisplay-admin-page.php

/**
 * The file defines the settings class
 */
class AdminPages {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }


    /**
     * Add admin menu
     *
     * @return void
     */
    public function add_admin_menu() {
        $pages = array(
            array(
                'page_title' => 'KW UserDisplay Settings',
                'menu_title' => 'KW UserDisplay',
                'capability' => 'manage_options',
                'menu_slug'  => 'kw-user-display',
                'callback'   => array($this, 'render_admin_page'),
                'icon_url'   => 'dashicons-admin-users',
                'position'   => 55,
            ),
        );

        foreach ($pages as $page) {
            add_menu_page(
                $page['page_title'],
                $page['menu_title'],
                $page['capability'],
                $page['menu_slug'],
                $page['callback'],
                $page['icon_url'],
                $page['position']
            );
        }
    }


    /**
     * Render admin page
     *
     * @return void
     */
    public function render_admin_page() {
        require_once KW_USERDISPLAY_PLUGIN_PATH . 'templates/admin-page.php';
    }


    /**
     * Register settings
     *
     * @return void
     */
    public function register_settings() {

        add_settings_section(
            'kw_user_display_settings_section',
            'User Meta Fields',
            array($this, 'render_settings_section'),
            'kw-user-display'
        );

        register_setting(
            'kw_user_display_settings_group',
            'kw_user_display_selected_meta_fields'
        );

        $this->add_settings_field_for_meta_fields();
    }

    /**
     * Render settings section
     *
     * @return void
     */
    public function render_settings_section() {
        echo '<p>Select the user meta fields to display.</p>';
        echo '<p><b>Note: Defaults fields will be displayed with a human-readable title.</b></p>';
    }


    /**
     * Method to exclude certain meta fields from the query.
     *
     * @return array Array of meta keys to exclude.
     */
    private function get_excluded_meta_keys() {
        return array(
            '_%',
            'closedpostboxes_%',
            'manageedit-%',
            'meta-box-order_%',
            'metaboxhidden_%',
            'session_tokens',
        );
    }

    /**
     * Get all user meta fields
     *
     * @return array
     */
    public function get_user_meta_fields() {
        global $wpdb;

        $excluded_meta_keys = $this->get_excluded_meta_keys();

        $sql = "
            SELECT DISTINCT meta_key
            FROM {$wpdb->usermeta}
            WHERE 1=1"; // Start building the SQL query

        foreach ($excluded_meta_keys as $key) {
            $sql .= " AND meta_key NOT LIKE '" . esc_sql($key) . "'";
        }

        $sql .= " ORDER BY meta_key";

        $meta_keys = $wpdb->get_col($sql);

        return $meta_keys;
    }

    /**
     * Add settings field for meta fields
     *
     * @return void
     */
    public function add_settings_field_for_meta_fields() {
        $meta_fields = $this->get_user_meta_fields();

        // Define default user meta fields and their human-readable titles
        $default_meta_fields = array(
            'user_login'              => 'Username',
            'first_name'              => 'First Name',
            'last_name'               => 'Last Name',
            'nickname'                => 'Nickname',
            'user_email'              => 'Email',
            'user_url'                => 'Website',
            'description'             => 'Biographical Info',
            'show_admin_bar_front'    => 'Show Admin Bar',
            'sslverify'               => 'SSL Usage',
            'comment_shortcuts'       => 'Keyboard Shortcuts for Comments',
            'billing_first_name'      => 'Billing First Name',
            'billing_last_name'       => 'Billing Last Name',
            'billing_company'         => 'Billing Company',
            'billing_address_1'       => 'Billing Address 1',
            'billing_address_2'       => 'Billing Address 2',
            'billing_city'            => 'Billing City',
            'billing_postcode'        => 'Billing Postcode',
            'billing_country'         => 'Billing Country',
            'billing_state'           => 'Billing State',
            'billing_email'           => 'Billing Email',
            'billing_phone'           => 'Billing Phone',
            'shipping_first_name'     => 'Shipping First Name',
            'shipping_last_name'      => 'Shipping Last Name',
            'shipping_company'        => 'Shipping Company',
            'shipping_address_1'      => 'Shipping Address 1',
            'shipping_address_2'      => 'Shipping Address 2',
            'shipping_city'           => 'Shipping City',
            'shipping_postcode'       => 'Shipping Postcode',
            'shipping_country'        => 'Shipping Country',
            'shipping_state'          => 'Shipping State',

        );

        foreach ($meta_fields as $meta_field) {
            $field_title = $meta_field; // Default title

            // Check if it's a default field and use the human-readable title if available
            if (array_key_exists($meta_field, $default_meta_fields)) {
                $field_title = $default_meta_fields[$meta_field];
            }

            add_settings_field(
                'kw_user_display_meta_field_' . sanitize_title($meta_field),
                $field_title,
                array($this, 'render_meta_field_checkbox'),
                'kw-user-display',
                'kw_user_display_settings_section',
                array('meta_field' => $meta_field)
            );
        }
    }

    /**
     * Render meta field checkbox
     *
     * @param array $args Arguments passed to the callback
     * @return void
     */
    public function render_meta_field_checkbox($args) {
        $meta_field = $args['meta_field'];

        $options = get_option('kw_user_display_selected_meta_fields', array()); // Add default value

        $checked = (is_array($options) && in_array($meta_field, $options)) ? 'checked' : '';

        // Get value from the first user available.
        $users = get_users(array('number' => 1));
        $meta_value = '';
        if (! empty($users)) {
            $user       = $users[0];
            $meta_value = get_user_meta($user->ID, $meta_field, true);
        }


        //Sanitize html
        $meta_value_html = esc_html(maybe_serialize($meta_value));

        echo '<label>';
        echo '<input type="checkbox" name="kw_user_display_selected_meta_fields[]" value="' . esc_attr($meta_field) . '" ' . $checked . ' /> ';
        echo esc_html($meta_field) . ': <code>' . $meta_value_html . '</code>';
        echo '</label><br>';
    }
}

new AdminPages();

