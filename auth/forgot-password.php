<?php 
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/User.php';

$message = '';

if(isset($_POST['reset'])) {
    $database = new Database();
    $db = $database->getConnection();
    
    $user = new User($db);
    
    if($user->requestPasswordReset($_POST['email'])) {
        $message = "If an account exists with this email, you will receive password reset instructions.";
    } else {
        $message = "If an account exists with this email, you will receive password reset instructions.";
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col s12 m6 offset-m3">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Reset Password</span>
                    
                    <?php if($message): ?>
                        <div class="blue-text"><?php echo $message; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="input-field">
                            <input type="email" id="email" name="email" required>
                            <label for="email">Email Address</label>
                        </div>
                        
                        <button class="btn waves-effect waves-light" type="submit" name="reset">
                            Reset Password
                            <i class="material-icons right">send</i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

```</rewritten_file>
