<?php

/**
 * Admin page template
 */

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

?>

<div class="kw-userdisplay-admin-container">

    <h1>KW UserDisplay</h1>

    <p>Easily display <em>any</em> users data on your website.</p>

    <hr />

    <div class="wrap">

        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <form method="post" action="options.php">

            <?php

            settings_fields('kw_user_display_settings_group');
            do_settings_sections('kw-user-display');

            // Close the table after the settings fields are rendered.
            echo '</tbody></table>';

            submit_button();

            ?>

        </form>

    </div>

</div>