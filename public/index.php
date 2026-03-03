<?php
include __DIR__ . "/../config.php";
$type        = $_GET['type']        ?? '';
$size        = $_GET['size']        ?? '';
$gender      = $_GET['gender']      ?? '';
$temperament = $_GET['temperament'] ?? '';
$neutered    = $_GET['neutered']    ?? '';
$vaccines    = $_GET['vaccines']    ?? '';

$sql = "SELECT * FROM pets WHERE 1=1";
if ($type !== '')        $sql .= " AND type = '"        . $conn->real_escape_string($type)        . "'";
if ($size !== '')        $sql .= " AND size = '"        . $conn->real_escape_string($size)        . "'";
if ($neutered !== '')    $sql .= " AND neutered = '"    . $conn->real_escape_string($neutered)    . "'";
if ($vaccines !== '')    $sql .= " AND vaccines LIKE '%" . $conn->real_escape_string($vaccines)   . "%'";
if ($gender !== '')      $sql .= " AND gender = '"      . $conn->real_escape_string($gender)      . "'";
if ($temperament !== '') $sql .= " AND temperament = '" . $conn->real_escape_string($temperament) . "'";
$sql .= " ORDER BY id DESC";
$result = $conn->query($sql);
$pet_count   = $result ? $result->num_rows : 0;
$is_filtered = ($type || $size || $gender || $temperament || $neutered || $vaccines);

// Stats for counter strip
$totalPets    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM pets WHERE available=1"))['c'] ?? 0;
$totalAdopted = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM adoptions WHERE status='Approved'"))['c'] ?? 0;
$totalStrays  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM stray_reports"))['c'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PawsConnect – Find Your Pet</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Pacifico&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/skeleton.css">
</head>
<body>

<div class="bg-blobs">
  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>
  <div class="blob blob-3"></div>
</div>
<div class="paws-bg" id="pawsBg"></div>

<!-- ── HEADER ── -->
<div class="header">
  <a href="stray_report.php" class="btn-nav">
    <i class="fa-solid fa-triangle-exclamation"></i> Stray Report
  </a>
  <img src="assets/images/logo.png" alt="PawsConnect Logo" class="logo">
  <div style="display:flex; gap:12px; align-items:center;">
    <a href="home.php" class="btn-nav" title="Home">
      <i class="fa-solid fa-house"></i>
    </a>
    <a href="admin_login.php" class="btn-nav">
      <i class="fa-solid fa-right-to-bracket"></i> Log In
    </a>
  </div>
</div>

<!-- ── HERO ── -->
<div class="hero">
  <h1>Find Your Perfect Companion</h1>
  <p>Every pet here is waiting for a loving home — could it be yours?</p>
</div>

<!-- ── COUNTER STRIP ── -->
<div class="counter-strip" data-aos="fade-up" data-aos-delay="100">
  <div class="counter-item">
    <span class="counter-num" data-target="<?= $totalPets ?>">0</span>
    <span class="counter-label"><i class="fa-solid fa-paw"></i> Pets Available</span>
  </div>
  <div class="counter-divider"></div>
  <div class="counter-item">
    <span class="counter-num" data-target="<?= $totalAdopted ?>">0</span>
    <span class="counter-label"><i class="fa-solid fa-house-heart"></i> Happy Adoptions</span>
  </div>
  <div class="counter-divider"></div>
  <div class="counter-item">
    <span class="counter-num" data-target="<?= $totalStrays ?>">0</span>
    <span class="counter-label"><i class="fa-solid fa-flag"></i> Stray Reports</span>
  </div>
</div>

