
<?php

/**
 * User List Table Template
 *
 * This template displays users information in a sortable table
 *
 * @package KW\UserDisplay\Inc
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

<table class="kw-user-table">
    <thead>
        <tr>
            <th onclick="kwSortTable('username')">Username</th>
            <th onclick="kwSortTable('firstName')">First Name</th>
            <th onclick="kwSortTable('lastName')">Last Name</th>
            <th onclick="kwSortTable('email')">Email</th>
            <th onclick="kwSortTable('role')">Role</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="kwTableBody">
        <!-- Rows will be populated by JavaScript -->
    </tbody>
</table>

<script>

    // Placeholder
    // const kwUsers = [
    //     { username: 'johndoe', firstName: 'John', lastName: 'Doe', email: 'john@example.com', role: 'Admin' },
    //     { username: 'janedoe', firstName: 'Jane', lastName: 'Smith', email: 'jane@example.com', role: 'User' },
    //     { username: 'alexj', firstName: 'Alex', lastName: 'Johnson', email: 'alex@example.com', role: 'Moderator' }
    // ];

    // Real passed values
    const kwUsers = <?php echo $js_users_json; ?>;

    let kwSortColumn = '';
    let kwSortDirection = 1;

    function kwRenderTable(data) {
        const kwTbody = document.getElementById('kwTableBody');
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

    function kwSortTable(column) {
        if (column === kwSortColumn) {
            kwSortDirection *= -1;
        } else {
            kwSortColumn = column;
            kwSortDirection = 1;
        }

        const kwSorted = [...kwUsers].sort((a, b) => {
            if (a[column] < b[column]) return -kwSortDirection;
            if (a[column] > b[column]) return kwSortDirection;
            return 0;
        });

        document.querySelectorAll('th').forEach(th => {
            th.classList.remove('sorted', 'asc', 'desc');
            if (th.textContent.toLowerCase().replace(' ', '') === column) {
                th.classList.add('sorted', kwSortDirection === 1 ? 'asc' : 'desc');
            }
        });

        kwRenderTable(kwSorted);
    }

    // Initial render
    kwRenderTable(kwUsers);
</script>
