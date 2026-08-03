# Expense_Tracker
Expense Tracker est une application web permettant de gérer facilement ses finances personnelles. L'utilisateur entre ses revenus et dépenses, et l'application calcule automatiquement le solde, génère un historique complet et affiche des statistiques détaillées pour comprendre où part son argent.

✨ Fonctionnalités
🔐 Authentification sécurisée — Inscription et connexion avec mot de passe haché
💰 Gestion des revenus — Enregistrement de toutes les sources de revenus
💸 Suivi des dépenses — Catégorisation complète des dépenses
📊 Tableau de bord — Vue d'ensemble en temps réel (revenus, dépenses, solde)
📋 Historique des transactions — Filtres, recherche et pagination
📈 Statistiques avancées — Répartition par catégorie, top des dépenses, évolution mensuelle sur 6 mois
⚙️ Gestion du profil — Modification des informations et du mot de passe
🎨 Design moderne et responsive — Compatible mobile, tablette et desktop
🛠️ Technologies utilisées
Technologie	Usage
PHP	Logique backend et gestion des sessions
MySQL / PDO	Stockage des données avec requêtes préparées
HTML5 / CSS3	Structure et design de l'interface
JavaScript	Interactions dynamiques (filtres, notifications, validations)
Font Awesome	Icônes
📦 Prérequis
XAMPP (ou WAMP/MAMP) avec PHP 7.4+ et MySQL
Un navigateur web moderne
🔒Sécurité
Mots de passe hachés avec password_hash() (bcrypt)
Requêtes SQL préparées (PDO) contre les injections SQL
Nettoyage systématique des entrées utilisateur
Vérification de l'authentification sur toutes les pages protégées
Vérification de propriété des données avant toute suppression
