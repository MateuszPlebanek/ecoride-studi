<?php

namespace App\Model;

use PDO;

class CarpoolRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findByCityAndDate(
        string $departureCity,
        string $arrivalCity,
        string $date,
        array $filters = []
    ): array {
        $sql = "
            SELECT 
                c.*,
                TIMESTAMPDIFF(
                    MINUTE,
                    c.departure_datetime,
                    c.arrival_datetime
                ) AS duration,
                u.pseudo AS driver_pseudo,
                u.photo  AS driver_photo,
                u.rating AS driver_rating,
                v.model  AS vehicle_model,
                v.energy AS vehicle_energy,
                b.name   AS vehicle_brand
            FROM carpools c
            JOIN users u    ON c.driver_id  = u.id
            JOIN vehicles v ON c.vehicle_id = v.id
            JOIN brands b   ON v.brand_id   = b.id
            WHERE c.departure_city = :dep
              AND c.arrival_city   = :arr
              AND DATE(c.departure_datetime) = :date
              AND c.remaining_seats > 0
        ";

        if (!empty($filters['eco'])) {
            $sql .= " AND c.is_electric = 1";
        }

        if (!empty($filters['max_price'])) {
            $sql .= " AND c.price <= :max_price";
        }

        if (!empty($filters['max_duration'])) {
            $sql .= " AND TIMESTAMPDIFF(
                MINUTE,
                c.departure_datetime,
                c.arrival_datetime
            ) <= :max_duration";
        }

        if (!empty($filters['min_rating'])) {
            $sql .= " AND u.rating >= :min_rating";
        }

        $sql .= " ORDER BY c.departure_datetime ASC";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':dep',  $departureCity);
        $stmt->bindValue(':arr',  $arrivalCity);
        $stmt->bindValue(':date', $date);

        if (!empty($filters['max_price'])) {
            $stmt->bindValue(':max_price', (float) $filters['max_price']);
        }

        if (!empty($filters['max_duration'])) {
            $stmt->bindValue(':max_duration', (int) $filters['max_duration'], PDO::PARAM_INT);
        }

        if (!empty($filters['min_rating'])) {
            $stmt->bindValue(':min_rating', (float) $filters['min_rating']);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }


    public function findNextAvailable(string $departureCity, string $arrivalCity, string $date): ?array
    {
        $sql = "
            SELECT 
                c.*,
                TIMESTAMPDIFF(
                    MINUTE,
                    c.departure_datetime,
                    c.arrival_datetime
                ) AS duration,
                u.pseudo AS driver_pseudo,
                u.photo  AS driver_photo,
                u.rating AS driver_rating,
                v.model  AS vehicle_model,
                v.energy AS vehicle_energy,
                b.name   AS vehicle_brand
            FROM carpools c
            JOIN users u    ON c.driver_id  = u.id
            JOIN vehicles v ON c.vehicle_id = v.id
            JOIN brands b   ON v.brand_id   = b.id
            WHERE c.departure_city = :dep
              AND c.arrival_city   = :arr
              AND DATE(c.departure_datetime) > :date
              AND c.remaining_seats > 0
            ORDER BY c.departure_datetime ASC
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':dep'  => $departureCity,
            ':arr'  => $arrivalCity,
            ':date' => $date,
        ]);

        $result = $stmt->fetch();
        return $result ?: null;
    }
}