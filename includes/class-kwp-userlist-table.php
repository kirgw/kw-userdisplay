<?php

/**
 * The file defines the table class
 *
 * @package    KWP_UserList_Table
 * @subpackage KWP_UserList/includes
 */

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

if (!class_exists('KWP_UserList_Table')) {

/**
 * Table class - all table data
 *
 * @class KWP_UserList_Table
 */
class KWP_UserList_Table {

    public $sorting;
    public $sort_by;
    public $role_filter;

    public $table_labels;
    public $table_data;

    public $current_page;
    public $items_on_page;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct($sorting = 'ASC', $sort_by = 'id', $role_filter = 'all') {

        // Set the properties
        $this->sorting = $sorting;
        $this->sort_by = $sort_by;
        $this->role_filter = $role_filter;

        // Table header labels
        $this->table_labels = $this->get_labels();

        // Table data (users data)
        $this->table_data = $this->get_data();

        // Pages
        $this->current_page = 1;
        $this->items_on_page = 10;
    }

    
    /**
     * Get table labels
     *
     * @return array
     */
    public function get_labels() {

        return array(
            'username' => __('User', KWP_USERLIST_PLUGIN_NAME),
            'email'    => __('Email', KWP_USERLIST_PLUGIN_NAME),
            'role'     => __('Role', KWP_USERLIST_PLUGIN_NAME),
        );

    }


    /**
     * Get users data
     *
     * @return array $users_data
     */
    public function get_data() {

        // Setup the args with sorting parameters
        $args = array(
            'orderby' => $this->sort_by,
            'order'   => $this->sorting,
        );

        // If role filtering is active
        if ($this->role_filter !== 'all') {
            $args['role'] = $this->role_filter;
        }

        // Get users data
        $users = get_users($args);

        // Format the data correctly
        $users_data = array();

        foreach ($users as $user) {

            $role = array_shift($user->roles);

            $users_data[] = new KWP_UserList_User(
                $user->user_login,
                $user->user_email,
                $role,
            );
        }

        return $users_data;
    }


    /**
     * Get table body HTML
     *
     * @param  mixed $users_data
     * @return string $html
     */
    public function get_table_body_html($users_data) {

        $html = '';

        // Add users data (TODO - add paged view of users)
        foreach($users_data as $user) {

            $html .= 
                '<tr>
                    <td>' . $user->get_name() . '</td>
                    <td>' . $user->get_email() . '</td>
                    <td>' . $user->get_role() . '</td>
                </tr>';

        }

        // Is pagination needed?
        if (count($users_data) > $this->items_on_page) {

            $pages = ceil(count($users_data) / $this->items_on_page);

            $html .= '<tr><td colspan=3>';

            // Add page links
            for ($i = 1; $i < $pages; $i++) {
                if ($i !== $this->current_page) {
                    $html .= '<a href="?kwp-list-page="' . $i . '">' . $i . '</a> ';
                }
                else {
                    $html .= $i . ' ';
                }
            }

            $html .=  '</td></tr>';
        }

        return $html;
    }


    /**
     * Include the table template
     *
     * @param string $type
     * @return void
     */
    public function get_template($type = 'main') {

        // Pass the variables
        $labels = $this->table_labels;
        $users_data = $this->table_data;
        $table_body_html = $this->get_table_body_html($users_data);

        // Check the file and include
        $filename = KWP_USERLIST_PLUGIN_PATH . 'templates/kwp-userlist-table-' . $type . '.php';

        if (file_exists($filename)) {
            include $filename;
        }
    }


}
}