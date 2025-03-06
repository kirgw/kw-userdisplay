<?php
// Example of the UserData class
namespace KW\UserDisplay;

class UserData {

    /**
     * get_users
     *
     * @param  mixed $args
     * @return void
     */
    public static function get_users(array $args = []) {

        // Apply defaults to $args
        $defaults = array(
            'orderby' => 'user_login',
            'order'   => 'ASC',
            'role'    => '', // '' means no role filter
            'number'  => 10,
            'offset'  => 0,
            'fields'  => [], // Array of meta fields to retrieve
        );
        $args = wp_parse_args($args, $defaults);

        // Build the WP_User_Query args
        $query_args = array(
            'orderby' => $args['orderby'] === 'user_email' ? 'email' : 'login', // Translate email to WP Query
            'order'   => $args['order'],
            'number'  => $args['number'],
            'offset'  => $args['offset'],
        );
        if (! empty($args['role']) && $args['role'] !== 'all') {
            $query_args['role'] =  $args['role'];
        }

        $user_query = new \WP_User_Query($query_args);
        $users = $user_query->get_results();

        $formatted_users = [];

        foreach ($users as $user) {
            $user_data = [
                'user_login' => $user->user_login,
                'user_email' => $user->user_email,
                'role'       => array_shift($user->roles)
            ];

            // Fetch additional meta fields if specified.
            foreach ($args['fields'] as $field) {
                $user_data[$field] = get_user_meta($user->ID, $field, true);
            }

            $formatted_users[] = $user_data;
        }

        return $formatted_users;
    }

    /**
     * get_total_users
     *
     * @param  mixed $args
     * @return void
     */
    public static function get_total_users(array $args = []) {

        $defaults = array(
            'role'    => '', // '' means no role filter
        );

        $args = wp_parse_args($args, $defaults);

        $query_args = [];

        if (! empty($args['role']) && $args['role'] !== 'all') {
            $query_args['role'] =  $args['role'];
        }

        $user_query = new \WP_User_Query($query_args); // no pagination here, it's counting
        return $user_query->get_total();
    }
}
