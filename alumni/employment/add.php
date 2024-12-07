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

if (isset($_POST['add_employment'])) {
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

    if ($employment->addEmployment($data)) {
        $message = '<div class="green-text">Employment record added successfully!</div>';
    } else {
        $message = '<div class="red-text">Failed to add employment record.</div>';
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
            <h4>Add Employment Record</h4>
            <?php echo $message; ?>
            
            <div class="card">
                <div class="card-content">
                    <form method="POST" action="add.php">
                        <div class="row">
                            <div class="input-field col s12 m6">
                                <input type="text" id="company_name" name="company_name" required>
                                <label for="company_name">Company Name</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input type="text" id="position_title" name="position_title" required>
                                <label for="position_title">Position Title</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s12 m6">
                                <select name="employment_type" required>
                                    <option value="" disabled selected>Choose employment type</option>
                                    <option value="Full-time">Full-time</option>
                                    <option value="Part-time">Part-time</option>
                                    <option value="Freelance">Freelance</option>
                                    <option value="Contract">Contract</option>
                                    <option value="Self-employed">Self-employed</option>
                                </select>
                                <label>Employment Type</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input type="text" id="industry" name="industry" required>
                                <label for="industry">Industry</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s12 m6">
                                <input type="date" id="start_date" name="start_date" required>
                                <label for="start_date">Start Date</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input type="date" id="end_date" name="end_date">
                                <label for="end_date">End Date</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col s12">
                                <label>
                                    <input type="checkbox" name="is_current" id="is_current" class="filled-in" />
                                    <span>This is my current job</span>
                                </label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s12">
                                <input type="text" id="location" name="location" required>
                                <label for="location">Location</label>
                            </div>
                        </div>

                        <button class="btn waves-effect waves-light" type="submit" name="add_employment">
                            Add Employment
                            <i class="material-icons right">send</i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
