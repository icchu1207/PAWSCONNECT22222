<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Find a Match – PawsConnect</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Pacifico&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/match.css" rel="stylesheet">
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
<div class="match-hero">
  <div class="container">
    <a href="index.php" class="text-white text-decoration-none d-inline-flex align-items-center gap-2 mb-4"
       style="opacity:.8; font-weight:700; font-size:.9rem;">
      <i class="bi bi-arrow-left"></i> Back to Pets
    </a>
    <h1>Find Your Perfect Match</h1>
    <p>Tell us what you're looking for and we'll find your ideal companion <i class="bi bi-paw-fill"></i></p>
  </div>
</div>

<div class="container" style="max-width:680px;">

  <!-- Step Bar -->
  <div class="steps-bar mt-4" id="stepsBar">
    <div class="step-item active" onclick="goToStep(1)">
      <div class="step-num">1</div><div class="step-label">About You</div>
    </div>
    <div class="step-connector" id="con-1"></div>
    <div class="step-item" onclick="goToStep(2)">
      <div class="step-num">2</div><div class="step-label">Pet Type</div>
    </div>
    <div class="step-connector" id="con-2"></div>
    <div class="step-item" onclick="goToStep(3)">
      <div class="step-num">3</div><div class="step-label">Traits</div>
    </div>
    <div class="step-connector" id="con-3"></div>
    <div class="step-item" onclick="goToStep(4)">
      <div class="step-num">4</div><div class="step-label">Lifestyle</div>
    </div>
  </div>

  <div class="progress-wrap">
    <div class="progress-fill" id="progressFill" style="width:25%"></div>
  </div>

  <form action="results.php" method="post" id="matchForm" novalidate>

    <!-- ── STEP 1 · About You ── -->
    <div class="form-section active" id="step-1">
      <div class="section-card">
        <div class="section-heading">
          <div class="heading-icon"><i class="bi bi-person-fill"></i></div>
          <div><h4>About You</h4><p>Just a few contact details</p></div>
        </div>
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Full Name <span class="req">*</span></label>
            <input class="form-control" name="name" placeholder="e.g. Juan dela Cruz" required>
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

    <!-- ── STEP 2 · Pet Type & Size ── -->
    <div class="form-section" id="step-2">
      <div class="section-card">
        <div class="section-heading">
          <div class="heading-icon"><i class="bi bi-heart-fill"></i></div>
          <div><h4>What Kind of Pet?</h4><p>Pick a type and size — or leave as No Preference</p></div>
        </div>

        <label class="form-label mb-2">Pet Type <span class="optional-badge">Optional</span></label>
        <div class="pref-group mb-4">
          <label class="pref-tile">
            <input type="radio" name="pref_type" value="">
            <div class="tile-inner"><span class="tile-emoji"><i class="bi bi-paw-fill"></i></span>No Preference</div>
          </label>
          <label class="pref-tile">
            <input type="radio" name="pref_type" value="Dog">
            <div class="tile-inner"><span class="tile-emoji"><i class="fa-solid fa-dog"></i></span>Dog</div>
          </label>
          <label class="pref-tile">
            <input type="radio" name="pref_type" value="Cat">
            <div class="tile-inner"><span class="tile-emoji"><i class="fa-solid fa-cat"></i></span>Cat</div>
          </label>
          <label class="pref-tile">
            <input type="radio" name="pref_type" value="Bird">
            <div class="tile-inner"><span class="tile-emoji"><i class="fa-solid fa-dove"></i></span>Bird</div>
          </label>
        </div>

        <label class="form-label mb-2">Pet Size <span class="optional-badge">Optional</span></label>
        <div class="pref-group">
          <label class="pref-tile">
            <input type="radio" name="pref_size" value="">
            <div class="tile-inner"><span class="tile-emoji"><i class="bi bi-paw-fill"></i></span>No Preference</div>
          </label>
          <label class="pref-tile">
            <input type="radio" name="pref_size" value="Small">
            <div class="tile-inner"><span class="tile-emoji"><i class="bi bi-arrows-angle-contract"></i></span>Small</div>
          </label>
          <label class="pref-tile">
            <input type="radio" name="pref_size" value="Medium">
            <div class="tile-inner"><span class="tile-emoji"><i class="bi bi-dash-circle"></i></span>Medium</div>
          </label>
          <label class="pref-tile">
            <input type="radio" name="pref_size" value="Large">
            <div class="tile-inner"><span class="tile-emoji"><i class="bi bi-arrows-angle-expand"></i></span>Large</div>
          </label>
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

    <!-- ── STEP 3 · Traits ── -->
    <div class="form-section" id="step-3">
      <div class="section-card">
        <div class="section-heading">
          <div class="heading-icon"><i class="bi bi-stars"></i></div>
          <div><h4>Personality & Traits</h4><p>What personality fits your home?</p></div>
        </div>

        <label class="form-label mb-2">Temperament <span class="optional-badge">Optional</span></label>
        <div class="pref-group mb-4">
          <label class="pref-tile">
            <input type="radio" name="pref_temperament" value="">
            <div class="tile-inner"><span class="tile-emoji"><i class="bi bi-paw-fill"></i></span>No Preference</div>
          </label>
          <label class="pref-tile">
            <input type="radio" name="pref_temperament" value="Friendly">
            <div class="tile-inner"><span class="tile-emoji"><i class="bi bi-emoji-laughing-fill"></i></span>Friendly</div>
          </label>
          <label class="pref-tile">
            <input type="radio" name="pref_temperament" value="Calm">
            <div class="tile-inner"><span class="tile-emoji"><i class="bi bi-emoji-neutral-fill"></i></span>Calm</div>
          </label>
          <label class="pref-tile">
            <input type="radio" name="pref_temperament" value="Playful">
            <div class="tile-inner"><span class="tile-emoji"><i class="bi bi-controller"></i></span>Playful</div>
          </label>
          <label class="pref-tile">
            <input type="radio" name="pref_temperament" value="Independent">
            <div class="tile-inner"><span class="tile-emoji"><i class="bi bi-person-walking"></i></span>Independent</div>
          </label>
        </div>

        <label class="form-label mb-2">Gender <span class="optional-badge">Optional</span></label>
        <div class="pref-group">
          <label class="pref-tile">
            <input type="radio" name="pref_gender" value="">
            <div class="tile-inner"><span class="tile-emoji"><i class="bi bi-paw-fill"></i></span>No Preference</div>
          </label>
          <label class="pref-tile">
            <input type="radio" name="pref_gender" value="Male">
            <div class="tile-inner"><span class="tile-emoji"><i class="bi bi-gender-male"></i></span>Male</div>
          </label>
          <label class="pref-tile">
            <input type="radio" name="pref_gender" value="Female">
            <div class="tile-inner"><span class="tile-emoji"><i class="bi bi-gender-female"></i></span>Female</div>
          </label>
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

    <!-- ── STEP 4 · Lifestyle ── -->
    <div class="form-section" id="step-4">
      <div class="section-card">
        <div class="section-heading">
          <div class="heading-icon"><i class="bi bi-house-heart-fill"></i></div>
          <div><h4>Your Lifestyle</h4><p>Help us find a pet that fits your life</p></div>
        </div>

        <label class="form-label mb-2">Preferred Age Range <span class="optional-badge">Optional</span></label>
        <div class="pref-group mb-4">
          <label class="pref-tile">
            <input type="radio" name="pref_age_range" value="">
            <div class="tile-inner"><span class="tile-emoji"><i class="bi bi-paw-fill"></i></span>No Preference</div>
          </label>
          <label class="pref-tile">
            <input type="radio" name="pref_age_range" value="0-1">
            <div class="tile-inner"><span class="tile-emoji"><i class="bi bi-stars"></i></span>Baby<small style="color:#aaa;font-weight:600;font-size:.7rem;">0–1 yr</small></div>
          </label>
          <label class="pref-tile">
            <input type="radio" name="pref_age_range" value="2-4">
            <div class="tile-inner"><span class="tile-emoji"><i class="bi bi-lightning-fill"></i></span>Young<small style="color:#aaa;font-weight:600;font-size:.7rem;">2–4 yrs</small></div>
          </label>
          <label class="pref-tile">
            <input type="radio" name="pref_age_range" value="5-8">
            <div class="tile-inner"><span class="tile-emoji"><i class="bi bi-tree-fill"></i></span>Adult<small style="color:#aaa;font-weight:600;font-size:.7rem;">5–8 yrs</small></div>
          </label>
          <label class="pref-tile">
            <input type="radio" name="pref_age_range" value="9-99">
            <div class="tile-inner"><span class="tile-emoji"><i class="bi bi-house-heart-fill"></i></span>Senior<small style="color:#aaa;font-weight:600;font-size:.7rem;">9+ yrs</small></div>
          </label>
        </div>

        <label class="form-label mb-2">Neutered / Spayed <span class="optional-badge">Optional</span></label>
        <div class="pref-group">
          <label class="pref-tile">
            <input type="radio" name="pref_neutered" value="">
            <div class="tile-inner"><span class="tile-emoji"><i class="bi bi-paw-fill"></i></span>No Preference</div>
          </label>
          <label class="pref-tile">
            <input type="radio" name="pref_neutered" value="1">
            <div class="tile-inner"><span class="tile-emoji"><i class="bi bi-check-circle-fill text-success"></i></span>Yes</div>
          </label>
          <label class="pref-tile">
            <input type="radio" name="pref_neutered" value="0">
            <div class="tile-inner"><span class="tile-emoji"><i class="bi bi-x-circle-fill text-danger"></i></span>No</div>
          </label>
        </div>
      </div>
      <div class="nav-btns">
        <button type="button" class="btn-prev" onclick="prevStep(4)">
          <i class="bi bi-arrow-left"></i> Back
        </button>
        <button type="submit" class="btn-submit">
          Find My Match <i class="bi bi-search-heart"></i>
        </button>
      </div>
    </div>

  </form>
</div>

<div style="height:60px;"></div>

<script>
  let currentStep = 1;
  const totalSteps = 4;

  // Default all radio groups to "No Preference" (empty value) on load
  document.addEventListener('DOMContentLoaded', () => {
    ['pref_type','pref_size','pref_temperament','pref_gender','pref_age_range','pref_neutered'].forEach(name => {
      const first = document.querySelector(`input[name="${name}"][value=""]`);
      if (first) first.checked = true;
    });
  });

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
