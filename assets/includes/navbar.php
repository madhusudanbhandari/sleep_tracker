<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar">
    <a href="<?= isset($rootPath) ? $rootPath : '' ?>dashboard.php" class="brand">
        <span>🌙</span> SleepTrack
    </a>
    <div class="nav-links">
        <a href="<?= isset($rootPath) ? $rootPath : '' ?>dashboard.php" 
           class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
            <span>📊</span> Dashboard
        </a>
        <a href="<?= isset($rootPath) ? $rootPath : '' ?>sleep/add.php" 
           class="<?= $currentPage === 'add.php' ? 'active' : '' ?>">
            <span>➕</span> Add Sleep
        </a>
        <a href="<?= isset($rootPath) ? $rootPath : '' ?>sleep/history.php" 
           class="<?= $currentPage === 'history.php' ? 'active' : '' ?>">
            <span>📋</span> History
        </a>
        <a href="<?= isset($rootPath) ? $rootPath : '' ?>auth/logout.php" class="btn-logout">
            Logout
        </a>
    </div>
</nav>