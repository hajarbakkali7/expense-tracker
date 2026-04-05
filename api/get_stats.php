<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
checkAuth();

header('Content-Type: application/json');

$userId = $_SESSION['user_id'];
$period = $_GET['period'] ?? 'month';

// Déterminer la période
switch ($period) {
    case 'week':
        $dateFrom = date('Y-m-d', strtotime('-7 days'));
        break;
    case 'month':
        $dateFrom = date('Y-m-d', strtotime('-30 days'));
        break;
    case 'year':
        $dateFrom = date('Y-m-d', strtotime('-365 days'));
        break;
    default:
        $dateFrom = date('Y-m-d', strtotime('-30 days'));
}

// Statistiques par catégorie
$stmt = $pdo->prepare("
    SELECT 
        category,
        type,
        SUM(amount) as total,
        COUNT(*) as count
    FROM transactions 
    WHERE user_id = ? AND date >= ?
    GROUP BY category, type
    ORDER BY total DESC
");
$stmt->execute([$userId, $dateFrom]);
$categoryStats = $stmt->fetchAll();

// Évolution journalière
$stmt = $pdo->prepare("
    SELECT 
        DATE(date) as day,
        type,
        SUM(amount) as total
    FROM transactions 
    WHERE user_id = ? AND date >= ?
    GROUP BY DATE(date), type
    ORDER BY day ASC
");
$stmt->execute([$userId, $dateFrom]);
$dailyStats = $stmt->fetchAll();

echo json_encode([
    'categories' => $categoryStats,
    'daily' => $dailyStats,
    'period' => $period
]);
?>