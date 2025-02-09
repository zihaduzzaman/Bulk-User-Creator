<?php
/*
Plugin Name: Bulk User Creator
Description: Create multiple users (up to 5) and display them in All Users.
Version: 1.1
Author: Zihaduzzaman Zihad
Powered by: PouchCare
*/

function bulk_user_creator_menu() {
    add_menu_page(
        'Bulk User Creator',
        'Bulk User Creator',
        'manage_options',
        'bulk-user-creator',
        'bulk_user_creator_page',
        'dashicons-admin-users',
        6
    );
}
add_action('admin_menu', 'bulk_user_creator_menu');

function bulk_user_creator_page() {
    if (isset($_POST['submit'])) {
        $users_created = [];

        for ($i = 1; $i <= 5; $i++) {
            $username = sanitize_text_field($_POST["username_$i"]);
            $email = sanitize_email($_POST["email_$i"]);
            $password = sanitize_text_field($_POST["password_$i"]);
            $role = sanitize_text_field($_POST["role_$i"]);

            if (!empty($username) && !empty($email) && !empty($password) && !empty($role)) {
                if (!username_exists($username) && !email_exists($email)) {
                    $user_id = wp_create_user($username, $password, $email);
                    if (!is_wp_error($user_id)) {
                        $user = new WP_User($user_id);
                        $user->set_role($role);
                        $users_created[] = $username;
                    }
                }
            }
        }

        if (!empty($users_created)) {
            echo '<div class="updated"><p>Users created successfully: ' . implode(', ', $users_created) . '.</p></div>';
        } else {
            echo '<div class="error"><p>No new users were created. Please check your input.</p></div>';
        }
    }

    // Display all users
    $users = get_users();
    ?>

    <div class="wrap">
        <h1>Create Multiple Users</h1>
        <form method="post" action="">
            <h3>Enter details for 5 users</h3>
            <table class="form-table">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <tr><th colspan="2"><h3>User <?php echo $i; ?></h3></th></tr>
                    <tr>
                        <th><label for="username_<?php echo $i; ?>">Username:</label></th>
                        <td><input type="text" name="username_<?php echo $i; ?>" required class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="email_<?php echo $i; ?>">Email:</label></th>
                        <td><input type="email" name="email_<?php echo $i; ?>" required class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="password_<?php echo $i; ?>">Password:</label></th>
                        <td><input type="password" name="password_<?php echo $i; ?>" required class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="role_<?php echo $i; ?>">Role:</label></th>
                        <td>
                            <select name="role_<?php echo $i; ?>" required>
                                <option value="subscriber">Subscriber</option>
                                <option value="author">Author</option>
                                <option value="editor">Editor</option>
                                <option value="administrator">Administrator</option>
                            </select>
                        </td>
                    </tr>
                <?php endfor; ?>
            </table>
            <input type="submit" name="submit" value="Create Users" class="button button-primary" />
        </form>

        <h2>All Users</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo esc_html($user->user_login); ?></td>
                        <td><?php echo esc_html($user->user_email); ?></td>
                        <td><?php echo implode(', ', $user->roles); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>
