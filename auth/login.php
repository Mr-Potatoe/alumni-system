<?php 
require '../config/config.php';
require '../config/database.php';
require '../classes/User.php';

if(isset($_POST['login'])) {
    $database = new Database();
    $db = $database->getConnection();
    
    $user = new User($db);
    $login = $user->login($_POST['email'], $_POST['password']);
    
    if($login) {
        $_SESSION['user_id'] = $login['user_id'];
        $_SESSION['user_name'] = $login['first_name'] . ' ' . $login['last_name'];
        header("Location: ../alumni/dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password";
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col s12 m6 offset-m3">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Login</span>
                    <?php if(isset($error)): ?>
                        <div class="red-text"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="input-field">
                            <input type="email" id="email" name="email" required>
                            <label for="email">Email</label>
                        </div>
                        <div class="input-field">
                            <input type="password" id="password" name="password" required>
                            <label for="password">Password</label>
                        </div>
                        <button class="btn waves-effect waves-light" type="submit" name="login">
                            Login
                            <i class="material-icons right">send</i>
                        </button>
                    </form>
                    <!-- already have an account? -->
                    <a href="<?php echo BASE_URL; ?>/auth/register.php">Don't have an account? Register here</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
