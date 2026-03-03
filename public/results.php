<?php
include "../config.php";

// ── Collect POST data ─────────────────────────────────────
$name          = trim($_POST['name']          ?? '');
$email         = trim($_POST['email']         ?? '');
$phone         = trim($_POST['phone']         ?? '');
$pref_type        = trim($_POST['pref_type']        ?? '');
$pref_size        = trim($_POST['pref_size']        ?? '');
$pref_gender      = trim($_POST['pref_gender']      ?? '');
$pref_temperament = trim($_POST['pref_temperament'] ?? '');
$pref_age_range   = trim($_POST['pref_age_range']   ?? '');
$pref_neutered    = trim($_POST['pref_neutered']    ?? '');

if (!$name || !$email || !$phone) {
    header("Location: match.php");
    exit;
}

// ── Save adopter ──────────────────────────────────────────
$stmt = $conn->prepare("
    INSERT INTO adopters (name, email, phone, pref_type, pref_size, pref_gender, pref_temperament, pref_age_range, pref_neutered)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("sssssssss", $name, $email, $phone, $pref_type, $pref_size, $pref_gender, $pref_temperament, $pref_age_range, $pref_neutered);
$stmt->execute();
$adopter_id = $conn->insert_id;
$stmt->close();

// ── Build exact match query ───────────────────────────────
$conditions = ["available = 1"];
$params     = [];
$types      = "";

if ($pref_type)        { $conditions[] = "type = ?";        $params[] = $pref_type;        $types .= "s"; }
if ($pref_size)        { $conditions[] = "size = ?";        $params[] = $pref_size;        $types .= "s"; }
if ($pref_gender)      { $conditions[] = "gender = ?";      $params[] = $pref_gender;      $types .= "s"; }
if ($pref_temperament) { $conditions[] = "temperament = ?"; $params[] = $pref_temperament; $types .= "s"; }
if ($pref_neutered !== '') { $conditions[] = "neutered = ?"; $params[] = $pref_neutered;   $types .= "s"; }

// Age range
$age_min = null; $age_max = null;
if ($pref_age_range) {
    [$age_min, $age_max] = explode('-', $pref_age_range);
    $conditions[] = "age BETWEEN ? AND ?";
    $params[] = intval($age_min); $types .= "i";
    $params[] = intval($age_max); $types .= "i";
}

$sql  = "SELECT * FROM pets WHERE " . implode(" AND ", $conditions) . " ORDER BY id DESC";
$stmt = $conn->prepare($sql);
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$exact_matches = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── Save exact matches ────────────────────────────────────
foreach ($exact_matches as $pet) {
    $s = $conn->prepare("INSERT INTO matches (adopter_id, pet_id) VALUES (?, ?)");
    $s->bind_param("ii", $adopter_id, $pet['id']);
    $s->execute();
    $s->close();
}

// ── Closest matches (if no exact matches) ────────────────
// Drop filters one by one (least important first) until we get results
$closest_matches  = [];
$dropped_filters  = [];

if (empty($exact_matches)) {
    $filter_priority = ['pref_neutered','pref_age_range','pref_size','pref_gender','pref_temperament','pref_type'];
    $active = ['pref_type'=>$pref_type,'pref_size'=>$pref_size,'pref_gender'=>$pref_gender,
               'pref_temperament'=>$pref_temperament,'pref_age_range'=>$pref_age_range,'pref_neutered'=>$pref_neutered];

    foreach ($filter_priority as $drop) {
        if (empty($active[$drop]) && $active[$drop] !== '0') continue;
        $dropped_filters[] = $drop;
        unset($active[$drop]);

        // Rebuild query with remaining filters
        $c2 = ["available = 1"]; $p2 = []; $t2 = "";
        foreach (['pref_type'=>'type','pref_size'=>'size','pref_gender'=>'gender','pref_temperament'=>'temperament'] as $pk => $col) {
            if (!empty($active[$pk])) { $c2[] = "$col = ?"; $p2[] = $active[$pk]; $t2 .= "s"; }
        }
        if (isset($active['pref_neutered']) && $active['pref_neutered'] !== '') {
            $c2[] = "neutered = ?"; $p2[] = $active['pref_neutered']; $t2 .= "s";
        }
        if (!empty($active['pref_age_range'])) {
            [$mn,$mx] = explode('-', $active['pref_age_range']);
            $c2[] = "age BETWEEN ? AND ?"; $p2[] = intval($mn); $p2[] = intval($mx); $t2 .= "ii";
        }

        $s2   = $conn->prepare("SELECT * FROM pets WHERE " . implode(" AND ", $c2) . " ORDER BY id DESC");
        if ($t2) $s2->bind_param($t2, ...$p2);
        $s2->execute();
        $closest_matches = $s2->get_result()->fetch_all(MYSQLI_ASSOC);
        $s2->close();

        if (!empty($closest_matches)) break;
    }
}

// ── Helper: readable filter label ────────────────────────
function filterLabel($key) {
    return match($key) {
        'pref_type'        => 'Pet Type',
        'pref_size'        => 'Size',
        'pref_gender'      => 'Gender',
        'pref_temperament' => 'Temperament',
        'pref_age_range'   => 'Age Range',
        'pref_neutered'    => 'Neutered/Spayed',
        default            => $key
    };
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Matches – PawsConnect</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="../assets/css/results.css" rel="stylesheet">
</head>
<body>

<!-- ── HERO ── -->
<div class="results-hero">
  <div class="container">
    <a href="match.php" class="text-white text-decoration-none d-inline-flex align-items-center gap-2 mb-4"
       style="opacity:.8; font-weight:700; font-size:.9rem;">
      <i class="bi bi-arrow-left"></i> Back to Preferences
    </a>
    <?php if (!empty($exact_matches)): ?>
      <h1>We Found <?= count($exact_matches) ?> Match<?= count($exact_matches) > 1 ? 'es' : '' ?>!</h1>
      <p>These pets match all your preferences perfectly 🎉</p>
    <?php elseif (!empty($closest_matches)): ?>
      <h1>No Exact Matches — But Close!</h1>
      <p>We found some pets that almost fit what you're looking for 🐾</p>
    <?php else: ?>
      <h1>No Matches Found</h1>
      <p>Try broadening your preferences to find more pets 🐾</p>
    <?php endif; ?>
  </div>
</div>

<div class="container" style="max-width:900px;">

  <!-- ── User Summary Card ── -->
  <div class="summary-card">
    <div class="summary-left">
      <div class="summary-avatar"><i class="bi bi-person-fill"></i></div>
      <div>
        <p class="user-name"><?= htmlspecialchars($name) ?></p>
        <p class="user-email"><?= htmlspecialchars($email) ?> · <?= htmlspecialchars($phone) ?></p>
      </div>
    </div>
    <?php if (!empty($exact_matches)): ?>
      <div class="match-count-badge">🐾 <?= count($exact_matches) ?> exact match<?= count($exact_matches) > 1 ? 'es' : '' ?></div>
    <?php elseif (!empty($closest_matches)): ?>
      <div class="match-count-badge" style="background:linear-gradient(135deg,#f0a500,#e07b00);">
        🔍 <?= count($closest_matches) ?> close match<?= count($closest_matches) > 1 ? 'es' : '' ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- ── EXACT MATCHES ── -->
  <?php if (!empty($exact_matches)): ?>
  <p class="section-label"><i class="bi bi-patch-check-fill me-2"></i> Perfect Matches</p>
  <div class="pet-grid">
    <?php foreach ($exact_matches as $pet): ?>
    <div class="pet-card">
      <?php if (!empty($pet['image'])): ?>
        <img class="pet-card-img"
             src="../upload/<?= htmlspecialchars($pet['image']) ?>"
             alt="<?= htmlspecialchars($pet['name']) ?>"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div class="pet-card-img-placeholder" style="display:none;">🐾</div>
      <?php else: ?>
        <div class="pet-card-img-placeholder">🐾</div>
      <?php endif; ?>
      <div class="pet-card-body">
        <p class="pet-card-name"><?= htmlspecialchars($pet['name']) ?></p>
        <div class="pet-tags">
          <span class="pet-tag"><?= htmlspecialchars($pet['type']) ?></span>
          <span class="pet-tag"><?= htmlspecialchars($pet['size']) ?></span>
          <span class="pet-tag"><?= htmlspecialchars($pet['gender']) ?></span>
          <span class="pet-tag"><?= htmlspecialchars($pet['temperament']) ?></span>
          <?php if (!empty($pet['age'])): ?>
            <span class="pet-tag"><?= htmlspecialchars($pet['age']) ?> yr<?= $pet['age'] > 1 ? 's' : '' ?></span>
          <?php endif; ?>
          <?php if ($pet['neutered']): ?>
            <span class="pet-tag">✅ Neutered</span>
          <?php endif; ?>
        </div>
        <?php if (!empty($pet['description'])): ?>
          <p class="pet-card-desc"><?= htmlspecialchars($pet['description']) ?></p>
        <?php endif; ?>
        <a href="adopt.php?pet_id=<?= $pet['id'] ?>" target="_blank" class="btn-adopt">
          <i class="bi bi-heart-fill"></i> Adopt <?= htmlspecialchars($pet['name']) ?>
        </a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- ── NO EXACT MATCHES — SHOW CLOSEST ── -->
  <?php elseif (!empty($closest_matches)): ?>
  <div class="closest-section">
    <h3>🔍 Closest Matches</h3>
    <p class="subtitle">
      No pets matched all your preferences, so we relaxed
      <?php
        $labels = array_map('filterLabel', $dropped_filters);
        echo '<strong>' . implode('</strong> and <strong>', $labels) . '</strong>';
      ?>
      to find these for you:
    </p>
    <div class="pet-grid">
      <?php foreach ($closest_matches as $pet): ?>
      <div class="pet-card">
        <?php if (!empty($pet['image'])): ?>
          <img class="pet-card-img"
               src="../upload/<?= htmlspecialchars($pet['image']) ?>"
               alt="<?= htmlspecialchars($pet['name']) ?>"
               onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
          <div class="pet-card-img-placeholder" style="display:none;">🐾</div>
        <?php else: ?>
          <div class="pet-card-img-placeholder">🐾</div>
        <?php endif; ?>
        <div class="pet-card-body">
          <p class="pet-card-name"><?= htmlspecialchars($pet['name']) ?></p>
          <div class="pet-tags">
            <span class="pet-tag"><?= htmlspecialchars($pet['type']) ?></span>
            <span class="pet-tag"><?= htmlspecialchars($pet['size']) ?></span>
            <span class="pet-tag"><?= htmlspecialchars($pet['gender']) ?></span>
            <span class="pet-tag"><?= htmlspecialchars($pet['temperament']) ?></span>
            <?php if (!empty($pet['age'])): ?>
              <span class="pet-tag"><?= htmlspecialchars($pet['age']) ?> yr<?= $pet['age'] > 1 ? 's' : '' ?></span>
            <?php endif; ?>
            <!-- Show which filters were dropped -->
            <?php foreach ($dropped_filters as $df): ?>
              <?php
                $label = filterLabel($df);
                $val   = $_POST[$df] ?? '';
                if ($val === '') continue;
              ?>
              <span class="pet-tag muted" title="You preferred: <?= htmlspecialchars($val) ?>">
                ≈ <?= $label ?>
              </span>
            <?php endforeach; ?>
          </div>
          <?php if (!empty($pet['description'])): ?>
            <p class="pet-card-desc"><?= htmlspecialchars($pet['description']) ?></p>
          <?php endif; ?>
          <a href="adopt.php?pet_id=<?= $pet['id'] ?>" target="_blank" class="btn-adopt">
            <i class="bi bi-heart-fill"></i> Adopt <?= htmlspecialchars($pet['name']) ?>
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ── TRULY NO RESULTS ── -->
  <?php else: ?>
  <div class="section-card no-results">
    <span class="no-icon">🐾</span>
    <h2>No Pets Found</h2>
    <p>We couldn't find any pets matching your preferences right now.<br>
       Try going back and selecting <strong>No Preference</strong> for some filters.</p>
  </div>
  <?php endif; ?>

  <!-- Back button -->
  <div class="text-center mb-5">
    <a href="match.php" class="btn-back">
      <i class="bi bi-arrow-left"></i> Try Different Preferences
    </a>
  </div>

</div>
</body>
</html>
