
<?php

/**
 * User List Table Template
 *
 * This template displays users information in a sortable table
 *
 * @package KGWP\UserDataDisplay\Inc
 *
 * @param Array of users data
 * Array
 * (
 *     [0] => Array
 *         (
 *             [id] =>
 *             [name] =>
 *             [email] =>
 *             [meta] => Array
 *                 (
 *                     [user_login] =>
 *                     [first_name] =>
 *
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

// Prepare the users data for JavaScript
$js_users = array_map(function($user) {
    return [
        'username'  => $user['meta']['user_login'] ?? '',
        'firstName' => $user['meta']['first_name'] ?? '',
        'lastName'  => $user['meta']['last_name'] ?? '',
        'email'     => $user['email'] ?? '',
        'role'      => $user['meta']['role'] ?? 'User',
    ];
}, $data);

// Convert PHP array to JSON for JavaScript
$js_users_json = wp_json_encode($js_users);

?>

<table class="kgwp-user-data-display-table">
    <thead>
        <tr>
            <th onclick="kgwpSortTable('username')">Username</th>
            <th onclick="kgwpSortTable('firstName')">First Name</th>
            <th onclick="kgwpSortTable('lastName')">Last Name</th>
            <th onclick="kgwpSortTable('email')">Email</th>
            <th onclick="kgwpSortTable('role')">Role</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="kgwp-user-data-display-table-body">
        <!-- Rows will be populated by JavaScript -->
    </tbody>
</table>

<script>

    // Placeholder
    // const kgwpUsers = [
    //     { username: 'johndoe', firstName: 'John', lastName: 'Doe', email: 'john@example.com', role: 'Admin' },
    //     { username: 'janedoe', firstName: 'Jane', lastName: 'Smith', email: 'jane@example.com', role: 'User' },
    //     { username: 'alexj', firstName: 'Alex', lastName: 'Johnson', email: 'alex@example.com', role: 'Moderator' }
    // ];

    // Real passed values
    const kgwpUsers = <?php echo $js_users_json; ?>;

    let kgwpSortColumn = '';
    let kgwpSortDirection = 1;

    function kgwpRenderTable(data) {
        const kwTbody = document.getElementById('kgwp-user-data-display-table-body');
        kwTbody.innerHTML = data.map(user => `
            <tr>
                <td>${user.username}</td>
                <td>${user.firstName}</td>
                <td>${user.lastName}</td>
                <td>${user.email}</td>
                <td>${user.role}</td>
                <td><a href="#" class="edit-link">Edit</a></td>
            </tr>
        `).join('');
    }

    function kgwpSortTable(column) {
        if (column === kgwpSortColumn) {
            kgwpSortDirection *= -1;
        } else {
            kgwpSortColumn = column;
            kgwpSortDirection = 1;
        }

        const kgwpSorted = [...kgwpUsers].sort((a, b) => {
            if (a[column] < b[column]) return -kgwpSortDirection;
            if (a[column] > b[column]) return kgwpSortDirection;
            return 0;
        });

        document.querySelectorAll('th').forEach(th => {
            th.classList.remove('sorted', 'asc', 'desc');
            if (th.textContent.toLowerCase().replace(' ', '') === column) {
                th.classList.add('sorted', kgwpSortDirection === 1 ? 'asc' : 'desc');
            }
        });

        kgwpRenderTable(kgwpSorted);
    }

    // Initial render
    kgwpRenderTable(kgwpUsers);
</script>
