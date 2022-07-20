<?php
/**
 * Table template
 */

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

?>

<div class="kwp-userlist-container">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <?php if (!empty($table_body_html) && KWP_UserList::is_allowed_to_view()) : ?>

        <table class="kwp-userlist-table">

            <thead>
                <tr>

                    <th>
                        <i class="fa fa-sort-asc kwp-sort" id="username-ASC" title="<?php echo __('Sort by username, ascending', 'kwp-userlist'); ?>"></i>  
                        <?php echo $labels['username']; ?>
                        <i class="fa fa-sort-desc kwp-sort" id="username-DESC" title="<?php echo __('Sort by username, descending', 'kwp-userlist'); ?>"></i>
                    </th>

                    <th>
                        <i class="fa fa-sort-asc kwp-sort" id="email-ASC" title="<?php echo __('Sort by email, ascending', 'kwp-userlist'); ?>"></i>  
                        <?php echo $labels['email']; ?>
                        <i class="fa fa-sort-desc kwp-sort" id="email-DESC" title="<?php echo __('Sort by email, descending', 'kwp-userlist'); ?>"></i>
                    </th>

                    <th>
                        <?php echo $labels['role']; ?>
                        <span class="kwp-filter-icons">
                            <i class="fa fa-filter" id="kwp-filter-icon" title="<?php echo __('Filter is active', 'kwp-userlist'); ?>"></i>
                        (<i class="fa fa-times" id="kwp-role-remove" title="<?php echo __('Remove the filter', 'kwp-userlist'); ?>"></i>)
                        </span>
                    </th>               
                </tr>
            </thead>

            <tbody>
                <?php echo $table_body_html; ?>
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
