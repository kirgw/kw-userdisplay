<?php

/**
 * Template Handler Class
 *
 * @package KGWP\UserDataDisplay\Inc
 */

namespace KGWP\UserDataDisplay\Inc;

// Security check - exit if accessed directly
defined('ABSPATH') || exit;


/**
 * Template Handler Class
 */
class TemplateHandler {


    /**
     * Main function to handle template loading.
     *
     * @param string $template_variant Template name.
     * @param string $template_type Template type (e.g., 'card', 'list').
     * @param array  $params        Template parameters (should inject "is_allowed" key via Init::is_allowed_to_view()).
     * @return void
     */
    public static function get_template(string $template_type = 'card', string $template_variant = '',  array $params = array()) {

        // Locate Template
        $template_path = self::locate_template($template_type, $template_variant);

        // Consider a filter here - apply_filters('kgwp_user_data_display_template_path', $template_path, $data);

        // Include Template
        return self::render_template($template_path, data:$params);
    }


    /**
     * Locates the template file.
     *
     * @param string $template_variant Template name.
     * @param string $template_type Template type (e.g., 'card', 'list').
     * @return string|false The full path to the template file, or false if not found.
     */
    public static function locate_template(string $template_type = '', string $template_variant = ''): string|false {

        // Base paths: theme and plugin
        $template_base_paths = [
            'theme' => get_stylesheet_directory() . KGWP_USERDATADISPLAY_SLUG . '/',
            'plugin' => KGWP_USERDATADISPLAY_PLUGIN_PATH . 'templates/',
        ];

        // Sanitize template type.
        $template_type = !empty($template_type) ? sanitize_key($template_type) : 'card';

        // TODO: check access permissions
        $is_allowed = \KGWP\UserDataDisplay\Inc\Init::is_allowed_to_view($template_type);

        // Determine the template name to use
        $template_filename = sanitize_file_name($template_type . '-' . (!empty($template_variant) ? $template_variant : 'default') . '.php');

        // Iterate and check for template file
        foreach ($template_base_paths as $folder_path) {

            // Full path
            $full_path = trailingslashit($folder_path) . $template_filename;

            if (file_exists($full_path)) {
                return $full_path; // Template found
            }
        }

        // Template not found
        return false;
    }


    /**
     * Includes the template file with the prepared data.
     *
     * @param string $template_path The full path to the template file.
     * @param array  $data          Prepared data for the template.
     * @return string The rendered template content.
     */
    private static function render_template(string $template_path, array $data) {

        // Check if the template file exists and for data errors
        if (false === $template_path) {
            return '<p>' . KGWP_USERDATADISPLAY_SLUG . ' plugin error: Template not found (' . $template_path . ')</p>';
        }

        // Render the template
        ob_start();
        include $template_path; // template has access to $data passed here
        return ob_get_clean();
    }

}
