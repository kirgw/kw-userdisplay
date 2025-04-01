<?php

/**
 * User Card Template (Flipping card)
 *
 * This template displays user information in a card format
 *
 * @package KW\UserDisplay\Inc
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

// Add meta data
$meta = $data['meta'];

?>


<div class="kg-user-card">

    <div class="front">

        <div class="avatar"></div>

        <div class="content">

            <div class="username">
                <?php
                // echo esc_html(apply_filters('kw_userdisplay_profile_label', __('Show Profile', 'kw-userdisplay')));
                ?>
                <a href="<?php echo esc_url(isset($meta['user_url']) ? $meta['user_url'] : '#'); ?>" class="profile-link">
                    <?php echo '@' . $meta['user_login']; ?>
                </a>
            </div>

            <div class="full-name">
                <?php echo $meta['first_name']; ?>
                <?php echo ' ' . $meta['last_name']; ?>
            </div>

            <div class="description">
                <?php echo $meta['description']; ?>
            </div>

        </div>
    </div>

    <div class="back">
        <?php echo $meta['user_login']; ?>
    </div>

</div>