<?php 
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/User.php';

$errors = [];
$success = false;

if(isset($_POST['register'])) {
    $database = new Database();
    $db = $database->getConnection();
    
    $user = new User($db);
    
    // Validate inputs
    if(empty($_POST['email'])) $errors[] = "Email is required";
    if(empty($_POST['password'])) $errors[] = "Password is required";
    if($_POST['password'] !== $_POST['confirm_password']) $errors[] = "Passwords do not match";
    if(empty($_POST['first_name'])) $errors[] = "First name is required";
    if(empty($_POST['last_name'])) $errors[] = "Last name is required";
    if(empty($_POST['graduation_year'])) $errors[] = "Graduation year is required";
    if(empty($_POST['department_id'])) $errors[] = "Department is required";
    
    if(empty($errors)) {
        $data = [
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'middle_name' => $_POST['middle_name'],
            'graduation_year' => $_POST['graduation_year'],
            'department_id' => $_POST['department_id']
        ];
        
        if($user->register($data)) {
            $success = true;
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}

$database = new Database();
$db = $database->getConnection();
$stmt = $db->prepare("SELECT department_id, department_name FROM departments ORDER BY department_name");
$stmt->execute();
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col s12 m8 offset-m2">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Alumni Registration</span>
                    
                    <?php if($success): ?>
                        <div class="green-text">Registration successful! Please wait for admin approval. <a href="login.php">Login here</a></div>
                    <?php endif; ?>

                    <?php if(!empty($errors)): ?>
                        <div class="red-text">
                            <?php foreach($errors as $error): ?>
                                <p><?php echo $error; ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row">
                            <div class="input-field col s12">
                                <input type="email" id="email" name="email" required>
                                <label for="email">Email</label>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="input-field col s6">
                                <input type="password" id="password" name="password" required>
                                <label for="password">Password</label>
                            </div>
                            <div class="input-field col s6">
                                <input type="password" id="confirm_password" name="confirm_password" required>
                                <label for="confirm_password">Confirm Password</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s4">
                                <input type="text" id="first_name" name="first_name" required>
                                <label for="first_name">First Name</label>
                            </div>
                            <div class="input-field col s4">
                                <input type="text" id="middle_name" name="middle_name">
                                <label for="middle_name">Middle Name</label>
                            </div>
                            <div class="input-field col s4">
                                <input type="text" id="last_name" name="last_name" required>
                                <label for="last_name">Last Name</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s12">
                                <select name="department_id" id="department_id" required>
                                    <option value="" disabled selected>Choose your department</option>
                                    <?php foreach($departments as $department): ?>
                                        <option value="<?php echo $department['department_id']; ?>">
                                            <?php echo htmlspecialchars($department['department_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Department</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s12">
                                <input type="number" id="graduation_year" name="graduation_year" required>
                                <label for="graduation_year">Graduation Year</label>
                            </div>
                        </div>

                        <button class="btn waves-effect waves-light" type="submit" name="register">
                            Register
                            <i class="material-icons right">send</i>
                        </button>
                    </form>
                    <!-- already have an account? -->
                    <a href="<?php echo BASE_URL; ?>/auth/login.php">Already have an account? Login here</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('select');
    var instances = M.FormSelect.init(elems);
});
</script>

<?php include '../includes/footer.php'; ?>

```</rewritten_file>