<?php
include __DIR__ . "/../config.php";

// Fetch featured pet
$featured = null;
$res = $conn->query("SELECT * FROM pets WHERE available = 1 ORDER BY RAND() LIMIT 1");
if ($res && $res->num_rows > 0) {
    $featured = $res->fetch_assoc();
    $featured['image_url'] = !empty($featured['image'])
        ? "http://localhost/pawsconnect-main/kalibo_pet_shelter/upload/" . $featured['image']
        : "https://placehold.co/300x340/ffd6e7/ff5c9d?text=No+Photo";
}

// Fetch stats
$totalPets    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM pets WHERE available=1"))['c'] ?? 0;
$totalAdopted = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM adoptions WHERE status='Approved'"))['c'] ?? 0;
$totalReports = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM stray_reports"))['c'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PawsConnect – Give a Pet a Home</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Pacifico&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/home.css">
</head>
<body>

<!-- Background blobs -->
<div class="bg-blobs">
  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>
  <div class="blob blob-3"></div>
</div>

<!-- Floating paw prints -->
<div class="paws-bg" id="pawsBg"></div>

<!-- Header -->
<div class="header">
  <a href="stray_report.php" class="btn-nav">
    <i class="fa-solid fa-triangle-exclamation"></i> Stray Report
  </a>
  <a href="home.php">
    <img src="assets/images/logo.png" alt="PawsConnect Logo" class="logo">
  </a>
  <div style="display:flex; gap:12px; align-items:center;">
    <a href="index.php" class="btn-nav" title="Browse Pets">
      <i class="fa-solid fa-paw"></i>
    </a>
    <a href="admin_login.php" class="btn-nav">
      <i class="fa-solid fa-right-to-bracket"></i> Log In
    </a>
  </div>
</div>

<!-- Main Layout -->
<div class="layout">

  <!-- LEFT -->
  <div class="left">

    <!-- Eyebrow + Tagline -->
    <div class="tagline">
      <span class="eyebrow">
        <i class="fa-solid fa-paw"></i> Kalibo Pet Shelter
      </span>
      <h1>Every Pet Deserves</h1>
      <h1><span class="highlight">A Loving Home</span></h1>
      <p class="subtitle">Browse available pets, find your perfect match, or help a stray animal get the care it needs.</p>
    </div>

    <!-- Live Stats -->
    <div class="stats">
      <div class="stat-item">
        <span class="stat-number"><?= $totalPets ?>+</span>
        <span class="stat-label">Pets Available</span>
      </div>
      <div class="stat-divider"></div>
      <div class="stat-item">
        <span class="stat-number"><?= $totalAdopted ?>+</span>
        <span class="stat-label">Happy Adoptions</span>
      </div>
      <div class="stat-divider"></div>
      <div class="stat-item">
        <span class="stat-number"><?= $totalReports ?>+</span>
        <span class="stat-label">Strays Reported</span>
      </div>
    </div>

    <!-- Menu -->
    <nav class="menu">

      <a href="index.php" class="menu-btn menu-btn-primary">
        <span class="menu-icon"><i class="fa-solid fa-heart"></i></span>
        <span>
          Browse All Pets
          <div class="menu-btn-desc">See all available animals</div>
        </span>
        <i class="fa-solid fa-arrow-right menu-arrow"></i>
      </a>

      <a href="match.php" class="menu-btn">
        <span class="menu-icon"><i class="fa-solid fa-paw"></i></span>
        <span>
          Find a Match
          <div class="menu-btn-desc">Answer a few questions, we'll find your fit</div>
        </span>
        <i class="fa-solid fa-arrow-right menu-arrow"></i>
      </a>

      <a href="stray_report.php" class="menu-btn">
        <span class="menu-icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
        <span>
          Report a Stray
          <div class="menu-btn-desc">Help an animal in need</div>
        </span>
        <i class="fa-solid fa-arrow-right menu-arrow"></i>
      </a>

      <a href="admin_login.php" class="menu-btn">
        <span class="menu-icon"><i class="fa-solid fa-right-to-bracket"></i></span>
        <span>
          Admin Login
          <div class="menu-btn-desc">Manage shelter operations</div>
        </span>
        <i class="fa-solid fa-arrow-right menu-arrow"></i>
      </a>

    </nav>
  </div>

  <!-- RIGHT: Featured Pet Card -->
  <div class="right">
    <div id="featured-root"></div>
  </div>

</div>

<!-- Pass PHP pet data to JS -->
<script>
  window.FEATURED_PET = <?= $featured ? json_encode([
    'id'        => $featured['id'],
    'name'      => $featured['name'],
    'breed'     => $featured['breed'] ?? '',
    'type'      => $featured['type']  ?? '',
    'image_url' => $featured['image_url'],
  ]) : 'null' ?>;
  window.ADOPT_URL = <?= $featured
    ? json_encode("http://localhost/pawsconnect-main/kalibo_pet_shelter/public/adopt.php?pet_id=" . $featured['id'])
    : 'null' ?>;
</script>

<!-- React via CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/react/18.2.0/umd/react.production.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/react-dom/18.2.0/umd/react-dom.production.min.js"></script>
<script src="assets/js/featured-widget.js"></script>

<!-- Floating paw prints -->
<script>
  const container = document.getElementById('pawsBg');
  for (let i = 0; i < 14; i++) {
    const el = document.createElement('span');
    el.className = 'paw';
    el.innerHTML = '<i class="fa-solid fa-paw"></i>';
    el.style.left              = Math.random() * 100 + 'vw';
    el.style.animationDuration = (12 + Math.random() * 15) + 's';
    el.style.animationDelay    = (Math.random() * 15) + 's';
    el.style.fontSize          = (16 + Math.random() * 18) + 'px';
    container.appendChild(el);
  }
</script>

</body>
</html>
