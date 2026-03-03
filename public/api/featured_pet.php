<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include __DIR__ . "/../../config.php";

$result = $conn->query("SELECT * FROM pets WHERE available = 1 ORDER BY RAND() LIMIT 1");

if ($result && $result->num_rows > 0) {
    $pet = $result->fetch_assoc();
    $pet['image_url'] = $pet['image'] 
        ? "http://localhost/pawsconnect-main/kalibo_pet_shelter/upload/" . $pet['image']
        : "https://placehold.co/300x300/ffd6e7/ff5c9d?text=🐾";
    echo json_encode($pet);
} else {
    echo json_encode(null);
}
?>
