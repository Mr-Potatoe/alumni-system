<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Admin.php';

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    // Route based on role if already logged in
    switch ($_SESSION['admin_role']) {
        case 'Department Head':
            header("Location: ../department/dashboard.php");
            break;
        case 'Super Admin':
            header("Location: ../dashboard.php");
            break;
        case 'Staff':
            header("Location: ../staff/dashboard.php");
            break;
        default:
            header("Location: ../dashboard.php");
    }
    exit();
}

$database = new Database();
$db = $database->getConnection();
$admin = new Admin($db);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $message = '<div class="red-text">Please fill in all fields.</div>';
    } else {
        $adminData = $admin->authenticate($username, $password);
        
        if ($adminData) {
            if ($adminData['is_active']) {
                // Set session variables
                $_SESSION['admin_id'] = $adminData['admin_id'];
                $_SESSION['admin_username'] = $adminData['username'];
                $_SESSION['admin_role'] = $adminData['role'];
                $_SESSION['admin_name'] = $adminData['first_name'] . ' ' . $adminData['last_name'];
                $_SESSION['department_id'] = $adminData['department_id'] ?? null;

                // Log the successful login
                $admin->logAudit(
                    $adminData['admin_id'],
                    'LOGIN',
                    'Admin login successful',
                    $_SERVER['REMOTE_ADDR']
                );
                
                // Route based on role
                switch ($adminData['role']) {
                    case 'Department Head':
                        header("Location: ../department/dashboard.php");
                        break;
                    case 'Super Admin':
                        header("Location: ../dashboard.php");
                        break;
                    case 'Staff':
                        header("Location: ../staff/dashboard.php");
                        break;
                    default:
                        header("Location: ../dashboard.php");
                }
                exit();
            } else {
                $message = '<div class="red-text">Your account has been deactivated. Please contact the system administrator.</div>';
            }
        } else {
            $message = '<div class="red-text">Invalid username or password.</div>';
            // Log failed login attempt
            $admin->logAudit(
                null,
                'LOGIN_FAILED',
                "Failed login attempt for username: $username",
                $_SERVER['REMOTE_ADDR']
            );
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo SITE_NAME; ?></title>
    <!-- Materialize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .login-container {
            margin-top: 50px;
            max-width: 500px;
        }
        .login-box {
            padding: 20px;
            border-radius: 5px;
        }
        .brand-logo {
            margin-bottom: 20px;
            text-align: center;
        }
        .brand-logo i {
            font-size: 48px;
            color: #1565c0;
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <div class="card login-box">
            <div class="brand-logo">
                <i class="material-icons">admin_panel_settings</i>
                <h4><?php echo SITE_NAME; ?></h4>
                <h5>Admin Login</h5>
            </div>
            
            <?php if ($message): ?>
                <div class="row">
                    <div class="col s12">
                        <?php echo $message; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="row">
                    <div class="input-field col s12">
                        <i class="material-icons prefix">person</i>
                        <input type="text" id="username" name="username" required>
                        <label for="username">Username</label>
                    </div>
                </div>
                
                <div class="row">
                    <div class="input-field col s12">
                        <i class="material-icons prefix">lock</i>
                        <input type="password" id="password" name="password" required>
                        <label for="password">Password</label>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col s12">
                        <button type="submit" class="btn waves-effect waves-light blue darken-3 w-100" style="width: 100%;">
                            Login
                            <i class="material-icons right">send</i>
                        </button>
                    </div>
                </div>
            </form>
            
            <div class="row">
                <div class="col s12 center-align">
                    <a href="forgot_password.php">Forgot Password?</a>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html> 