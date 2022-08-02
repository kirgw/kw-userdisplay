<?php

/**
 * The file defines the settings class
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
 * @class KW\UserDisplay\Inc\AdminPages
 */
class AdminPages {

    public $pages;
    
    /**
     * __construct
     *
     * @return void
     */
    public function __construct() {
 
        $this->pages = array(
            array(
                'page_title' => 'KW User Display',
                'menu_title' => 'KW UserDisplay',
                'capability' => 'manage_options',
                'menu_slug'  => 'kw-user-display',
                'callback'   => array($this, 'render_admin_page'),
                'icon_url'   => 'dashicons-universal-access-alt',
                'position'   => 120,
            ),
        );

        add_action('admin_menu', array($this, 'add_admin_pages'));
    }


    /**
     * Add admin pages
     *
     * @return void
     */
    public function add_admin_pages()
    {
        foreach ($this->pages as $page) {
            add_menu_page(
                $page['page_title'],
                $page['menu_title'],
                $page['capability'],
                $page['menu_slug'],
                $page['callback'],
                $page['icon_url'],
                $page['position'],
            );
        }
    }


    /**
     * Render admin page
     *
     * @return void
     */
    public function render_admin_page()
    {
        require_once KW_USERDISPLAY_PLUGIN_PATH . 'templates/admin-page.php';
    }


}

new AdminPages();
