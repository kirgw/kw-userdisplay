<?php
// Example of the UserData class
namespace KW\UserDisplay\Inc;

class UserData {


    // Properties passed from shortcode
    private $args;

    // Default shortcode types
    private $default_types = ['card', 'list', 'table', 'leaderboard'];


    /**
     * Constructor
     *
     * @param array $args {
     *     @type string $type       Shortcode type (as in $default_types)
     *     @type string $role       Role to filter by
     *     @type int    $limit      Number of users to display per page (optional, default: 100).
     *     @type string $meta_key   Meta field to filter by
     * }
     */
    public function __construct($args) {
        $this->args = $args;
    }


    /**
     * Retrieves user data based on the shortcode type specified in the arguments.
     *
     * @return array|\WP_Error Returns an array of user data if successful,
     *                         or a \WP_Error object if the type is invalid or no data is found.
     */
    public function get_data() {

        $data = [];

        // Validate shortcode type
        if (!in_array($this->args['type'], $this->default_types, true)) {
            return new \WP_Error('invalid_type', 'Invalid shortcode type.');
        }

        // Fetch data based on type
        switch ($this->args['type']) {

            case 'card':
                $data = $this->get_single_user($this->args['uid']);
                break;

            default:
                $data = $this->get_multiple_users();
        }

        // Check if data exists
        if (empty($data)) {
            return new \WP_Error('no_data', 'No user data found.');
        }

        return $data;
    }


    /**
     * Retrieves multiple users based on the constructor arguments.
     *
     * This method constructs a WP_User_Query using parameters from the class's $args property,
     * including role filtering, pagination (limit/offset), ordering, and meta field filtering.
     * Returns an array of user objects ready for display in list/table/leaderboard formats.
     *
     * @return array Array of WP_User objects or empty array if no users match.
     */
    private function get_multiple_users(): array {

        $user_ids = [];

        // Handle uids parameter
        if (!empty($this->args['uids'])) {
            $user_ids = array_map('intval', explode(',', $this->args['uids']));
        }

        // Fallback to role-based query
        else {

            // Set query arguments
            $query_args = [
                'fields' => 'ids'
            ];

            // Check role filter
            if ($this->args['role']) $query_args['role'] = $this->args['role'];

            // Check limit
            $query_args['number'] = $this->args['limit'] > 0 ? $this->args['limit'] : 100; // default limit is 100

            // Check for meta_key filter
            if ($this->args['meta_key']) {
                $query_args['meta_key'] = $this->args['meta_key'];
                $query_args['orderby'] = 'meta_value';
            }

            // Query the users
            $user_ids = \get_users($query_args);
        }

        // Fetch and validate each user
        $users_found = [];

        // Iterate either found IDs or provided in shortcode
        foreach ($user_ids as $id) {
            $user = $this->get_single_user($id);
            if ($user) $users_found[] = $user;
        }

        return $users_found;
    }


    /**
     * Retrieves a single user's data by user ID.
     *
     * Fetches core user data using get_userdata() and collects additional meta information.
     * Returns a formatted array with user ID, display name, email, and metadata.
     *
     * @param int $user_id The ID of the user to fetch.
     * @return array|null Array of user data if found, null if user doesn't exist.
     */
    private function get_single_user(int $user_id): ?array {

        // Get WordPress user object
        $user = get_userdata($user_id);
        if (!$user) return null;

        // Return formatted user data array
        return [
            'id'    => $user->ID,
            'name'  => $user->display_name,
            'email' => $user->user_email,
            'meta'  => self::get_user_metadata($user->ID) // calls inner method for metadata
        ];
    }


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
    public static function get_user_metadata(int $user_id): array {

        // Placeholder
        $user_data = array();

        $global_fields = self::get_global_fields();

        // Default data
        $user = get_userdata($user_id);

        if (!$user) {
            // error_log(KW_USERDISPLAY_SLUG . " error: Invalid user ID {$user_id}");
            return $user_data;
        }

        foreach ($global_fields as $field) {

            // Check if the field exists as a property in the user object
            if (isset($user->$field)) {
                $user_data[$field] = esc_html($user->$field);
            }

            // If not a property, try to get it as user meta
            else {

                $meta_value = get_user_meta($user_id, $field, true);

                // Check if a meta value was found + escape it
                if (!empty($meta_value)) {
                    $user_data[$field] = esc_html($meta_value);
                }

                // Handle cases where the meta field is not found
                else {
                    $user_data[$field] = "N/A ({$field})";
                }
            }
        }

        return $user_data;
    }
}
