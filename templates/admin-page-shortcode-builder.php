<?php

/**
 * Admin page template - Shortcode Builder
 * @package KGWP\UserDataDisplay\Templates
 */

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

?>

<div class="kgwp-user-data-display-admin-container">

    <h1><?php _e('KG WP User Data Display', KGWP_USERDATADISPLAY_SLUG); ?></h1>

    <p><?php _e('Easily display <em>any</em> users data on your website.', KGWP_USERDATADISPLAY_SLUG); ?></p>

    <hr />

    <div class="wrap">

        <h1><?php _e('Shortcode Builder', KGWP_USERDATADISPLAY_SLUG); ?></h1>
        <p><?php _e('Generate custom shortcodes to display user information on your website.', KGWP_USERDATADISPLAY_SLUG); ?></p>

        <form id="kgwp-user-data-display-shortcode-form">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="display-type"><?php _e('Display Type', KGWP_USERDATADISPLAY_SLUG); ?></label>
                    </th>
                    <td>
                        <select id="display-type" name="type" class="regular-text">
                            <option value="card">Card</option>
                            <option value="list">List</option>
                            <option value="table">Table</option>
                        </select>
                    </td>
                </tr>

                <!-- Card variant dropdown -->
                <tr id="variant-row" class="type-dependent type-card">
                    <th scope="row">
                        <label for="variant"><?php _e('Card Style', KGWP_USERDATADISPLAY_SLUG); ?></label>
                    </th>
                    <td>
                        <select id="variant" name="variant" class="regular-text">
                            <option value="">Default (flip)</option>
                            <option value="simple">Simple</option>
                        </select>
                    </td>
                </tr>

                <!-- Card-specific fields -->
                <tr id="user-id-row" class="type-dependent type-card">
                    <th scope="row">
                        <label for="uid">User ID</label>
                    </th>
                    <td>
                        <input type="text" id="uid" name="uid" class="regular-text"
                            placeholder="<?php esc_attr_e('Leave empty for current user', KGWP_USERDATADISPLAY_SLUG); ?>">
                        <p class="description"><?php _e('Enter specific user ID or leave blank for current user', KGWP_USERDATADISPLAY_SLUG); ?></p>
                    </td>
                </tr>

                <!-- List/Table fields -->
                <tr id="uids-row" class="type-dependent type-list type-table" style="display:none;">
                    <th scope="row">
                        <label for="uids"><?php _e('User IDs', KGWP_USERDATADISPLAY_SLUG); ?></label>
                    </th>
                    <td>
                        <input type="text" id="uids" name="uids" class="regular-text"
                            placeholder="<?php esc_attr_e('Comma-separated IDs (e.g., 1,5,23)', KGWP_USERDATADISPLAY_SLUG); ?>">
                        <p class="description"><?php _e('Enter multiple user IDs separated by commas', KGWP_USERDATADISPLAY_SLUG); ?></p>
                    </td>
                </tr>

                <tr id="role-row" class="type-dependent type-list type-table" style="display:none;">
                    <th scope="row">
                        <label for="role"><?php _e('OR Filter by Role', KGWP_USERDATADISPLAY_SLUG); ?></label>
                    </th>
                    <td>
                        <select id="role" name="role" class="regular-text">
                            <option value=""><?php _e('Any Role', KGWP_USERDATADISPLAY_SLUG); ?></option>
                            <?php foreach (get_editable_roles() as $role_name => $role_info): ?>
                                <option value="<?php echo esc_attr($role_name); ?>">
                                    <?php echo esc_html($role_info['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <button type="button" id="generate-shortcode" class="button button-primary">
                    <?php _e('Generate Shortcode', KGWP_USERDATADISPLAY_SLUG); ?>
                </button>
            </p>
        </form>

        <div id="shortcode-result" style="display:none; margin-top: 20px;" class="postbox">
            <div class="inside">
                <h3><?php _e('Your Shortcode', KGWP_USERDATADISPLAY_SLUG); ?></h3>
                <textarea id="generated-shortcode" class="large-text code" rows="2" readonly onclick="this.select();"></textarea>
                <p class="description"><?php _e('Click on the shortcode to select it automatically, then copy and paste this shortcode into your posts/pages/widgets', KGWP_USERDATADISPLAY_SLUG); ?></p>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {

        // Show/hide fields based on selected type
        $('#display-type').change(function() {
            $('.type-dependent').hide();
            const selectedType = $(this).val();
            $('.type-' + selectedType).show();
        }).trigger('change'); // Trigger on load to set initial state

        // Generate shortcode when button is clicked
        $('#generate-shortcode').click(function() {

            let attrs = [];
            const type = $('#display-type').val();

            // Always include basic attributes
            attrs.push('type="' + type + '"');

            // Handle specific attributes based on type
            if (type === 'card') {
                const uid = $('#uid').val().trim();
                const variant = $('#variant').val().trim();
                if (uid) attrs.push('uid="' + uid + '"');
                if (variant) attrs.push('variant="' + variant + '"');
            }
            else {
                const uids = $('#uids').val().trim();
                const role = $('#role').val().trim();
                if (uids) attrs.push('uids="' + uids + '"');
                if (role) attrs.push('role="' + role + '"');
            }

            // Build the shortcode
            const shortcode = '[kgwp_user_data ' + attrs.join(' ') + ']';

            // Display the result
            $('#generated-shortcode').val(shortcode);
            $('#shortcode-result').show();
        });
    });
</script>