<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Admin.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$admin = new Admin($db);

$message = '';

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($admin->verifyCurrentPassword($_SESSION['admin_id'], $current_password)) {
        if ($new_password === $confirm_password) {
            if ($admin->validatePasswordStrength($new_password)) {
                if ($admin->checkPasswordHistory($_SESSION['admin_id'], $new_password)) {
                    if ($admin->updatePassword($_SESSION['admin_id'], $new_password)) {
                        $message = '<div class="green-text">Password updated successfully!</div>';
                    } else {
                        $message = '<div class="red-text">Failed to update password.</div>';
                    }
                } else {
                    $message = '<div class="red-text">Password was used recently. Please choose a different password.</div>';
                }
            } else {
                $message = '<div class="red-text">Password must be at least 8 characters long and contain uppercase, 
                           lowercase, number, and special character.</div>';
            }
        } else {
            $message = '<div class="red-text">New passwords do not match.</div>';
        }
    } else {
        $message = '<div class="red-text">Current password is incorrect.</div>';
    }
}

// Handle system settings
if (isset($_POST['update_settings'])) {
    $settings = [
        'auto_approve' => isset($_POST['auto_approve']) ? 1 : 0,
        'email_notifications' => isset($_POST['email_notifications']) ? 1 : 0,
        'maintenance_mode' => isset($_POST['maintenance_mode']) ? 1 : 0
    ];
    
    if ($admin->updateSystemSettings($settings)) {
        $message = '<div class="green-text">System settings updated successfully!</div>';
    } else {
        $message = '<div class="red-text">Failed to update system settings.</div>';
    }
}

$currentSettings = $admin->getSystemSettings();
?>

<?php include '../includes/header.php'; ?>
<?php include 'includes/admin_navbar.php'; ?>

<div class="container">
    <div class="row">
        <div class="col s12">
            <h4>Admin Settings</h4>
            <?php echo $message; ?>

            <!-- Change Password -->
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Change Admin Password</span>
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

            <!-- System Settings -->
            <div class="card">
                <div class="card-content">
                    <span class="card-title">System Settings</span>
                    <form method="POST">
                        <p>
                            <label>
                                <input type="checkbox" class="filled-in" name="auto_approve"
                                       <?php echo $currentSettings['auto_approve'] ? 'checked' : ''; ?> />
                                <span>Auto-approve new registrations</span>
                            </label>
                        </p>
                        <p>
                            <label>
                                <input type="checkbox" class="filled-in" name="email_notifications"
                                       <?php echo $currentSettings['email_notifications'] ? 'checked' : ''; ?> />
                                <span>Enable email notifications</span>
                            </label>
                        </p>
                        <p>
                            <label>
                                <input type="checkbox" class="filled-in" name="maintenance_mode"
                                       <?php echo $currentSettings['maintenance_mode'] ? 'checked' : ''; ?> />
                                <span>Enable maintenance mode</span>
                            </label>
                        </p>
                        <button class="btn waves-effect waves-light" type="submit" name="update_settings">
                            Save Settings
                            <i class="material-icons right">save</i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?> 