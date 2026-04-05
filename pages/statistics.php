<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
checkAuth();

$pageTitle = 'Statistiques';
$userId = $_SESSION['user_id'];

// Statistiques générales
$balance = getBalance($pdo, $userId);
$expensesByCategory = getExpensesByCategory($pdo, $userId);

// Statistiques mensuelles
$stmt = $pdo->prepare("
    SELECT 
        DATE_FORMAT(date, '%Y-%m') as month,
        type,
        SUM(amount) as total
    FROM transactions 
    WHERE user_id = ? AND date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(date, '%Y-%m'), type
    ORDER BY month DESC
");
$stmt->execute([$userId]);
$monthlyStats = $stmt->fetchAll();

// Top 5 dépenses
$stmt = $pdo->prepare("
    SELECT * FROM transactions 
    WHERE user_id = ? AND type = 'expense' 
    ORDER BY amount DESC 
    LIMIT 5
");
$stmt->execute([$userId]);
$topExpenses = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon income">
            <i class="fas fa-arrow-up"></i>
        </div>
        <div class="stat-info">
            <h3>Revenus totaux</h3>
            <p><?php echo formatMoney($balance['income']); ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon expense">
            <i class="fas fa-arrow-down"></i>
        </div>
        <div class="stat-info">
            <h3>Dépenses totales</h3>
            <p><?php echo formatMoney($balance['expense']); ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon balance">
            <i class="fas fa-wallet"></i>
        </div>
        <div class="stat-info">
            <h3>Solde actuel</h3>
            <p><?php echo formatMoney($balance['balance']); ?></p>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">
    <!-- Dépenses par catégorie -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-chart-pie"></i>
            Dépenses par catégorie
        </div>
        
        <?php if (count($expensesByCategory) > 0): ?>
            <?php 
            $totalExpenses = array_sum(array_column($expensesByCategory, 'total'));
            foreach ($expensesByCategory as $category): 
                $percentage = ($category['total'] / $totalExpenses * 100);
            ?>
            <div style="margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="font-weight: 600;"><?php echo $category['category']; ?></span>
                    <span style="color: var(--gray);"><?php echo formatMoney($category['total']); ?></span>
                </div>
                <div style="background: var(--light); border-radius: 10px; height: 10px; overflow: hidden;">
                    <div style="background: linear-gradient(90deg, var(--danger), var(--warning)); height: 100%; width: <?php echo $percentage; ?>%; border-radius: 10px;"></div>
                </div>
                <div style="text-align: right; color: var(--gray); font-size: 0.875rem; margin-top: 0.25rem;">
                    <?php echo number_format($percentage, 1); ?>%
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: var(--gray); padding: 2rem;">
                <i class="fas fa-chart-pie" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
                Aucune dépense enregistrée
            </p>
        <?php endif; ?>
    </div>
    
    <!-- Top 5 dépenses -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-fire"></i>
            Top 5 des dépenses
        </div>
        
        <?php if (count($topExpenses) > 0): ?>
            <?php foreach ($topExpenses as $index => $expense): ?>
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--light); border-radius: 8px; margin-bottom: 1rem;">
                <div style="width: 40px; height: 40px; background: var(--danger); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                    #<?php echo $index + 1; ?>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 600;"><?php echo $expense['description'] ?: $expense['category']; ?></div>
                    <div style="color: var(--gray); font-size: 0.875rem;">
                        <?php echo date('d/m/Y', strtotime($expense['date'])); ?>
                    </div>
                </div>
                <div style="font-weight: bold; color: var(--danger);">
                    <?php echo formatMoney($expense['amount']); ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: var(--gray); padding: 2rem;">
                <i class="fas fa-fire" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
                Aucune dépense enregistrée
            </p>
        <?php endif; ?>
    </div>
</div>

<!-- Évolution mensuelle -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-chart-line"></i>
        Évolution des 6 derniers mois
    </div>
    
    <?php
    // Organiser les données par mois
    $months = [];
    foreach ($monthlyStats as $stat) {
        if (!isset($months[$stat['month']])) {
            $months[$stat['month']] = ['income' => 0, 'expense' => 0];
        }
        $months[$stat['month']][$stat['type']] = $stat['total'];
    }
    
    if (count($months) > 0):
    ?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Mois</th>
                    <th style="color: var(--secondary);">Revenus</th>
                    <th style="color: var(--danger);">Dépenses</th>
                    <th>Solde</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_reverse($months, true) as $month => $data): ?>
                <tr>
                    <td style="font-weight: 600;">
                        <?php 
                        $date = DateTime::createFromFormat('Y-m', $month);
                        echo strftime('%B %Y', $date->getTimestamp()); 
                        ?>
                    </td>
                    <td style="color: var(--secondary); font-weight: bold;">
                        <?php echo formatMoney($data['income']); ?>
                    </td>
                    <td style="color: var(--danger); font-weight: bold;">
                        <?php echo formatMoney($data['expense']); ?>
                    </td>
                    <td style="font-weight: bold; color: <?php echo ($data['income'] - $data['expense']) >= 0 ? 'var(--secondary)' : 'var(--danger)'; ?>">
                        <?php echo formatMoney($data['income'] - $data['expense']); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <p style="text-align: center; color: var(--gray); padding: 2rem;">
            <i class="fas fa-chart-line" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
            Aucune donnée disponible
        </p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>