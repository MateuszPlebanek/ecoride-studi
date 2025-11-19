<section class="hero">
    <div class="hero-text">
        <h1>Trouvez un covoiturage écologique</h1>
        <p>
            EcoRide vous aide à réduire votre impact environnemental
            en partageant vos trajets avec d'autres voyageurs soucieux de l'écologie.
        </p>

        <div class="search-card">
            <h2 class="search-title">Rechercher un itinéraire</h2>

            <form action="index.php" method="get" class="search-form">
                <input type="hidden" name="page" value="carpools">

                <div class="field">
                    <label>Ville de départ</label>
                    <input type="text" name="departure_city" placeholder="Ex : Paris">
                </div>

                <div class="field">
                    <label>Ville d'arrivée</label>
                    <input type="text" name="arrival_city" placeholder="Ex : Lyon">
                </div>

                <div class="field">
                    <label>Date</label>
                    <input type="date" name="departure_date">
                </div>

                <button type="submit">Rechercher un itinéraire</button>
            </form>
        </div>
    </div>

    <div class="hero-image">
        <img src="assets/images/ecoride-hero.jpg" alt="Voiture écologique sur la route">
    </div>
</section>

<section class="about">
    <h2>À propos d’EcoRide</h2>
    <p>
        EcoRide est une plateforme de covoiturage pensée pour les voyageurs soucieux
        de l'environnement. En favorisant les véhicules électriques et le partage des trajets,
        nous contribuons à réduire les émissions de CO₂ et à rendre les déplacements plus économiques.
    </p>

    <div class="about-images">
        <img src="assets/images/ecoride-nature.jpg" alt="Route dans la nature">
        <img src="assets/images/ecoride-car.jpg" alt="Voiture électrique en ville">
        <img src="assets/images/ecoride-people.jpg" alt="Personnes en covoiturage">
    </div>
</section>
