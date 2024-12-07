<nav class="blue darken-3">
    <div class="nav-wrapper container">
        <a href="<?php echo BASE_URL; ?>/alumni/dashboard.php" class="brand-logo"><?php echo SITE_NAME; ?></a>
        <a href="#" data-target="mobile-nav" class="sidenav-trigger"><i class="material-icons">menu</i></a>
        <ul id="nav-mobile" class="right hide-on-med-and-down">
            <li><a href="<?php echo BASE_URL; ?>/alumni/dashboard.php">Dashboard</a></li>
            <li><a href="<?php echo BASE_URL; ?>/alumni/profile.php">Profile</a></li>
            <li><a href="<?php echo BASE_URL; ?>/alumni/employment/view.php">Employment</a></li>
            <li><a href="<?php echo BASE_URL; ?>/alumni/address.php">Addresses</a></li>
            <li>
                <a class="dropdown-trigger" href="#!" data-target="account-dropdown">
                    <?php echo $_SESSION['user_name']; ?>
                    <i class="material-icons right">arrow_drop_down</i>
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- Mobile Navigation -->
<ul class="sidenav" id="mobile-nav">
    <li><a href="<?php echo BASE_URL; ?>/alumni/dashboard.php">Dashboard</a></li>
    <li><a href="<?php echo BASE_URL; ?>/alumni/profile.php">Profile</a></li>
    <li><a href="<?php echo BASE_URL; ?>/alumni/employment/view.php">Employment</a></li>
    <li><a href="<?php echo BASE_URL; ?>/auth/logout.php">Logout</a></li>
</ul>

<!-- Dropdown Structure -->
<ul id="account-dropdown" class="dropdown-content">
    <li><a href="<?php echo BASE_URL; ?>/alumni/settings.php">Settings</a></li>
    <li class="divider"></li>
    <li><a href="<?php echo BASE_URL; ?>/auth/logout.php">Logout</a></li>
</ul> 