<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Employment.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$employment = new Employment($db);

$message = '';
$employmentId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$employmentId) {
    header("Location: view.php");
    exit();
}

$employmentData = $employment->getEmploymentById($employmentId, $_SESSION['user_id']);

if (!$employmentData) {
    header("Location: view.php");
    exit();
}

if (isset($_POST['update_employment'])) {
    $data = [
        'user_id' => $_SESSION['user_id'],
        'company_name' => $_POST['company_name'],
        'position_title' => $_POST['position_title'],
        'employment_type' => $_POST['employment_type'],
        'start_date' => $_POST['start_date'],
        'end_date' => $_POST['is_current'] ? null : $_POST['end_date'],
        'is_current' => isset($_POST['is_current']) ? 1 : 0,
        'industry' => $_POST['industry'],
        'location' => $_POST['location']
    ];

    if ($employment->updateEmployment($employmentId, $data)) {
        $message = '<div class="green-text">Employment record updated successfully!</div>';
        $employmentData = $employment->getEmploymentById($employmentId, $_SESSION['user_id']);
    } else {
        $message = '<div class="red-text">Failed to update employment record.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <!-- Materialize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/custom.css">
</head>
<body>

<?php include '../includes/alumni_navbar.php'; ?>

<div class="container">
    <div class="row">
        <div class="col s12">
            <h4>Edit Employment Record</h4>
            <?php echo $message; ?>
            
            <div class="card">
                <div class="card-content">
                    <form method="POST">
                        <div class="row">
                            <div class="input-field col s12 m6">
                                <input type="text" id="company_name" name="company_name" 
                                       value="<?php echo htmlspecialchars($employmentData['company_name']); ?>" required>
                                <label for="company_name">Company Name</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input type="text" id="position_title" name="position_title" 
                                       value="<?php echo htmlspecialchars($employmentData['position_title']); ?>" required>
                                <label for="position_title">Position Title</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s12 m6">
                                <select name="employment_type" required>
                                    <option value="" disabled>Choose employment type</option>
                                    <?php
                                    $types = ['Full-time', 'Part-time', 'Freelance', 'Contract', 'Self-employed'];
                                    foreach ($types as $type) {
                                        $selected = ($employmentData['employment_type'] == $type) ? 'selected' : '';
                                        echo "<option value=\"$type\" $selected>$type</option>";
                                    }
                                    ?>
                                </select>
                                <label>Employment Type</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input type="text" id="industry" name="industry" 
                                       value="<?php echo htmlspecialchars($employmentData['industry']); ?>" required>
                                <label for="industry">Industry</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s12 m6">
                                <input type="date" id="start_date" name="start_date" 
                                       value="<?php echo $employmentData['start_date']; ?>" required>
                                <label for="start_date">Start Date</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input type="date" id="end_date" name="end_date" 
                                       value="<?php echo $employmentData['end_date']; ?>" 
                                       <?php echo $employmentData['is_current'] ? 'disabled' : ''; ?>>
                                <label for="end_date">End Date</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col s12">
                                <label>
                                    <input type="checkbox" name="is_current" class="filled-in" 
                                           <?php echo $employmentData['is_current'] ? 'checked' : ''; ?> />
                                    <span>This is my current job</span>
                                </label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s12">
                                <input type="text" id="location" name="location" 
                                       value="<?php echo htmlspecialchars($employmentData['location']); ?>" required>
                                <label for="location">Location</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col s12">
                                <button class="btn waves-effect waves-light" type="submit" name="update_employment">
                                    Update Employment
                                    <i class="material-icons right">save</i>
                                </button>
                                <a href="view.php" class="btn waves-effect waves-light red">
                                    Cancel
                                    <i class="material-icons right">cancel</i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?> 