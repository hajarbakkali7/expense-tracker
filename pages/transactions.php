<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
checkAuth();

$pageTitle = 'Toutes les transactions';
$userId = $_SESSION['user_id'];

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Filtres
$typeFilter = $_GET['type'] ?? 'all';
$categoryFilter = $_GET['category'] ?? 'all';

// Construction de la requête
$where = "user_id = ?";
$params = [$userId];

if ($typeFilter !== 'all') {
    $where .= " AND type = ?";
    $params[] = $typeFilter;
}

if ($categoryFilter !== 'all') {
    $where .= " AND category = ?";
    $params[] = $categoryFilter;
}

// Compter le total
$stmt = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE $where");
$stmt->execute($params);
$totalTransactions = $stmt->fetchColumn();
$totalPages = ceil($totalTransactions / $perPage);

// Récupérer les transactions
$params[] = $perPage;
$params[] = $offset;
$stmt = $pdo->prepare("
    SELECT * FROM transactions 
    WHERE $where 
    ORDER BY date DESC, id DESC 
    LIMIT ? OFFSET ?
");
$stmt->execute($params);
$transactions = $stmt->fetchAll();

// Récupérer toutes les catégories
$stmt = $pdo->prepare("SELECT DISTINCT category FROM transactions WHERE user_id = ? ORDER BY category");
$stmt->execute([$userId]);
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <i class="fas fa-list"></i>
        Historique des transactions
    </div>
    
    <!-- Filtres -->
    <form method="GET" style="margin-bottom: 2rem;">
        <div class="form-row">
            <div class="form-group">
                <label for="type">Type</label>
                <select id="type" name="type" class="form-control" onchange="this.form.submit()">
                    <option value="all" <?php echo $typeFilter === 'all' ? 'selected' : ''; ?>>Tous</option>
                    <option value="income" <?php echo $typeFilter === 'income' ? 'selected' : ''; ?>>Revenus</option>
                    <option value="expense" <?php echo $typeFilter === 'expense' ? 'selected' : ''; ?>>Dépenses</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="category">Catégorie</label>
                <select id="category" name="category" class="form-control" onchange="this.form.submit()">
                    <option value="all" <?php echo $categoryFilter === 'all' ? 'selected' : ''; ?>>Toutes</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat; ?>" <?php echo $categoryFilter === $cat ? 'selected' : ''; ?>>
                            <?php echo $cat; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>&nbsp;</label>
                <a href="transactions.php" class="btn btn-secondary" style="display: block;">
                    <i class="fas fa-times"></i> Réinitialiser
                </a>
            </div>
        </div>
    </form>
    
    <!-- Barre de recherche -->
    <div class="form-group">
        <input type="text" id="searchInput" class="form-control" placeholder="🔍 Rechercher dans les transactions...">
    </div>
    
    <!-- Tableau des transactions -->
    <div class="table-container">
        <table id="transactionsTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Catégorie</th>
                    <th>Description</th>
                    <th>Montant</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($transactions) > 0): ?>
                    <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($transaction['date'])); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $transaction['type']; ?>">
                                <?php echo $transaction['type'] === 'income' ? '💰 Revenu' : '💸 Dépense'; ?>
                            </span>
                        </td>
                        <td><?php echo $transaction['category']; ?></td>
                        <td><?php echo $transaction['description'] ?: '-'; ?></td>
                        <td style="font-weight: bold; color: <?php echo $transaction['type'] === 'income' ? 'var(--secondary)' : 'var(--danger)'; ?>">
                            <?php echo formatMoney($transaction['amount']); ?>
                        </td>
                        <td>
                            <button onclick="confirmDelete(<?php echo $transaction['id']; ?>)" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: var(--gray);">
                            <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                            Aucune transaction trouvée
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 2rem;">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&type=<?php echo $typeFilter; ?>&category=<?php echo $categoryFilter; ?>" 
               class="btn <?php echo $i === $page ? 'btn-primary' : 'btn-secondary'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<script>
// Recherche en temps réel
filterTable('searchInput', 'transactionsTable');
</script>

<?php include '../includes/footer.php'; ?>