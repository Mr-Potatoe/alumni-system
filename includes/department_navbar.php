<nav class="blue darken-3">
    <div class="nav-wrapper container">
        <a href="<?php echo BASE_URL; ?>/admin/department/dashboard.php" class="brand-logo">
            <?php echo SITE_NAME; ?>
        </a>
        <a href="#" data-target="mobile-nav" class="sidenav-trigger"><i class="material-icons">menu</i></a>
        
        <ul class="right hide-on-med-and-down">
            <li><a href="<?php echo BASE_URL; ?>/admin/department/dashboard.php">
                <i class="material-icons left">dashboard</i>Dashboard
            </a></li>
            <li><a href="<?php echo BASE_URL; ?>/admin/department/view-alumni.php">
                <i class="material-icons left">people</i>Alumni
            </a></li>
            <li><a href="<?php echo BASE_URL; ?>/admin/department/employment-stats.php">
                <i class="material-icons left">assessment</i>Statistics
            </a></li>
            <li><a href="<?php echo BASE_URL; ?>/admin/department/send-announcement.php">
                <i class="material-icons left">announcement</i>Announcements
            </a></li>
            <li><a href="<?php echo BASE_URL; ?>/admin/auth/logout.php">
                <i class="material-icons left">exit_to_app</i>Logout
            </a></li>
        </ul>
    </div>
</nav>

<!-- Mobile Navigation -->
<ul class="sidenav" id="mobile-nav">
    <li><div class="user-view">
        <div class="background blue darken-3">
            <img src="<?php echo BASE_URL; ?>/assets/images/nav-bg.jpg" alt="background">
        </div>
        <span class="white-text name"><?php echo $_SESSION['admin_name']; ?></span>
        <span class="white-text email">Department Head</span>
    </div></li>
    <li><a href="<?php echo BASE_URL; ?>/admin/department/dashboard.php">
        <i class="material-icons">dashboard</i>Dashboard
    </a></li>
    <li><a href="<?php echo BASE_URL; ?>/admin/department/view-alumni.php">
        <i class="material-icons">people</i>Alumni
    </a></li>
    <li><a href="<?php echo BASE_URL; ?>/admin/department/employment-stats.php">
        <i class="material-icons">assessment</i>Statistics
    </a></li>
    <li><a href="<?php echo BASE_URL; ?>/admin/department/send-announcement.php">
        <i class="material-icons">announcement</i>Announcements
    </a></li>
    <li><div class="divider"></div></li>
    <li><a href="<?php echo BASE_URL; ?>/admin/auth/logout.php">
        <i class="material-icons">exit_to_app</i>Logout
    </a></li>
</ul>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var sidenav = document.querySelectorAll('.sidenav');
    M.Sidenav.init(sidenav);
});
</script> 