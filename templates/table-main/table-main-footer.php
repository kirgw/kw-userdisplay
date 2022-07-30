<?php
/**
 * Table template - Main - Footer
 */

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

?>

</table>

<?php if (count($params['users_data']) < $params['object']->items_on_page) : ?>

    <div class="kw-userdisplay-import-links">
        <a href="?kw-userdisplay-import=real"><?php echo __('Import default users', 'kw-userdisplay'); ?></a>
        <a href="?kw-userdisplay-import=random"><?php echo __('Import random users', 'kw-userdisplay'); ?></a>
    </div> 
    
<?php endif; ?>
