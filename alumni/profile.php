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

$userDetails = $user->getUserById($_SESSION['user_id']);
$message = '';

if (isset($_POST['update_profile'])) {
    $data = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'middle_name' => $_POST['middle_name'],
        'contact_number' => $_POST['contact_number'],
        'birth_date' => $_POST['birth_date'],
        'gender' => $_POST['gender'],
        'graduation_semester' => $_POST['graduation_semester']
    ];

    if ($user->updateProfile($_SESSION['user_id'], $data)) {
        $message = '<div class="green-text">Profile updated successfully!</div>';
        $userDetails = $user->getUserById($_SESSION['user_id']); // Refresh data
    } else {
        $message = '<div class="red-text">Failed to update profile.</div>';
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include 'includes/alumni_navbar.php'; ?>

<div class="container">
    <div class="row">
        <div class="col s12">
            <h4>Profile Management</h4>
            <?php echo $message; ?>
            
            <div class="card">
                <div class="card-content">
                    <form method="POST">
                        <div class="row">
                            <div class="input-field col s12 m4">
                                <input type="text" id="first_name" name="first_name" 
                                       value="<?php echo $userDetails['first_name']; ?>" required>
                                <label for="first_name">First Name</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <input type="text" id="middle_name" name="middle_name" 
                                       value="<?php echo $userDetails['middle_name']; ?>">
                                <label for="middle_name">Middle Name</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <input type="text" id="last_name" name="last_name" 
                                       value="<?php echo $userDetails['last_name']; ?>" required>
                                <label for="last_name">Last Name</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s12 m6">
                                <input type="tel" id="contact_number" name="contact_number" 
                                       value="<?php echo $userDetails['contact_number']; ?>">
                                <label for="contact_number">Contact Number</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input type="date" id="birth_date" name="birth_date" 
                                       value="<?php echo $userDetails['birth_date']; ?>">
                                <label for="birth_date">Birth Date</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s12 m6">
                                <select name="gender">
                                    <option value="" disabled>Choose gender</option>
                                    <option value="Male" <?php echo $userDetails['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo $userDetails['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                                </select>
                                <label>Gender</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <select name="graduation_semester">
                                    <option value="" disabled>Choose semester</option>
                                    <option value="First" <?php echo $userDetails['graduation_semester'] == 'First' ? 'selected' : ''; ?>>First</option>
                                    <option value="Second" <?php echo $userDetails['graduation_semester'] == 'Second' ? 'selected' : ''; ?>>Second</option>
                                    <option value="Summer" <?php echo $userDetails['graduation_semester'] == 'Summer' ? 'selected' : ''; ?>>Summer</option>
                                </select>
                                <label>Graduation Semester</label>
                            </div>
                        </div>

                        <button class="btn waves-effect waves-light" type="submit" name="update_profile">
                            Update Profile
                            <i class="material-icons right">save</i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
