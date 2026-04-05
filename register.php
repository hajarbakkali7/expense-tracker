<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Si l'utilisateur est déjà connecté, rediriger vers le dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: pages/dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = cleanInput($_POST['name']);
    $email = cleanInput($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = "Tous les champs sont obligatoires";
    } elseif ($password !== $confirmPassword) {
        $error = "Les mots de passe ne correspondent pas";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse email invalide";
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = "Cet email est déjà utilisé";
        } else {
            // Créer le compte
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$name, $email, $hashedPassword])) {
                // Connexion automatique après inscription
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['user_name'] = $name;
                header('Location: pages/dashboard.php');
                exit;
            } else {
                $error = "Une erreur est survenue lors de l'inscription";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Expense Manager</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-user-plus"></i>
                <h1>Créer un compte</h1>
                <p>Rejoignez Expense Manager</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-exclamation-circle"></i> 
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="name">
                        <i class="fas fa-user"></i> Nom complet
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-control" 
                           placeholder="Ex: Mohamed Alami"
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control" 
                           placeholder="exemple@email.com"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Mot de passe
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           placeholder="••••••••"
                           required>
                    <small style="color: var(--gray); display: block; margin-top: 0.5rem;">
                        <i class="fas fa-info-circle"></i> Minimum 6 caractères
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">
                        <i class="fas fa-lock"></i> Confirmer le mot de passe
                    </label>
                    <input type="password" 
                           id="confirm_password" 
                           name="confirm_password" 
                           class="form-control" 
                           placeholder="••••••••"
                           required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    <i class="fas fa-user-plus"></i> Créer mon compte
                </button>
            </form>
            
            <div style="text-align: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <p style="color: var(--gray); margin: 0;">
                    Vous avez déjà un compte ? 
                    <a href="index.php" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </a>
                </p>
            </div>
        </div>
    </div>
    
    <script>
        // Validation en temps réel
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirm = this.value;
            
            if (confirm && password !== confirm) {
                this.style.borderColor = 'var(--danger)';
            } else {
                this.style.borderColor = 'var(--border)';
            }
        });
        
        // Afficher la force du mot de passe
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const length = password.length;
            
            if (length > 0 && length < 6) {
                this.style.borderColor = 'var(--danger)';
            } else if (length >= 6 && length < 10) {
                this.style.borderColor = 'var(--warning)';
            } else if (length >= 10) {
                this.style.borderColor = 'var(--secondary)';
            } else {
                this.style.borderColor = 'var(--border)';
            }
        });
    </script>
</body>
</html>