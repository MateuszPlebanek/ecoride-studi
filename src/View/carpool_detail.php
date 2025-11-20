<?php
// src/View/carpool_detail.php
?>

<section class="carpool-detail">
    <h1>Détail du covoiturage</h1>

    <section class="detail-card detail-trip">
        <h2>Trajet</h2>

        <div class="trip-row">
            <div class="trip-cities">
                <p class="trip-city">
                    <strong>De :</strong> <?= htmlspecialchars($carpool['departure_city']) ?>
                </p>
                <p class="trip-city">
                    <strong>À :</strong> <?= htmlspecialchars($carpool['arrival_city']) ?>
                </p>
            </div>

            <div class="trip-dates">
                <p>
                    <strong>Départ :</strong>
                    <?= date('d/m/Y H:i', strtotime($carpool['departure_datetime'])) ?>
                </p>
                <p>
                    <strong>Arrivée :</strong>
                    <?= date('d/m/Y H:i', strtotime($carpool['arrival_datetime'])) ?>
                </p>
            </div>

            <div class="trip-extra">
                <p>
                    <strong>Prix par personne :</strong>
                    <?= number_format((float)$carpool['price'], 2, ',', ' ') ?> €
                </p>
                <p>
                    <strong>Places restantes :</strong>
                    <?= (int)$carpool['remaining_seats'] ?>
                </p>
                <p>
                    <strong>Voyage écologique :</strong>
                    <?= ((int)$carpool['is_electric'] === 1) ? 'Oui' : 'Non' ?>
                </p>
            </div>
        </div>
    </section>

    <section class="detail-card detail-driver">
        <div class="detail-driver-header">
            <?php
            $photo = !empty($carpool['driver_photo'])
                ? $carpool['driver_photo']
                : '/assets/images/driver-avatar.svg';
            ?>
            <img
                class="driver-photo"
                src="<?= htmlspecialchars($photo) ?>"
                alt="Photo de <?= htmlspecialchars($carpool['driver_pseudo']) ?>"
            >

            <div class="driver-main">
                <h2 class="driver-name">
                    <?= htmlspecialchars($carpool['driver_pseudo']) ?>
                </h2>

                <?php if (!is_null($carpool['driver_rating'])): ?>
                    <p class="driver-rating-line">
                        ⭐ <strong><?= htmlspecialchars($carpool['driver_rating']) ?>/5</strong>
                    </p>
                <?php endif; ?>

                <p class="driver-small-info">
                    Covoitureur EcoRide
                </p>
            </div>
        </div>

        <div class="detail-driver-body">
            <section class="detail-block">
                <h3>Véhicule</h3>
                <p><strong>Marque :</strong> <?= htmlspecialchars($carpool['vehicle_brand']) ?></p>
                <p><strong>Modèle :</strong> <?= htmlspecialchars($carpool['vehicle_model']) ?></p>
                <p><strong>Énergie :</strong> <?= htmlspecialchars($carpool['vehicle_energy']) ?></p>
            </section>

            <section class="detail-block">
                <h3>Préférences du conducteur</h3>
                <?php if (!empty($preferences)): ?>
                    <ul>
                        <?php foreach ($preferences as $pref): ?>
                            <li><?= htmlspecialchars($pref) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucune préférence renseignée.</p>
                <?php endif; ?>
            </section>

            <section class="detail-block">
                <h3>Avis sur ce covoiturage</h3>
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $review): ?>
                        <article class="review-card">
                            <p class="review-header">
                                <strong>Passager :</strong>
                                <?= htmlspecialchars($review['author_pseudo'] ?? 'Anonyme') ?>
                            </p>
                            <p class="review-rating">
                                Note : ⭐ <?= htmlspecialchars($review['rating']) ?>/5
                            </p>
                            <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                            <p class="review-date">
                                <?= date('d/m/Y H:i', strtotime($review['created_at'])) ?>
                            </p>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun avis n’a encore été déposé pour ce covoiturage.</p>
                <?php endif; ?>
            </section>
        </div>
    </section>

    <p class="detail-back-link">
        <a href="index.php?page=carpools">
            ← Retour à la liste des covoiturages
        </a>
    </p>
</section>
