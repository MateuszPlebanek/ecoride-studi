<?php

namespace App\Controller;

use App\Model\CarpoolRepository;

class CarpoolController
{
    public function index(): void
    {
        $title = 'Covoiturages - EcoRide';

        // Connexion BDD
        require __DIR__ . '/../../config/db.php';

        // Instanciation du repository
        $repository = new CarpoolRepository($pdo);

        // Récupération des paramètres de recherche (GET)
        $departureCity = $_GET['departure_city'] ?? null;
        $arrivalCity   = $_GET['arrival_city'] ?? null;
        $departureDate = $_GET['departure_date'] ?? null; // format YYYY-MM-DD

        $carpools         = [];
        $suggestedCarpool = null;
        $requestedDate    = $departureDate;

        if ($departureCity && $arrivalCity && $departureDate) {
            // 1) Trajets correspondant à la recherche
            $carpools = $repository->findByCityAndDate(
                $departureCity,
                $arrivalCity,
                $departureDate
            );

            // 2) Si aucun résultat, chercher le prochain trajet disponible
            if (empty($carpools)) {
                $suggestedCarpool = $repository->findNextAvailable(
                    $departureCity,
                    $arrivalCity,
                    $departureDate
                );
            }
        }

        ob_start();
        require __DIR__ . '/../View/carpools.php';
        $content = ob_get_clean();

        require __DIR__ . '/../View/layout.php';
    }
}
