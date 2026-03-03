<?php
include "../config.php";

$success = false;
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $reporter_name  = trim($_POST['reporter_name']  ?? '');
    $reporter_email = trim($_POST['reporter_email'] ?? '');
    $reporter_phone = trim($_POST['reporter_phone'] ?? '');
    $location       = trim($_POST['location']       ?? '');
    $animal_type    = trim($_POST['animal_type']    ?? '');
    $animal_condition = trim($_POST['animal_condition'] ?? '');
    $time_of_sight  = trim($_POST['time_of_sight']  ?? '');
    $description    = trim($_POST['description']    ?? '');

    $photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $upload_dir = "../upload/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $photo = time() . '_' . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo);
    }

    $stmt = $conn->prepare("
        INSERT INTO stray_reports
            (reporter_name, reporter_email, reporter_phone, location, animal_type, animal_condition, time_of_sight, description, photo)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssssssss",
        $reporter_name, $reporter_email, $reporter_phone,
        $location, $animal_type, $animal_condition,
        $time_of_sight, $description, $photo
    );

    if ($stmt->execute()) {
        $success = true;
    } else {
        $error = "Something went wrong. Please try again.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Report a Stray – PawsConnect</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Pacifico&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/stray_report.css" rel="stylesheet">
  <style>
    @font-face {
      font-family: 'CreatoDisplay';
      src: url('../assets/fonts/CreatoDisplay-Black.otf') format('opentype');
      font-weight: 900;
    }
    :root {
      --pink:       #ff5c9d;
      --pink-dark:  #e91e63;
      --pink-light: #ffd6e7;
      --cream:      #fff9f5;
      --text:       #3a2a35;
      --shadow:     0 8px 30px rgba(255, 92, 157, 0.15);
    }
    body { background: var(--cream); color: var(--text); }
    h1, h2 { font-family: 'CreatoDisplay', 'Nunito', sans-serif; }
  </style>
</head>
<body>

<!-- ── HERO ── -->
<div class="stray-hero">
  <div class="container">
    <a href="index.php" class="text-white text-decoration-none d-inline-flex align-items-center gap-2 mb-4"
       style="opacity:.8; font-weight:700; font-size:.9rem;">
      <i class="bi bi-arrow-left"></i> Back to Pets
    </a>
    <h1>Report a Stray Animal</h1>
    <p>Help us rescue animals in need — every report makes a difference <i class="bi bi-paw-fill"></i></p>
  </div>
</div>

<div class="container" style="max-width:700px;">
  <div class="report-card">

    <?php if ($success): ?>
    <!-- ── SUCCESS ── -->
    <div class="success-screen">
      <div class="success-icon"><i class="bi bi-paw-fill"></i></div>
      <h2>Report Submitted!</h2>
      <p>Thank you for looking out for this animal.<br>
         Our team will follow up on your report as soon as possible.</p>
      <a href="index.php" class="btn-back-home">
        <i class="bi bi-house-heart-fill"></i> Back to Home
      </a>
    </div>

    <?php else: ?>

    <?php if ($error): ?>
      <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-exclamation-circle-fill"></i> <?= $error ?>
      </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" id="strayForm" novalidate>

      <!-- ── SECTION 1: Reporter Info ── -->
      <div class="form-section-title">
        <div class="title-icon"><i class="bi bi-person-fill"></i></div>
        <div><h5>Your Information</h5><p>Who is making this report?</p></div>
      </div>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">Your Name <span class="req">*</span></label>
          <input class="form-control" name="reporter_name" placeholder="e.g. Maria Santos" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Email <span class="optional-badge">Optional</span></label>
          <input class="form-control" type="email" name="reporter_email" placeholder="you@email.com">
        </div>
        <div class="col-md-6">
          <label class="form-label">Phone <span class="optional-badge">Optional</span></label>
          <input class="form-control" name="reporter_phone" placeholder="e.g. 09XX XXX XXXX">
        </div>
      </div>

      <!-- ── SECTION 2: Animal Details ── -->
      <div class="form-section-title">
        <div class="title-icon"><i class="bi bi-heart-fill"></i></div>
        <div><h5>Animal Details</h5><p>Tell us about the animal you saw</p></div>
      </div>
      <div class="row g-3">

        <div class="col-12">
          <label class="form-label">Animal Type <span class="req">*</span></label>
          <div class="type-group">
            <label class="type-tile">
              <input type="radio" name="animal_type" value="Dog" required>
              <div class="tile-inner"><span class="tile-emoji"><i class="fa-solid fa-dog"></i></span>Dog</div>
            </label>
            <label class="type-tile">
              <input type="radio" name="animal_type" value="Cat">
              <div class="tile-inner"><span class="tile-emoji"><i class="fa-solid fa-cat"></i></span>Cat</div>
            </label>
            <label class="type-tile">
              <input type="radio" name="animal_type" value="Bird">
              <div class="tile-inner"><span class="tile-emoji"><i class="fa-solid fa-dove"></i></span>Bird</div>
            </label>
            <label class="type-tile">
              <input type="radio" name="animal_type" value="Other">
              <div class="tile-inner"><span class="tile-emoji"><i class="fa-solid fa-paw"></i></span>Other</div>
            </label>
          </div>
        </div>

        <div class="col-12">
          <label class="form-label">Condition of Animal <span class="req">*</span></label>
          <div class="condition-group">
            <label class="condition-pill">
              <input type="radio" name="animal_condition" value="Healthy" required>
              <div class="pill-inner"><i class="bi bi-heart-fill text-success"></i> Healthy</div>
            </label>
            <label class="condition-pill">
              <input type="radio" name="animal_condition" value="Injured">
              <div class="pill-inner"><i class="bi bi-bandaid-fill text-danger"></i> Injured</div>
            </label>
            <label class="condition-pill">
              <input type="radio" name="animal_condition" value="Malnourished">
              <div class="pill-inner"><i class="bi bi-emoji-frown-fill text-warning"></i> Malnourished</div>
            </label>
            <label class="condition-pill">
              <input type="radio" name="animal_condition" value="Aggressive">
              <div class="pill-inner"><i class="bi bi-exclamation-triangle-fill text-danger"></i> Aggressive</div>
            </label>
            <label class="condition-pill">
              <input type="radio" name="animal_condition" value="Unknown">
              <div class="pill-inner"><i class="bi bi-question-circle-fill text-secondary"></i> Unknown</div>
            </label>
          </div>
        </div>

        <div class="col-12">
          <label class="form-label">Description <span class="optional-badge">Optional</span></label>
          <textarea class="form-control" name="description"
            placeholder="Color, size, distinguishing marks, behavior — anything that helps our team identify the animal…"></textarea>
        </div>

      </div>

      <!-- ── SECTION 3: Location & Time ── -->
      <div class="form-section-title">
        <div class="title-icon"><i class="bi bi-geo-alt-fill"></i></div>
        <div><h5>Location & Time</h5><p>Where and when did you see the animal?</p></div>
      </div>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">Location <span class="req">*</span></label>
          <input class="form-control" name="location"
            placeholder="e.g. Cor. Roxas Ave & Mabini St, Kalibo" required>
          <div style="font-size:.78rem;color:#bbb;margin-top:4px;">
            <i class="bi bi-info-circle"></i> Be as specific as possible — street name, barangay, nearby landmarks
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label">Time of Sighting <span class="req">*</span></label>
          <input class="form-control" type="datetime-local" name="time_of_sight" required id="timeInput">
        </div>
      </div>

      <!-- ── SECTION 4: Photo ── -->
      <div class="form-section-title">
        <div class="title-icon"><i class="bi bi-camera-fill"></i></div>
        <div><h5>Photo</h5><p>A photo helps our team respond faster</p></div>
      </div>
      <div class="file-upload-wrap" id="photoWrap">
        <input type="file" name="photo" id="photoInput" accept="image/*"
               onchange="showFileName('photoInput','photoName')">
        <div class="upload-icon"><i class="bi bi-cloud-arrow-up-fill"></i></div>
        <p>Click or drag a photo here (JPG, PNG)</p>
        <div class="file-name" id="photoName"></div>
      </div>

      <!-- ── SUBMIT ── -->
      <div class="d-flex justify-content-between align-items-center mt-4">
        <a href="index.php" class="text-decoration-none"
           style="color:#ff1a75;font-weight:700;font-size:.9rem;">
          <i class="bi bi-arrow-left"></i> Cancel
        </a>
        <button type="submit" name="submit" class="btn-submit">
          Submit Report <i class="bi bi-send-fill"></i>
        </button>
      </div>

    </form>
    <?php endif; ?>

  </div>
</div>

<div style="height:40px;"></div>

<script>
  // Default time to now
  document.addEventListener('DOMContentLoaded', () => {
    const t = document.getElementById('timeInput');
    if (t) {
      const now = new Date();
      now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
      t.value = now.toISOString().slice(0, 16);
    }
  });

  // Show selected filename
  function showFileName(inputId, labelId) {
    const input = document.getElementById(inputId);
    const label = document.getElementById(labelId);
    if (input.files && input.files[0]) {
      label.innerHTML = '<i class="bi bi-check-circle-fill"></i> ' + input.files[0].name;
    }
  }

  // Validate radio groups on submit
  document.getElementById('strayForm')?.addEventListener('submit', function(e) {
    let valid = true;

    // Check animal_type
    const typeSelected = document.querySelector('input[name="animal_type"]:checked');
    if (!typeSelected) {
      document.querySelector('.type-group').style.outline = '2px solid #ff4d4d';
      document.querySelector('.type-group').style.borderRadius = '14px';
      valid = false;
    } else {
      document.querySelector('.type-group').style.outline = '';
    }

    // Check animal_condition
    const condSelected = document.querySelector('input[name="animal_condition"]:checked');
    if (!condSelected) {
      document.querySelector('.condition-group').style.outline = '2px solid #ff4d4d';
      document.querySelector('.condition-group').style.borderRadius = '14px';
      valid = false;
    } else {
      document.querySelector('.condition-group').style.outline = '';
    }

    if (!valid) e.preventDefault();
  });
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
