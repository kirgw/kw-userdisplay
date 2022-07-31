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
    public $total_pages;

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
        $this->total_pages = $this->total_users > 0 ? ceil($this->total_users / $this->items_on_page) : 1;
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
     * Render the part of the content - AJAX reload
     *
     * @param array $kw_state
     * @return void
     */
    public static function table_reload(array $kw_state) {

        // Set the params - from array $kw_state('sortby', 'sorting', 'filter', 'page')
        $sort_by = ($kw_state['sortby'] === 'email') ? 'user_email' : 'user_login';
        $sort_type = $kw_state['sorting'];

        // Initialize the table
        $Table = new self($sort_type, $sort_by, $kw_state['filter'], $kw_state['page']);

        // Set the params
        $params = array(
            'object'     => $Table,
            'users_data' => $Table->table_data,
        );

        // Start the buffering
        ob_start();

        // Include the body template
        $filename = KW_USERDISPLAY_PLUGIN_PATH . 'templates/table-main/table-main-body.php';

        if (file_exists($filename)) {
             include $filename;
        }

        // Pass the buffer back to JS
        $result = json_encode(array(
            'table_html' => ob_get_clean(),   
        ));

        echo $result;
        wp_die();
    }


}
