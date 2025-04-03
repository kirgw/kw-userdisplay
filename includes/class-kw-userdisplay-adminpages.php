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
class AdminPages {

    // Pre-define the capability
    private $menu_capability = 'manage_options';

    // Set the base menu slug (also used in sub-pages as a prefix)
    private $menu_slug = 'kw-user-display';

    // Placeholder to store the settings pages
    private $settings_pages = [];


    /**
     * Constructor
     *
     * Sets the properties and adds the action hooks to add menu pages and add options.
     *
     * @return void
     */
    public function __construct() {

        // delete_option('kw_user_display_selected_meta_fields');

        // return;

        $this->settings_pages = [
            'main' => [
                'page_title' => 'KW UserDisplay Settings',
                'menu_title' => 'KW UserDisplay',
                'capability' => $this->menu_capability,
                'menu_slug'  => $this->menu_slug,
                // 'callback'   => array($this, 'render_admin_page_settings'), // NO object! (too big)
                'callback'   => 'render_admin_page_settings',
                'icon_url'   => 'dashicons-admin-users',
                // 'position'   => 55, // not forcing any position
            ],
            'children' => [
                [
                    'page_title' => 'Settings',
                    'menu_title' => 'Settings',
                    'capability' => $this->menu_capability,
                    'menu_slug'  => $this->menu_slug, // . '-settings',
                    'callback'   => 'render_admin_page_settings',
                ],
                [
                    'page_title' => 'Shortcode Builder',
                    'menu_title' => 'Shortcode Builder',
                    'capability' => $this->menu_capability,
                    'menu_slug'  => $this->menu_slug . '-shortcode-builder',
                    'callback'   => 'render_admin_page_shortcode_builder',
                ]
            ]
        ];

        // Add menu pages
        add_action('admin_menu', array($this, 'admin_pages_init'));

        // Add options
        add_action('admin_init', array($this, 'admin_options_init'));

        // Enqueue admin styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
    }



    /**
     * Enqueue admin styles
     *
     * Checks admin page (containing the menu slug),
     * loads the admin styles for that page.
     *
     * @return void
     */
    public function enqueue_admin_styles() {

        // Check if we're on our admin page
        $screen = get_current_screen();

        // Load for all admin pages containing your menu slug (including subpages)
        if (strpos($screen->id, $this->menu_slug) !== false) {

            wp_enqueue_style(
                'kw-userdisplay-admin-styles',
                KW_USERDISPLAY_PLUGIN_URL . 'assets/kw-userdisplay-admin-style.css',
                array(),
                KW_USERDISPLAY_PLUGIN_VERSION
            );
        }
    }


    /**
     * Add admin menu pages
     *
     * Adds the main plugin page and its sub-pages.
     *
     * @return void
     */
    public function admin_pages_init() {

        // Add main page
        $page = $this->settings_pages['main'];

        add_menu_page(
            __($page['page_title'], $this->text_domain),
            __($page['menu_title'], $this->text_domain),
            $page['capability'],
            $page['menu_slug'],
            array($this, $page['callback']), // add a hook to cb name here
            $page['icon_url'],
        );

        // Add sub-pages
        foreach ($this->settings_pages['children'] as $subpage) {

            add_submenu_page(
                $page['menu_slug'],
                __($subpage['page_title'], $this->text_domain),
                __($subpage['menu_title'], $this->text_domain),
                $subpage['capability'],
                $subpage['menu_slug'],
                array($this, $subpage['callback']),
            );
        }
    }


    /**
     * Initialize admin options
     *
     * Registers settings and adds settings sections for user meta fields.
     *
     * @return void
     */

