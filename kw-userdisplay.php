<?php
/*
* Plugin Name: KW UserDisplay (former KWP UserList)
* Description: Plugin to show the sortable user list
* Author: Kirill G.
* Version: 1.1.0
* License: GPLv2 or later
* Text Domain: kw-userdisplay
*/

namespace KW\UserDisplay;

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

// Setup the constants
define('KW_USERDISPLAY_PLUGIN_NAME', plugin_basename(__FILE__));
define('KW_USERDISPLAY_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('KW_USERDISPLAY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KW_USERDISPLAY_PLUGIN_VERSION', '1.1.0');

// Add the main file of the plugin
require_once KW_USERDISPLAY_PLUGIN_PATH . 'includes/class-kw-userdisplay.php';

// Start the plugin
\KW\UserDisplay\Inc\Init::instance();

