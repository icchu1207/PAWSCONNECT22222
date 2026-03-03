<?php
session_start();
include "../config.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['action'], $_POST['adoption_id'], $_POST['pet_id'])) {
    $adoption_id = (int)$_POST['adoption_id'];
    $pet_id      = (int)$_POST['pet_id'];

    $res     = $conn->query("SELECT email, fullname FROM adoptions WHERE id=$adoption_id");
    $adopter = $res->fetch_assoc();

    if ($_POST['action'] === 'approve') {
        $conn->query("UPDATE adoptions SET status='Approved' WHERE id=$adoption_id");
        $conn->query("UPDATE pets SET available=0 WHERE id=$pet_id");
        if (!empty($adopter['email'])) {
            mail($adopter['email'], "Your Adoption Request is Approved!",
                "Hi {$adopter['fullname']},\n\nCongratulations! Your adoption request has been approved.\n\nThank you for adopting through PawsConnect!",
                "From: noreply@pawsconnect.com\r\n");
        }
    } elseif ($_POST['action'] === 'decline') {
        $conn->query("UPDATE adoptions SET status='Declined' WHERE id=$adoption_id");
    }

    header("Location: matches.php?updated=1");
    exit;
}

$result   = $conn->query("SELECT adoptions.*, pets.name AS pet_name, pets.type AS pet_type, pets.image AS pet_image FROM adoptions JOIN pets ON adoptions.pet_id = pets.id ORDER BY adoptions.id DESC");
$total    = $result ? $result->num_rows : 0;
$pending  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM adoptions WHERE status='Pending'"))['c'] ?? 0;
$approved = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM adoptions WHERE status='Approved'"))['c'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Adoption Requests – PawsConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<div class="admin-wrapper">
<?php include 'sidebar.php'; ?>
<div class="main-content" style="padding:0;">

    <div class="page-hero">
        <h1><i class="fa-solid fa-heart"></i> Adoption Requests</h1>
        <div class="stat-pills">
            <div class="stat-pill"><i class="fa-solid fa-list"></i> Total <span><?= $total ?></span></div>
            <div class="stat-pill"><i class="fa-solid fa-clock"></i> Pending <span><?= $pending ?></span></div>
            <div class="stat-pill"><i class="fa-solid fa-circle-check"></i> Approved <span><?= $approved ?></span></div>
        </div>
    </div>

    <div class="content-area">
        <?php if (isset($_GET['updated'])): ?>
            <div class="alert-success-custom"><i class="fa-solid fa-circle-check"></i> Adoption request updated successfully.</div>
        <?php endif; ?>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <?php
                $status  = $row['status'] ?? 'Pending';
                $imgPath = '../upload/' . ($row['pet_image'] ?? '');
                $hasImg  = !empty($row['pet_image']) && file_exists($imgPath);
            ?>
            <div class="request-card">
                <?php if ($hasImg): ?>
                    <img src="<?= $imgPath ?>" class="pet-thumb" alt="Pet">
                <?php else: ?>
                    <div class="pet-thumb-placeholder"><i class="fa-solid fa-paw"></i></div>
                <?php endif; ?>

                <div class="request-body">
                    <div class="request-header">
                        <div>
                            <p class="pet-name"><?= htmlspecialchars($row['pet_name'] ?? '') ?></p>
                            <p class="pet-type"><?= htmlspecialchars($row['pet_type'] ?? '') ?></p>
                        </div>
                        <span class="badge-status badge-<?= $status ?>"><?= $status ?></span>
                    </div>

                    <div class="request-details">
                        <div class="detail-item"><i class="fa-solid fa-user"></i><div><span class="detail-label">Adopter</span><?= htmlspecialchars($row['fullname'] ?? '—') ?></div></div>
                        <div class="detail-item"><i class="fa-solid fa-envelope"></i><div><span class="detail-label">Email</span><?= htmlspecialchars($row['email'] ?? '—') ?></div></div>
                        <div class="detail-item"><i class="fa-solid fa-phone"></i><div><span class="detail-label">Phone</span><?= htmlspecialchars($row['phone'] ?? '—') ?></div></div>
                        <div class="detail-item"><i class="fa-solid fa-calendar"></i><div><span class="detail-label">Submitted</span><?= !empty($row['created_at']) ? date('M d, Y', strtotime($row['created_at'])) : '—' ?></div></div>
                    </div>

                    <?php if (!empty($row['preferences']) || !empty($row['message'])): ?>
                    <div class="message-box">
                        <?php if (!empty($row['preferences'])): ?><div><strong>Preferences:</strong> <?= htmlspecialchars($row['preferences']) ?></div><?php endif; ?>
                        <?php if (!empty($row['message'])): ?><div style="margin-top:4px;"><strong>Message:</strong> <?= htmlspecialchars($row['message']) ?></div><?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($status === 'Pending'): ?>
                    <div class="action-btns">
                        <form method="POST" style="display:contents;">
                            <input type="hidden" name="adoption_id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="pet_id"      value="<?= $row['pet_id'] ?>">
                            <button name="action" value="approve" class="btn-approve" onclick="return confirm('Approve this adoption request?')">
                                <i class="fa-solid fa-check"></i> Approve
                            </button>
                            <button name="action" value="decline" class="btn-decline" onclick="return confirm('Decline this adoption request?')">
                                <i class="fa-solid fa-xmark"></i> Decline
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fa-solid fa-heart"></i>
                <p>No adoption requests yet.</p>
            </div>
        <?php endif; ?>
    </div>

</div>
</div>
</body>
</html>
