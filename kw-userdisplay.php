<?php
/*
* Plugin Name: KW UserDisplay (former KWP UserList)
* Description: Plugin to show the sortable user list
* Author: Kirill G.
* Version: 2.0
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
define('KW_USERDISPLAY_PLUGIN_VERSION', '2.0');
define('KW_USERDISPLAY_SLUG', 'kw-userdisplay'); // slug both for menu and i18n

// PSR-4 Autoloader (WordPress-style class names)
spl_autoload_register(function ($class) {

    $prefix = 'KW\\UserDisplay\\';
    $base_dir = KW_USERDISPLAY_PLUGIN_PATH . 'includes/';

    // Check if the class uses the namespace prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Convert namespace to WordPress-style class filename
    $relative_class = substr($class, $len);
    $relative_class = str_replace('Inc\\', '', $relative_class); // rem "Inc\" from the path
    $relative_class = ltrim($relative_class, '\\');

    // Remove leading slashes and replace namespace separators with hyphens
    $file_name = str_replace('\\', '-', $relative_class);

    // Construct the file path
    $file = $base_dir . 'class-kw-userdisplay-' . strtolower($file_name) . '.php';

    // Check if the file exists
    if (file_exists($file)) {
        require $file;
    }
    elseif (defined('WP_DEBUG') && WP_DEBUG) {
        error_log("Autoloader failed: Class {$class} not found at {$file}");
    }
});

// Start the plugin
\KW\UserDisplay\Inc\Init::instance();

