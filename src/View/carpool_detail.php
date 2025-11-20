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
                    <strong>De :</strong>
                    <?= htmlspecialchars($carpool['departure_city']) ?>
                </p>
                <p class="trip-city">
                    <strong>À :</strong>
                    <?= htmlspecialchars($carpool['arrival_city']) ?>
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
                    <?= number_format((float) $carpool['price'], 2, ',', ' ') ?> €
                </p>
                <p>
                    <strong>Places restantes :</strong>
                    <?= (int) $carpool['remaining_seats'] ?>
                </p>
                <p>
                    <strong>Voyage écologique :</strong>
                    <?= ((int) $carpool['is_electric'] === 1) ? 'Oui' : 'Non' ?>
                </p>
            </div>
        </div>
    </section>

    <section class="detail-card detail-driver">
        <div class="detail-driver-header">
            <?php
            
            $photo = !empty($carpool['driver_photo'])
                ? $carpool['driver_photo']
                : 'assets/images/driver-avatar.svg';
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

    <?php
    
    $isLoggedIn   = !empty($_SESSION['user_id'] ?? null);
    $hasSeatsLeft = (int) $carpool['remaining_seats'] > 0;
    ?>

    <?php if ($hasSeatsLeft): ?>
        <form
            action="index.php"
            method="post"
            class="participate-form js-participate-form"
            data-logged="<?= $isLoggedIn ? '1' : '0' ?>"
            data-price="<?= htmlspecialchars($carpool['price']) ?>"
            data-login-url="index.php?page=login"
        >
            <input type="hidden" name="page" value="carpool_participate">
            <input type="hidden" name="id" value="<?= (int) $carpool['id'] ?>">

            <button type="submit" class="btn-participate">
                Participer à ce covoiturage
            </button>
        </form>
    <?php else: ?>
        <p class="no-seats">
            Ce covoiturage est complet.
        </p>
   <?php endif; ?>
  
    <div class="modal-overlay" id="login-modal">
        <div class="modal-box">
            <button
                type="button"
                class="modal-close"
                aria-label="Fermer la fenêtre de connexion requise"
                data-close-modal="login-modal"
            >
                &times;
            </button>

            <h2>Connexion requise</h2>
            <p>
                Pour participer à ce covoiturage, vous devez être connecté ou créer un compte.
            </p>

            <div class="modal-actions">
                <a href="index.php?page=login" class="btn-modal">
                    Se connecter / Créer un compte
                </a>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="confirm-modal">
        <div class="modal-box">
            <button
                type="button"
                class="modal-close"
                aria-label="Fermer la fenêtre de confirmation"
                data-close-modal="confirm-modal"
            >
                &times;
            </button>

            <h2>Confirmer votre participation</h2>
            <p id="confirm-modal-text"></p>

            <div class="modal-actions">
                <button
                    type="button"
                    id="confirm-modal-yes"
                    class="btn-modal"
                >
                    Confirmer
                </button>

                <button
                    type="button"
                    class="btn-modal btn-modal-secondary"
                    data-close-modal="confirm-modal"
                >
                    Annuler
                </button>
            </div>
        </div>
    </div>

    <p class="detail-back-link">
        <a href="index.php?page=carpools">
            ← Retour à la liste des covoiturages
        </a>
    </p>
</section>
