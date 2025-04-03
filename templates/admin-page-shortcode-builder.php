<?php

/**
 * Admin page template - Shortcode Builder
 * @package KW\UserDisplay\Templates
 */

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

?>
<div class="kw-userdisplay-admin-container">
    <h1>KW UserDisplay Shortcode Builder</h1>
    <p>Generate custom shortcodes to display user information on your website.</p>

    <div class="wrap">
        <form id="kw-userdisplay-shortcode-form">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="display-type">Display Type</label>
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
                        <label for="variant">Card Style</label>
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
                            placeholder="Leave empty for current user">
                        <p class="description">Enter specific user ID or leave blank for current user</p>
                    </td>
                </tr>

                <!-- List/Table fields -->
                <tr id="uids-row" class="type-dependent type-list type-table" style="display:none;">
                    <th scope="row">
                        <label for="uids">User IDs</label>
                    </th>
                    <td>
                        <input type="text" id="uids" name="uids" class="regular-text"
                            placeholder="Comma-separated IDs (e.g., 1,5,23)">
                        <p class="description">Enter multiple user IDs separated by commas</p>
                    </td>
                </tr>

                <tr id="role-row" class="type-dependent type-list type-table" style="display:none;">
                    <th scope="row">
                        <label for="role">OR Filter by Role</label>
                    </th>
                    <td>
                        <select id="role" name="role" class="regular-text">
                            <option value="">Any Role</option>
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
                    Generate Shortcode
                </button>
            </p>
        </form>

        <div id="shortcode-result" style="display:none; margin-top: 20px;" class="postbox">
            <div class="inside">
                <h3>Your Shortcode</h3>
                <textarea id="generated-shortcode" class="large-text code" rows="2" readonly onclick="this.select();"></textarea>
                <p class="description">Click on the shortcode to select it automatically, then copy and paste this shortcode into your posts/pages/widgets</p>
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