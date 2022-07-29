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
 * Table class that stores all the table data
 *
 * @class KW\UserDisplay\Inc\UsersImport
 */
class UsersImport {

    /**
     * Import type
     *
     * @var string
     */
    public $import_type;    

    /**
     * Users data: prepared data
     *
     * @var array
     */
    public $users_data;

    /**
     * Import results: imported user ids
     *
     * @var array
     */
    public $import_results;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct($import_type = 'random') {

        // Set the type
        $this->import_type = $import_type;
    }


    /**
     * Gather and insert the data
     *
     * @return void
     */
    public function launch() {

        // Prepare the data of selected type
        $this->users_data = self::prepare_users_data($this->import_type);

        // Insert the data into the database
        $this->import_results = self::insert_users_in_db($this->users_data);

        // Return import results
        return $this->import_results;
    }


    /**
     * Prepare users data based on the selected import type
     *
     * @param string $type
     * @return array $users_data
     */
    public static function prepare_users_data($type = 'real') {

        switch ($type) {
            case 'real':
                return self::parse_users_from_csv();
            case 'random':
                return self::generate_random_users();
        }
    }


    /**
     * Parse users from CSV
     *
     * @return array $users_data
     */
    public static function parse_users_from_csv() {

        // Get sample file
        $file = fopen(KW_USERDISPLAY_PLUGIN_URL . 'sample-users.csv', 'r');

        // Exit if file isn't opened
        if ($file === false) {
            return false;
        }

        // Check headers
        $headers_check = array('username', 'email', 'role');
        $line = fgets($file);
        $headers_file = str_getcsv(trim($line), ',');

        // Exit if the headers don't match
        if ($headers_check !== $headers_file) {
            return false;
        }

        $users_data = array();

        while (($line_data = fgetcsv($file, 1000, ',')) !== false) {
            
            if (count($line_data) === 3) {

                $users_data[] = new \KW\UserDisplay\Inc\User(
                    $line_data[0],
                    $line_data[1],
                    $line_data[2],
                );
            }
        }

        fclose($file);

        return $users_data;
    }


    /**
     * Generate one random user
     *
     * @return \KW\UserDisplay\Inc\User
     */
    public static function generate_random_user() {

        // Sample roles list
        $roles = array('subscriber', 'editor', 'author');

        // Random
        $rand = rand();
        $uniqid = uniqid();

        return new \KW\UserDisplay\Inc\User(
            $uniqid,
            $rand . '@' . $uniqid . '.com',
            $roles[rand(0, (count($roles)-1))]
        );
    }


    /**
     * Generate multiple random users
     *
     * @param mixed $amount
     * @return array $users_data
     */
    public static function generate_random_users($amount = 3) {

        $users_data = array();

        // Iterate $amount times and create array of KW_UserDisplay_User users
        for ($i = 1; $i <= $amount; $i++) { 
            $users_data[] = self::generate_random_user();
        }

        return $users_data;
    }


    /**
     * Inserting the users into the database
     *
     * @param array $users_data - array of \KW\UserDisplay\Inc\User objects
     * @return array $imported - import results
     */
    public static function insert_users_in_db($users_data) {

        $imported = array();

        if (empty($users_data)) {
            return false;
        }

        foreach ($users_data as $userdisplay_user) {

            if (username_exists($userdisplay_user->get_name())) {
                continue;
            }

            $userdata = array(
                'user_login' =>  $userdisplay_user->get_name(),
                'user_email' =>  $userdisplay_user->get_email(),
                'role'       =>  $userdisplay_user->get_role(),
                'user_pass'  =>  '',
            );
             
            $imported[] = wp_insert_user($userdata);
        }

        return $imported;
    }


}
