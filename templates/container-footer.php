<?php
/**
 * Container template - Footer
 */

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

?>

<?php if ($allowed == false) : ?>

<div class="kw-userdisplay-empty">
    <?php echo __('No data available.', 'kw-userdisplay'); ?>
</div>

<?php endif; ?>

</div>
