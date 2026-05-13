<?php
$rootPath = '../';
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sleepTime   = $_POST['sleep_time'] ?? '';
    $wakeupTime  = $_POST['wakeup_time'] ?? '';
    $quality     = $_POST['sleep_quality'] ?? '';
    $notes       = trim($_POST['notes'] ?? '');

    if (empty($sleepTime) || empty($wakeupTime) || empty($quality)) {
        $error = 'Please fill in all required fields.';
    } else {
        $sleep  = new DateTime($sleepTime);
        $wakeup = new DateTime($wakeupTime);

        if ($wakeup <= $sleep) {
            // Allow sleeping past midnight
            $wakeup->modify('+1 day');
        }

        $diff     = $sleep->diff($wakeup);
        $duration = round($diff->h + ($diff->i / 60), 2);

        $stmt = $conn->prepare("INSERT INTO sleep_records (user_id, sleep_time, wakeup_time, duration, sleep_quality, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdss", $userId, $sleepTime, $wakeupTime, $duration, $quality, $notes);

        if ($stmt->execute()) {
            $success = "Sleep record added successfully! Duration: {$duration} hours.";
        } else {
            $error = 'Failed to save record. Please try again.';
        }
    }
}

$pageTitle = 'Add Sleep Record';
include '../includes/header.php';
?>
<?php include '../includes/navbar.php'; ?>
<div class="wrapper">
    <div class="page-header">
        <div class="page-title">
            Add Sleep Record
            <small>Log your sleep for tonight or a past night</small>
        </div>
        <a href="../sleep/history.php" class="btn btn-outline btn-sm">← View History</a>
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
                    <input type="datetime-local" name="sleep_time" value="<?= htmlspecialchars($_POST['sleep_time'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Wake Up Time *</label>
                    <input type="datetime-local" name="wakeup_time" value="<?= htmlspecialchars($_POST['wakeup_time'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Sleep Quality *</label>
                <select name="sleep_quality" required>
                    <option value="">Select quality...</option>
                    <option value="Excellent" <?= ($_POST['sleep_quality'] ?? '') === 'Excellent' ? 'selected' : '' ?>>⭐ Excellent</option>
                    <option value="Good"      <?= ($_POST['sleep_quality'] ?? '') === 'Good'      ? 'selected' : '' ?>>😊 Good</option>
                    <option value="Fair"      <?= ($_POST['sleep_quality'] ?? '') === 'Fair'      ? 'selected' : '' ?>>😐 Fair</option>
                    <option value="Poor"      <?= ($_POST['sleep_quality'] ?? '') === 'Poor'      ? 'selected' : '' ?>>😞 Poor</option>
                </select>
            </div>

            <div class="form-group">
                <label>Notes (Optional)</label>
                <textarea name="notes" rows="3" placeholder="Any notes about your sleep..."><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Save Sleep Record</button>
        </form>
    </div>
</div>
</body>
</html>