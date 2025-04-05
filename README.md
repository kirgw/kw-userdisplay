# KG WP User Data Display


License URI: http://www.gnu.org/licenses/gpl-2.0.html

## Description

The KG WP User Data Display plugin allows you to display WordPress user data in various formats using shortcodes. It provides options for displaying user information in tables, cards, and lists, with customizable templates and filtering options.

## Features

*   **Versatile User Data Display:** Display WordPress user data in various formats including tables, cards, and lists using shortcodes.
*   **Flexible Templates:** Utilize a variety of pre-designed templates or create custom templates to tailor the appearance of user data.
*   **Simple Shortcode Builder:** Generate shortcodes effortlessly with the integrated shortcode builder on the admin page.
*   **Customizable Meta Fields:** Select specific user meta fields to display in your templates.
*   **Translation Ready:** Translate the plugin into different languages using the included .pot file.

## Installation

1.  Upload the plugin folder to the `/wp-content/plugins/` directory.
2.  Activate the plugin through the 'Plugins' menu in WordPress.

## Usage

Use the `[kgwp_user_data]` shortcode to display user data. The shortcode accepts several attributes to customize the output.

### Shortcode Attributes

*   **type:** (string, optional) The type of display to use. Available options: `card`, `list`. Default: `card`.
*   **variant:** (string, optional) The name of the template variant to use.
*   **uid:** (integer, optional) Display a single user by ID. Defaults to the current user.
*   **uids:** (string, optional) Comma-separated list of user IDs (for multi-user display). Can only be used with multi-user templates, such as list and table.
*   **role:** (string, optional) Filter by user role.
*   **meta_key:** (string, optional) Custom meta field to retrieve.

### Examples

*   Display user data in a table: `[kgwp_user_data type="table"]`
*   Display user data in a card with a custom variant: `[kgwp_user_data type="card" variant="my-custom-variant"]`
*   Display a single user with ID 5: `[kgwp_user_data uid="5"]`
*   Display multiple users in a list: `[kgwp_user_data type="list" uids="1,2,3"]`
*   Display users with the editor role: `[kgwp_user_data type="table" role="editor"]`
*   Display a specific meta key: `[kgwp_user_data meta_key="twitter"]`

## Templates

The plugin supports custom templates for displaying user data. Templates are located in the `templates/` directory.

*   `card-default.php`: Default template for displaying user data in a card format, with two sides and flip animation.
*   `card-simple.php`: Default template for displaying user data in a card format, one side.
*   `table-new.php`: Template for displaying user data in a table format.
*   `list-default.php`: Default template for displaying user data in a list format.

You can create your own templates by copying an existing template and modifying it to suit your needs. To use a custom template, specify the template name in the `template` attribute of the `[kgwp_user_data]` shortcode.

You can also override the default templates by creating a folder named `kgwp-user-data-display` in your theme's directory. Place your custom templates in this folder, and the plugin will use them instead of the default templates.

## Customization

You can customize the plugin's behavior by modifying the template files, adding, overriding, or creating their own template PHP files, or by adding custom CSS styles.

## Roadmap

*   More control of access for different shortcodes
*   Full support for ACF
*   Full support for WooCommerce

## Changelog

### 2.0.0

*   Major rewrite for improved structure, performance, and flexibility
*   Multiple template types (tables, cards, lists)
*   New settings page with custom field selection
*   Shortcode builder page for easy shortcode generation
*   Various new small features and improvements

### 1.1.0

*   Fixed table rendering issues
*   Small bug fixes

### 1.0.0

*   Initial release.
