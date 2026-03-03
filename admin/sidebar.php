<?php
// Get counts for sidebar badges
$pendingCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM adoptions WHERE status='Pending'"))['total'] ?? 0;
$strayCount   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM stray_reports"))['total'] ?? 0;
$currentPage  = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<button class="sidebar-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open')">
    <i class="fa-solid fa-bars"></i>
</button>

<div class="sidebar">
    <div class="sidebar-brand">
        <h2><i class="fa-solid fa-paw"></i> PawsConnect</h2>
        <small>Admin Panel</small>
    </div>

    <nav class="sidebar-nav">
        <a href="index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fa-solid fa-house"></i></span> Dashboard
        </a>
        <a href="pets.php" class="<?= $currentPage === 'pets.php' ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fa-solid fa-dog"></i></span> Manage Pets
        </a>
        <a href="add_pet.php" class="<?= $currentPage === 'add_pet.php' ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fa-solid fa-plus"></i></span> Add Pet
        </a>
        <a href="matches.php" class="<?= $currentPage === 'matches.php' ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fa-solid fa-heart"></i></span> Adoption Requests
            <?php if ($pendingCount > 0): ?>
                <span class="nav-badge"><?= $pendingCount ?></span>
            <?php endif; ?>
        </a>
        <a href="stray_reports.php" class="<?= $currentPage === 'stray_reports.php' ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fa-solid fa-flag"></i></span> Stray Reports
            <?php if ($strayCount > 0): ?>
                <span class="nav-badge"><?= $strayCount ?></span>
            <?php endif; ?>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="logout.php">
            <span class="nav-icon"><i class="fa-solid fa-right-from-bracket"></i></span> Logout
        </a>
    </div>
</div>
