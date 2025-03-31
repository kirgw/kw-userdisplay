<?php
// Example of the UserData class
namespace KW\UserDisplay\Inc;

class UserData {


    /**
     * Get the global fields
     * Reads the selected meta fields from the settings page and sets them as the global fields.
     *
     * @return array List of fields to retrieve for users.
     */
    public static function get_global_fields(): array {

        $default_options = array('user_login', 'user_email', 'display_name');
        $options = get_option('kw_user_display_selected_meta_fields', array());

        return !empty($options) ? $options : $default_options;
    }

    /**
     * Get user data for a single user.
     *
     * @param int $user_id User ID.
     * @return array array of user data (empty if user not found).
     */
    public static function get_user_data(int $user_id): array {

        // Placeholder
        $user_data = array();

        $global_fields = self::get_global_fields();

        // Default data
        $user = get_userdata($user_id);

        // // Meta data
        // $user_meta = get_user_meta($user_id);

        if (!$user) {
            error_log(KW_USERDISPLAY_SLUG . " error: Invalid user ID {$user_id}");
            return $user_data;
        }

        foreach ($global_fields as $field) {

            // Check if the field exists as a property in the user object
            if (isset($user->$field)) {
                $user_data[$field] = esc_html($user->$field);
            }

            else {
                // If not a property, try to get it as user meta
                $meta_value = get_user_meta($user_id, $field, true);

                // Check if a meta value was found
                if (!empty($meta_value)) {
                    $user_data[$field] = esc_html($meta_value);
                }
                // else {
                //     // Optionally, handle cases where the meta field is not found: null or skip
                //     // $user_data[$field] = null;
                // }
            }
        }

        return $user_data;
    }


    /**
     * Get user IDs based on the parameters
     *
     * @param array $args {
     *     @type string $orderby  Field to order by (default: 'user_login').
     *     @type string $order    Order direction (ASC/DESC, default: 'ASC').
     *     @type string $role     Role to filter by (default: '' for all roles).
     *     @type int    $number   Number of users per page (default: 100).
     *     @type int    $offset   Pagination offset (default: 0).
     * }
     * @return array {
     *     @type array  $users       List of user data.
     *     @type int    $total_users Total users matching query.
     * }
     */
    public static function get_users(array $args = []): array {

        // Apply defaults to $args
        $defaults = array(
            'orderby' => 'user_login',
            'order'   => 'ASC',
            'role'    => '', // no role filter
            'number'  => 100, // default limit
            'offset'  => 0,
            // 'fields'  => [], // Array of meta fields to retrieve: not needed anymore
        );

        // Validate orderby to prevent invalid SQL queries
        $allowed_orderby = ['user_login', 'user_email', 'display_name', 'ID', 'role'];
        if (!in_array($args['orderby'], $allowed_orderby, true)) {
            $args['orderby'] = 'user_login';
        }

        $args = wp_parse_args($args, $defaults);

        // Build the WP_User_Query args
        $query_args = array(
            'orderby' => $args['orderby'],
            'order'   => $args['order'],
            'number'  => $args['number'],
            'offset'  => $args['offset'],
            'fields'  => ['ID', 'user_login', 'user_email', 'roles'],
        );

        // Check the role filter
        if (!empty($args['role']) && $args['role'] !== 'all') {
            $query_args['role'] =  $args['role'];
        }

        // TODO:
        // Maybe cache the request results
        // $cache_key = md5(json_encode($query_args));
        // $cache_key = 'kw_userdisplay_users_' . md5(serialize($args));
        // $cached = get_transient($cache_key);
        // if ($cached) return $cached;
        // // ... (after query)
        // set_transient($cache_key, $formatted_users, HOUR_IN_SECONDS);

        $user_query = new \WP_User_Query($query_args);
        $users = $user_query->get_results();

        // Get the total number of users
        $total_users = $user_query->get_total();

        $formatted_users = [];
        $user_ids = [];

        foreach ($users as $user) {

            // Saving ids
            $user_ids[] = $user->ID;

            // Saving some data (redundant right now!)
            $formatted_users[$user->ID] = array(
                'user_login' => $user->user_login,
                'user_email' => $user->user_email,
                'role'       => !empty($user->roles) ? array_shift($user->roles) : '',
            );
        }

        // Saving the totals (might be needed for sorting)
        return array(
            'users'       => $formatted_users,
            'user_ids'    => $user_ids,
            'total_users' => $total_users,
        );
    }


}