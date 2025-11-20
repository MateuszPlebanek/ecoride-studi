<section class="account">
    <h1>Mon compte</h1>

    <div class="account-card">
        <div class="account-avatar-wrapper">
            <?php
            $photo = !empty($user['photo'])
                ? $user['photo']
                : 'assets/images/driver-avatar.svg';
            ?>
            <img
                class="account-avatar"
                src="<?= htmlspecialchars($photo) ?>"
                alt="Avatar de <?= htmlspecialchars($user['pseudo']) ?>"
            >
        </div>

        <div class="account-info">
            <h2 class="account-name"><?= htmlspecialchars($user['pseudo']) ?></h2>
            <p class="account-email"><?= htmlspecialchars($user['email']) ?></p>
        </div>

        <div class="account-credits-card">
            <p class="account-credits-label">Crédits disponibles</p>
            <p class="account-credits-value">
                <?= number_format((float)$user['credits'], 2, ',', ' ') ?> crédits
            </p>
            <p class="account-credits-text">
                Vous pouvez utiliser vos crédits pour participer à des covoiturages EcoRide
                depuis la page des trajets.
            </p>
        </div>
    </div>

</section>
