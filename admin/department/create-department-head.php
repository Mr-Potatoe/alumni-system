<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Admin.php';

// Only Super Admin can create Department Heads
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'Super Admin') {
    header("Location: ../auth/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$admin = new Admin($db);

$message = '';
$departments = $admin->getAllDepartments();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'username' => $_POST['username'],
        'email' => $_POST['email'],
        'password' => $_POST['password'],
        'confirm_password' => $_POST['confirm_password'],
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'department_id' => $_POST['department_id']
    ];

    if ($data['password'] !== $data['confirm_password']) {
        $message = '<div class="card-panel red lighten-4 red-text text-darken-4">Passwords do not match!</div>';
    } else {
        $result = $admin->createDepartmentHead($data);
        if ($result['success']) {
            $message = '<div class="card-panel green lighten-4 green-text text-darken-4">Department Head created successfully!</div>';
        } else {
            $message = '<div class="card-panel red lighten-4 red-text text-darken-4">' . $result['message'] . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Department Head - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <?php include '../includes/admin_navbar.php'; ?>

    <div class="container">
        <div class="row">
            <div class="col s12">
                <h4>Create Department Head</h4>
                <?php echo $message; ?>

                <div class="card">
                    <div class="card-content">
                        <span class="card-title">New Department Head</span>
                        <form method="POST" id="createDeptHeadForm">
                            <div class="row">
                                <div class="input-field col s12 m6">
                                    <i class="material-icons prefix">person</i>
                                    <input type="text" id="username" name="username" required>
                                    <label for="username">Username</label>
                                </div>

                                <div class="input-field col s12 m6">
                                    <i class="material-icons prefix">email</i>
                                    <input type="email" id="email" name="email" required>
                                    <label for="email">Email</label>
                                </div>

                                <div class="input-field col s12 m6">
                                    <i class="material-icons prefix">person_outline</i>
                                    <input type="text" id="first_name" name="first_name" required>
                                    <label for="first_name">First Name</label>
                                </div>

                                <div class="input-field col s12 m6">
                                    <i class="material-icons prefix">person_outline</i>
                                    <input type="text" id="last_name" name="last_name" required>
                                    <label for="last_name">Last Name</label>
                                </div>

                                <div class="input-field col s12 m6">
                                    <i class="material-icons prefix">lock</i>
                                    <input type="password" id="password" name="password" required>
                                    <label for="password">Password</label>
                                </div>

                                <div class="input-field col s12 m6">
                                    <i class="material-icons prefix">lock_outline</i>
                                    <input type="password" id="confirm_password" name="confirm_password" required>
                                    <label for="confirm_password">Confirm Password</label>
                                </div>

                                <div class="input-field col s12">
                                    <i class="material-icons prefix">business</i>
                                    <select name="department_id" required>
                                        <option value="" disabled selected>Choose Department</option>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?php echo $dept['department_id']; ?>">
                                                <?php echo htmlspecialchars($dept['department_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label>Department</label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col s12">
                                    <button class="btn waves-effect waves-light blue darken-3" type="submit">
                                        Create Department Head
                                        <i class="material-icons right">send</i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var selects = document.querySelectorAll('select');
            M.FormSelect.init(selects);

            // Form validation
            document.getElementById('createDeptHeadForm').addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;

                if (password !== confirmPassword) {
                    e.preventDefault();
                    M.toast({html: 'Passwords do not match!', classes: 'red'});
                }
            });
        });
    </script>
</body>
</html> 