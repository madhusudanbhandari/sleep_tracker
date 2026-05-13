<?php
$rootPath = '../';
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Filter by quality if requested
$qualityFilter = $_GET['quality'] ?? '';
$whereExtra = '';
if (in_array($qualityFilter, ['Excellent', 'Good', 'Fair', 'Poor'])) {
    $qf = $conn->real_escape_string($qualityFilter);
    $whereExtra = " AND sleep_quality = '$qf'";
}

$records = $conn->query("SELECT * FROM sleep_records WHERE user_id = $userId $whereExtra ORDER BY sleep_time DESC");

$pageTitle = 'Sleep History';
include '../includes/header.php';
?>
<?php include '../includes/navbar.php'; ?>
<div class="wrapper">
    <div class="page-header">
        <div class="page-title">
            Sleep History
            <small>All your recorded sleep entries</small>
        </div>
        <a href="../sleep/add.php" class="btn btn-outline btn-sm">+ Add Record</a>
    </div>

    <!-- Filter Bar -->
    <div style="display:flex;gap:8px;margin-bottom:1.5rem;flex-wrap:wrap">
        <a href="history.php" class="btn btn-sm <?= !$qualityFilter ? 'btn-edit' : 'btn-outline' ?>">All</a>
        <a href="history.php?quality=Excellent" class="btn btn-sm <?= $qualityFilter==='Excellent' ? 'btn-edit' : 'btn-outline' ?>">Excellent</a>
        <a href="history.php?quality=Good" class="btn btn-sm <?= $qualityFilter==='Good' ? 'btn-edit' : 'btn-outline' ?>">Good</a>
        <a href="history.php?quality=Fair" class="btn btn-sm <?= $qualityFilter==='Fair' ? 'btn-edit' : 'btn-outline' ?>">Fair</a>
        <a href="history.php?quality=Poor" class="btn btn-sm <?= $qualityFilter==='Poor' ? 'btn-edit' : 'btn-outline' ?>">Poor</a>
    </div>

    <?php if ($records->num_rows > 0): ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Sleep Time</th>
                    <th>Wake Up Time</th>
                    <th>Duration</th>
                    <th>Quality</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php $i = 1; while ($row = $records->fetch_assoc()):
                $quality = $row['sleep_quality'];
                $badgeClass = strtolower($quality);
            ?>
                <tr>
                    <td style="color:var(--muted)"><?= $i++ ?></td>
                    <td><?= date('M d, Y', strtotime($row['sleep_time'])) ?></td>
                    <td><?= date('h:i A', strtotime($row['sleep_time'])) ?></td>
                    <td><?= date('h:i A', strtotime($row['wakeup_time'])) ?></td>
                    <td><strong><?= $row['duration'] ?></strong> hrs</td>
                    <td><span class="badge badge-<?= $badgeClass ?>"><?= $quality ?></span></td>
                    <td style="color:var(--muted);max-width:200px"><?= $row['notes'] ? htmlspecialchars(substr($row['notes'],0,60)).(strlen($row['notes'])>60?'...':'') : '—' ?></td>
                    <td>
                        <div class="td-actions">
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-edit">Edit</a>
                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="card">
        <div class="empty-state">
            <div class="icon">😴</div>
            <h3>No records found</h3>
            <p><?= $qualityFilter ? "No $qualityFilter quality records found." : "You haven't logged any sleep yet." ?></p>
            <br>
            <a href="add.php" class="btn btn-outline">Log Your First Sleep</a>
        </div>
    </div>
    <?php endif; ?>
</div>
</body>
</html>