    public function admin_options_init() {

        // Add settings
        register_setting(
            'kw_user_display_settings_group',
            'kw_user_display_selected_meta_fields',
            array( // args array
                'default'           => array('user_login', 'nickname', 'user_email'),
                'sanitize_callback' => array($this, 'sanitize_selected_meta_fields'),
            ),
        );

        // Add WP default fields section
        add_settings_section(
            'kw_user_display_wp_fields_section',
            __('Default User Fields', $this->text_domain),
            array($this, 'render_default_fields_section_description'),
            $this->menu_slug
        );

        // Add custom fields section
        add_settings_section(
            'kw_user_display_custom_fields_section',
            __('Custom User Meta Fields', $this->text_domain),
            array($this, 'render_custom_fields_section_description'),
            $this->menu_slug
        );

        // Get Default WP Fields
        $default_fields = $this->get_default_meta_fields();

        // Get Default WooCommerce Fields
        $default_ecomm_fields = $this->get_default_woocommerce_meta_fields();

        // Add settings fields for default fields
        foreach ($default_fields as $meta_key => $field_title) {
            $this->add_settings_field(
                $meta_key,
                $field_title,
                'wp_fields_section'
            );
        }

        // TODO: allow selecting WC fields
        if (false) {
            // Add settings fields for default WooCommerce fields
            foreach ($default_ecomm_fields as $meta_key => $field_title) {
                $this->add_settings_field(
                    $meta_key,
                    $field_title,
                    'wp_fields_section'
                );
            }
        }

        // Add settings fields for custom meta fields
        $custom_meta_fields = $this->get_user_meta_fields(); // use alt version maybe?

        // Filter out default fields // NO need - already done in SQL
        // $custom_meta_fields   = array_diff($custom_meta_fields, array_keys($default_fields));

        // Add settings fields for custom meta fields
        foreach ($custom_meta_fields as $meta_field) {
            $this->add_settings_field(
                $meta_field,
                $meta_field,
                'custom_fields_section'
            );
        }

        // Protection from empty - add defaults
        $existing_options = get_option('kw_user_display_selected_meta_fields', array());
        if (empty($existing_options)) {
            $existing_options = array_unique(array_merge($existing_options, array('user_login', 'nickname', 'user_email')));
            update_option('kw_user_display_selected_meta_fields', $existing_options);
        }

        // TODO: Allow adding custom fields by hand
        if (false) {

            register_setting('kw_user_display_settings_group', 'kw_user_display_custom_fields');

            add_settings_field(
                'kw_user_display_custom_fields',
                'Enter Your Custom Fields (separated by commas):',
                array($this, 'render_custom_fields_input'), // New render function
                $this->menu_slug,
                'kw_user_display_custom_fields_section'
            );
        }
    }


    /** TODO:
     * Adds settings fields for third-party plugins' meta fields such as WooCommerce and ACF
     *
     * @return void
     */

    public function add_third_party_meta_fields() {

        // DB settings (possible structure):
        // kw_user_display_wp_fields
        // kw_user_display_woocommerce_fields
        // kw_user_display_acf_fields
        // kw_user_display_custom_fields

        // WooCommerce Section
        if (is_plugin_active('woocommerce/woocommerce.php')) {

            register_setting('kw_user_display_settings_group', 'kw_user_display_woocommerce_fields');

            add_settings_section(
                'kw_user_display_woocommerce_section',
                'WooCommerce Fields',
                array($this, 'section_info_callback'),
                $this->menu_slug
            );
            add_settings_field(
                'kw_user_display_woocommerce_fields',
                'Select WooCommerce Fields to Display:',
                array($this, 'render_woocommerce_fields_checkboxes'), // New render function
                $this->menu_slug,
                'kw_user_display_woocommerce_section'
            );
        }

        // ACF Section
        if (defined('ACF')) {  // Check if ACF is defined as a constant

            register_setting('kw_user_display_settings_group', 'kw_user_display_acf_fields');

            add_settings_section(
                'kw_user_display_acf_section',
                'ACF Fields',
                array($this, 'section_info_callback'),
                $this->menu_slug
            );

            add_settings_field(
                'kw_user_display_acf_fields',
                'Select ACF Fields to Display:',
                array($this, 'render_acf_fields_checkboxes'), // New render function
                $this->menu_slug,
                'kw_user_display_acf_section'
            );
        }
    }


    /**
     * Render admin page for settings
     *
     * @return void
     */
    public function render_admin_page_settings() {
        // require_once KW_USERDISPLAY_PLUGIN_PATH . 'templates/admin-page-settings.php';
        require_once KW_USERDISPLAY_PLUGIN_PATH . 'templates/admin-page.php';
    }


