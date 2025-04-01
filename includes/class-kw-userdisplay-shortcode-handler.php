<?php

/**
 * Template Handler Class
 *
 * @package KW\UserDisplay\Inc
 */

namespace KW\UserDisplay\Inc;

// Security check - exit if accessed directly
defined('ABSPATH') || exit;


/**
 * Template Handler Class
 */

class ShortcodeHandler {


    /**
     * Constructor to register the shortcode handler
     *
     * Registers the shortcodes
     *
     * @return void
     */
    public function __construct() {
        add_shortcode('kgwp_user_data', [$this, 'handle_shortcode']);

        // TODO: maybe add type-wrappers?
        // add_shortcode('kgwp_udata_card', [$this, 'handle_shortcode']);
        // add_shortcode('kgwp_udata_card_flip', [$this, 'handle_shortcode']);
        // add_shortcode('kgwp_udata_list', [$this, 'handle_shortcode']);
        // add_shortcode('kgwp_udata_table', [$this, 'handle_shortcode']);
        // add_shortcode('kgwp_udata_leaderboard', [$this, 'handle_shortcode']);
    }


    /**
     * Handles the [kgwp_user_data] shortcode to display user information.
     *
     * @param array $atts Shortcode attributes
     * @return string The requested user field value or empty string if not found/accessible.
     */
    public function handle_shortcode($atts, $use_caching = false) {

        // Set default attributes and merge with user-provided attributes
        $atts = shortcode_atts([
            'type'        => 'card',       // Display type (card/list/etc)
            'variant'     => '',           // Custom template variant
            'uid'         => get_current_user_id(), // Default to current user
            'uids'        => '',           // Comma-separated list of user IDs (for multi-user display)
            'role'        => '',           // Filter by user role
            'meta_key'    => '',           // Custom meta field to retrieve
            // 'limit'    => -1,        // Number of users to show (-1 = all) // TODO: handle -1 later
        ], $atts);

        if ($use_caching) { // Check cache if activated

            // Generate unique cache key based on attributes
            $cache_key = 'user_data_' . md5(serialize($atts));
            $cached    = get_transient($cache_key);

            // Return cached version if available
            if ($cached) {
                return $cached;
            }
        }

        // Fetch user data using parameters
        $user_data = new UserData($atts);
        $data      = $user_data->get_data();

        // Process template with retrieved data
        $template_handler = new TemplateHandler();
        $output = $template_handler->get_template(
            $atts['type'],    // Template type
            $atts['variant'], // Template variant
            $data,            // User data array
        );

        if ($use_caching) { // Maybe cache full output
            // Cache rendered output for 1 hour to optimize performance
            set_transient($cache_key, $output, HOUR_IN_SECONDS);
        }

        return $output;
    }


}
new ShortcodeHandler();