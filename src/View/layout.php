<?php
// src/View/layout.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = !empty($_SESSION['user_id'] ?? null);
$pseudo     = $_SESSION['user_pseudo'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) : 'EcoRide' ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="logo">EcoRide</div>

    <button class="menu-toggle" type="button" aria-label="Ouvrir le menu">
        <svg width="26" height="26" viewBox="0 0 24 24">
            <path fill="white" d="M3 6h18v2H3zm0 5h18v2H3zm0 5h18v2H3z"/>
        </svg>
    </button>

    <nav class="nav">
        <a href="index.php?page=home">Accueil</a>
        <a href="index.php?page=carpools">Covoiturages</a>

        <?php if ($isLoggedIn): ?>
            <a href="index.php?page=account">
                Mon compte<?= $pseudo ? ' (' . htmlspecialchars($pseudo) . ')' : '' ?>
            </a>
            <a href="index.php?page=logout">Déconnexion</a>
        <?php else: ?>
            <a href="index.php?page=login">Connexion</a>
        <?php endif; ?>

        <a href="index.php?page=contact">Contact</a>
    </nav>
</header>

<main class="main">
    <?= $content ?? '' ?>
</main>

<footer class="footer">
    <p>
        Contact :
        <a href="mailto:contact@ecoride.fr">contact@ecoride.fr</a>
    </p>
    <p>
        <a href="index.php?page=legal">Mentions légales</a>
    </p>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>
