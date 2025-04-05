<?php

/**
 * Admin page template - Settings
 * @package KGWP\UserDataDisplay\Templates
 */

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

?>

<div class="kgwp-user-data-display-admin-container">

    <h1>KG WP User Data Display</h1>

    <p>Easily display <em>any</em> users data on your website.</p>

    <hr />

    <div class="wrap">

        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <form method="post" action="options.php">

            <?php

            settings_fields('kgwp_user_data_display_settings_group');
            do_settings_sections(KGWP_USERDATADISPLAY_SLUG);

            // Close the table after the settings fields are rendered.
            echo '</tbody></table>';

            submit_button();

            ?>

        </form>

    </div>

</div>