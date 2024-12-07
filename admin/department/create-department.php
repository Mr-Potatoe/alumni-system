<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Admin.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Check if user is Super Admin
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'Super Admin') {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    exit();
}

// Handle department creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create':
            $department_name = trim($_POST['department_name']);
            
            $stmt = $db->prepare("INSERT INTO departments (department_name) VALUES (:department_name)");
            $stmt->bindParam(':department_name', $department_name);
            
            if ($stmt->execute()) {
                $_SESSION['success_msg'] = "Department created successfully!";
            } else {
                $_SESSION['error_msg'] = "Error creating department: " . $stmt->errorInfo()[2];
            }
            break;
            
        case 'delete':
            $department_id = $_POST['department_id'];
            
            // Check if department has associated users or admins
            $check_stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE department_id = :dept_id 
                                      UNION ALL 
                                      SELECT COUNT(*) FROM admins WHERE department_id = :dept_id");
            $check_stmt->bindParam(':dept_id', $department_id);
            $check_stmt->execute();
            $result = $check_stmt->fetchAll(PDO::FETCH_ASSOC);
            $has_dependencies = false;
            
            foreach ($result as $row) {
                if ($row['count'] > 0) {
                    $has_dependencies = true;
                    break;
                }
            }
            
            if ($has_dependencies) {
                $_SESSION['error_msg'] = "Cannot delete department: It has associated users or admins.";
            } else {
                $stmt = $db->prepare("DELETE FROM departments WHERE department_id = :department_id");
                $stmt->bindParam(':department_id', $department_id);
                
                if ($stmt->execute()) {
                    $_SESSION['success_msg'] = "Department deleted successfully!";
                } else {
                    $_SESSION['error_msg'] = "Error deleting department: " . $stmt->errorInfo()[2];
                }
            }
            break;
    }
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch existing departments
$departments = [];
$result = $db->query("SELECT * FROM departments ORDER BY department_name");
if ($result) {
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $departments[] = $row;
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
<!-- Main Content -->
<div class="container">
    <h4 class="center-align">Manage Departments</h4>
    
    <!-- Messages -->
    <?php if (isset($_SESSION['success_msg'])): ?>
        <div class="card-panel green lighten-4 green-text text-darken-4">
            <?php 
            echo $_SESSION['success_msg'];
            unset($_SESSION['success_msg']);
            ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_msg'])): ?>
        <div class="card-panel red lighten-4 red-text text-darken-4">
            <?php 
            echo $_SESSION['error_msg'];
            unset($_SESSION['error_msg']);
            ?>
        </div>
    <?php endif; ?>
    
    <!-- Create Department Form -->
    <div class="row">
        <div class="col s12 m6">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Add New Department</span>
                    <form method="POST">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="input-field">
                            <input type="text" id="department_name" name="department_name" required>
                            <label for="department_name">Department Name</label>
                        </div>
                        
                        <button class="btn waves-effect waves-light blue darken-3" type="submit">
                            Create Department
                            <i class="material-icons right">add</i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Existing Departments List -->
        <div class="col s12 m6">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Existing Departments</span>
                    <?php if (empty($departments)): ?>
                        <p>No departments found.</p>
                    <?php else: ?>
                        <table class="striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($departments as $dept): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($dept['department_name']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($dept['created_at'])); ?></td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="department_id" value="<?php echo $dept['department_id']; ?>">
                                                <button type="submit" class="btn-small red" onclick="return confirm('Are you sure you want to delete this department?')">
                                                    <i class="material-icons">delete</i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
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
    });
</script>
</body>
</html> 