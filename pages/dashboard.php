<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
checkAuth();

$pageTitle = 'Tableau de bord';
$userId = $_SESSION['user_id'];
$balance = getBalance($pdo, $userId);
$recentTransactions = getRecentTransactions($pdo, $userId, 5);

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

<div class="card">
    <div class="card-header">
        <i class="fas fa-plus-circle"></i>
        Ajouter une transaction
    </div>
    
    <form action="../api/add_transaction.php" method="POST">
        <div class="form-row">
            <div class="form-group">
                <label for="type">Type</label>
                <select id="type" name="type" class="form-control" required>
                    <option value="income">Revenu</option>
                    <option value="expense">Dépense</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="amount">Montant (MAD)</label>
                <input type="number" id="amount" name="amount" step="0.01" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="category">Catégorie</label>
                <select id="category" name="category" class="form-control" required>
                    <option value="Salaire">Salaire</option>
                    <option value="Freelance">Freelance</option>
                    <option value="Alimentation">Alimentation</option>
                    <option value="Transport">Transport</option>
                    <option value="Logement">Logement</option>
                    <option value="Loisirs">Loisirs</option>
                    <option value="Santé">Santé</option>
                    <option value="Autre">Autre</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" id="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" id="description" name="description" class="form-control" placeholder="Ex: Achat de courses">
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Enregistrer
        </button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-history"></i>
        Transactions récentes
    </div>
    
    <div class="table-container">
        <table>
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
                <?php foreach ($recentTransactions as $transaction): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($transaction['date'])); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $transaction['type']; ?>">
                            <?php echo $transaction['type'] === 'income' ? 'Revenu' : 'Dépense'; ?>
                        </span>
                    </td>
                    <td><?php echo $transaction['category']; ?></td>
                    <td><?php echo $transaction['description']; ?></td>
                    <td style="font-weight: bold; color: <?php echo $transaction['type'] === 'income' ? 'var(--secondary)' : 'var(--danger)'; ?>">
                        <?php echo formatMoney($transaction['amount']); ?>
                    </td>
                    <td>
                        <button onclick="confirmDelete(<?php echo $transaction['id']; ?>)" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>