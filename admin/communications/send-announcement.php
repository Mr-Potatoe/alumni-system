<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Admin.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$admin = new Admin($db);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'subject' => $_POST['subject'],
        'message' => $_POST['message'],
        'recipient_group' => $_POST['recipient_group'],
        'graduation_year' => $_POST['graduation_year'] ?? null
    ];

    if ($admin->sendAnnouncement($data)) {
        $message = '<div class="card-panel green lighten-4 green-text text-darken-4">Announcement sent successfully!</div>';
    } else {
        $message = '<div class="card-panel red lighten-4 red-text text-darken-4">Failed to send announcement.</div>';
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
<?php include '../includes/admin_navbar.php'; ?>

<div class="container">
    <div class="row">
        <div class="col s12">
            <h4 class="header">Send Announcement</h4>
            <?php echo $message; ?>

            <div class="card">
                <div class="card-content">
                    <span class="card-title">New Announcement</span>
                    <form method="POST">
                        <div class="row">
                            <div class="input-field col s12">
                                <i class="material-icons prefix">title</i>
                                <input type="text" id="subject" name="subject" required>
                                <label for="subject">Subject</label>
                            </div>

                            <div class="input-field col s12">
                                <i class="material-icons prefix">message</i>
                                <textarea id="message" name="message" class="materialize-textarea" required></textarea>
                                <label for="message">Message</label>
                            </div>

                            <div class="input-field col s12 m6">
                                <i class="material-icons prefix">group</i>
                                <select id="recipient_group" name="recipient_group" required>
                                    <option value="" disabled selected>Choose recipient group</option>
                                    <option value="All Alumni">All Alumni</option>
                                    <option value="Specific Graduation Year">Specific Graduation Year</option>
                                    <option value="Filtered Group">Filtered Group</option>
                                </select>
                                <label>Recipients</label>
                            </div>

                            <div class="input-field col s12 m6" id="yearSelect" style="display: none;">
                                <i class="material-icons prefix">date_range</i>
                                <select id="graduation_year" name="graduation_year">
                                    <option value="" disabled selected>Select Year</option>
                                    <?php 
                                    $currentYear = date('Y');
                                    for ($year = $currentYear; $year >= $currentYear - 10; $year--) {
                                        echo "<option value='$year'>$year</option>";
                                    }
                                    ?>
                                </select>
                                <label>Graduation Year</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col s12">
                                <button class="btn waves-effect waves-light blue darken-3" type="submit">
                                    Send Announcement
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize select elements
    var selects = document.querySelectorAll('select');
    M.FormSelect.init(selects);

    // Initialize textareas
    var textareas = document.querySelectorAll('.materialize-textarea');
    M.textareaAutoResize(textareas[0]);

    const form = document.querySelector('form');
    const recipientGroupSelect = document.getElementById('recipient_group');
    const yearSelect = document.getElementById('yearSelect');
    const graduationYearSelect = document.getElementById('graduation_year');

    // Handle recipient group selection
    recipientGroupSelect.addEventListener('change', function() {
        yearSelect.style.display = this.value === 'Specific Graduation Year' ? 'block' : 'none';
        
        if (this.value === 'Specific Graduation Year') {
            graduationYearSelect.required = true;
            M.FormSelect.init(graduationYearSelect);
        } else {
            graduationYearSelect.required = false;
        }
    });

    // Form validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const subject = document.getElementById('subject').value.trim();
        const message = document.getElementById('message').value.trim();
        
        if (!subject || !message) {
            M.toast({html: 'Please fill in all required fields', classes: 'red'});
            return;
        }

        if (recipientGroupSelect.value === 'Specific Graduation Year' && !graduationYearSelect.value) {
            M.toast({html: 'Please select a graduation year', classes: 'red'});
            return;
        }

        // Show loading indicator
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="material-icons right">loop</i>Sending...';
        submitBtn.disabled = true;

        // Submit the form
        form.submit();
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
