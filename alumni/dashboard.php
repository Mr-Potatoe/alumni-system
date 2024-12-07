<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/User.php';
require_once '../classes/Employment.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$employment = new Employment($db);

// Get user details
$userDetails = $user->getUserById($_SESSION['user_id']);
$employmentHistory = $employment->getUserEmployment($_SESSION['user_id']);
?>

<?php include '../includes/header.php'; ?>
<?php include 'includes/alumni_navbar.php'; ?>

<div class="container">
    <div class="row">
        <div class="col s12">
            <h4>Welcome, <?php echo $_SESSION['user_name']; ?></h4>
        </div>
    </div>
    <div class="row">
        <div class="col s12">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Quick Actions</span>
                    <div class="row">
                        <div class="col s12 m3">
                            <a href="profile.php" class="btn-large waves-effect waves-light blue darken-1 white-text" style="width: 100%">
                                <i class="material-icons left">person</i>
                                Update Profile
                            </a>
                        </div>
                        <div class="col s12 m3">
                            <a href="employment/add.php" class="btn-large waves-effect waves-light green darken-1 white-text" style="width: 100%">
                                <i class="material-icons left">work</i>
                                Add Job
                            </a>
                        </div>
                        <div class="col s12 m3">
                            <a href="address.php" class="btn-large waves-effect waves-light orange darken-1 white-text" style="width: 100%">
                                <i class="material-icons left">location_on</i>
                                Update Address
                            </a>
                        </div>
                        <div class="col s12 m3">
                            <a href="communications.php" class="btn-large waves-effect waves-light purple darken-1 white-text" style="width: 100%">
                                <i class="material-icons left">mail</i>
                                Messages
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Profile Summary Card -->
        <div class="col s12 m6">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Profile Summary</span>
                    <p><strong>Email:</strong> <?php echo $userDetails['email']; ?></p>
                    <p><strong>Graduation Year:</strong> <?php echo $userDetails['graduation_year']; ?></p>
                    <p><strong>Status:</strong> <?php echo $userDetails['registration_status']; ?></p>
                    <a href="profile.php" class="btn waves-effect waves-light">Edit Profile</a>
                </div>
            </div>
        </div>

        <!-- Employment Summary Card -->
        <div class="col s12 m6">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Employment Summary</span>
                    <?php if ($currentJob = $employment->getCurrentEmployment($_SESSION['user_id'])): ?>
                        <p><strong>Current Position:</strong> <?php echo $currentJob['position_title']; ?></p>
                        <p><strong>Company:</strong> <?php echo $currentJob['company_name']; ?></p>
                        <p><strong>Location:</strong> <?php echo $currentJob['location']; ?></p>
                    <?php else: ?>
                        <p>No current employment recorded</p>
                    <?php endif; ?>
                    <div class="card-action">
                        <a href="employment/view.php" class="btn waves-effect waves-light">View History</a>
                        <a href="employment/add.php" class="btn waves-effect waves-light">Add Employment</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

<?php include '../includes/footer.php'; ?>