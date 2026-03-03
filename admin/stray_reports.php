<?php
session_start();
include "../config.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$reports = [];
$res = mysqli_query($conn, "SELECT * FROM stray_reports ORDER BY id DESC");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) $reports[] = $r;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stray Reports – PawsConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<div class="admin-wrapper">
<?php include 'sidebar.php'; ?>
<div class="main-content" style="padding:0;">

    <div class="page-hero">
        <h1><i class="fa-solid fa-flag"></i> Stray Reports</h1>
        <p><?= count($reports) ?> report<?= count($reports) !== 1 ? 's' : '' ?> submitted</p>
    </div>

    <div class="content-area">
        <?php if (empty($reports)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-flag"></i>
                <p>No stray reports yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($reports as $r): ?>
            <?php
                $photoPath = '../upload/' . ($r['photo'] ?? '');
                $hasPhoto  = !empty($r['photo']) && file_exists($photoPath);
            ?>
            <div class="report-card">
                <?php if ($hasPhoto): ?>
                    <img src="<?= $photoPath ?>" class="report-photo" alt="Stray Photo">
                <?php else: ?>
                    <div class="report-photo-placeholder"><i class="fa-solid fa-paw"></i></div>
                <?php endif; ?>

                <div class="report-body">
                    <div class="report-meta">
                        <?php if (!empty($r['animal_type'])): ?>
                            <?php $typeIcon = match(strtolower($r['animal_type'])) { 'dog' => 'fa-dog', 'cat' => 'fa-cat', 'bird' => 'fa-dove', default => 'fa-paw' }; ?>
                            <span class="meta-pill"><i class="fa-solid <?= $typeIcon ?>"></i> <?= htmlspecialchars($r['animal_type']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($r['animal_condition'])): ?>
                            <span class="condition-pill condition-<?= htmlspecialchars($r['animal_condition']) ?>"><?= htmlspecialchars($r['animal_condition']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($r['time_of_sight'])): ?>
                            <span class="meta-pill" style="background:#f0f4ff; color:#3b5bdb;">
                                <i class="fa-solid fa-clock"></i> <?= date('M d, Y g:i A', strtotime($r['time_of_sight'])) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="report-details">
                        <div class="detail-item"><i class="fa-solid fa-user"></i><div><span class="detail-label">Reporter</span><?= htmlspecialchars($r['reporter_name'] ?? '—') ?></div></div>
                        <div class="detail-item"><i class="fa-solid fa-envelope"></i><div><span class="detail-label">Email</span><?= htmlspecialchars($r['reporter_email'] ?? '—') ?></div></div>
                        <div class="detail-item"><i class="fa-solid fa-phone"></i><div><span class="detail-label">Phone</span><?= htmlspecialchars($r['reporter_phone'] ?? '—') ?></div></div>
                        <div class="detail-item"><i class="fa-solid fa-location-dot"></i><div><span class="detail-label">Location</span><?= htmlspecialchars($r['location'] ?? '—') ?></div></div>
                    </div>

                    <?php if (!empty($r['description'])): ?>
                        <div class="description-box">
                            <i class="fa-solid fa-align-left" style="color:#ff6fab; margin-right:6px;"></i>
                            <?= htmlspecialchars($r['description']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>
</div>
</body>
</html>
