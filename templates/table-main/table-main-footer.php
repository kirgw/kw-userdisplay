<?php
/**
 * Table template - Main - Footer
 */

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

?>

</table>

<?php if (count($users_data) < 10) : ?>

    <div class="kw-userdisplay-import-links">
        <a href="?kw-userdisplay-import=real"><?php echo __('Import default users', 'kw-userdisplay'); ?></a>
        <a href="?kw-userdisplay-import=random"><?php echo __('Import random users', 'kw-userdisplay'); ?></a>
    </div> 
    
<?php endif; ?>
