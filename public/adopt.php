<?php
include "../config.php";

$pet    = null;
$success = false;
$error   = "";

// ── Load pet ──────────────────────────────────────────────
if (isset($_GET['pet_id'])) {
    $pet_id = intval($_GET['pet_id']);
    $stmt   = $conn->prepare("SELECT * FROM pets WHERE id = ?");
    $stmt->bind_param("i", $pet_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pet    = $result->num_rows > 0 ? $result->fetch_assoc() : null;
    $stmt->close();
    if (!$pet) die("Pet not found.");
} else {
    die("Invalid request.");
}

// ── Handle form submission ────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $pet_id   = intval($_POST['pet_id']);
    $fullname = trim($_POST['fullname'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $phone    = trim($_POST['phone']    ?? '');

    // Pack extra fields into preferences & message
    $prefs = [];
    if (!empty($_POST['home_type']))      $prefs[] = "Home: "             . $_POST['home_type'];
    if (!empty($_POST['num_people']))     $prefs[] = "Household size: "   . $_POST['num_people'];
    if (!empty($_POST['has_pets']))       $prefs[] = "Has pets: "         . $_POST['has_pets'];
    if (!empty($_POST['hours_alone']))    $prefs[] = "Hours alone: "      . $_POST['hours_alone'];
    if (!empty($_POST['monthly_budget'])) $prefs[] = "Monthly budget: "  . $_POST['monthly_budget'];
    if (!empty($_POST['owned_before']))   $prefs[] = "Owned pets before: ". $_POST['owned_before'];
    $preferences = implode(" | ", $prefs);

    $parts = [];
    if (!empty($_POST['reason']))        $parts[] = "Reason: "       . $_POST['reason'];
    if (!empty($_POST['stay_location'])) $parts[] = "Pet will stay: ". $_POST['stay_location'];
    if (!empty($_POST['travel_plan']))   $parts[] = "Travel plan: "  . $_POST['travel_plan'];
    if (!empty($_POST['what_happened'])) $parts[] = "Past pets: "    . $_POST['what_happened'];
    $message = implode("\n", $parts);

    $stmt = $conn->prepare("
        INSERT INTO adoptions (pet_id, fullname, email, phone, preferences, message, status)
        VALUES (?, ?, ?, ?, ?, ?, 'Pending')
    ");
    $stmt->bind_param("isssss", $pet_id, $fullname, $email, $phone, $preferences, $message);
    $success = $stmt->execute();
    if (!$success) $error = "Something went wrong. Please try again.";
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Adopt <?= htmlspecialchars($pet['name']) ?> – PawsConnect</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Pacifico&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/adopt.css" rel="stylesheet">
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

<!-- ── HERO ───────────────────────────────────────────────── -->
<div class="adopt-hero">
  <div class="container">
    <a href="index.php" class="text-white text-decoration-none d-inline-flex align-items-center gap-2 mb-4"
       style="opacity:.8; font-weight:700; font-size:.9rem;">
      <i class="bi bi-arrow-left"></i> Back to Pets
    </a>
    <h1>Adoption Application</h1>
    <p>You're one step closer to giving a pet a forever home <i class="bi bi-paw-fill"></i></p>
  </div>
</div>

<!-- ── MAIN CONTENT ───────────────────────────────────────── -->
<div class="container" style="max-width:780px;">

  <!-- Pet Preview -->
  <div class="pet-preview">
    <?php if (!empty($pet['image'])): ?>
      <img src="../upload/<?= htmlspecialchars($pet['image']) ?>"
           alt="<?= htmlspecialchars($pet['name']) ?>"
           onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
      <div class="pet-placeholder" style="display:none;"><i class="bi bi-emoji-smile-fill"></i></div>
    <?php else: ?>
      <div class="pet-placeholder"><i class="bi bi-emoji-smile-fill"></i></div>
    <?php endif; ?>
    <div>
      <p class="pet-name"><?= htmlspecialchars($pet['name']) ?></p>
      <div class="pet-meta">
        <?php if (!empty($pet['species'])): ?><span><i class="bi bi-heart-fill"></i> <?= htmlspecialchars($pet['species']) ?></span><?php endif; ?>
        <?php if (!empty($pet['breed'])):   ?><span><?= htmlspecialchars($pet['breed']) ?></span><?php endif; ?>
        <?php if (!empty($pet['age'])):     ?><span><?= htmlspecialchars($pet['age']) ?></span><?php endif; ?>
      </div>
    </div>
  </div>

  <!-- ── SUCCESS SCREEN ──────────────────────────────────── -->
  <?php if ($success): ?>
  <div class="section-card success-screen">
    <div class="success-icon"><i class="bi bi-paw-fill"></i></div>
    <h2>Application Submitted!</h2>
    <p>Thank you for applying to adopt <strong><?= htmlspecialchars($pet['name']) ?></strong>.<br>
       The shelter will review your application and reach out to you soon.</p>
    <a href="index.php" class="btn-back-home">
      <i class="bi bi-house-heart-fill"></i> Back to All Pets
    </a>
  </div>

  <?php else: ?>

  <?php if ($error): ?>
    <div class="alert alert-danger d-flex align-items-center gap-2">
      <i class="bi bi-exclamation-circle-fill"></i> <?= $error ?>
    </div>
  <?php endif; ?>

  <!-- ── STEP BAR ────────────────────────────────────────── -->
  <div class="steps-bar">
    <div class="step-item active" onclick="goToStep(1)">
      <div class="step-num">1</div><div class="step-label">You</div>
    </div>
    <div class="step-connector" id="con-1"></div>
    <div class="step-item" onclick="goToStep(2)">
      <div class="step-num">2</div><div class="step-label">Household</div>
    </div>
    <div class="step-connector" id="con-2"></div>
    <div class="step-item" onclick="goToStep(3)">
      <div class="step-num">3</div><div class="step-label">Pet Care</div>
    </div>
    <div class="step-connector" id="con-3"></div>
    <div class="step-item" onclick="goToStep(4)">
      <div class="step-num">4</div><div class="step-label">Experience</div>
    </div>
    <div class="step-connector" id="con-4"></div>
    <div class="step-item" onclick="goToStep(5)">
      <div class="step-num">5</div><div class="step-label">Submit</div>
    </div>
  </div>

  <div class="progress-wrap">
    <div class="progress-fill" id="progressFill" style="width:20%"></div>
  </div>

  <!-- ── FORM ────────────────────────────────────────────── -->
  <form method="post" id="adoptForm" novalidate>
    <input type="hidden" name="pet_id" value="<?= $pet['id'] ?>">

    <!-- Step 1 · Contact Info -->
    <div class="form-section active" id="step-1">
      <div class="section-card">
        <div class="section-heading">
          <div class="heading-icon"><i class="bi bi-person-fill"></i></div>
          <div><h4>Your Information</h4><p>Basic contact details</p></div>
        </div>
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Full Name <span class="req">*</span></label>
            <input class="form-control" name="fullname" placeholder="e.g. Juan dela Cruz" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email Address <span class="req">*</span></label>
            <input class="form-control" type="email" name="email" placeholder="you@email.com" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone Number <span class="req">*</span></label>
            <input class="form-control" name="phone" placeholder="e.g. 09XX XXX XXXX" required>
          </div>
        </div>
      </div>
      <div class="nav-btns">
        <span></span>
        <button type="button" class="btn-next" onclick="nextStep(1)">
          Next <i class="bi bi-arrow-right"></i>
        </button>
      </div>
    </div>

    <!-- Step 2 · Household -->
    <div class="form-section" id="step-2">
      <div class="section-card">
        <div class="section-heading">
          <div class="heading-icon"><i class="bi bi-house-fill"></i></div>
          <div><h4>Household Information</h4><p>Help us understand your living situation</p></div>
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Type of Home</label>
            <select name="home_type" class="form-select">
              <option value="">Select type…</option>
              <option>House</option>
              <option>Rent</option>
              <option>Apartment</option>
              <option>Condo</option>
              <option>Others</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Number of People in Household</label>
            <input class="form-control" name="num_people" placeholder="e.g. 4">
          </div>
          <div class="col-md-6">
            <label class="form-label">Do you currently have other pets?</label>
            <select name="has_pets" class="form-select">
              <option value="">Select…</option>
              <option>Yes</option>
              <option>No</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Does your landlord allow pets?</label>
            <select name="landlord_allow" class="form-select">
              <option value="">Select…</option>
              <option>Yes</option>
              <option>No</option>
              <option>N/A – I own the property</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Children's Ages <span class="optional-badge">Optional</span></label>
            <input class="form-control" name="children_info" placeholder="e.g. 5, 9 — or 'No children'">
          </div>
        </div>
      </div>
      <div class="nav-btns">
        <button type="button" class="btn-prev" onclick="prevStep(2)">
          <i class="bi bi-arrow-left"></i> Back
        </button>
        <button type="button" class="btn-next" onclick="nextStep(2)">
          Next <i class="bi bi-arrow-right"></i>
        </button>
      </div>
    </div>

    <!-- Step 3 · Pet Care -->
    <div class="form-section" id="step-3">
      <div class="section-card">
        <div class="section-heading">
          <div class="heading-icon"><i class="bi bi-heart-fill"></i></div>
          <div>
            <h4>Pet Care Plan</h4>
            <p>How will you care for <?= htmlspecialchars($pet['name']) ?>?</p>
          </div>
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Hours Pet Will Be Alone Daily</label>
            <input class="form-control" name="hours_alone" placeholder="e.g. 4–6 hours">
          </div>
          <div class="col-md-6">
            <label class="form-label">Monthly Budget for Pet (₱)</label>
            <input class="form-control" name="monthly_budget" placeholder="e.g. ₱2,000">
          </div>
          <div class="col-12">
            <label class="form-label">Why do you want to adopt? <span class="req">*</span></label>
            <textarea class="form-control" name="reason" placeholder="Share your reason for adopting…" required></textarea>
          </div>
          <div class="col-12">
            <label class="form-label">Where will the pet stay?</label>
            <input class="form-control" name="stay_location" placeholder="e.g. Inside the house, backyard with shelter">
          </div>
          <div class="col-12">
            <label class="form-label">If you travel or move, what happens to the pet?</label>
            <textarea class="form-control" name="travel_plan" placeholder="Describe your plan…"></textarea>
          </div>
        </div>
      </div>
      <div class="nav-btns">
        <button type="button" class="btn-prev" onclick="prevStep(3)">
          <i class="bi bi-arrow-left"></i> Back
        </button>
        <button type="button" class="btn-next" onclick="nextStep(3)">
          Next <i class="bi bi-arrow-right"></i>
        </button>
      </div>
    </div>

    <!-- Step 4 · Experience -->
    <div class="form-section" id="step-4">
      <div class="section-card">
        <div class="section-heading">
          <div class="heading-icon"><i class="bi bi-star-fill"></i></div>
          <div><h4>Pet Ownership Experience</h4><p>Your history with animals</p></div>
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Have you owned pets before?</label>
            <select name="owned_before" class="form-select">
              <option value="">Select…</option>
              <option>Yes</option>
              <option>No</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">If yes, what happened to them? <span class="optional-badge">Optional</span></label>
            <textarea class="form-control" name="what_happened" placeholder="e.g. Passed away of old age, rehomed…"></textarea>
          </div>
          <div class="col-12">
            <label class="form-label">Have you ever surrendered a pet? If so, why? <span class="optional-badge">Optional</span></label>
            <textarea class="form-control" name="surrendered" placeholder="Be honest — it won't automatically disqualify you"></textarea>
          </div>
        </div>
      </div>
      <div class="nav-btns">
        <button type="button" class="btn-prev" onclick="prevStep(4)">
          <i class="bi bi-arrow-left"></i> Back
        </button>
        <button type="button" class="btn-next" onclick="nextStep(4)">
          Next <i class="bi bi-arrow-right"></i>
        </button>
      </div>
    </div>

    <!-- Step 5 · Agreement & Submit -->
    <div class="form-section" id="step-5">
      <div class="section-card">
        <div class="section-heading">
          <div class="heading-icon"><i class="bi bi-pen-fill"></i></div>
          <div><h4>Review & Submit</h4><p>Almost there!</p></div>
        </div>
        <div class="agreement-box">
          <span class="agreement-title"><i class="bi bi-clipboard-check-fill"></i> Adoption Agreement</span>
          I, the applicant, declare that all information provided is true and accurate.
          I understand that false information may result in rejection of my application.
          I commit to providing a loving, safe, and permanent home for the adopted pet
          and to comply with all conditions set by Kalibo Pet Shelter.
        </div>
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Additional Notes <span class="optional-badge">Optional</span></label>
            <textarea class="form-control" name="extra_notes" placeholder="Anything else you'd like the shelter to know…"></textarea>
          </div>
        </div>
      </div>
      <div class="nav-btns">
        <button type="button" class="btn-prev" onclick="prevStep(5)">
          <i class="bi bi-arrow-left"></i> Back
        </button>
        <button type="submit" name="submit" class="btn-submit">
          Submit Application <i class="bi bi-send-fill"></i>
        </button>
      </div>
    </div>

  </form>
  <?php endif; ?>

</div><!-- /container -->

<div style="height:60px;"></div>

<!-- ── JAVASCRIPT ───────────────────────────────────────── -->
<script>
  let currentStep = 1;
  const totalSteps = 5;

  function goToStep(n) {
    document.getElementById('step-' + currentStep).classList.remove('active');
    currentStep = n;
    document.getElementById('step-' + currentStep).classList.add('active');

    document.querySelectorAll('.step-item').forEach((el, i) => {
      el.classList.remove('active', 'done');
      if (i + 1 < currentStep) el.classList.add('done');
      if (i + 1 === currentStep) el.classList.add('active');
    });

    for (let i = 1; i < totalSteps; i++) {
      const c = document.getElementById('con-' + i);
      if (c) c.classList.toggle('done', i < currentStep);
    }

    document.getElementById('progressFill').style.width = (currentStep / totalSteps * 100) + '%';
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function nextStep(from) {
    const section = document.getElementById('step-' + from);
    let valid = true;
    section.querySelectorAll('[required]').forEach(el => {
      if (!el.value.trim()) {
        el.style.borderColor = '#ff4d4d';
        el.style.boxShadow   = '0 0 0 3px rgba(255,77,77,.15)';
        valid = false;
        el.addEventListener('input', () => {
          el.style.borderColor = '';
          el.style.boxShadow   = '';
        }, { once: true });
      }
    });
    if (!valid) { section.querySelector('[required]').focus(); return; }
    if (from < totalSteps) goToStep(from + 1);
  }

  function prevStep(from) {
    if (from > 1) goToStep(from - 1);
  }
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
