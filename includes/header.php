<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Expense Manager'; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <i class="fas fa-wallet"></i>
                <span>Expense Manager</span>
            </div>
            <ul class="nav-menu">
                <li><a href="dashboard.php" class="nav-link"><i class="fas fa-home"></i> Tableau de bord</a></li>
                <li><a href="transactions.php" class="nav-link"><i class="fas fa-list"></i> Transactions</a></li>
                <li><a href="statistics.php" class="nav-link"><i class="fas fa-chart-pie"></i> Statistiques</a></li>
                <li><a href="settings.php" class="nav-link"><i class="fas fa-cog"></i> Paramètres</a></li>
                <li><a href="../logout.php" class="nav-link logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </div>
    </nav>
    <main class="main-content">