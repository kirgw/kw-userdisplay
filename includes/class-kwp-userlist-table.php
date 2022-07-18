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
 * @package    KWP_UserList_Table
 */
class KWP_UserList_Table {

    public $current_page;
    public $items_on_page;
    public $sorting;
    public $sort_by;
    public $role_filter;
    public $table_labels;
    public $table_data;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct($sorting = 'ASC', $sort_by = 'id') {

        // Set the properties
        $this->current_page = 1;
        $this->items_on_page = 10;
        $this->sorting = $sorting;
        $this->sort_by = $sort_by;
        $this->role_filter = 'all';

        // Table header labels
        $this->table_labels = $this->get_labels();

        // Table data (users data)
        $this->table_data = $this->get_data();


    }

    
    /**
     * get_labels
     *
     * @return void
     */
    public function get_labels() {

        return array(
            'username' => __('User', KWP_USERLIST_PLUGIN_NAME),
            'email'    => __('Email', KWP_USERLIST_PLUGIN_NAME),
            'role'     => __('Role', KWP_USERLIST_PLUGIN_NAME),
        );

    }

    
    /**
     * get_data
     *
     * @param  mixed $args
     * @return void
     */
    public function get_data() {

        // Setup the args with sorting parameters
        $args = array(
            'orderby' => 'id',
            'order'   => 'ASC'
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

            $users_list[] = new KWP_UserList_User(
                $user->ID,
                $user->username,
                $user->user_email,
                $role,
            );
        }

        return $users_data;
    }


    /**
     * Include the table template
     *
     * @param  mixed $type
     * @return void
     */
    public function get_template($type = 'main') {

        $filename = KWP_USERLIST_PLUGIN_PATH . 'templates/kwp-userlist-table-' . $type . '.php';

        if (file_exists($filename)) {
            require_once $filename;
        }

    }




}



}