<?php

/**
 * The file defines the table class
 *
 * @package    KW\UserDisplay
 * @subpackage KW\UserDisplay\Inc
 */

namespace KW\UserDisplay\Inc;

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Table class - all table data
 *
 * @class KW\UserDisplay\Inc\Table
 */
class Table {

    // Sorting properties
    public $sorting;
    public $sort_by;
    public $role_filter;

    // Table data
    public $table_labels;
    public $table_data;

    // Pagination
    public $total_users;
    public $current_page;
    public $items_on_page;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(
        $sorting = 'ASC',
        $sort_by = 'user_login',
        $role_filter = 'all',
        $current_page = 1,
        $items_on_page = 10) {

        // Set the properties
        $this->sorting = $sorting;
        $this->sort_by = $sort_by;
        $this->role_filter = $role_filter;

        // Pages
        $this->current_page = $current_page;
        $this->items_on_page = $items_on_page;

        // Table header labels
        $this->table_labels = $this->get_labels();

        // Table data (users data)
        $this->total_users = $this->get_users_total();
        $this->table_data = $this->get_data();
    }

    
    /**
     * Get table labels
     *
     * @return array
     */
    public function get_labels() {

        return array(
            'username' => __('User', KW_USERDISPLAY_PLUGIN_NAME),
            'email'    => __('Email', KW_USERDISPLAY_PLUGIN_NAME),
            'role'     => __('Role', KW_USERDISPLAY_PLUGIN_NAME),
        );

    }


    /**
     * Get total amount of users for pagination
     *
     * @return void
     */
    public function get_users_total() {
    
        $args = array();
        
        // Need to count considering the filter
        if ($this->role_filter !== 'all') {
            $args['role'] = $this->role_filter;
        }

        $all_users = get_users($args);
        return count($all_users);
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

        // Add number
        $args['number'] = $this->items_on_page;

        // Add the offset if needed
        if ($this->current_page > 1) {
            $args['offset'] = ($this->current_page - 1) * $this->items_on_page;
        }

        // Get users data
        $users = get_users($args);

        // Format the data correctly
        $users_data = array();

        foreach ($users as $user) {

            $role = array_shift($user->roles);

            $users_data[] = new \KW\UserDisplay\Inc\User(
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
        
        // TODO: rearrange the templating system


        $html = '';

        // Iterate users data
        foreach($users_data as $user) {

            $html .= 
                '<tr>
                    <td>' . $user->get_name() . '</td>
                    <td>' . $user->get_email() . '</td>
                    <td><span class="kw-role-name">' . $user->get_role() . '</span></td>
                </tr>';

        }

        // Is pagination needed?
        if ($this->total_users > $this->items_on_page) {

            $pages = ceil($this->total_users / $this->items_on_page);

            $html .= 
                '<tr>
                    <td colspan="3">
                        <div class="kw-pagination">
                            <div>';

            // Add page links
            for ($i = 1; $i <= $pages; $i++) {
                if ($i != $this->current_page) {
                    $html .= '<a href="#" class="kw-page">' . $i . '</a> ';
                }
                else {
                    $html .= '<span class="kw-page-active">' . $i . '</span> ';
                }
            }

            $html .= 
                           '</div>
                            <div>
                                <i class="fa fa-user" title="' . __('Total users found', 'kw-userdisplay') . '"></i> ' . $this->total_users . '
                            </div>
                        </div>
                    </td>
                </tr>';
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
        $filename = KW_USERDISPLAY_PLUGIN_PATH . 'templates/kw-userdisplay-table-' . $type . '.php';

        if (file_exists($filename)) {
            include $filename;
        }
    }

}
