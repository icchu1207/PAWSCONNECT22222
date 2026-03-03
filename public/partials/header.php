<?php
// public/partials/header.php
// Set $pageTitle and optionally $pageCSS before including this file.
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'PawsConnect') ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Pacifico&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
  <?php if (!empty($pageCSS)): ?>
  <link rel="stylesheet" href="../assets/css/<?= htmlspecialchars($pageCSS) ?>">
  <?php endif; ?>

  <style>
    @font-face {
      font-family: 'CreatoDisplay';
      src: url('../assets/fonts/CreatoDisplay-Black.otf') format('opentype');
      font-weight: 900;
    }
  </style>
</head>
<body>

<!-- ── BACKGROUND BLOBS ── -->
<div class="bg-blobs">
  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>
  <div class="blob blob-3"></div>
</div>

<!-- ── FLOATING PAW PRINTS ── -->
<div class="paws-bg" id="pawsBg"></div>

<!-- ── HEADER ── -->
<div class="header">
  <a href="stray_report.php" class="btn-nav">
    <i class="fa-solid fa-triangle-exclamation"></i> Stray Report
  </a>
  <a href="index.php">
    <img src="assets/images/logo.png" alt="PawsConnect Logo" class="logo">
  </a>
  <div style="display:flex; gap:12px; align-items:center;">
    <a href="index.php" class="btn-nav" title="Home">
      <i class="fa-solid fa-house"></i>
    </a>
    <a href="admin_login.php" class="btn-nav">
      <i class="fa-solid fa-right-to-bracket"></i> Log In
    </a>
  </div>
</div>
