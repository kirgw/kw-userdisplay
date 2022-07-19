<?php
/**
 * Table template
 */

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

?>

<div class="kwp-userlist-container">

    <?php if (!empty($users_data) && KWP_UserList::is_allowed_to_view()) : ?>

        <table>
            <thead>
                <tr>
                    <th><?php echo $labels['username']; ?></th>
                    <th><?php echo $labels['email']; ?></th>
                    <th><?php echo $labels['role']; ?></th>               
                </tr>
            </thead>

            <tbody>

                <?php foreach($users_data as $user) : ?>

                    <tr>
                        <td><?php echo $user->get_name(); ?></td>
                        <td><?php echo $user->get_email(); ?></td>
                        <td><?php echo $user->get_role(); ?></td>
                    </tr>

                <?php endforeach; ?>

            </tbody>
        </table>

        <?php if (count($users_data) < 10) : ?>

            <div class="kwp-userlist-import-links">
                <a href="?kwp-userlist-import=real"><?php echo __('Import default users', 'kwp-userlist'); ?></a>
                <a href="?kwp-userlist-import=random"><?php echo __('Import random users', 'kwp-userlist'); ?></a>
            </div> 
            
        <?php endif; ?>

    <?php else : ?>

        <div class="kwp-userlist-empty">
            <?php echo __('No data available.', 'kwp-userlist'); ?>
        </div>

    <?php endif; ?>

</div>
