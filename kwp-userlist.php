<?php
/*
* Plugin Name: KWP UserList (renaming to KW User Display)
* Description: Plugin to show the sortable user list
* Author: Kirill G.
* Version: 1.0.0
* License: GPLv2 or later
* Text Domain: kwp-userlist
*/

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

// Setup the constants
define('KWP_USERLIST_PLUGIN_NAME', plugin_basename(__FILE__));
define('KWP_USERLIST_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('KWP_USERLIST_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KWP_USERLIST_PLUGIN_VERSION', '1.0.0');

// Activation function
function kwp_userlist_activate() {
    // ... empty for now
}
register_activation_hook(__FILE__, 'kwp_userlist_activate');

// Deactivation function
function kwp_userlist_deactivate() {
    // ... empty for now
}
register_deactivation_hook(__FILE__, 'kwp_userlist_deactivate');

// Add the main file of the plugin
require_once KWP_USERLIST_PLUGIN_PATH . 'includes/class-kwp-userlist.php';

// Start the plugin
KWP_UserList::instance();
