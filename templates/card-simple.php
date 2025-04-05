<?php

/**
 * User Card Template (simple card)
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

<div class="kgwp-user-data-display-card-simple">

    <div class="content">

        <div class="username"><?php echo '@' . $meta['user_login']; ?></div>

        <div class="full-name">
            <?php echo $meta['first_name']; ?>
            <?php echo ' ' . $meta['last_name']; ?>
        </div>

        <div class="description">
            <?php echo $meta['description']; ?>
        </div>
    </div>

    <a href="<?php echo esc_url(isset($meta['user_url']) ? $meta['user_url'] : '#'); ?>" class="profile-link">VIEW PROFILE</a>

</div>