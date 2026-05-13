<?php
if (session_status() === PHP_SESSION_NONE) {
    require_once __DIR__ . '/../config/db.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — SleepTrack' : 'SleepTrack' ?></title>
    <link rel="stylesheet" href="<?= isset($rootPath) ? $rootPath : '' ?>assets/style.css">
</head>
<body>