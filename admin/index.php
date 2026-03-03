<?php
session_start();
include "../config.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$totalPets    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pets"))['total'] ?? 0;
$totalMatches = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM adoptions WHERE status='Approved'"))['total'] ?? 0;
$totalPending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM adoptions WHERE status='Pending'"))['total'] ?? 0;
$totalStrays  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM stray_reports"))['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard – PawsConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<div class="admin-wrapper">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header d-flex justify-content-between align-items-start">
            <div>
                <h1>Dashboard</h1>
                <p class="text-muted">Welcome back, <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?>!</p>
            </div>
            <a href="../public/index.php" class="btn btn-outline-secondary" target="_blank">
                <i class="fa-solid fa-globe"></i> View Public Site
            </a>
        </div>

        <div class="row g-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card text-center border-0 shadow-sm rounded-4 p-3">
                    <div style="font-size:36px; color:#e91e63;"><i class="fa-solid fa-paw"></i></div>
                    <h5 class="mt-2 text-muted">Total Pets</h5>
                    <div style="font-size:42px; font-weight:bold; color:#e91e63;"><?= $totalPets ?></div>
                    <a href="pets.php" class="btn btn-sm mt-3 btn-pink">Manage Pets</a>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card text-center border-0 shadow-sm rounded-4 p-3">
                    <div style="font-size:36px; color:#4CAF50;"><i class="fa-solid fa-circle-check"></i></div>
                    <h5 class="mt-2 text-muted">Approved Adoptions</h5>
                    <div style="font-size:42px; font-weight:bold; color:#e91e63;"><?= $totalMatches ?></div>
                    <a href="matches.php" class="btn btn-sm mt-3 btn-pink">View Adoptions</a>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card text-center border-0 shadow-sm rounded-4 p-3">
                    <div style="font-size:36px; color:#ff9800;"><i class="fa-solid fa-clock"></i></div>
                    <h5 class="mt-2 text-muted">Pending Approvals</h5>
                    <div style="font-size:42px; font-weight:bold; color:#e91e63;"><?= $totalPending ?></div>
                    <a href="matches.php" class="btn btn-sm mt-3 btn-pink">Review Now</a>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card text-center border-0 shadow-sm rounded-4 p-3">
                    <div style="font-size:36px; color:#2196F3;"><i class="fa-solid fa-clipboard-list"></i></div>
                    <h5 class="mt-2 text-muted">Stray Reports</h5>
                    <div style="font-size:42px; font-weight:bold; color:#e91e63;"><?= $totalStrays ?></div>
                    <a href="stray_reports.php" class="btn btn-sm mt-3 btn-pink">View Reports</a>
                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
