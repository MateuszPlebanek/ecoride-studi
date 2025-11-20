<section class="auth">
    <div class="auth-card">
        <h1 class="auth-title">Connexion</h1>

        <?php if (!empty($_GET['registered'])): ?>
            <p class="auth-message auth-message--success">
                Votre compte a été créé, vous pouvez maintenant vous connecter.
            </p>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <p class="auth-message auth-message--error">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="post" action="index.php?page=login" class="auth-form">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($csrfToken ?? '') ?>"
            >

            <div class="auth-field">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="vous@example.com"
                    required
                >
            </div>

            <div class="auth-field">
                <label for="password">Mot de passe</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Votre mot de passe"
                    required
                >
            </div>

            <button type="submit" class="auth-button">
                Se connecter
            </button>
        </form>

        <p class="auth-bottom-text">
            Pas encore de compte ?
            <a href="index.php?page=register">Créer un compte</a>
        </p>
    </div>
</section>
