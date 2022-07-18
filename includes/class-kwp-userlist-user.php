<?php

/**
 * The file defines the user class
 *
 * @package    KWP_UserList_User
 * @subpackage KWP_UserList/includes
 */

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

if (!class_exists('KWP_UserList_User')) {

/**
 * User class - all user data
 *
 * @package    KWP_UserList_User
 */
class KWP_UserList_User {

    // TODO - make protected
    //public $id;
    protected $name;
    protected $email;
    protected $role;
    
    /**
     * __construct
     *
     * @return void
     */
    public function __construct($name, $email, $role) {

        // Set the properties
        $this->name = $name;
        $this->email = $email;
        $this->role = $role;
    }

    public function get_user() {
        return $this;
    }

    public function get_name() {
        return $this->name;
    }

    public function get_email() {
        return $this->email;
    }

    public function get_role() {
        return $this->role;
    }


}
}