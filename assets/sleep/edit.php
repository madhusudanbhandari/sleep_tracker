<?php
$rootPath = '../';
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$id = intval($_GET['id'] ?? 0);

if (!$id) {
    header("Location: history.php");
    exit;
}

// Fetch record — make sure it belongs to this user
$stmt = $conn->prepare("SELECT * FROM sleep_records WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $userId);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();

if (!$record) {
    header("Location: history.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sleepTime  = $_POST['sleep_time'] ?? '';
    $wakeupTime = $_POST['wakeup_time'] ?? '';
    $quality    = $_POST['sleep_quality'] ?? '';
    $notes      = trim($_POST['notes'] ?? '');

    if (empty($sleepTime) || empty($wakeupTime) || empty($quality)) {
        $error = 'Please fill in all required fields.';
    } else {
        $sleep  = new DateTime($sleepTime);
        $wakeup = new DateTime($wakeupTime);

        if ($wakeup <= $sleep) {
            $wakeup->modify('+1 day');
        }

        $diff     = $sleep->diff($wakeup);
        $duration = round($diff->h + ($diff->i / 60), 2);

        $upd = $conn->prepare("UPDATE sleep_records SET sleep_time=?, wakeup_time=?, duration=?, sleep_quality=?, notes=? WHERE id=? AND user_id=?");
        $upd->bind_param("ssdssii", $sleepTime, $wakeupTime, $duration, $quality, $notes, $id, $userId);

        if ($upd->execute()) {
            $success = "Record updated! New duration: {$duration} hours.";
            // Refresh record
            $stmt->execute();
            $result = $stmt->get_result();
            $record = $result->fetch_assoc();
        } else {
            $error = 'Update failed. Please try again.';
        }
    }
}

// Format for datetime-local input
$sleepVal  = date('Y-m-d\TH:i', strtotime($record['sleep_time']));
$wakeupVal = date('Y-m-d\TH:i', strtotime($record['wakeup_time']));

$pageTitle = 'Edit Record';
include '../includes/header.php';
?>
<?php include '../includes/navbar.php'; ?>
<div class="wrapper">
    <div class="page-header">
        <div class="page-title">
            Edit Sleep Record
            <small>Logged on <?= date('M d, Y', strtotime($record['sleep_time'])) ?></small>
        </div>
        <a href="history.php" class="btn btn-outline btn-sm">← Back to History</a>
    </div>

    <div class="card" style="max-width:600px">
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Sleep Time *</label>
                    <input type="datetime-local" name="sleep_time" value="<?= $sleepVal ?>" required>
                </div>
                <div class="form-group">
                    <label>Wake Up Time *</label>
                    <input type="datetime-local" name="wakeup_time" value="<?= $wakeupVal ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Sleep Quality *</label>
                <select name="sleep_quality" required>
                    <option value="">Select quality...</option>
                    <?php foreach (['Excellent','Good','Fair','Poor'] as $q): ?>
                    <option value="<?= $q ?>" <?= $record['sleep_quality'] === $q ? 'selected' : '' ?>><?= $q ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Notes (Optional)</label>
                <textarea name="notes" rows="3"><?= htmlspecialchars($record['notes'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update Record</button>
        </form>
    </div>
</div>
</body>
</html>