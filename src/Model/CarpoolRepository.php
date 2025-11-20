<?php

namespace App\Model;

use PDO;

class CarpoolRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findByCityAndDate(string $departureCity, string $arrivalCity, string $date): array
    {
        $stmt = $this->pdo->prepare("
             SELECT 
            c.*,
            u.pseudo AS driver_pseudo,
            u.photo AS driver_photo,
            u.rating AS driver_rating,
            v.energy AS vehicle_energy
        FROM carpools AS c
        JOIN users AS u ON u.id = c.driver_id
        JOIN vehicles AS v ON v.id = c.vehicle_id
        WHERE c.departure_city = :dep
          AND c.arrival_city   = :arr
          AND DATE(c.departure_datetime) = :date
          AND c.remaining_seats > 0
        ORDER BY c.departure_datetime ASC
        ");

        $stmt->execute([
            ':dep'  => $departureCity,
            ':arr'  => $arrivalCity,
            ':date' => $date,
        ]);

        return $stmt->fetchAll();
    }

    public function findNextAvailable(string $departureCity, string $arrivalCity, string $date): ?array
    {
        $stmt = $this->pdo->prepare("
           SELECT 
            c.*,
            u.pseudo AS driver_pseudo,
            u.photo AS driver_photo,
            u.rating AS driver_rating,
            v.energy AS vehicle_energy
        FROM carpools AS c
        JOIN users AS u ON u.id = c.driver_id
        JOIN vehicles AS v ON v.id = c.vehicle_id
        WHERE c.departure_city = :dep
          AND c.arrival_city   = :arr
          AND DATE(c.departure_datetime) > :date
          AND c.remaining_seats > 0
        ORDER BY c.departure_datetime ASC
        LIMIT 1
        ");

        $stmt->execute([
            ':dep'  => $departureCity,
            ':arr'  => $arrivalCity,
            ':date' => $date,
        ]);

        $result = $stmt->fetch();

        return $result ?: null;
    }
}
