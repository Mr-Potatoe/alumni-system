<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/User.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$message = '';

// Handle password change
if (isset($_POST['change_password'])) {
    if ($user->verifyCurrentPassword($_SESSION['user_id'], $_POST['current_password'])) {
        if ($_POST['new_password'] === $_POST['confirm_password']) {
            if ($user->updatePassword($_SESSION['user_id'], $_POST['new_password'])) {
                $message = '<div class="green-text">Password updated successfully!</div>';
            } else {
                $message = '<div class="red-text">Failed to update password.</div>';
            }
        } else {
            $message = '<div class="red-text">New passwords do not match.</div>';
        }
    } else {
        $message = '<div class="red-text">Current password is incorrect.</div>';
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include 'includes/alumni_navbar.php'; ?>

<div class="container">
    <div class="row">
        <div class="col s12">
            <h4>Account Settings</h4>
            <?php echo $message; ?>
            
            <!-- Change Password Card -->
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Change Password</span>
                    <form method="POST">
                        <div class="row">
                            <div class="input-field col s12">
                                <input type="password" id="current_password" name="current_password" required>
                                <label for="current_password">Current Password</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12 m6">
                                <input type="password" id="new_password" name="new_password" required>
                                <label for="new_password">New Password</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input type="password" id="confirm_password" name="confirm_password" required>
                                <label for="confirm_password">Confirm New Password</label>
                            </div>
                        </div>
                        <button class="btn waves-effect waves-light" type="submit" name="change_password">
                            Update Password
                            <i class="material-icons right">lock</i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Email Preferences -->
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Email Preferences</span>
                    <form method="POST">
                        <p>
                            <label>
                                <input type="checkbox" class="filled-in" name="email_notifications" checked />
                                <span>Receive email notifications</span>
                            </label>
                        </p>
                        <p>
                            <label>
                                <input type="checkbox" class="filled-in" name="newsletter_subscription" checked />
                                <span>Subscribe to newsletter</span>
                            </label>
                        </p>
                        <button class="btn waves-effect waves-light" type="submit" name="update_preferences">
                            Save Preferences
                            <i class="material-icons right">save</i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

```</rewritten_file>
