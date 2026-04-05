<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
checkAuth();

if (isset($_GET['id'])) {
    $transactionId = intval($_GET['id']);
    $userId = $_SESSION['user_id'];
    
    // Vérifier que la transaction appartient à l'utilisateur
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
    
    if ($stmt->execute([$transactionId, $userId])) {
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '?deleted=1');
    } else {
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=1');
    }
} else {
    header('Location: ../pages/dashboard.php');
}
exit;
?>