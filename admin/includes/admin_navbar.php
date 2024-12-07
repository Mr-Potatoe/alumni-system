<nav class="blue darken-3">
    <div class="nav-wrapper container">
        <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="brand-logo">Admin Panel</a>
        <a href="#" data-target="mobile-nav" class="sidenav-trigger"><i class="material-icons">menu</i></a>
        <ul id="nav-mobile" class="right hide-on-med-and-down">
            <!-- Main Navigation -->
            <li><a href="<?php echo BASE_URL; ?>/admin/dashboard.php">
                <i class="material-icons left">dashboard</i>Dashboard
            </a></li>
            
            <li><a href="<?php echo BASE_URL; ?>/admin/alumni.php">
                <i class="material-icons left">people</i>Alumni
            </a></li>
            
            <!-- Registration Dropdown -->
            <li>
                <a class="dropdown-trigger" href="#!" data-target="registration-dropdown">
                    <i class="material-icons left">person_add</i>Registrations
                    <i class="material-icons right">arrow_drop_down</i>
                </a>
            </li>
            
            <!-- Communications Dropdown -->
            <li>
                <a class="dropdown-trigger" href="#!" data-target="communications-dropdown">
                    <i class="material-icons left">email</i>Communications
                    <i class="material-icons right">arrow_drop_down</i>
                </a>
            </li>
            
            <!-- Reports Dropdown -->
            <li>
                <a class="dropdown-trigger" href="#!" data-target="reports-dropdown">
                    <i class="material-icons left">assessment</i>Reports
                    <i class="material-icons right">arrow_drop_down</i>
                </a>
            </li>
            
            <!-- Admin Dropdown -->
            <li>
                <a class="dropdown-trigger" href="#!" data-target="admin-dropdown">
                    <i class="material-icons left">account_circle</i>Admin
                    <i class="material-icons right">arrow_drop_down</i>
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- Registration Dropdown Structure -->
<ul id="registration-dropdown" class="dropdown-content">
    <li><a href="<?php echo BASE_URL; ?>/admin/registrations.php">All Registrations</a></li>
    <li><a href="<?php echo BASE_URL; ?>/admin/pending.php">Pending Approvals</a></li>
</ul>

<!-- Communications Dropdown Structure -->
<ul id="communications-dropdown" class="dropdown-content">
    <li><a href="<?php echo BASE_URL; ?>/admin/communications/send-announcement.php">Send Announcement</a></li>
    <li><a href="<?php echo BASE_URL; ?>/admin/communications/view-logs.php">Communication Logs</a></li>
</ul>

<!-- Reports Dropdown Structure -->
<ul id="reports-dropdown" class="dropdown-content">
    <li><a href="<?php echo BASE_URL; ?>/admin/reports.php">Overview</a></li>
    <li><a href="<?php echo BASE_URL; ?>/admin/reports/employment-stats.php">Employment Stats</a></li>
    <li><a href="<?php echo BASE_URL; ?>/admin/reports/alumni-directory.php">Alumni Directory</a></li>
</ul>

<!-- Admin Dropdown Structure -->
<ul id="admin-dropdown" class="dropdown-content">
    <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'Super Admin'): ?>
    <li><a href="<?php echo BASE_URL; ?>/admin/department/create-department.php">
        <i class="material-icons">business</i>Manage Departments
    </a></li>
    <li><a href="<?php echo BASE_URL; ?>/admin/department/create-department-head.php">
        <i class="material-icons">person_add</i>Add Department Head
    </a></li>
    <li class="divider"></li>
    <?php endif; ?>
    <li><a href="<?php echo BASE_URL; ?>/admin/settings.php">
        <i class="material-icons">settings</i>Settings
    </a></li>
    <li class="divider"></li>
    <li><a href="<?php echo BASE_URL; ?>/admin/auth/logout.php">
        <i class="material-icons">exit_to_app</i>Logout
    </a></li>
</ul>

<!-- Mobile Navigation -->
<ul class="sidenav" id="mobile-nav">
    <li><div class="user-view">
        <div class="background blue darken-3"></div>
        <span class="white-text name"><?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Administrator'; ?></span>
        <span class="white-text email"><?php echo isset($_SESSION['admin_email']) ? $_SESSION['admin_email'] : ''; ?></span>
    </div></li>
    
    <li><a href="<?php echo BASE_URL; ?>/admin/dashboard.php">
        <i class="material-icons">dashboard</i>Dashboard
    </a></li>
    <li><a href="<?php echo BASE_URL; ?>/admin/alumni.php">
        <i class="material-icons">people</i>Alumni
    </a></li>
    
    <li><div class="divider"></div></li>
    <li><a class="subheader">Registrations</a></li>
    <li><a href="<?php echo BASE_URL; ?>/admin/registrations.php">
        <i class="material-icons">list</i>All Registrations
    </a></li>
    <li><a href="<?php echo BASE_URL; ?>/admin/pending.php">
        <i class="material-icons">pending</i>Pending Approvals
    </a></li>
    
    <li><div class="divider"></div></li>
    <li><a class="subheader">Communications</a></li>
    <li><a href="<?php echo BASE_URL; ?>/admin/communications/send-announcement.php">
        <i class="material-icons">send</i>Send Announcement
    </a></li>
    <li><a href="<?php echo BASE_URL; ?>/admin/communications/view-logs.php">
        <i class="material-icons">history</i>Communication Logs
    </a></li>
    
    <li><div class="divider"></div></li>
    <li><a class="subheader">Reports</a></li>
    <li><a href="<?php echo BASE_URL; ?>/admin/reports.php">
        <i class="material-icons">assessment</i>Overview
    </a></li>
    <li><a href="<?php echo BASE_URL; ?>/admin/reports/employment-stats.php">
        <i class="material-icons">work</i>Employment Stats
    </a></li>
    <li><a href="<?php echo BASE_URL; ?>/admin/reports/alumni-directory.php">
        <i class="material-icons">book</i>Alumni Directory
    </a></li>
    
    <li><div class="divider"></div></li>
    <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'Super Admin'): ?>
    <li><a href="<?php echo BASE_URL; ?>/admin/department/create-department.php">
        <i class="material-icons">business</i>Manage Departments
    </a></li>
    <li><a href="<?php echo BASE_URL; ?>/admin/department/create-department-head.php">
        <i class="material-icons">person_add</i>Add Department Head
    </a></li>
    <?php endif; ?>
    <li><a href="<?php echo BASE_URL; ?>/admin/settings.php">
        <i class="material-icons">settings</i>Settings
    </a></li>
    <li><a href="<?php echo BASE_URL; ?>/admin/auth/logout.php">
        <i class="material-icons">exit_to_app</i>Logout
    </a></li>
</ul>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dropdowns
    var dropdowns = document.querySelectorAll('.dropdown-trigger');
    M.Dropdown.init(dropdowns, {
        coverTrigger: false,
        constrainWidth: false
    });
    
    // Initialize sidenav
    var sidenav = document.querySelectorAll('.sidenav');
    M.Sidenav.init(sidenav, {
        edge: 'left'
    });
});
</script>