<!-- ── FILTERS ── -->
<form class="filters" method="GET" data-aos="fade-up">
  <div class="filter-item">
    <i class="fa-solid fa-paw filter-icon"></i>
    <select name="type">
      <option value="">All Types</option>
      <option value="Dog"  <?= $type=="Dog"  ? "selected":""?>>Dog</option>
      <option value="Cat"  <?= $type=="Cat"  ? "selected":""?>>Cat</option>
      <option value="Bird" <?= $type=="Bird" ? "selected":""?>>Bird</option>
    </select>
  </div>
  <div class="filter-item">
    <i class="fa-solid fa-ruler-vertical filter-icon"></i>
    <select name="size">
      <option value="">All Sizes</option>
      <option value="Small"  <?= $size=="Small"  ? "selected":""?>>Small</option>
      <option value="Medium" <?= $size=="Medium" ? "selected":""?>>Medium</option>
      <option value="Large"  <?= $size=="Large"  ? "selected":""?>>Large</option>
    </select>
  </div>
  <div class="filter-item">
    <i class="fa-solid fa-venus-mars filter-icon"></i>
    <select name="gender">
      <option value="">All Genders</option>
      <option value="Male"   <?= $gender=="Male"   ? "selected":""?>>Male</option>
      <option value="Female" <?= $gender=="Female" ? "selected":""?>>Female</option>
    </select>
  </div>
  <div class="filter-item">
    <i class="fa-solid fa-face-smile filter-icon"></i>
    <select name="temperament">
      <option value="">All Temperaments</option>
      <option value="Friendly"    <?= $temperament=="Friendly"    ? "selected":""?>>Friendly</option>
      <option value="Calm"        <?= $temperament=="Calm"        ? "selected":""?>>Calm</option>
      <option value="Playful"     <?= $temperament=="Playful"     ? "selected":""?>>Playful</option>
      <option value="Shy"         <?= $temperament=="Shy"         ? "selected":""?>>Shy</option>
      <option value="Independent" <?= $temperament=="Independent" ? "selected":""?>>Independent</option>
      <option value="neutral" <?= $temperament=="neutral" ? "selected":""?>>"quiet</option>
      <option value="quiet" <?= $temperament=="quiet" ? "selected":""?>>"quiet"</option>
      <option value="docile" <?= $temperament=="docile" ? "selected":""?>>"docile"</option>
      <option value="bold" <?= $temperament=="bold" ? "selected":""?>>"bold"</option>
    </select>
  </div>
  <div class="filter-item">
    <i class="fa-solid fa-scissors filter-icon"></i>
    <select name="neutered">
      <option value="">Neutered?</option>
      <option value="1" <?= $neutered=="1"  ? "selected":""?>>Yes</option>
      <option value="0" <?= $neutered==="0" ? "selected":""?>>No</option>
    </select>
  </div>
  <button type="submit">
    <i class="fa-solid fa-magnifying-glass"></i> Search
  </button>
  <?php if ($is_filtered): ?>
  <a href="index.php" class="filter-reset">
    <i class="fa-solid fa-rotate-left"></i> Reset
  </a>
  <?php endif; ?>
</form>

<!-- ── FIND A MATCH BANNER ── -->
<?php if (!$is_filtered): ?>
<div class="match-banner" data-aos="fade-up">
  <div class="match-banner-inner">
    <div class="match-banner-text">
      <h3><i class="fa-solid fa-magnifying-glass-heart"></i> Not sure which pet is right for you?</h3>
      <p>Answer a few quick questions and we'll find your perfect match!</p>
    </div>
    <a href="match.php" class="match-banner-btn">Find My Match <i class="fa-solid fa-arrow-right"></i></a>
  </div>
</div>
<?php endif; ?>

<!-- ── SKELETON LOADER ── -->
<div id="skeletonWrapper">
<div class="skeleton-grid" id="skeletonGrid">
<?php for ($s = 0; $s < min(6, max(3, $pet_count)); $s++): ?>
  <div class="skeleton-card">
    <div class="sk sk-image"></div>
    <div class="sk-body">
      <div class="sk sk-title"></div>
      <div class="sk-pills">
        <div class="sk sk-pill"></div>
        <div class="sk sk-pill"></div>
        <div class="sk sk-pill"></div>
      </div>
      <div class="sk-details">
        <div class="sk sk-line"></div>
        <div class="sk sk-line"></div>
        <div class="sk sk-line"></div>
      </div>
      <div class="sk sk-btn"></div>
    </div>
  </div>
<?php endfor; ?>
</div>

<!-- ── PET CARDS ── -->
<div class="pet-cards" id="petCards" style="opacity:0;">
<?php if ($result && $result->num_rows > 0):
  $delay = 0;
  while ($pet = $result->fetch_assoc()):
    $available = isset($pet['available']) ? (bool)$pet['available'] : true;
    $available = isset($pet['status']) && strtolower($pet['status']) === 'adopted' ? false : $available;
