<?php
/**
 * Single user template - Main
 */

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

$title_level = 'h3'; // default TODO: in settings

?>

<div class="kw-userdisplay-single-user">

    <div class="kw-user-title">

        <<?php echo $title_level; ?>>

            <?php echo $user_title; ?>

        </<?php echo $title_level; ?>>

    </div>

    <div class="kw-user-image">

        <?php echo $user_image; ?>

    </div>

    <div class="kw-user-name">

        <?php echo $user_name; ?>
        
    </div>

    <div class="kw-about-text">

    </div>

</div>