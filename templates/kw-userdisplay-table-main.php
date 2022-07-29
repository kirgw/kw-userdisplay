<?php
/**
 * Table template
 */

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

?>

<div class="kw-userdisplay-container">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <?php if (!empty($table_body_html) && KW_UserDisplay::is_allowed_to_view()) : ?>

        <table class="kw-userdisplay-table">

            <thead>
                <tr>

                    <th>
                        <i class="fa fa-sort-asc kw-sort" id="username-ASC" title="<?php echo __('Sort by username, ascending', 'kw-userdisplay'); ?>"></i>  
                        <?php echo $labels['username']; ?>
                        <i class="fa fa-sort-desc kw-sort" id="username-DESC" title="<?php echo __('Sort by username, descending', 'kw-userdisplay'); ?>"></i>
                    </th>

                    <th>
                        <i class="fa fa-sort-asc kw-sort" id="email-ASC" title="<?php echo __('Sort by email, ascending', 'kw-userdisplay'); ?>"></i>  
                        <?php echo $labels['email']; ?>
                        <i class="fa fa-sort-desc kw-sort" id="email-DESC" title="<?php echo __('Sort by email, descending', 'kw-userdisplay'); ?>"></i>
                    </th>

                    <th>
                        <?php echo $labels['role']; ?>
                        <span class="kw-filter-icons">
                            <i class="fa fa-filter" id="kw-filter-icon" title="<?php echo __('Filter is active', 'kw-userdisplay'); ?>"></i>
                        (<i class="fa fa-times" id="kw-role-remove" title="<?php echo __('Remove the filter', 'kw-userdisplay'); ?>"></i>)
                        </span>
                    </th>               
                </tr>
            </thead>

            <tbody>
                <?php echo $table_body_html; ?>
            </tbody>

        </table>

        <?php if (count($users_data) < 10) : ?>

            <div class="kw-userdisplay-import-links">
                <a href="?kw-userdisplay-import=real"><?php echo __('Import default users', 'kw-userdisplay'); ?></a>
                <a href="?kw-userdisplay-import=random"><?php echo __('Import random users', 'kw-userdisplay'); ?></a>
            </div> 
            
        <?php endif; ?>

    <?php else : ?>

        <div class="kw-userdisplay-empty">
            <?php echo __('No data available.', 'kw-userdisplay'); ?>
        </div>

    <?php endif; ?>

</div>
