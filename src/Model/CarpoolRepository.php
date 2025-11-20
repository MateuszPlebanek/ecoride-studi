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

    public function findDetail(int $id): ?array
    {
        $sql = "
            SELECT 
                c.*,
                u.id          AS driver_id,
                u.pseudo      AS driver_pseudo,
                u.photo       AS driver_photo,
                u.rating      AS driver_rating,
                u.preferences AS driver_preferences,
                v.model       AS vehicle_model,
                v.energy      AS vehicle_energy,
                b.name        AS vehicle_brand
            FROM carpools c
            JOIN users u    ON c.driver_id  = u.id
            JOIN vehicles v ON c.vehicle_id = v.id
            JOIN brands b   ON v.brand_id   = b.id
            WHERE c.id = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $carpool = $stmt->fetch();
        return $carpool ?: null;
    }

    public function findReviewsForCarpool(int $carpoolId): array
    {
        $sql = "
            SELECT 
                r.id,
                r.rating,
                r.comment,
                r.created_at,
                u.pseudo AS author_pseudo
            FROM reviews r
            LEFT JOIN users u ON r.author_id = u.id
            WHERE r.carpool_id = :carpool_id
            ORDER BY r.created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':carpool_id', $carpoolId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function userAlreadyInCarpool(int $userId, int $carpoolId): bool
    {
        $sql = "
            SELECT id
            FROM passenger_trips
            WHERE user_id = :user_id
              AND carpool_id = :carpool_id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id'    => $userId,
            ':carpool_id' => $carpoolId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function participateUser(int $userId, int $carpoolId): bool
    {
        try {
            $this->pdo->beginTransaction();

            $stmtCarpool = $this->pdo->prepare("
                SELECT *
                FROM carpools
                WHERE id = :id
                FOR UPDATE
            ");
            $stmtCarpool->execute([':id' => $carpoolId]);
            $carpool = $stmtCarpool->fetch(PDO::FETCH_ASSOC);

            if (!$carpool) {
                $this->pdo->rollBack();
                return false;
            }

            if ((int) $carpool['remaining_seats'] <= 0) {
                $this->pdo->rollBack();
                return false;
            }

            $stmtUser = $this->pdo->prepare("
                SELECT id, pseudo, credits
                FROM users
                WHERE id = :id
                FOR UPDATE
            ");
            $stmtUser->execute([':id' => $userId]);
            $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $this->pdo->rollBack();
                return false;
            }

            $price = (float) $carpool['price'];

            if ((float) $user['credits'] < $price) {
                $this->pdo->rollBack();
                return false;
            }

            if ($this->userAlreadyInCarpool($userId, $carpoolId)) {
                $this->pdo->rollBack();
                return false;
            }

            $stmtInsert = $this->pdo->prepare("
                INSERT INTO passenger_trips (user_id, carpool_id, created_at)
                VALUES (:user_id, :carpool_id, NOW())
            ");
            $stmtInsert->execute([
                ':user_id'    => $userId,
                ':carpool_id' => $carpoolId,
            ]);

            $stmtCredits = $this->pdo->prepare("
                UPDATE users
                SET credits = credits - :price
                WHERE id = :user_id
            ");
            $stmtCredits->execute([
                ':price'   => $price,
                ':user_id' => $userId,
            ]);

            $stmtSeats = $this->pdo->prepare("
                UPDATE carpools
                SET remaining_seats = remaining_seats - 1
                WHERE id = :carpool_id
            ");
            $stmtSeats->execute([
                ':carpool_id' => $carpoolId,
            ]);

            $this->pdo->commit();
            return true;

        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return false;
        }
    }
}
