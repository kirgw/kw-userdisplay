<?php
/**
 * Table template - Main - Body
 */

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

?>

<tbody>

<?php foreach($params['users_data'] as $user) : ?>

    <tr>
        <td><?php echo $user->get_name(); ?></td>

        <td><?php echo $user->get_email(); ?></td>

        <td>
            <span class="kw-role-name">
                <?php echo $user->get_role(); ?>
            </span>
        </td>
    </tr>

<?php endforeach; ?>

<?php if ($params['object']->total_pages > 1) : //  Is pagination needed? ?>

    <tr>
        <td colspan="3">

            <div class="kw-pagination">

                <div>

                    <?php for ($i = 1; $i <= $params['object']->total_pages; $i++) : ?>

                        <?php if ($i != $params['object']->current_page) : ?>

                            <a href="#" class="kw-page"><?php echo $i; ?></a>

                        <?php else : ?>

                            <span class="kw-page-active"><?php echo $i; ?></span>

                        <?php endif; ?>

                    <?php endfor; ?>

                </div>

                <div>
                    <i class="fa fa-user" title="' . __('Total users found', 'kw-userdisplay') . '"></i><?php echo $params['object']->total_users; ?>
                </div>

            </div>
        </td>
    </tr>

<?php endif; ?>

</tbody>



