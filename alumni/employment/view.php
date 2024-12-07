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

$employmentHistory = $employment->getUserEmployment($_SESSION['user_id']);
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
            <h4>Employment History</h4>
            <a href="add.php" class="btn waves-effect waves-light">
                <i class="material-icons left">add</i>
                Add New Employment
            </a>
            
            <?php if (empty($employmentHistory)): ?>
                <div class="card-panel">
                    <p>No employment records found.</p>
                </div>
            <?php else: ?>
                <div class="timeline">
                    <?php foreach ($employmentHistory as $employment): ?>
                        <div class="card">
                            <div class="card-content">
                                <span class="card-title"><?php echo $employment['position_title']; ?></span>
                                <p class="company"><strong><?php echo $employment['company_name']; ?></strong></p>
                                <p class="duration">
                                    <?php 
                                    echo date('M Y', strtotime($employment['start_date'])) . ' - ';
                                    echo $employment['is_current'] ? 'Present' : date('M Y', strtotime($employment['end_date']));
                                    ?>
                                </p>
                                <p class="type"><?php echo $employment['employment_type']; ?></p>
                                <p class="location"><?php echo $employment['location']; ?></p>
                                <p class="industry"><em><?php echo $employment['industry']; ?></em></p>
                                
                                <?php if ($employment['is_current']): ?>
                                    <span class="new badge blue" data-badge-caption="Current"></span>
                                <?php endif; ?>
                                <div class="card-action">
                                    <a href="edit.php?id=<?php echo $employment['employment_id']; ?>" 
                                       class="btn waves-effect waves-light blue">
                                        <i class="material-icons left">edit</i>Edit
                                    </a>
                                    <button class="btn waves-effect waves-light red delete-employment" 
                                            data-id="<?php echo $employment['employment_id']; ?>">
                                        <i class="material-icons left">delete</i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete button clicks
    document.querySelectorAll('.delete-employment').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this employment record?')) {
                const employmentId = this.dataset.id;
                
                fetch('delete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'employment_id=' + employmentId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the card from the DOM
                        this.closest('.card').remove();
                        M.toast({html: 'Employment record deleted successfully'});
                    } else {
                        M.toast({html: data.message || 'Failed to delete employment record'});
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    M.toast({html: 'An error occurred while deleting the record'});
                });
            }
        });
    });
});
</script>
</body>
</html>