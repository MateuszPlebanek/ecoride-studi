<section class="carpools">
    <h1>Covoiturages disponibles</h1>

    <!-- 🔎 Formulaire de recherche sur la page Covoiturages -->
    <form action="index.php" method="get" class="search-form search-form-inline">
        <input type="hidden" name="page" value="carpools">

        <div class="field">
            <label>Ville de départ</label>
            <input
                type="text"
                name="departure_city"
                value="<?= htmlspecialchars($departureCity ?? '') ?>"
                placeholder="Ex : Paris"
            >
        </div>

        <div class="field">
            <label>Ville d'arrivée</label>
            <input
                type="text"
                name="arrival_city"
                value="<?= htmlspecialchars($arrivalCity ?? '') ?>"
                placeholder="Ex : Lyon"
            >
        </div>

        <div class="field">
            <label>Date</label>
            <input
                type="date"
                name="departure_date"
                value="<?= htmlspecialchars($requestedDate ?? '') ?>"
            >
        </div>

        <button type="submit">Rechercher</button>
    </form>

    <hr>

    <?php if (empty($departureCity) || empty($arrivalCity) || empty($requestedDate)): ?>
        <p>
            Aucun covoiturage n’est affiché par défaut.<br>
            Veuillez renseigner une ville de départ, une ville d’arrivée et une date
            pour lancer une recherche.
        </p>
    <?php else: ?>

        <p>
            Résultats pour
            <strong><?= htmlspecialchars($departureCity) ?></strong> →
            <strong><?= htmlspecialchars($arrivalCity) ?></strong>
            le <strong><?= htmlspecialchars(date('d/m/Y', strtotime($requestedDate))) ?></strong>.
        </p>

        <?php if (!empty($carpools)): ?>

            <div class="carpools-list">
                <?php foreach ($carpools as $carpool): ?>
                    <article class="carpool-card">
                        <div class="carpool-header">
                            <div class="driver-info">
                                <?php if (!empty($carpool['driver_photo'])): ?>
                                    <img class="driver-photo"
                                         src="<?= htmlspecialchars($carpool['driver_photo']) ?>"
                                         alt="Photo de <?= htmlspecialchars($carpool['driver_pseudo']) ?>">
                                <?php endif; ?>

                                <div>
                                    <div class="driver-pseudo">
                                        <?= htmlspecialchars($carpool['driver_pseudo']) ?>
                                    </div>
                                    <?php if (!is_null($carpool['driver_rating'])): ?>
                                        <div class="driver-rating">
                                            ⭐ <?= htmlspecialchars($carpool['driver_rating']) ?>/5
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if ((int)$carpool['is_electric'] === 1): ?>
                                <span class="badge-eco">Voyage écologique</span>
                            <?php endif; ?>
                        </div>

                        <div class="carpool-body">
                            <p class="route">
                                <?= htmlspecialchars($carpool['departure_city']) ?>
                                → <?= htmlspecialchars($carpool['arrival_city']) ?>
                            </p>
                            <p class="datetime">
                                Départ :
                                <?= date('d/m/Y H:i', strtotime($carpool['departure_datetime'])) ?><br>
                                Arrivée :
                                <?= date('d/m/Y H:i', strtotime($carpool['arrival_datetime'])) ?>
                            </p>
                            <p class="seats-price">
                                Places restantes :
                                <strong><?= (int)$carpool['remaining_seats'] ?></strong><br>
                                Prix par place :
                                <strong><?= number_format($carpool['price'], 2, ',', ' ') ?> €</strong>
                            </p>
                        </div>

                        <div class="carpool-footer">
                            <button class="btn-detail" type="button">
                                Détail
                            </button>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

        <?php else: ?>

            <p>Aucun covoiturage n’est disponible pour cette date.</p>

            <?php if (!empty($suggestedCarpool)): ?>
                <p>
                    Le premier covoiturage disponible est le
                    <strong><?= date('d/m/Y H:i', strtotime($suggestedCarpool['departure_datetime'])) ?></strong>.
                </p>
            <?php else: ?>
                <p>
                    Aucun covoiturage n’est disponible prochainement pour cet itinéraire.
                    Vous pouvez essayer une autre ville ou une autre date.
                </p>
            <?php endif; ?>

        <?php endif; ?>

    <?php endif; ?>
</section>
