<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/DepartmentHead.php';
require_once '../../includes/department_auth.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'Department Head') {
    header("Location: ../auth/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$deptHead = new DepartmentHead($db);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'subject' => $_POST['subject'],
        'message' => $_POST['message'],
        'graduation_year' => $_POST['graduation_year'] ?? null
    ];

    if ($deptHead->sendDepartmentAnnouncement($data)) {
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
    <title>Send Department Announcement - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
<?php include '../includes/department_navbar.php'; ?>

    <div class="container">
        <div class="row">
            <div class="col s12">
                <h4>Send Department Announcement</h4>
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

                                <div class="input-field col s12">
                                    <i class="material-icons prefix">date_range</i>
                                    <select id="graduation_year" name="graduation_year">
                                        <option value="">All Years</option>
                                        <?php 
                                        $currentYear = date('Y');
                                        for ($year = $currentYear; $year >= $currentYear - 10; $year--) {
                                            echo "<option value='$year'>$year</option>";
                                        }
                                        ?>
                                    </select>
                                    <label>Specific Graduation Year (Optional)</label>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var selects = document.querySelectorAll('select');
            M.FormSelect.init(selects);

            var textareas = document.querySelectorAll('.materialize-textarea');
            M.textareaAutoResize(textareas[0]);
        });
    </script>
</body>
</html> 