?>
  <div class="pet-card <?= !$available ? 'pet-card--unavailable' : '' ?>" data-aos="fade-up" data-aos-delay="<?= ($delay % 5) * 100 ?>">

    <!-- Image -->
    <div class="pet-card-img-wrap">
      <img src="../upload/<?= htmlspecialchars($pet['image'] ?? '') ?>"
           alt="<?= htmlspecialchars($pet['name']) ?>"
           onerror="this.src='https://placehold.co/400x260/ffd6e7/ff5c9d?text=No+Photo'">

      <!-- Type badge -->
      <div class="pet-card-badge">
        <?php
          $typeIcon = match(strtolower($pet['type'] ?? '')) {
            'dog'  => 'fa-dog',
            'cat'  => 'fa-cat',
            'bird' => 'fa-dove',
            default => 'fa-paw'
          };
        ?>
        <i class="fa-solid <?= $typeIcon ?>"></i> <?= htmlspecialchars($pet['type']) ?>
      </div>

      <!-- Gender badge -->
      <?php if (!empty($pet['gender'])): ?>
      <div class="pet-card-gender <?= strtolower($pet['gender']) ?>">
        <i class="fa-solid <?= $pet['gender'] == 'Male' ? 'fa-mars' : 'fa-venus' ?>"></i>
      </div>
      <?php endif; ?>

      <!-- Unavailable overlay -->
      <?php if (!$available): ?>
      <div class="pet-card-adopted">
        <i class="fa-solid fa-heart"></i> Adopted
      </div>
      <?php endif; ?>
    </div>

    <!-- Body -->
    <div class="pet-card-body">
      <h2><?= htmlspecialchars($pet['name']) ?></h2>

      <!-- Key pills -->
      <div class="pet-pills">
        <?php if (!empty($pet['size'])): ?>
          <span class="pill"><i class="fa-solid fa-ruler-vertical"></i> <?= htmlspecialchars($pet['size']) ?></span>
        <?php endif; ?>
        <?php if (!empty($pet['temperament'])): ?>
          <span class="pill"><i class="fa-solid fa-face-smile"></i> <?= htmlspecialchars($pet['temperament']) ?></span>
        <?php endif; ?>
        <?php if (!empty($pet['age'])): ?>
          <span class="pill"><i class="fa-solid fa-cake-candles"></i> <?= htmlspecialchars($pet['age']) ?> yr<?= $pet['age'] > 1 ? 's' : '' ?></span>
        <?php endif; ?>
      </div>

      <!-- Details -->
      <div class="pet-details">
        <?php if (!empty($pet['breed'])): ?>
        <div class="pet-detail-row">
          <span><?= htmlspecialchars($pet['breed']) ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($pet['location'])): ?>
        <div class="pet-detail-row">
          <span><?= htmlspecialchars($pet['location']) ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($pet['vaccines'])): ?>
        <div class="pet-detail-row">
          <span><?= htmlspecialchars($pet['vaccines']) ?></span>
        </div>
        <?php endif; ?>
        <?php if (isset($pet['neutered'])): ?>
        <div class="pet-detail-row">
          <span><?= $pet['neutered'] ? 'Neutered / Spayed' : 'Not neutered' ?></span>
        </div>
        <?php endif; ?>
      </div>

      <?php if ($available): ?>
      <a class="adopt-btn" href="adopt.php?pet_id=<?= $pet['id'] ?>">
        <i class="fa-solid fa-heart"></i> Adopt Me
      </a>
      <?php else: ?>
      <div class="adopted-label">
        <i class="fa-solid fa-house-heart"></i> Already Adopted
      </div>
      <?php endif; ?>
    </div>
  </div>
<?php
    $delay++;
  endwhile;
else: ?>
  <div class="no-pets" data-aos="fade-up">
    <i class="fa-solid fa-paw no-pets-icon"></i>
    <p><?= $is_filtered ? 'No pets match your filters.' : 'No pets available right now.' ?></p>
    <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap; margin-top:20px;">
      <?php if ($is_filtered): ?>
        <a href="index.php" class="adopt-btn" style="text-decoration:none;">
          <i class="fa-solid fa-rotate-left"></i> Reset Filters
        </a>
      <?php endif; ?>
      <a href="match.php" class="adopt-btn" style="text-decoration:none; background:white; color:var(--pink); border:2px solid var(--pink-light);">
        <i class="fa-solid fa-magnifying-glass-heart"></i> Try Find a Match
      </a>
    </div>
  </div>
<?php endif; ?>
</div>
</div><!-- /#skeletonWrapper -->

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  // Skeleton shimmer → real cards (AOS inits after swap so card animations play on reveal)
  (function() {
    var skeleton = document.getElementById('skeletonGrid');
    var wrapper  = document.getElementById('skeletonWrapper');
    var cards    = document.getElementById('petCards');
    setTimeout(function() {
      skeleton.style.opacity = '0';
      setTimeout(function() {
        skeleton.style.display = 'none';
        cards.classList.add('revealed');
        cards.style.opacity = '1';
        // Init AOS now so card fade-up animations fire as the cards appear
        AOS.init({ duration: 700, once: true, easing: 'ease-out-cubic', offset: 60 });
      }, 300);
    }, 300);
  })();

  // Floating paw prints
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

  // Animated counters
  function animateCounter(el) {
    const target = +el.dataset.target;
    if (target === 0) { el.textContent = '0'; return; }
    const duration = 1400;
    const step = target / (duration / 16);
    let current = 0;
    const timer = setInterval(() => {
      current += step;
      if (current >= target) { el.textContent = target; clearInterval(timer); return; }
      el.textContent = Math.floor(current);
    }, 16);
  }

  // Trigger counters when strip scrolls into view
  const counters = document.querySelectorAll('.counter-num');
  const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        counters.forEach(animateCounter);
        observer.disconnect();
      }
    });
  }, { threshold: 0.5 });
  const strip = document.querySelector('.counter-strip');
  if (strip) observer.observe(strip);
</script>
</body>
</html>
