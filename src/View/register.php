<section class="auth">
    <div class="auth-card">
        <h1 class="auth-title">Créer un compte</h1>

        <?php if (!empty($error)): ?>
            <p class="error-msg">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="post"
              action="index.php?page=register"
              class="auth-form">

            <input type="hidden" name="csrf_token"
                   value="<?= htmlspecialchars($csrfToken ?? '') ?>">

            <div class="auth-field">
                <label for="pseudo">Pseudo</label>
                <input
                    type="text"
                    id="pseudo"
                    name="pseudo"
                    placeholder="Votre pseudo"
                    required
                    value="<?= htmlspecialchars($_POST['pseudo'] ?? '') ?>"
                >
            </div>

            <div class="auth-field">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="vous@example.com"
                    required
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                >
            </div>

            <div class="auth-field">
                <label for="password">Mot de passe</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Mot de passe sécurisé"
                    required
                >
                <small class="auth-help">
                    Au moins 8 caractères, avec une majuscule, une minuscule et un chiffre.
                </small>
            </div>

            <button type="submit" class="auth-button">
                Créer mon compte
            </button>
        </form>

        <p class="auth-bottom-text">
            Vous avez déjà un compte ?
            <a href="index.php?page=login">Se connecter</a>
        </p>
    </div>
</section>
