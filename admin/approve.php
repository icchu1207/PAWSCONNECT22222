<?php
session_start();
include "../config.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: /kalibo_pet_shelter/public/login.php");
    exit;
}

if (isset($_GET['approve'])) {
    $adoption_id = intval($_GET['approve']);


    $stmt = $conn->prepare("SELECT pet_id FROM adoptions WHERE id = ?");
    $stmt->bind_param("i", $adoption_id);
    $stmt->execute();
    $result_pet = $stmt->get_result();

    if ($result_pet->num_rows > 0) {
        $data = $result_pet->fetch_assoc();
        $pet_id = $data['pet_id'];
        $stmt->close();

        $stmt = $conn->prepare("UPDATE adoptions SET status = 'Approved' WHERE id = ?");
        $stmt->bind_param("i", $adoption_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("UPDATE pets SET is_available = 0 WHERE id = ?");
        $stmt->bind_param("i", $pet_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: /kalibo_pet_shelter/admin/adoptions.php");
    exit;
}

if (isset($_GET['reject'])) {
    $adoption_id = intval($_GET['reject']);

    $stmt = $conn->prepare("UPDATE adoptions SET status = 'Rejected' WHERE id = ?");
    $stmt->bind_param("i", $adoption_id);
    $stmt->execute();
    $stmt->close();

    header("Location: /kalibo_pet_shelter/admin/adoptions.php");
    exit;
}

header("Location: /kalibo_pet_shelter/admin/adoptions.php");
exit;
?>
