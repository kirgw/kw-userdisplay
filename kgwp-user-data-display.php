<?php
/*
* Plugin Name: KG WP User Data Display
* Description: Plugin to show the user data in various templates
* Author: Kirill G.
* Version: 2.0
* License: GPLv2 or later
* Text Domain: kgwp-user-data-display
*/

namespace KGWP\UserDataDisplay;

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

// Setup the constants
define('KGWP_USERDATADISPLAY_PLUGIN_NAME', plugin_basename(__FILE__));
define('KGWP_USERDATADISPLAY_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('KGWP_USERDATADISPLAY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KGWP_USERDATADISPLAY_PLUGIN_VERSION', '2.0');
define('KGWP_USERDATADISPLAY_SLUG', 'kgwp-user-data-display'); // slug both for menu and i18n

// PSR-4 Autoloader (WordPress-style class names)
spl_autoload_register(function ($class) {

    $prefix = 'KGWP\\UserDataDisplay\\';
    $base_dir = KGWP_USERDATADISPLAY_PLUGIN_PATH . 'includes/';

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
    $file = $base_dir . 'class-kgwp-user-data-display-' . strtolower($file_name) . '.php';

    // Check if the file exists
    if (file_exists($file)) {
        require $file;
    }
    elseif (defined('WP_DEBUG') && WP_DEBUG) {
        error_log("Autoloader failed: Class {$class} not found at {$file}");
    }
});

// Start the plugin
\KGWP\UserDataDisplay\Inc\Init::instance();
