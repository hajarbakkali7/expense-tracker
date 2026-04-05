<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
checkAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $type = cleanInput($_POST['type']);
    $amount = floatval($_POST['amount']);
    $category = cleanInput($_POST['category']);
    $description = cleanInput($_POST['description']);
    $date = $_POST['date'];
    
    $stmt = $pdo->prepare("
        INSERT INTO transactions (user_id, type, amount, category, description, date) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    if ($stmt->execute([$userId, $type, $amount, $category, $description, $date])) {
        header('Location: ../pages/dashboard.php?success=1');
    } else {
        header('Location: ../pages/dashboard.php?error=1');
    }
    exit;
}
?>

═══