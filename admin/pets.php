<?php
session_start();
include "../config.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM adoptions WHERE pet_id=$id");
    mysqli_query($conn, "DELETE FROM matches WHERE pet_id=$id");
    $res = mysqli_query($conn, "SELECT image FROM pets WHERE id=$id");
    $row = mysqli_fetch_assoc($res);
    if ($row && $row['image'] && file_exists("../upload/" . $row['image'])) {
        unlink("../upload/" . $row['image']);
    }
    mysqli_query($conn, "DELETE FROM pets WHERE id=$id");
    header("Location: pets.php?deleted=1");
    exit;
}

$pets = [];
$result = mysqli_query($conn, "SELECT * FROM pets ORDER BY id DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) $pets[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Pets – PawsConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<div class="admin-wrapper">
<?php include 'sidebar.php'; ?>
<div class="main-content">

    <div class="top-bar">
        <h1 class="m-0" style="color:#e91e63;">Manage Pets</h1>
        <div class="d-flex gap-2">
            <a href="index.php" class="btn btn-secondary">← Dashboard</a>
            <a href="add_pet.php" class="btn btn-pink">+ Add New Pet</a>
        </div>
    </div>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Pet deleted successfully.</div>
    <?php endif; ?>

    <div class="row">
        <?php if (!empty($pets)): ?>
            <?php foreach($pets as $pet): ?>
                <div class="col-md-4 d-flex">
                    <div class="pet-card w-100">
                        <?php
                            $imgPath = '../upload/' . ($pet['image'] ?? '');
                            $imgSrc  = (!empty($pet['image']) && file_exists($imgPath))
                                ? $imgPath
                                : '../assets/images/placeholder.png';
                        ?>
                        <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($pet['name'] ?? 'Pet') ?>">
                        <h5><?= htmlspecialchars($pet['name'] ?? 'N/A') ?></h5>
                        <p class="mb-1"><strong>Type:</strong> <?= htmlspecialchars($pet['type'] ?? 'N/A') ?></p>
                        <p class="mb-1"><strong>Breed:</strong> <?= htmlspecialchars($pet['breed'] ?? 'N/A') ?></p>
                        <p class="mb-1"><strong>Age:</strong> <?= htmlspecialchars($pet['age'] ?? 'N/A') ?></p>
                        <p class="mb-1"><strong>Gender:</strong> <?= htmlspecialchars($pet['gender'] ?? 'N/A') ?></p>
                        <p class="mb-1"><strong>Temperament:</strong> <?= htmlspecialchars($pet['temperament'] ?? 'N/A') ?></p>
                        <p class="mb-2"><strong>Location:</strong> <?= htmlspecialchars($pet['location'] ?? 'N/A') ?></p>
                        <p class="mb-3">
                            <span class="<?= !empty($pet['available']) ? 'badge-available' : 'badge-unavailable' ?>">
                                <?= !empty($pet['available']) ? 'Available' : 'Not Available' ?>
                            </span>
                        </p>
                        <a href="edit_pet.php?id=<?= $pet['id'] ?>" class="btn-edit">Edit</a>
                        <a href="?delete=<?= $pet['id'] ?>" class="btn-delete"
                           onclick="return confirm('Are you sure you want to delete this pet?')">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-muted w-100">No pets yet. <a href="add_pet.php">Add one now</a>.</p>
        <?php endif; ?>
    </div>

</div>
</div>
</body>
</html>
