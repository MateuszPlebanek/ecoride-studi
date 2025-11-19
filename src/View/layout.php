<?php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? htmlspecialchars($title) : 'EcoRide' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="logo">EcoRide</div>

    <button class="menu-toggle" aria-label="Ouvrir le menu">
        <svg width="26" height="26" viewBox="0 0 24 24">
            <path fill="white" d="M3 6h18v2H3zm0 5h18v2H3zm0 5h18v2H3z"/>
        </svg>
    </button>

    <nav class="nav">
        <a href="index.php?page=home">Accueil</a>
        <a href="index.php?page=carpools">Covoiturages</a>
        <a href="index.php?page=login">Connexion</a>
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
