<?php

/**
 * The file defines the user class
 *
 * @package    KW_UserDisplay_User
 * @subpackage KW_UserDisplay/includes
 */

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

if (!class_exists('KW_UserDisplay_User')) {

/**
 * User class that stores all the user data
 *
 * @class KW_UserDisplay_User
 */
class KW_UserDisplay_User {
    
    /**
     * User name
     *
     * @var string
     */
    protected $name;    

    /**
     * User email
     *
     * @var string
     */
    protected $email;   

    /**
     * User role
     *
     * @var string
     */
    protected $role;

    
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct($name, $email, $role) {

        // Set the properties
        $this->name = $name;
        $this->email = $email;
        $this->role = $role;
    }


    /**
     * Get user name
     *
     * @return string
     */
    public function get_name() {
        return $this->name;
    }


    /**
     * Get user email
     *
     * @return string
     */
    public function get_email() {
        return $this->email;
    }


    /**
     * Get user role
     *
     * @return string
     */
    public function get_role() {
        return $this->role;
    }


}
}