<?php
/**
 * Table template - Main - Header
 */

// Security check - exit if accessed directly
defined('ABSPATH') || exit;

?>
<table class="kw-userdisplay-table">

    <thead>
        <tr>

            <th>
                <i class="fa fa-sort-asc kw-sort" id="username-ASC" title="<?php echo __('Sort by username, ascending', 'kw-userdisplay'); ?>"></i>  
                <?php echo $params['labels']['username']; ?>
                <i class="fa fa-sort-desc kw-sort" id="username-DESC" title="<?php echo __('Sort by username, descending', 'kw-userdisplay'); ?>"></i>
            </th>

            <th>
                <i class="fa fa-sort-asc kw-sort" id="email-ASC" title="<?php echo __('Sort by email, ascending', 'kw-userdisplay'); ?>"></i>  
                <?php echo $params['labels']['email']; ?>
                <i class="fa fa-sort-desc kw-sort" id="email-DESC" title="<?php echo __('Sort by email, descending', 'kw-userdisplay'); ?>"></i>
            </th>

            <th>
                <?php echo $params['labels']['role']; ?>
                <span class="kw-filter-icons">
                    <i class="fa fa-filter" id="kw-filter-icon" title="<?php echo __('Filter is active', 'kw-userdisplay'); ?>"></i>
                (<i class="fa fa-times" id="kw-role-remove" title="<?php echo __('Remove the filter', 'kw-userdisplay'); ?>"></i>)
                </span>
            </th>               
        </tr>
    </thead>