    /**
     * Render admin page for shortcode builder
     *
     * @return void
     */
    public function render_admin_page_shortcode_builder() {
        require_once KW_USERDISPLAY_PLUGIN_PATH . 'templates/admin-page-shortcode-builder.php';
    }


    /**
     * Render description for default fields section
     *
     * @return void
     */
    public function render_default_fields_section_description() {
        echo '<p>Select the default user fields to include in template.</p>';
        echo '<p><b>Only default fields displayed with more readable label.</b></p>';
    }


    /**
     * Render description for custom fields section
     *
     * @return void
     */
    public function render_custom_fields_section_description() {
        echo '<p>Select the custom user meta fields to include in template.</p>';
    }


    /**
     * Get array of default WordPress user fields with readable names
     *
     * @return array
     */
    public function get_default_meta_fields() {

        return array(
            'user_login'                         => 'Username',
            'first_name'                         => 'First Name',
            'last_name'                          => 'Last Name',
            'nickname'                           => 'Nickname',
            'user_email'                         => 'Email',
            'user_url'                           => 'Website',
            'description'                        => 'Biographical Info',
            'show_admin_bar_front'               => 'Show Admin Bar',
            'sslverify'                          => 'SSL Usage',
            'comment_shortcuts'                  => 'Keyboard Shortcuts for Comments',
            'admin_color'                        => 'Admin Color Scheme',
            'locale'                             => 'Locale',
            'rich_editing'                       => 'Rich Editing',
            'syntax_highlighting'                => 'Syntax Highlighting',
            'use_ssl'                            => 'Force SSL',
            'last_update'                        => 'Last Update',
        );
    }


    /**
     * Retrieves an array of user meta keys that should be excluded from display,
     * because they are internal, configuration-related, or otherwise not relevant
     * for front-end display or user editing.
     *
     * @return array A simple array of meta keys to exclude.
     */
    private function get_excluded_meta_fields() {
        return array(
            'wp_capabilities',
            'wp_user_level',
            'dismissed_wp_pointers',
            'show_welcome_panel',
            'wp_dashboard_quick_press_last_post_id',
            'wp_media_library_mode',
            'wp_persisted_preferences',
            'wp_user-settings',
            'wp_user-settings-time',
            'nav_menu_recently_edited',
            'last_update',
            'managenav-menuscolumnshidden',
        );
    }


