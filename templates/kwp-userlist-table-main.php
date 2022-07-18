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
                <a href=""><?php echo __('Import default users', KWP_USERLIST_PLUGIN_NAME); ?></a>
                <a href=""><?php echo __('Import random users', KWP_USERLIST_PLUGIN_NAME); ?></a>
            </div> 
            
        <?php endif; ?>

    <?php else : ?>

        <div class="kwp-userlist-empty">
            <?php echo __('No data available.', KWP_USERLIST_PLUGIN_NAME); ?>
        </div>

    <?php endif; ?>

</div>