<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
include '../config.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: pets.php"); exit; }

$stmt = $conn->prepare("SELECT * FROM pets WHERE id=?");
$stmt->execute([$id]);
$pet = $stmt->get_result()->fetch_assoc();
if (!$pet) { header("Location: pets.php"); exit; }

$success = $error = '';

if (isset($_POST['submit'])) {
    $name             = $_POST['name'] ?? '';
    $type             = $_POST['type'] ?? '';
    $breed            = $_POST['breed'] ?? '';
    $shelter_location = $_POST['shelter_location'] ?? '';
    $location         = $_POST['location'] ?? '';
    $description      = $_POST['description'] ?? '';
    $size             = $_POST['size'] ?? '';
    $age              = intval($_POST['age'] ?? 0);
    $gender           = $_POST['gender'] ?? '';
    $temperament      = $_POST['temperament'] ?? '';
    $vaccines         = $_POST['vaccines'] ?? '';
    $neutered         = isset($_POST['neutered']) ? 1 : 0;
    $available        = isset($_POST['available']) ? 1 : 0;

    $image = $pet['image'];
    if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
        $image = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], '../upload/' . $image);
    }

    $update = $conn->prepare("UPDATE pets SET name=?,type=?,breed=?,shelter_location=?,location=?,description=?,size=?,age=?,gender=?,temperament=?,image=?,vaccines=?,neutered=?,available=? WHERE id=?");
    if ($update->execute([$name,$type,$breed,$shelter_location,$location,$description,$size,$age,$gender,$temperament,$image,$vaccines,$neutered,$available,$id])) {
        $pet = array_merge($pet, $_POST);
        $success = 'Pet updated successfully!';
    } else {
        $error = 'Something went wrong. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Pet – PawsConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="form-page-body">
<div class="admin-wrapper">
<?php include 'sidebar.php'; ?>
<div class="main-content">
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Pet</h1>
        <a href="pets.php" class="btn btn-secondary">← Back to Pets</a>
    </div>

    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

    <div class="card p-4">
        <form method="post" enctype="multipart/form-data">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($pet['name'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-control" required>
                        <option value="">Select Type</option>
                        <?php foreach (['Dog','Cat','Bird','Other'] as $o): ?>
                            <option value="<?= $o ?>" <?= ($pet['type'] ?? '') == $o ? 'selected' : '' ?>><?= $o ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Breed</label>
                    <input type="text" name="breed" class="form-control" value="<?= htmlspecialchars($pet['breed'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-control">
                        <option value="">Select Gender</option>
                        <?php foreach (['Male','Female'] as $o): ?>
                            <option value="<?= $o ?>" <?= ($pet['gender'] ?? '') == $o ? 'selected' : '' ?>><?= $o ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Age (years)</label>
                    <input type="number" name="age" class="form-control" value="<?= htmlspecialchars($pet['age'] ?? '') ?>" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Size</label>
                    <select name="size" class="form-control">
                        <?php foreach (['Small','Medium','Large'] as $o): ?>
                            <option value="<?= $o ?>" <?= ($pet['size'] ?? '') == $o ? 'selected' : '' ?>><?= $o ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Temperament</label>
                    <select name="temperament" class="form-control">
                        <?php foreach (['Friendly','Calm','Playful','Shy','Aggressive','Energetic','Independent'] as $o): ?>
                            <option value="<?= $o ?>" <?= ($pet['temperament'] ?? '') == $o ? 'selected' : '' ?>><?= $o ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Shelter Location</label>
                    <input type="text" name="shelter_location" class="form-control" value="<?= htmlspecialchars($pet['shelter_location'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Location / Area</label>
                    <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($pet['location'] ?? '') ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($pet['description'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Vaccines</label>
                <input type="text" name="vaccines" class="form-control" value="<?= htmlspecialchars($pet['vaccines'] ?? '') ?>" placeholder="e.g. Rabies, Distemper">
            </div>
            <div class="mb-3">
                <label class="form-label">Current Photo</label><br>
                <?php if (!empty($pet['image']) && file_exists('../upload/' . $pet['image'])): ?>
                    <img src="../upload/<?= htmlspecialchars($pet['image']) ?>" width="150" class="rounded mb-2">
                <?php else: ?>
                    <p class="text-muted">No photo uploaded.</p>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Change Photo</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="d-flex gap-4 mb-3">
                <div class="form-check">
                    <input type="checkbox" name="neutered" class="form-check-input" id="neutered" <?= !empty($pet['neutered']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="neutered">Neutered / Spayed</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="available" class="form-check-input" id="available" <?= !empty($pet['available']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="available">Available for Adoption</label>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" name="submit" class="btn btn-pink flex-fill">Update Pet</button>
                <a href="pets.php" class="btn btn-secondary flex-fill">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
</body>
</html>
