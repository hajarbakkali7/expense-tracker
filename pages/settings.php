<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
checkAuth();

$pageTitle = 'Paramètres';
$userId = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = cleanInput($_POST['name']);
        $email = cleanInput($_POST['email']);
        
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        if ($stmt->execute([$name, $email, $userId])) {
            $_SESSION['user_name'] = $name;
            $success = "Profil mis à jour avec succès !";
            // Recharger les données
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
        }
    }
    
    if (isset($_POST['change_password'])) {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        if (password_verify($currentPassword, $user['password'])) {
            if ($newPassword === $confirmPassword) {
                if (strlen($newPassword) >= 6) {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    if ($stmt->execute([$hashedPassword, $userId])) {
                        $success = "Mot de passe modifié avec succès !";
                    }
                } else {
                    $error = "Le mot de passe doit contenir au moins 6 caractères";
                }
            } else {
                $error = "Les mots de passe ne correspondent pas";
            }
        } else {
            $error = "Mot de passe actuel incorrect";
        }
    }
    
    if (isset($_POST['delete_all'])) {
        $stmt = $pdo->prepare("DELETE FROM transactions WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $success = "Toutes les transactions ont été supprimées !";
        }
    }
}

// Statistiques du compte
$stmt = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE user_id = ?");
$stmt->execute([$userId]);
$totalTransactions = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT created_at FROM users WHERE id = ?");
$stmt->execute([$userId]);
$accountCreated = $stmt->fetchColumn();

include '../includes/header.php';
?>

<?php if (isset($success)): ?>
<div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
</div>
<?php endif; ?>

<?php if (isset($error)): ?>
<div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
</div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 2rem;">
    
    <!-- Informations du compte -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-user"></i>
            Informations du compte
        </div>
        
        <div style="background: var(--light); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                <div style="width: 60px; height: 60px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold;">
                    <?php echo strtoupper(substr($user['name'], 0, 2)); ?>
                </div>
                <div>
                    <div style="font-weight: bold; font-size: 1.2rem;"><?php echo $user['name']; ?></div>
                    <div style="color: var(--gray);"><?php echo $user['email']; ?></div>
                </div>
            </div>
            
            <div style="border-top: 1px solid var(--border); padding-top: 1rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: var(--gray);">📊 Transactions totales</span>
                    <span style="font-weight: bold;"><?php echo $totalTransactions; ?></span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--gray);">📅 Membre depuis</span>
                    <span style="font-weight: bold;"><?php echo date('d/m/Y', strtotime($accountCreated)); ?></span>
                </div>
            </div>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="name">Nom complet</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo $user['name']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo $user['email']; ?>" required>
            </div>
            
            <button type="submit" name="update_profile" class="btn btn-primary">
                <i class="fas fa-save"></i> Mettre à jour le profil
            </button>
        </form>
    </div>
    
    <!-- Changer le mot de passe -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-lock"></i>
            Sécurité
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="current_password">Mot de passe actuel</label>
                <input type="password" id="current_password" name="current_password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="new_password">Nouveau mot de passe</label>
                <input type="password" id="new_password" name="new_password" class="form-control" required>
                <small style="color: var(--gray);">Minimum 6 caractères</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            
            <button type="submit" name="change_password" class="btn btn-primary">
                <i class="fas fa-key"></i> Changer le mot de passe
            </button>
        </form>
    </div>
    
</div>

<!-- Zone de danger -->
<div class="card" style="border: 2px solid var(--danger);">
    <div class="card-header" style="color: var(--danger);">
        <i class="fas fa-exclamation-triangle"></i>
        Zone de danger
    </div>
    
    <p style="color: var(--gray); margin-bottom: 1.5rem;">
        Les actions ci-dessous sont irréversibles. Veuillez procéder avec prudence.
    </p>
    
    <form method="POST" onsubmit="return confirm('⚠️ ATTENTION ! Cette action supprimera TOUTES vos transactions de manière permanente. Cette action est irréversible. Êtes-vous absolument sûr de vouloir continuer ?');">
        <button type="submit" name="delete_all" class="btn btn-danger">
            <i class="fas fa-trash-alt"></i> Supprimer toutes les transactions
        </button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>