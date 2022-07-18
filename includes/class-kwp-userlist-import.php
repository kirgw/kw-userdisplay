<?php

/**
 * The file defines the table class
 *
 * @package    KWP_UserList_Import
 * @subpackage KWP_UserList/includes
 */

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

if (!class_exists('KWP_UserList_Import')) {

/**
 * Table class - all table data
 *
 * @package    KWP_UserList_Import
 */
class KWP_UserList_Import {

    public $import_type;
    public $users_data;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct($import_type = 'random') {

        // Set the type and call for the data
        $this->import_type = $import_type;

    }
    
    /**
     * Gather and insert the data
     *
     * @return void
     */
    protected function launch() {
        $this->users_data = self::prepare_users_data($this->import_type);
        $this->import_results = self::insert_users_in_db($this->users_data);
    }


    /**
     * parse_users_from_csv
     *
     * @return void
     */
    public static function parse_users_from_csv() {

        // Get file
        $file = fopen(KWP_USERLIST_PLUGIN_PATH . '/sample-data/users.csv', 'r');

        // Exit if file isn't opened
        if ($file === false) {
            return false;
        }

        // Check headers
        $headers_check = array('username', 'email', 'role');
        $line = fgets($file);
        $headers_file = str_getcsv(trim($line), ',', '');

        // Exit if the headers don't match
        if ($headers_check !== $headers_file) {
            return false;
        }

        $users_data = array();

        while (($line_data = fgetcsv($file, 1000, ',', '')) !== false) {
            
            if (count($line_data) === 3) {

                $users_data[] = new KWP_UserList_User(
                    rand(),
                    $line_data['username'],
                    $line_data['email'],
                    $line_data['role'],
                );
            }
        }

        fclose($file);

        return $users_data;
    }

    
    /**
     * prepare_users_data
     *
     * @param  mixed $type
     * @return void
     */
    public static function prepare_users_data($type = 'random') {

        // Like this?
        if ($type === 'real') {
            //return self::parse_users_from_csv();
        }

        if ($type === 'random') {
            //return self::generate_random_users();
        }

        // Or like this?
        switch ($type) {
            case 'real':
                return self::parse_users_from_csv();
            case 'random':
                return self::generate_random_users();
        }
    }


    /**
     * generate_random_user
     *
     * @return void
     */
    public static function generate_random_user() {

        // Sample roles list
        $roles = array('subscriber', 'editor', 'admin');

        // Random
        $rand = rand();
        $uniqid = uniqid();

        return new KWP_UserList_User(
            $uniqid,
            $rand . '@' . $uniqid . '.com',
            $roles[rand(1, count($roles))]
        );
    }



    /**
     * generate_random_users
     *
     * @param  mixed $amount
     * @return void
     */
    public static function generate_random_users($args = array()) {

        $amount = !empty($args['amount']) ? $args['amount'] : 30;
        $users_data = array();

        // Iterate $amount times and create array of KWP_UserList_User users
        for ($i = 1; $i <= $amount; $i++) { 
            $users_data[] = KWP_UserList_User::generate_random_user();
        }

        return $users_data;
    }



    /**
     * import
     *
     * @param  array $users_data - array of KWP_UserList_User objects
     * @return void
     */
    public static function insert_users_in_db($users_data) {

        $imported = array();

        foreach ($users_data as $userlist_user) {

            if (username_exists($userlist_user->username)) {
                continue;
            }

            $userdata = array(
                'user_login'    =>  $userlist_user->username,
                'user_nicename' =>  $userlist_user->username,
                'user_email'    =>  $userlist_user->email,
                'role'          =>  $userlist_user->role,
                'user_pass'     =>  uniqid(),
            );
             
            $imported[] = wp_insert_user( $userdata ) ;
        }

        return $imported;
    }


}
}