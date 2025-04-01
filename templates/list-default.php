
<?php

/**
 * User List Template
 *
 * This template displays users information in a list format
 *
 * @package KW\UserDisplay\Inc
 *
 * @param Array of users data
 * Array
 * (
 *     [0] => Array
 *         (
 *             [id] =>
 *             [name] =>
 *             [email] =>
 *             [meta] => Array
 *                 (
 *                     [user_login] =>
 *                     [first_name] =>
 *
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

?>


<div class="kw-user-list">

    <?php foreach ($data as $user) : $meta = $user['meta']; ?>

    <div class="user-item">
        <div class="avatar-placeholder"></div>
        <div class="user-content">
            <div class="username"><?php echo '@' . $meta['user_login']; ?></div>

            <div class="full-name">
                <?php echo $meta['first_name']; ?>
                <?php echo ' ' . $meta['last_name']; ?>
            </div>

            <div class="description">
                <?php echo $meta['description']; ?>
            </div>
        </div>
        <a href="<?php echo esc_url(isset($meta['user_url']) ? $meta['user_url'] : '#'); ?>" class="profile-link">VIEW</a>
    </div>

    <?php endforeach; ?>

</div>
