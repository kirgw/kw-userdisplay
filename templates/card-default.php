<?php

/**
 * User Card Template (Flipping card)
 *
 * This template displays user information in a card format
 *
 * @package KGWP\UserDataDisplay\Inc
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

// Add meta data
$meta = $data['meta'];

?>


<div class="kgwp-user-data-display-card">

    <div class="front">

        <div class="avatar"></div>

        <div class="content">

            <div class="username">

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