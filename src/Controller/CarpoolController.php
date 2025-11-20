<?php

namespace App\Controller;

use App\Model\CarpoolRepository;
use App\Security\Csrf;

class CarpoolController
{
    public function index(): void
    {
        $title = 'Covoiturages - EcoRide';

        require __DIR__ . '/../../config/db.php';

        $repository = new CarpoolRepository($pdo);

        $departureCity = $_GET['departure_city'] ?? null;
        $arrivalCity   = $_GET['arrival_city'] ?? null;
        $departureDate = $_GET['departure_date'] ?? null; 

        $maxPrice = $_GET['max_price'] ?? null;

        $hoursInput   = $_GET['max_duration_hours'] ?? null;
        $minutesInput = $_GET['max_duration_minutes'] ?? null;

        $maxDuration = null;
        if ($hoursInput !== null || $minutesInput !== null) {
            $h = (int) ($hoursInput ?: 0);
            $m = (int) ($minutesInput ?: 0);
            $totalMinutes = $h * 60 + $m;
            if ($totalMinutes > 0) {
                $maxDuration = $totalMinutes;
            }
        }

        $minRating = $_GET['min_rating'] ?? null;
        if ($minRating !== null && $minRating !== '') {
            $minRating = (float) $minRating;
        }

        $filters = [
            'eco'          => $_GET['eco'] ?? null,
            'max_price'    => $maxPrice,
            'max_duration' => $maxDuration,
            'min_rating'   => $minRating,
        ];

        $carpools         = [];
        $suggestedCarpool = null;
        $requestedDate    = $departureDate;

        if ($departureCity && $arrivalCity && $departureDate) {
            $carpools = $repository->findByCityAndDate(
                $departureCity,
                $arrivalCity,
                $departureDate,
                $filters
            );

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

    public function show(): void
    {
        $title = 'Détail du covoiturage - EcoRide';

        require __DIR__ . '/../../config/db.php';
        $repository = new CarpoolRepository($pdo);

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id <= 0) {
            http_response_code(400);
            echo 'Covoiturage invalide.';
            return;
        }

        $carpool = $repository->findDetail($id);

        if ($carpool === null) {
            http_response_code(404);
            echo 'Covoiturage introuvable.';
            return;
        }

        $reviews = $repository->findReviewsForCarpool($id);

        $preferencesRaw = $carpool['driver_preferences'] ?? '';
        $preferences = [];

        if (!empty($preferencesRaw)) {
            $parts = preg_split('/[\r\n,]+/', $preferencesRaw);
            foreach ($parts as $p) {
                $p = trim($p);
                if ($p !== '') {
                    $preferences[] = $p;
                }
            }
        }

        $csrfParticipateToken = Csrf::getToken('participate_form');

        ob_start();
        require __DIR__ . '/../View/carpool_detail.php';
        $content = ob_get_clean();

        require __DIR__ . '/../View/layout.php';
    }

    public function participate(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=carpools');
            exit;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!Csrf::checkToken('participate_form', $token)) {
            http_response_code(400);
            echo "Requête invalide (token CSRF manquant ou invalide).";
            exit;
        }

        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $userId    = (int) $_SESSION['user_id'];
        $carpoolId = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        if ($carpoolId <= 0) {
            header('Location: index.php?page=carpools');
            exit;
        }

        require __DIR__ . '/../../config/db.php';
        $repository = new CarpoolRepository($pdo);

        $success = $repository->participateUser($userId, $carpoolId);
       
        header('Location: index.php?page=carpool_show&id=' . $carpoolId);
        exit;
    }
}