    /**
     * Get array of default WooCommerce user fields with readable names
     *
     * @return array
     */
    public function get_default_woocommerce_meta_fields() {
        return array(
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
    }


    /**
     * Add a single settings field (wrapper for default add_settings_field() )
     *
     * @param string $meta_field The meta field key.
     * @param string $field_title The title of the field.
     * @param string $section The section to add the field to.
     */
    public function add_settings_field($meta_field, $field_title, $section) {

        add_settings_field(
            'kw_user_display_meta_field_' . sanitize_title($meta_field),
            esc_html($field_title),
            array($this, 'render_meta_field_checkbox'),
            $this->menu_slug,
            'kw_user_display_' . $section,
            array('meta_field' => $meta_field)
        );
    }


    /**
     * Get all user meta fields
     *
     * @return array
     */

    public function get_user_meta_fields() {

        global $wpdb;

        // Get default meta keys from helper functions
        $default_meta_fields = $this->get_default_meta_fields();
        $excluded_meta_fields = $this->get_excluded_meta_fields();
        $default_ecomm_meta_fields = $this->get_default_woocommerce_meta_fields();

        // Merge excluded keys with default keys - to filter out them all
        $all_default_keys = array_merge(array_keys($default_meta_fields), $excluded_meta_fields, array_keys($default_ecomm_meta_fields));

        // Format for SQL NOT IN clause
        $formatted_default_keys = "'" . implode("','", array_map('esc_sql', $all_default_keys)) . "'";

        // SQL query
        $sql = $wpdb->prepare("
            SELECT DISTINCT meta_key
            FROM {$wpdb->usermeta}
            WHERE meta_key NOT LIKE '\_%'
            AND meta_key NOT LIKE 'closedpostboxes_%'
            AND meta_key NOT LIKE 'manageedit-%'
            AND meta_key NOT LIKE 'meta-box-order_%'
            AND meta_key NOT LIKE 'metaboxhidden_%'
            AND meta_key NOT LIKE 'session_tokens'
            AND meta_key NOT IN ($formatted_default_keys)
            ORDER BY meta_key
        ");

        $meta_keys = $wpdb->get_col($sql);
        return $meta_keys;
    }



    /**
     * Get all user meta fields
     *
     * @return array
     */
    public function get_user_meta_fields_old() {

        global $wpdb;

        // Exclude the standard admin related meta keys + the internal keys
        $sql = $wpdb->prepare("
            SELECT DISTINCT meta_key
            FROM {$wpdb->usermeta}
            WHERE meta_key NOT LIKE '\_%'
            AND meta_key NOT LIKE 'closedpostboxes_%'
            AND meta_key NOT LIKE 'manageedit-%'
            AND meta_key NOT LIKE 'meta-box-order_%'
            AND meta_key NOT LIKE 'metaboxhidden_%'
            AND meta_key NOT LIKE 'session_tokens'
            ORDER BY meta_key
        ");

        $meta_keys = $wpdb->get_col($sql);
        return $meta_keys;
    }


    /**
     * Method to exclude certain meta fields from the query
     *
     * @return array Array of meta keys to exclude
     */
    private function get_excluded_meta_keys() {
        return array(
            '\_%',
            'closedpostboxes_%',
            'manageedit-%',
            'meta-box-order_%',
            'metaboxhidden_%',
            'session_tokens',
        );
    }


    /**
     * Get all user meta fields - Alt version, more flexible
     *
     * @return array
     */
    public function get_user_meta_fields_alt() {
        global $wpdb;
        $sql = "SELECT DISTINCT meta_key FROM {$wpdb->usermeta}";

        $excluded_meta_keys = $this->get_excluded_meta_keys();

        if (!empty($excluded_meta_keys)) {
            $sql .= " WHERE ";

            $conditions = [];
            foreach ($excluded_meta_keys as $key) {
                $conditions[] = "meta_key NOT LIKE '" .  $key . "'";
            }
            $sql .= implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY meta_key";
        $sql = $wpdb->prepare($sql);
        $meta_keys = $wpdb->get_col($sql);
        return $meta_keys;
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

        echo '
            <input type="checkbox" name="kw_user_display_selected_meta_fields[]" value="' . esc_attr($meta_field) . '" ' . $checked . ' />' .
            '<code>' . esc_attr($meta_field) . // '&nbsp;' . // some space
            '<span class="dashicons ' . ($checked ? 'dashicons-yes' : 'dashicons-no-alt') . // icon
            '"></span></code>';
        // Get value from the first user available (for display) - disabling for now
        // $meta_value_display = $this->render_meta_field_sample_value($meta_field);
        // echo '<code>' . $meta_value_display . '</code>'; // Display the value inline
    }


    public function render_meta_field_sample_value($meta_key) {

        // Get value from the first user available
        $users = get_users(array('number' => 1));

        if ($users) {
            $user = $users[0];
            // Get the meta value.  Handle user properties or meta values.
            if (property_exists($user, $meta_key)) {
                $meta_value = $user->$meta_key;  // Access as property
            } else {
                $meta_value = get_user_meta($user->ID, $meta_key, true);
            }

            // Sanitize the meta value for display.  Important!
            $meta_value_display = esc_attr(maybe_serialize($meta_value)); // Serialize, then escape
        } else {
            $meta_value_display = 'No users found.';
        }

        return $meta_value_display;
    }



    /**
     * Sanitize selected meta fields
     *
     * @param array $input The array of selected meta fields
     * @return array The sanitized array of selected meta fields
     */
    public function sanitize_selected_meta_fields($input) {

        $sanitized_fields = array();

        if (is_array($input)) {
            foreach ($input as $field) {
                $sanitized_fields[] = sanitize_text_field($field);
            }
        } elseif (is_string($input)) {
            // Handle the case where a single string is passed
            $sanitized_fields[] = sanitize_text_field($input);
        }

        // If not an array or string then just return an empty array
        return $sanitized_fields;
    }
}



