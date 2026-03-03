<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

include '../config.php';
$stmt = $conn->prepare("SELECT a.*, p.name as pet_name FROM adoptions a JOIN pets p ON a.pet_id = p.id ORDER BY a.id DESC");
$stmt->execute();
$result = $stmt->get_result();
$adoptions = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Adoption Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        table tbody tr:hover {
            background: linear-gradient(90deg, #ffe6f0, #ffb3d9);
        }

        th {
            background: linear-gradient(45deg, #ff66b3, #ff1a75);
            color: white;
            text-align: center;
        }

        td {
            vertical-align: middle;
        }

        .table {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        .btn-approve { background: #4CAF50; color: white; font-weight: bold; }
        .btn-reject { background: #f44336; color: white; font-weight: bold; }
        .status-pending { color: orange; font-weight: bold; }
        .status-approved { color: green; font-weight: bold; }
        .status-rejected { color: red; font-weight: bold; }
    </style>
</head>
<body>
<div class="container mt-5 mb-5">
    <h1 class="text-center mb-4">Adoption Requests</h1>

    <?php if(count($adoptions) > 0): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pet</th>
                    <th>Adopter Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Preferences</th>
                    <th>Message</th>
                    <th>Requested At</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($adoptions as $a): ?>
                <tr>
                    <td><?= $a['id'] ?></td>
                    <td><?= htmlspecialchars($a['pet_name']) ?></td>
                    <td><?= htmlspecialchars($a['adopter_name']) ?></td>
                    <td><?= htmlspecialchars($a['adopter_email']) ?></td>
                    <td><?= htmlspecialchars($a['adopter_phone']) ?></td>
                    <td><?= htmlspecialchars($a['preferences']) ?></td>
                    <td><?= htmlspecialchars($a['message']) ?></td>
                    <td><?= $a['requested_at'] ?></td>
                    <td class="status-<?= strtolower($a['status']) ?>">
                        <?= $a['status'] ?? 'Pending' ?>
                    </td>
                    <td>
                        <?php if(($a['status'] ?? 'Pending') == 'Pending'): ?>
                            <a href="approve.php?approve=<?= $a['id'] ?>" 
                               class="btn btn-approve btn-sm"
                               onclick="return confirm('Approve this application?')">Approve</a>
                            <a href="approve.php?reject=<?= $a['id'] ?>" 
                               class="btn btn-reject btn-sm"
                               onclick="return confirm('Reject this application?')">Reject</a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <p class="text-center alert alert-success">No adoption requests yet!</p>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="index.php" class="btn btn-secondary btn-lg">Back to Dashboard</a>
    </div>
</div>
</body>
</html>
