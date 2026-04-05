<?php
// Fonction pour formater les montants
function formatMoney($amount) {
    return number_format($amount, 2, ',', ' ') . ' MAD';
}

// Fonction pour calculer le solde
function getBalance($pdo, $userId) {
    $stmt = $pdo->prepare("
        SELECT 
            COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as total_income,
            COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as total_expense
        FROM transactions 
        WHERE user_id = ?
    ");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    
    return [
        'income' => $result['total_income'],
        'expense' => $result['total_expense'],
        'balance' => $result['total_income'] - $result['total_expense']
    ];
}

// Fonction pour obtenir les transactions récentes
function getRecentTransactions($pdo, $userId, $limit = 10) {
    $stmt = $pdo->prepare("
        SELECT * FROM transactions 
        WHERE user_id = ? 
        ORDER BY date DESC, id DESC 
        LIMIT ?
    ");
    $stmt->execute([$userId, $limit]);
    return $stmt->fetchAll();
}

// Fonction pour obtenir les dépenses par catégorie
function getExpensesByCategory($pdo, $userId) {
    $stmt = $pdo->prepare("
        SELECT category, SUM(amount) as total 
        FROM transactions 
        WHERE user_id = ? AND type = 'expense' 
        GROUP BY category 
        ORDER BY total DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// Vérifier si l'utilisateur est connecté
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }
}

// Nettoyer les entrées utilisateur
function cleanInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>