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

// Get filters
$year = isset($_GET['year']) ? $_GET['year'] : '';
$industry = isset($_GET['industry']) ? $_GET['industry'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';

// Get filter options
$years = $admin->getGraduationYears();
$industries = $admin->getIndustries();
$locations = $admin->getLocations();

// Get alumni list
$alumni = $admin->getAlumniDirectory($year, $industry, $location);

// Debug: Print the number of alumni found
echo "<!-- Debug: Found " . count($alumni) . " alumni -->";

// Add this section right before the table to debug the data
echo "<!-- Debug Information -->";
echo "<!-- Year filter: " . htmlspecialchars($year) . " -->";
echo "<!-- Industry filter: " . htmlspecialchars($industry) . " -->";
echo "<!-- Location filter: " . htmlspecialchars($location) . " -->";

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
            <h4>Alumni Directory</h4>

            <!-- Filters -->
            <div class="card">
                <div class="card-content">
                    <span class="card-title">
                        <i class="material-icons left">filter_list</i>
                        Filter Alumni
                    </span>
                    <form method="GET">
                        <div class="row">
                            <div class="input-field col s12 m4">
                                <i class="material-icons prefix">date_range</i>
                                <select name="year">
                                    <option value="">All Years</option>
                                    <?php foreach ($years as $y): ?>
                                        <option value="<?php echo $y; ?>" <?php echo $y == $year ? 'selected' : ''; ?>>
                                            <?php echo $y; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Graduation Year</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <i class="material-icons prefix">business</i>
                                <select name="industry">
                                    <option value="">All Industries</option>
                                    <?php foreach ($industries as $ind): ?>
                                        <option value="<?php echo $ind; ?>" <?php echo $ind == $industry ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($ind); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Industry</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <i class="material-icons prefix">location_on</i>
                                <select name="location">
                                    <option value="">All Locations</option>
                                    <?php foreach ($locations as $loc): ?>
                                        <option value="<?php echo $loc; ?>" <?php echo $loc == $location ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($loc); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Location</label>
                            </div>
                        </div>
                        <button class="btn waves-effect waves-light blue" type="submit">
                            Apply Filters
                            <i class="material-icons right">search</i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Alumni List -->
            <div class="card">
                <div class="card-content">
                    <span class="card-title">
                        <i class="material-icons left">people</i>
                        Alumni List
                        <span class="badge new blue" data-badge-caption="alumni"><?php echo count($alumni); ?></span>
                    </span>
                    
                    <table class="striped highlight responsive-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Graduation Year</th>
                                <th>Current Position</th>
                                <th>Company</th>
                                <th>Location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alumni as $alum): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($alum['first_name'] . ' ' . $alum['last_name']); ?></td>
                                    <td>
                                        <span class="chip">
                                            <?php echo htmlspecialchars($alum['graduation_year']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($alum['current_position'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($alum['company'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($alum['location'] ?? 'N/A'); ?></td>
                                    <td>
                                        <a href="#modal-<?php echo $alum['user_id']; ?>" 
                                           class="btn-floating btn-small waves-effect waves-light blue modal-trigger tooltipped"
                                           data-position="left" data-tooltip="View Details">
                                            <i class="material-icons">visibility</i>
                                        </a>
                                    </td>
                                </tr>

                                <!-- Modal Structure -->
                                <div id="modal-<?php echo $alum['user_id']; ?>" class="modal">
                                    <div class="modal-content">
                                        <h4><?php echo htmlspecialchars($alum['first_name'] . ' ' . $alum['last_name']); ?></h4>
                                        <div class="row">
                                            <div class="col s12">
                                                <ul class="collection">
                                                    <li class="collection-item avatar">
                                                        <i class="material-icons circle blue">person</i>
                                                        <span class="title">Personal Information</span>
                                                        <p>
                                                            Email: <?php echo htmlspecialchars($alum['email']); ?><br>
                                                            Phone: <?php echo htmlspecialchars($alum['contact_number'] ?? 'N/A'); ?>
                                                        </p>
                                                    </li>
                                                    <li class="collection-item avatar">
                                                        <i class="material-icons circle green">work</i>
                                                        <span class="title">Employment Information</span>
                                                        <p>
                                                            Position: <?php echo htmlspecialchars($alum['current_position'] ?? 'N/A'); ?><br>
                                                            Company: <?php echo htmlspecialchars($alum['company'] ?? 'N/A'); ?><br>
                                                            Industry: <?php echo htmlspecialchars($alum['industry'] ?? 'N/A'); ?>
                                                        </p>
                                                    </li>
                                                    <li class="collection-item avatar">
                                                        <i class="material-icons circle red">school</i>
                                                        <span class="title">Academic Information</span>
                                                        <p>
                                                            Graduation Year: <?php echo htmlspecialchars($alum['graduation_year']); ?><br>
                                                            Semester: <?php echo htmlspecialchars($alum['graduation_semester'] ?? 'N/A'); ?>
                                                        </p>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="#!" class="modal-close waves-effect waves-green btn-flat">Close</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add this debug section at the bottom of the table -->
<?php if (empty($alumni)): ?>
    <tr>
        <td colspan="6" class="center-align">
            <div class="card-panel yellow lighten-4">
                <span class="orange-text text-darken-4">
                    <i class="material-icons left">info</i>
                    No alumni found with the current filters.
                    <?php if ($year || $industry || $location): ?>
                        Try removing some filters.
                    <?php endif; ?>
                </span>
            </div>
        </td>
    </tr>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize select elements
    var selects = document.querySelectorAll('select');
    M.FormSelect.init(selects);

    // Initialize modals
    var modals = document.querySelectorAll('.modal');
    M.Modal.init(modals);

    // Initialize tooltips
    var tooltips = document.querySelectorAll('.tooltipped');
    M.Tooltip.init(tooltips);
});
</script>

<?php include '../../includes/footer.php'; ?>
